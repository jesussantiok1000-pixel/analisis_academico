<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class AnalyticsDashboardSource
{
    public static function load(): array
    {
        try {
            $connection = DB::connection('analytics');

            $itemRows = $connection->table('vw_indicador_item')
                ->select([
                    'grado',
                    'seccion',
                    'curso',
                    'item',
                    'orden_item',
                    'adecuados_item',
                    'parciales_item',
                    'inadecuados_item',
                    'omitidos_item',
                ])
                ->get();

            $studentRows = $connection->table('vw_indicador_estudiante')
                ->select([
                    'alumno',
                    'grado',
                    'seccion',
                    'curso',
                    'nivel_logro_raw',
                    'nivel_logro_panel',
                    'hipotesis_escritura',
                    'hipotesis_panel',
                ])
                ->get();
        } catch (Throwable $exception) {
            return [
                'connected' => false,
                'database' => 'academia_analisis',
                'heatmapDatasets' => [],
                'metricDatasets' => [],
                'analyticsDatasets' => [],
                'contextCount' => 0,
                'questionRowCount' => 0,
                'studentRowCount' => 0,
            ];
        }

        $contextKeys = [];

        foreach ($itemRows as $row) {
            $contextKeys[self::selectionKey(
                self::normalizeGrade($row->grado),
                self::normalizeSection($row->seccion),
                self::normalizeCourse($row->curso),
            )] = true;
        }

        return [
            'connected' => true,
            'database' => 'academia_analisis',
            'heatmapDatasets' => self::buildHeatmapDatasets($itemRows),
            'metricDatasets' => self::buildMetricDatasets($itemRows, $studentRows),
            'analyticsDatasets' => self::buildAnalyticsDatasets($studentRows),
            'contextCount' => count($contextKeys),
            'questionRowCount' => $itemRows->count(),
            'studentRowCount' => $studentRows->count(),
        ];
    }

    private static function buildHeatmapDatasets(Collection $rows): array
    {
        $datasets = [];

        foreach ($rows as $row) {
            $grade = self::normalizeGrade($row->grado);
            $section = self::normalizeSection($row->seccion);
            $course = self::normalizeCourse($row->curso);
            $item = strtoupper(trim((string) $row->item));

            foreach (self::selectionCombinations($grade, $section, $course) as [$selectionGrade, $selectionSection, $selectionCourse]) {
                $key = self::selectionKey($selectionGrade, $selectionSection, $selectionCourse);

                if (! isset($datasets[$key])) {
                    $datasets[$key] = [
                        'selection' => [
                            'grade' => $selectionGrade,
                            'section' => $selectionSection,
                            'course' => $selectionCourse,
                        ],
                        'series' => [
                            'adequate' => ['label' => 'Adecuados', 'tone' => 'success', 'values' => []],
                            'partial' => ['label' => 'Parciales', 'tone' => 'lavender', 'values' => []],
                            'inadequate' => ['label' => 'Inadecuados', 'tone' => 'violet', 'values' => []],
                            'omitted' => ['label' => 'Omitidos', 'tone' => 'dark', 'values' => []],
                        ],
                    ];
                }

                $datasets[$key]['series']['adequate']['values'][$item] = ($datasets[$key]['series']['adequate']['values'][$item] ?? 0) + (int) $row->adecuados_item;
                $datasets[$key]['series']['partial']['values'][$item] = ($datasets[$key]['series']['partial']['values'][$item] ?? 0) + (int) $row->parciales_item;
                $datasets[$key]['series']['inadequate']['values'][$item] = ($datasets[$key]['series']['inadequate']['values'][$item] ?? 0) + (int) $row->inadecuados_item;
                $datasets[$key]['series']['omitted']['values'][$item] = ($datasets[$key]['series']['omitted']['values'][$item] ?? 0) + (int) $row->omitidos_item;
            }
        }

        foreach ($datasets as $key => $dataset) {
            foreach ($dataset['series'] as $seriesKey => $series) {
                uksort(
                    $series['values'],
                    fn (string $left, string $right): int => self::questionOrder($left) <=> self::questionOrder($right)
                );

                $datasets[$key]['series'][$seriesKey]['values'] = $series['values'];
            }

            $datasets[$key]['series'] = array_values($datasets[$key]['series']);
        }

        ksort($datasets);

        return $datasets;
    }

    private static function buildMetricDatasets(Collection $itemRows, Collection $studentRows): array
    {
        $datasets = [];
        $studentSets = [];

        foreach ($itemRows as $row) {
            $grade = self::normalizeGrade($row->grado);
            $section = self::normalizeSection($row->seccion);
            $course = self::normalizeCourse($row->curso);
            $itemTotal = (int) $row->adecuados_item + (int) $row->parciales_item + (int) $row->inadecuados_item + (int) $row->omitidos_item;

            foreach (self::selectionCombinations($grade, $section, $course) as [$selectionGrade, $selectionSection, $selectionCourse]) {
                $key = self::selectionKey($selectionGrade, $selectionSection, $selectionCourse);

                if (! isset($datasets[$key])) {
                    $datasets[$key] = self::emptyMetricDataset();
                }

                $datasets[$key]['adequate'] += (int) $row->adecuados_item;
                $datasets[$key]['partial'] += (int) $row->parciales_item;
                $datasets[$key]['inadequate'] += (int) $row->inadecuados_item;
                $datasets[$key]['omitted'] += (int) $row->omitidos_item;
                $datasets[$key]['itemTotal'] += $itemTotal;
            }
        }

        foreach ($studentRows as $row) {
            $grade = self::normalizeGrade($row->grado);
            $section = self::normalizeSection($row->seccion);
            $course = self::normalizeCourse($row->curso);

            foreach (self::selectionCombinations($grade, $section, $course) as [$selectionGrade, $selectionSection, $selectionCourse]) {
                $key = self::selectionKey($selectionGrade, $selectionSection, $selectionCourse);

                if (! isset($datasets[$key])) {
                    $datasets[$key] = self::emptyMetricDataset();
                }

                $studentSets[$key][self::studentIdentity($row, $selectionCourse)] = true;
            }
        }

        foreach ($datasets as $key => $dataset) {
            $datasets[$key]['students'] = isset($studentSets[$key]) ? count($studentSets[$key]) : 0;
        }

        ksort($datasets);

        return $datasets;
    }

    private static function buildAnalyticsDatasets(Collection $rows): array
    {
        $studentSets = [];
        $levelSets = [];
        $hypothesisSets = [];
        $datasets = [];

        foreach ($rows as $row) {
            $grade = self::normalizeGrade($row->grado);
            $section = self::normalizeSection($row->seccion);
            $course = self::normalizeCourse($row->curso);
            $level = self::normalizeLevel($row->nivel_logro_raw ?? $row->nivel_logro_panel);
            $hypothesis = self::normalizeHypothesis($row->hipotesis_escritura ?? $row->hipotesis_panel);

            foreach (self::selectionCombinations($grade, $section, $course) as [$selectionGrade, $selectionSection, $selectionCourse]) {
                $key = self::selectionKey($selectionGrade, $selectionSection, $selectionCourse);
                $studentKey = self::studentIdentity($row, $selectionCourse);

                $studentSets[$key][$studentKey] = true;

                if ($level !== null) {
                    $levelSets[$key][$level][$studentKey] = true;
                }

                if ($hypothesis !== null) {
                    $hypothesisSets[$key][$hypothesis][$studentKey] = true;
                }
            }
        }

        $selectionKeys = array_unique(array_merge(
            array_keys($studentSets),
            array_keys($levelSets),
            array_keys($hypothesisSets),
        ));

        foreach ($selectionKeys as $key) {
            $students = isset($studentSets[$key]) ? count($studentSets[$key]) : 0;
            $previo = isset($levelSets[$key]['previo']) ? count($levelSets[$key]['previo']) : 0;
            $inicio = isset($levelSets[$key]['inicio']) ? count($levelSets[$key]['inicio']) : 0;
            $proceso = isset($levelSets[$key]['proceso']) ? count($levelSets[$key]['proceso']) : 0;
            $logro = isset($levelSets[$key]['logro']) ? count($levelSets[$key]['logro']) : 0;

            $datasets[$key] = [
                'riskBars' => [
                    ['key' => 'previo', 'label' => 'Previo al inicio', 'value' => $previo],
                    ['key' => 'inicio', 'label' => 'Inicio', 'value' => $inicio],
                ],
                'learningDistribution' => [
                    ['key' => 'previo', 'label' => 'Previo al inicio', 'value' => self::percentage($previo, $students), 'color' => '#c85d5d'],
                    ['key' => 'inicio', 'label' => 'Inicio', 'value' => self::percentage($inicio, $students), 'color' => '#d89aaf'],
                    ['key' => 'proceso', 'label' => 'Proceso', 'value' => self::percentage($proceso, $students), 'color' => '#c7a23a'],
                    ['key' => 'logro', 'label' => 'Logrado', 'value' => self::percentage($logro, $students), 'color' => '#5f9870'],
                ],
                'writingHypotheses' => [
                    ['key' => 'presilabico', 'label' => 'Presilabico', 'value' => isset($hypothesisSets[$key]['presilabico']) ? count($hypothesisSets[$key]['presilabico']) : 0],
                    ['key' => 'silabico', 'label' => 'Silabico', 'value' => isset($hypothesisSets[$key]['silabico']) ? count($hypothesisSets[$key]['silabico']) : 0],
                    ['key' => 'silabico_alfabetico', 'label' => 'Silabico-Alfabetico', 'value' => isset($hypothesisSets[$key]['silabico_alfabetico']) ? count($hypothesisSets[$key]['silabico_alfabetico']) : 0],
                    ['key' => 'alfabetico', 'label' => 'Alfabetico', 'value' => isset($hypothesisSets[$key]['alfabetico']) ? count($hypothesisSets[$key]['alfabetico']) : 0],
                ],
            ];
        }

        ksort($datasets);

        return $datasets;
    }

    private static function emptyMetricDataset(): array
    {
        return [
            'adequate' => 0,
            'partial' => 0,
            'inadequate' => 0,
            'omitted' => 0,
            'students' => 0,
            'itemTotal' => 0,
        ];
    }

    private static function selectionCombinations(string $grade, string $section, string $course): array
    {
        return [
            [$grade, $section, $course],
            [$grade, 'all', $course],
            ['all', $section, $course],
            ['all', 'all', $course],
            [$grade, $section, 'all'],
            [$grade, 'all', 'all'],
            ['all', $section, 'all'],
            ['all', 'all', 'all'],
        ];
    }

    private static function normalizeGrade(mixed $grade): string
    {
        $value = self::asciiUpper($grade);

        if ($value === 'ALL') {
            return 'all';
        }

        if (preg_match('/(\d+)/', $value, $matches) === 1) {
            return $matches[1];
        }

        $gradeWords = [
            'PRIMER' => '1',
            'PRIMERO' => '1',
            'SEGUNDO' => '2',
            'TERCER' => '3',
            'TERCERO' => '3',
            'CUARTO' => '4',
            'QUINTO' => '5',
            'SEXTO' => '6',
        ];

        foreach ($gradeWords as $label => $canonical) {
            if (str_contains($value, $label)) {
                return $canonical;
            }
        }

        return mb_strtolower($value);
    }

    private static function normalizeSection(mixed $section): string
    {
        $value = self::asciiUpper($section);

        if ($value === 'ALL') {
            return 'all';
        }

        if (preg_match('/^SECCION\s*([A-Z])$/', $value, $matches) === 1) {
            return $matches[1];
        }

        if (preg_match('/\b([A-Z])\b/', $value, $matches) === 1) {
            return $matches[1];
        }

        return $value;
    }

    private static function normalizeCourse(mixed $course): string
    {
        $value = self::asciiUpper($course);

        if ($value === 'ALL') {
            return 'all';
        }

        $courses = [
            'LECTURA' => 'lectura',
            'ESCRITURA' => 'escritura',
            'MATEMATICA' => 'matematica',
        ];

        foreach ($courses as $label => $canonical) {
            if (str_contains($value, $label)) {
                return $canonical;
            }
        }

        return mb_strtolower($value);
    }

    private static function normalizeLevel(mixed $level): ?string
    {
        $value = self::asciiUpper($level);

        if ($value === '' || $value === 'NAN') {
            return null;
        }

        if (str_contains($value, 'PREVIO AL INICIO')) {
            return 'previo';
        }

        if (str_contains($value, 'INICIO')) {
            return 'inicio';
        }

        if (str_contains($value, 'PROCESO')) {
            return 'proceso';
        }

        if (str_contains($value, 'SATISFACTORIO') || str_contains($value, 'LOGRADO')) {
            return 'logro';
        }

        return null;
    }

    private static function normalizeHypothesis(mixed $hypothesis): ?string
    {
        $value = self::asciiUpper($hypothesis);

        if ($value === '' || $value === 'NAN') {
            return null;
        }

        if (str_contains($value, 'SILABICO-ALFABETICO')) {
            return 'silabico_alfabetico';
        }

        if (str_contains($value, 'PRESILABICO')) {
            return 'presilabico';
        }

        if (str_contains($value, 'SILABICO')) {
            return 'silabico';
        }

        if (str_contains($value, 'ALFABETICO')) {
            return 'alfabetico';
        }

        return null;
    }

    private static function asciiUpper(mixed $value): string
    {
        $normalized = mb_strtoupper(trim((string) $value));

        return strtr($normalized, [
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U',
            'Ü' => 'U',
        ]);
    }

    private static function percentage(int $value, int $total): int
    {
        if ($total === 0) {
            return 0;
        }

        return (int) round(($value / $total) * 100);
    }

    private static function studentIdentity(object $row, string $selectionCourse): string
    {
        $identity = strtoupper(trim((string) $row->alumno));
        $grade = self::normalizeGrade($row->grado);
        $section = self::normalizeSection($row->seccion);
        $course = self::normalizeCourse($row->curso);

        if ($selectionCourse === 'all') {
            return implode('|', [$identity, $grade, $section]);
        }

        return implode('|', [$identity, $grade, $section, $course]);
    }

    private static function questionOrder(string $label): int
    {
        if (preg_match('/(\d+)/', $label, $matches) === 1) {
            return (int) $matches[1];
        }

        return PHP_INT_MAX;
    }

    private static function selectionKey(string $grade, string $section, string $course): string
    {
        return implode('|', [$grade, $section, $course]);
    }
}
