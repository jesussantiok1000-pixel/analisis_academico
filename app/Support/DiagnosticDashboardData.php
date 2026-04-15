<?php

namespace App\Support;

class DiagnosticDashboardData
{
    private static ?array $analyticsSource = null;

    public static function make(): array
    {
        $snapshots = self::snapshots();
        $metricSnapshot = ['metrics' => self::currentMetricValues()];
        $analyticsSnapshot = self::currentAnalyticsData();

        return [
            'header' => [
                'title' => 'Panel de control academico',
                'subtitle' => 'Analisis de evaluacion diagnostica de inicio I.E. 22455-JTU',
                'cohort' => '',
                'institution' => '',
            ],
            'filters' => self::filters(),
            'metrics' => self::metricCards($metricSnapshot),
            'metricDatasets' => self::metricDatasets(),
            'riskBars' => $analyticsSnapshot['riskBars'],
            'learningDistribution' => $analyticsSnapshot['learningDistribution'],
            'writingHypotheses' => $analyticsSnapshot['writingHypotheses'],
            'analyticsDatasets' => self::analyticsDatasets(),
            'performanceTable' => self::performanceTable(),
            'alertTable' => self::alertTable(),
            'heatmap' => self::heatmap(),
            'resources' => self::resources(),
            'modules' => self::modules(),
            'snapshots' => $snapshots,
        ];
    }

    private static function snapshots(): array
    {
        return [
            'general' => [
                'label' => 'Vista general',
                'note' => 'Panorama consolidado con lectura rapida de brechas, hipotesis y concentracion del riesgo.',
                'metrics' => [
                    'adequate' => 186,
                    'inadequate' => 96,
                    'partial' => 88,
                    'omitted' => 72,
                    'students' => 442,
                ],
                'riskBars' => [
                    'previo' => 8,
                    'escritura' => 21,
                    'lectura' => 12,
                    'matematica' => 19,
                ],
                'learningDistribution' => [
                    'inicio' => 34,
                    'proceso' => 28,
                    'logro' => 22,
                    'destacado' => 16,
                ],
                'writingHypotheses' => [
                    'silabico_alfabetico' => 22,
                    'silabico' => 38,
                    'presilabico' => 94,
                    'alfabetico' => 27,
                ],
            ],
            'primer_ciclo' => [
                'label' => 'Primer ciclo',
                'note' => 'Seguimiento del primer ciclo con mayor dispersion en escritura inicial y omisiones por aula.',
                'metrics' => [
                    'adequate' => 82,
                    'inadequate' => 54,
                    'partial' => 43,
                    'omitted' => 35,
                    'students' => 214,
                ],
                'riskBars' => [
                    'previo' => 13,
                    'escritura' => 24,
                    'lectura' => 17,
                    'matematica' => 18,
                ],
                'learningDistribution' => [
                    'inicio' => 39,
                    'proceso' => 30,
                    'logro' => 19,
                    'destacado' => 12,
                ],
                'writingHypotheses' => [
                    'silabico_alfabetico' => 18,
                    'silabico' => 44,
                    'presilabico' => 101,
                    'alfabetico' => 18,
                ],
            ],
            'segundo_ciclo' => [
                'label' => 'Segundo ciclo',
                'note' => 'El segundo ciclo sostiene mejor conversion a logro, pero todavia concentra alertas en matematica aplicada.',
                'metrics' => [
                    'adequate' => 104,
                    'inadequate' => 42,
                    'partial' => 45,
                    'omitted' => 37,
                    'students' => 228,
                ],
                'riskBars' => [
                    'previo' => 6,
                    'escritura' => 17,
                    'lectura' => 11,
                    'matematica' => 20,
                ],
                'learningDistribution' => [
                    'inicio' => 29,
                    'proceso' => 27,
                    'logro' => 26,
                    'destacado' => 18,
                ],
                'writingHypotheses' => [
                    'silabico_alfabetico' => 25,
                    'silabico' => 31,
                    'presilabico' => 78,
                    'alfabetico' => 36,
                ],
            ],
            'foco_critico' => [
                'label' => 'Foco critico',
                'note' => 'Escenario para intervencion rapida en aulas con combinacion de baja cobertura, omisiones y riesgo alto.',
                'metrics' => [
                    'adequate' => 31,
                    'inadequate' => 34,
                    'partial' => 29,
                    'omitted' => 24,
                    'students' => 118,
                ],
                'riskBars' => [
                    'previo' => 16,
                    'escritura' => 27,
                    'lectura' => 20,
                    'matematica' => 24,
                ],
                'learningDistribution' => [
                    'inicio' => 42,
                    'proceso' => 29,
                    'logro' => 18,
                    'destacado' => 11,
                ],
                'writingHypotheses' => [
                    'silabico_alfabetico' => 14,
                    'silabico' => 46,
                    'presilabico' => 109,
                    'alfabetico' => 13,
                ],
            ],
        ];
    }

    private static function metricCards(array $snapshot): array
    {
        $itemTotal = $snapshot['metrics']['itemTotal'] ?? 0;

        return [
            [
                'key' => 'adequate',
                'label' => 'Adecuados',
                'value' => $snapshot['metrics']['adequate'],
                'caption' => self::itemRatioCaption($snapshot['metrics']['adequate'], $itemTotal),
                'trend' => 'Criterio logrado',
                'icon' => 'bi-patch-check-fill',
                'tone' => 'success',
            ],
            [
                'key' => 'inadequate',
                'label' => 'Inadecuados',
                'value' => $snapshot['metrics']['inadequate'],
                'caption' => self::itemRatioCaption($snapshot['metrics']['inadequate'], $itemTotal),
                'trend' => 'Foco inmediato',
                'icon' => 'bi-exclamation-diamond-fill',
                'tone' => 'violet',
            ],
            [
                'key' => 'partial',
                'label' => 'Parciales',
                'value' => $snapshot['metrics']['partial'],
                'caption' => self::itemRatioCaption($snapshot['metrics']['partial'], $itemTotal),
                'trend' => 'Zona de avance',
                'icon' => 'bi-layers-half',
                'tone' => 'lavender',
            ],
            [
                'key' => 'omitted',
                'label' => 'Omitidos',
                'value' => $snapshot['metrics']['omitted'],
                'caption' => self::itemRatioCaption($snapshot['metrics']['omitted'], $itemTotal),
                'trend' => 'Cobertura pendiente',
                'icon' => 'bi-slash-circle-fill',
                'tone' => 'dark',
            ],
            [
                'key' => 'students',
                'label' => 'Alumnos',
                'value' => $snapshot['metrics']['students'],
                'caption' => 'Rindieron el examen',
                'trend' => 'Base evaluada',
                'icon' => 'bi-people-fill',
                'tone' => 'neutral',
            ],
        ];
    }

    private static function riskBars(array $snapshot): array
    {
        return [
            ['key' => 'previo', 'label' => 'Previo al inicio', 'value' => $snapshot['riskBars']['previo']],
            ['key' => 'escritura', 'label' => 'Escritura', 'value' => $snapshot['riskBars']['escritura']],
            ['key' => 'lectura', 'label' => 'Lectura', 'value' => $snapshot['riskBars']['lectura']],
            ['key' => 'matematica', 'label' => 'Matematica', 'value' => $snapshot['riskBars']['matematica']],
        ];
    }

    private static function learningDistribution(array $snapshot): array
    {
        return [
            ['key' => 'inicio', 'label' => 'Inicio', 'value' => $snapshot['learningDistribution']['inicio'], 'color' => '#c85d5d'],
            ['key' => 'proceso', 'label' => 'Proceso', 'value' => $snapshot['learningDistribution']['proceso'], 'color' => '#d89aaf'],
            ['key' => 'logro', 'label' => 'Logro', 'value' => $snapshot['learningDistribution']['logro'], 'color' => '#c7a23a'],
            ['key' => 'destacado', 'label' => 'Destacado', 'value' => $snapshot['learningDistribution']['destacado'], 'color' => '#5f9870'],
        ];
    }

    private static function writingHypotheses(array $snapshot): array
    {
        return [
            ['key' => 'silabico_alfabetico', 'label' => 'Silabico-alfabetico', 'value' => $snapshot['writingHypotheses']['silabico_alfabetico']],
            ['key' => 'silabico', 'label' => 'Silabico', 'value' => $snapshot['writingHypotheses']['silabico']],
            ['key' => 'presilabico', 'label' => 'Presilabico', 'value' => $snapshot['writingHypotheses']['presilabico']],
            ['key' => 'alfabetico', 'label' => 'Alfabetico', 'value' => $snapshot['writingHypotheses']['alfabetico']],
        ];
    }

    private static function performanceTable(): array
    {
        $selected = self::currentSelection();
        $showGradeComparison = $selected['grade'] === 'all';
        
        if ($showGradeComparison) {
            // Vista comparativa por grados con nuevas columnas de análisis
            return [
                'columns' => ['GRADOS', 'LOGRO', 'NIVEL DOMINANTE', 'BRECHA', 'PRIORIDAD'],
                'rows' => self::getRealPerformanceData()
            ];
        } else {
            // Vista individual por grado (funcionalidad existente)
            return [
                'columns' => ['Dimensión', 'Logro', 'Nivel dominante', 'Brecha', 'Prioridad'],
                'rows' => self::getIndividualGradePerformanceData()
            ];
        }
    }

    private static function alertTable(): array
    {
        return [
            'columns' => ['Aula', 'Riesgo', 'Lectura', 'Matematica', 'Accion inmediata'],
            'rows' => [
                ['classroom' => '1A', 'risk' => 'Alto', 'reading' => '32%', 'math' => '41%', 'action' => 'Reforzar conciencia fonologica y recuperacion de evidencias', 'tone' => 'high'],
                ['classroom' => '2B', 'risk' => 'Medio', 'reading' => '47%', 'math' => '52%', 'action' => 'Ajustar secuencia de practica guiada y cierre', 'tone' => 'medium'],
                ['classroom' => '3A', 'risk' => 'Alto', 'reading' => '39%', 'math' => '37%', 'action' => 'Tutorias cortas por subgrupo y seguimiento semanal', 'tone' => 'high'],
                ['classroom' => '4C', 'risk' => 'Bajo', 'reading' => '73%', 'math' => '69%', 'action' => 'Mantener nivel y documentar practicas efectivas', 'tone' => 'low'],
            ],
        ];
    }

    private static function filters(): array
    {
        $selected = self::currentSelection();

        $items = [
            [
                'key' => 'grade',
                'label' => 'Grado',
                'icon' => 'bi-diagram-3-fill',
                'value' => $selected['grade'],
                'options' => self::gradeOptions(),
            ],
            [
                'key' => 'section',
                'label' => 'Seccion',
                'icon' => 'bi-grid-fill',
                'value' => $selected['section'],
                'options' => self::sectionOptions(),
            ],
            [
                'key' => 'course',
                'label' => 'Curso',
                'icon' => 'bi-book-half',
                'value' => $selected['course'],
                'options' => self::courseOptions(),
            ],
        ];

        foreach ($items as $index => $item) {
            $items[$index]['selectedLabel'] = self::selectedOptionLabel($item['value'], $item['options']);
        }

        return [
            'title' => 'Panel de filtracion',
            'description' => self::filterDescription(),
            'items' => $items,
        ];
    }

    private static function heatmap(): array
    {
        $selection = self::currentSelection();
        $datasetKey = self::heatmapSelectionKey($selection);
        $datasets = self::renderHeatmapDatasets(self::resolvedHeatmapDatasets());
        $activeDataset = $datasets[$datasetKey] ?? self::emptyHeatmapDataset($selection);

        return [
            'title' => 'Distribucion por pregunta',
            'summary' => self::heatmapSummary(),
            'selection' => $activeDataset['selection'],
            'rows' => $activeDataset['rows'],
            'hasData' => $activeDataset['hasData'],
            'emptyMessage' => $activeDataset['emptyMessage'],
            'datasets' => $datasets,
        ];
    }

    private static function itemCells(array $values): array
    {
        $cells = [];

        foreach ($values as $label => $value) {
            $cells[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        return $cells;
    }

    private static function currentSelection(): array
    {
        $preferred = [
            'grade' => '2',
            'section' => 'A',
            'course' => 'lectura',
        ];

        if (isset(self::resolvedHeatmapDatasets()[self::heatmapSelectionKey($preferred)])) {
            return $preferred;
        }

        foreach (array_keys(self::resolvedHeatmapDatasets()) as $key) {
            [$grade, $section, $course] = explode('|', $key);

            if ($grade === 'all' || $section === 'all' || $course === 'all') {
                continue;
            }

            return [
                'grade' => $grade,
                'section' => $section,
                'course' => $course,
            ];
        }

        return $preferred;
    }

    private static function heatmapSelectionKey(array $selection): string
    {
        return implode('|', [$selection['grade'], $selection['section'], $selection['course']]);
    }

    private static function analyticsSource(): array
    {
        if (self::$analyticsSource !== null) {
            return self::$analyticsSource;
        }

        self::$analyticsSource = AnalyticsDashboardSource::load();

        return self::$analyticsSource;
    }

    private static function resolvedHeatmapDatasets(): array
    {
        $source = self::analyticsSource();

        if (! empty($source['connected']) && ! empty($source['heatmapDatasets'])) {
            return $source['heatmapDatasets'];
        }

        return self::fallbackHeatmapDatasets();
    }

    private static function metricDatasets(): array
    {
        $source = self::analyticsSource();

        return $source['metricDatasets'] ?? [];
    }

    private static function analyticsDatasets(): array
    {
        $source = self::analyticsSource();

        return $source['analyticsDatasets'] ?? [];
    }

    private static function currentMetricValues(): array
    {
        $datasets = self::metricDatasets();
        $key = self::heatmapSelectionKey(self::currentSelection());

        return $datasets[$key] ?? self::emptyMetricValues();
    }

    private static function currentAnalyticsData(): array
    {
        $datasets = self::analyticsDatasets();
        $key = self::heatmapSelectionKey(self::currentSelection());

        return $datasets[$key] ?? self::emptyAnalyticsData();
    }

    private static function filterDescription(): string
    {
        $source = self::analyticsSource();

        if (! empty($source['connected'])) {
            return sprintf(
                'Fuente activa: MySQL %s. Los filtros actualizan tarjetas, riesgos, tasa de aprendizaje, hipotesis de escritura y mapa por grado, seccion y curso. Contextos detectados: %d.',
                $source['database'],
                $source['contextCount'],
            );
        }

        return 'Selecciona grado del 1 al 6, seccion de A a E y curso. Si MySQL no esta disponible, el panel usa el dataset fijo de respaldo.';
    }

    private static function heatmapSummary(): string
    {
        $source = self::analyticsSource();

        if (! empty($source['connected'])) {
            return sprintf(
                'Se muestran los cuatro indicadores por item usando MySQL %s. El mapa y las tarjetas se actualizan cuando cambias grado, seccion o curso.',
                $source['database'],
            );
        }

        return 'Se muestran los cuatro indicadores por pregunta de P1 a P20: adecuados, parciales, inadecuados y omitidos. El mapa se actualiza cuando cambias grado, seccion o curso.';
    }

    private static function emptyMetricValues(): array
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

    private static function emptyAnalyticsData(): array
    {
        return [
            'riskBars' => [
                ['key' => 'previo', 'label' => 'Previo al inicio', 'value' => 0],
                ['key' => 'inicio', 'label' => 'Inicio', 'value' => 0],
            ],
            'learningDistribution' => [
                ['key' => 'previo', 'label' => 'Previo al inicio', 'value' => 0, 'color' => '#c85d5d'],
                ['key' => 'inicio', 'label' => 'Inicio', 'value' => 0, 'color' => '#d89aaf'],
                ['key' => 'proceso', 'label' => 'Proceso', 'value' => 0, 'color' => '#c7a23a'],
                ['key' => 'logro', 'label' => 'Logrado', 'value' => 0, 'color' => '#5f9870'],
            ],
            'writingHypotheses' => [
                ['key' => 'presilabico', 'label' => 'Presilabico', 'value' => 0],
                ['key' => 'silabico', 'label' => 'Silabico', 'value' => 0],
                ['key' => 'silabico_alfabetico', 'label' => 'Silabico-Alfabetico', 'value' => 0],
                ['key' => 'alfabetico', 'label' => 'Alfabetico', 'value' => 0],
            ],
        ];
    }

    private static function fallbackHeatmapDatasets(): array
    {
        return [
            '2|all|lectura' => [
                'selection' => ['grade' => '2', 'section' => 'all', 'course' => 'lectura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 32,
                            'P2' => 30,
                            'P3' => 30,
                            'P4' => 32,
                            'P5' => 21,
                            'P6' => 24,
                            'P7' => 19,
                            'P8' => 19,
                            'P9' => 18,
                            'P10' => 18,
                            'P11' => 14,
                            'P12' => 20,
                            'P13' => 19,
                            'P14' => 14,
                            'P15' => 12,
                            'P16' => 14,
                            'P17' => 17,
                            'P18' => 12,
                            'P19' => 13,
                            'P20' => 6,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 0,
                            'P9' => 0,
                            'P10' => 0,
                            'P11' => 0,
                            'P12' => 0,
                            'P13' => 0,
                            'P14' => 0,
                            'P15' => 0,
                            'P16' => 0,
                            'P17' => 0,
                            'P18' => 0,
                            'P19' => 0,
                            'P20' => 0,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 1,
                            'P2' => 3,
                            'P3' => 3,
                            'P4' => 1,
                            'P5' => 10,
                            'P6' => 6,
                            'P7' => 12,
                            'P8' => 12,
                            'P9' => 12,
                            'P10' => 12,
                            'P11' => 16,
                            'P12' => 10,
                            'P13' => 13,
                            'P14' => 16,
                            'P15' => 17,
                            'P16' => 15,
                            'P17' => 13,
                            'P18' => 17,
                            'P19' => 16,
                            'P20' => 23,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 1,
                            'P2' => 1,
                            'P3' => 1,
                            'P4' => 1,
                            'P5' => 3,
                            'P6' => 4,
                            'P7' => 3,
                            'P8' => 3,
                            'P9' => 4,
                            'P10' => 4,
                            'P11' => 4,
                            'P12' => 4,
                            'P13' => 2,
                            'P14' => 4,
                            'P15' => 5,
                            'P16' => 5,
                            'P17' => 4,
                            'P18' => 5,
                            'P19' => 5,
                            'P20' => 5,
                        ],
                    ],
                ],
            ],
            '2|A|lectura' => [
                'selection' => ['grade' => '2', 'section' => 'A', 'course' => 'lectura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 30,
                            'P2' => 29,
                            'P3' => 29,
                            'P4' => 31,
                            'P5' => 29,
                            'P6' => 29,
                            'P7' => 28,
                            'P8' => 22,
                            'P9' => 25,
                            'P10' => 22,
                            'P11' => 19,
                            'P12' => 16,
                            'P13' => 17,
                            'P14' => 14,
                            'P15' => 18,
                            'P16' => 19,
                            'P17' => 17,
                            'P18' => 17,
                            'P19' => 14,
                            'P20' => 10,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 0,
                            'P9' => 0,
                            'P10' => 0,
                            'P11' => 0,
                            'P12' => 0,
                            'P13' => 0,
                            'P14' => 0,
                            'P15' => 0,
                            'P16' => 0,
                            'P17' => 0,
                            'P18' => 0,
                            'P19' => 0,
                            'P20' => 0,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 1,
                            'P2' => 2,
                            'P3' => 2,
                            'P4' => 0,
                            'P5' => 2,
                            'P6' => 2,
                            'P7' => 3,
                            'P8' => 9,
                            'P9' => 6,
                            'P10' => 8,
                            'P11' => 10,
                            'P12' => 12,
                            'P13' => 13,
                            'P14' => 16,
                            'P15' => 10,
                            'P16' => 11,
                            'P17' => 10,
                            'P18' => 10,
                            'P19' => 13,
                            'P20' => 16,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 0,
                            'P9' => 0,
                            'P10' => 1,
                            'P11' => 2,
                            'P12' => 3,
                            'P13' => 1,
                            'P14' => 1,
                            'P15' => 3,
                            'P16' => 2,
                            'P17' => 4,
                            'P18' => 4,
                            'P19' => 4,
                            'P20' => 5,
                        ],
                    ],
                ],
            ],
            '2|A|escritura' => [
                'selection' => ['grade' => '2', 'section' => 'A', 'course' => 'escritura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 21,
                            'P2' => 8,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 5,
                            'P2' => 14,
                            'P3' => 18,
                            'P4' => 16,
                            'P5' => 14,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 4,
                            'P2' => 8,
                            'P3' => 12,
                            'P4' => 14,
                            'P5' => 16,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                        ],
                    ],
                ],
            ],
            '2|A|matematica' => [
                'selection' => ['grade' => '2', 'section' => 'A', 'course' => 'matematica'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 25,
                            'P2' => 25,
                            'P3' => 13,
                            'P4' => 13,
                            'P5' => 17,
                            'P6' => 16,
                            'P7' => 19,
                            'P8' => 10,
                            'P9' => 13,
                            'P10' => 14,
                            'P11' => 19,
                            'P12' => 14,
                            'P13' => 19,
                            'P14' => 14,
                            'P15' => 6,
                            'P16' => 21,
                            'P17' => 23,
                            'P18' => 6,
                            'P19' => 0,
                            'P20' => 4,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 0,
                            'P9' => 0,
                            'P10' => 0,
                            'P11' => 0,
                            'P12' => 0,
                            'P13' => 0,
                            'P14' => 0,
                            'P15' => 0,
                            'P16' => 0,
                            'P17' => 0,
                            'P18' => 7,
                            'P19' => 0,
                            'P20' => 10,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 7,
                            'P2' => 7,
                            'P3' => 19,
                            'P4' => 18,
                            'P5' => 14,
                            'P6' => 14,
                            'P7' => 12,
                            'P8' => 21,
                            'P9' => 18,
                            'P10' => 17,
                            'P11' => 10,
                            'P12' => 17,
                            'P13' => 12,
                            'P14' => 17,
                            'P15' => 25,
                            'P16' => 10,
                            'P17' => 8,
                            'P18' => 14,
                            'P19' => 24,
                            'P20' => 12,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 1,
                            'P5' => 1,
                            'P6' => 2,
                            'P7' => 1,
                            'P8' => 1,
                            'P9' => 1,
                            'P10' => 1,
                            'P11' => 3,
                            'P12' => 1,
                            'P13' => 1,
                            'P14' => 1,
                            'P15' => 1,
                            'P16' => 1,
                            'P17' => 1,
                            'P18' => 5,
                            'P19' => 8,
                            'P20' => 6,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                ],
            ],
            '2|B|matematica' => [
                'selection' => ['grade' => '2', 'section' => 'B', 'course' => 'matematica'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 24,
                            'P2' => 20,
                            'P3' => 11,
                            'P4' => 18,
                            'P5' => 22,
                            'P6' => 12,
                            'P7' => 24,
                            'P8' => 4,
                            'P9' => 6,
                            'P10' => 9,
                            'P11' => 18,
                            'P12' => 14,
                            'P13' => 17,
                            'P14' => 13,
                            'P15' => 6,
                            'P16' => 15,
                            'P17' => 24,
                            'P18' => 6,
                            'P19' => 3,
                            'P20' => 5,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 0,
                            'P9' => 0,
                            'P10' => 0,
                            'P11' => 0,
                            'P12' => 0,
                            'P13' => 0,
                            'P14' => 0,
                            'P15' => 0,
                            'P16' => 0,
                            'P17' => 0,
                            'P18' => 9,
                            'P19' => 0,
                            'P20' => 14,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 9,
                            'P2' => 12,
                            'P3' => 21,
                            'P4' => 14,
                            'P5' => 10,
                            'P6' => 20,
                            'P7' => 8,
                            'P8' => 28,
                            'P9' => 24,
                            'P10' => 18,
                            'P11' => 9,
                            'P12' => 12,
                            'P13' => 13,
                            'P14' => 16,
                            'P15' => 23,
                            'P16' => 15,
                            'P17' => 6,
                            'P18' => 9,
                            'P19' => 26,
                            'P20' => 7,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 0,
                            'P9' => 2,
                            'P10' => 5,
                            'P11' => 5,
                            'P12' => 6,
                            'P13' => 2,
                            'P14' => 3,
                            'P15' => 3,
                            'P16' => 2,
                            'P17' => 2,
                            'P18' => 8,
                            'P19' => 3,
                            'P20' => 6,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                ],
            ],
            '2|C|matematica' => [
                'selection' => ['grade' => '2', 'section' => 'C', 'course' => 'matematica'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 25,
                            'P2' => 21,
                            'P3' => 14,
                            'P4' => 14,
                            'P5' => 17,
                            'P6' => 18,
                            'P7' => 23,
                            'P8' => 5,
                            'P9' => 15,
                            'P10' => 13,
                            'P11' => 13,
                            'P12' => 19,
                            'P13' => 22,
                            'P14' => 15,
                            'P15' => 6,
                            'P16' => 18,
                            'P17' => 24,
                            'P18' => 6,
                            'P19' => 7,
                            'P20' => 10,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 0,
                            'P9' => 0,
                            'P10' => 0,
                            'P11' => 0,
                            'P12' => 0,
                            'P13' => 0,
                            'P14' => 0,
                            'P15' => 0,
                            'P16' => 0,
                            'P17' => 0,
                            'P18' => 2,
                            'P19' => 0,
                            'P20' => 1,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 5,
                            'P2' => 9,
                            'P3' => 16,
                            'P4' => 16,
                            'P5' => 13,
                            'P6' => 12,
                            'P7' => 7,
                            'P8' => 24,
                            'P9' => 14,
                            'P10' => 16,
                            'P11' => 16,
                            'P12' => 10,
                            'P13' => 7,
                            'P14' => 14,
                            'P15' => 23,
                            'P16' => 11,
                            'P17' => 5,
                            'P18' => 21,
                            'P19' => 22,
                            'P20' => 18,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 1,
                            'P9' => 1,
                            'P10' => 1,
                            'P11' => 1,
                            'P12' => 1,
                            'P13' => 1,
                            'P14' => 1,
                            'P15' => 1,
                            'P16' => 1,
                            'P17' => 1,
                            'P18' => 1,
                            'P19' => 1,
                            'P20' => 1,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                ],
            ],
            '2|D|matematica' => [
                'selection' => ['grade' => '2', 'section' => 'D', 'course' => 'matematica'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 19,
                            'P2' => 25,
                            'P3' => 13,
                            'P4' => 21,
                            'P5' => 14,
                            'P6' => 18,
                            'P7' => 19,
                            'P8' => 11,
                            'P9' => 11,
                            'P10' => 9,
                            'P11' => 20,
                            'P12' => 15,
                            'P13' => 20,
                            'P14' => 20,
                            'P15' => 6,
                            'P16' => 20,
                            'P17' => 22,
                            'P18' => 8,
                            'P19' => 1,
                            'P20' => 6,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 0,
                            'P9' => 0,
                            'P10' => 0,
                            'P11' => 0,
                            'P12' => 0,
                            'P13' => 0,
                            'P14' => 0,
                            'P15' => 0,
                            'P16' => 0,
                            'P17' => 0,
                            'P18' => 2,
                            'P19' => 0,
                            'P20' => 2,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 16,
                            'P2' => 10,
                            'P3' => 22,
                            'P4' => 14,
                            'P5' => 21,
                            'P6' => 17,
                            'P7' => 15,
                            'P8' => 23,
                            'P9' => 24,
                            'P10' => 26,
                            'P11' => 15,
                            'P12' => 20,
                            'P13' => 15,
                            'P14' => 15,
                            'P15' => 29,
                            'P16' => 15,
                            'P17' => 13,
                            'P18' => 25,
                            'P19' => 34,
                            'P20' => 27,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 1,
                            'P8' => 1,
                            'P9' => 0,
                            'P10' => 0,
                            'P11' => 0,
                            'P12' => 0,
                            'P13' => 0,
                            'P14' => 0,
                            'P15' => 0,
                            'P16' => 0,
                            'P17' => 0,
                            'P18' => 0,
                            'P19' => 0,
                            'P20' => 0,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                            'P25' => 0,
                            'P26' => 0,
                            'P27' => 0,
                            'P28' => 0,
                        ],
                    ],
                ],
            ],
            '2|B|escritura' => [
                'selection' => ['grade' => '2', 'section' => 'B', 'course' => 'escritura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 7,
                            'P2' => 8,
                            'P3' => 7,
                            'P4' => 8,
                            'P5' => 4,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 4,
                            'P2' => 4,
                            'P3' => 3,
                            'P4' => 3,
                            'P5' => 3,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 23,
                            'P2' => 22,
                            'P3' => 24,
                            'P4' => 23,
                            'P5' => 27,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                        ],
                    ],
                ],
            ],
            '2|C|escritura' => [
                'selection' => ['grade' => '2', 'section' => 'C', 'course' => 'escritura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 13,
                            'P2' => 9,
                            'P3' => 9,
                            'P4' => 10,
                            'P5' => 8,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 3,
                            'P2' => 4,
                            'P3' => 6,
                            'P4' => 6,
                            'P5' => 2,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 14,
                            'P2' => 17,
                            'P3' => 15,
                            'P4' => 14,
                            'P5' => 20,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                        ],
                    ],
                ],
            ],
            '2|D|escritura' => [
                'selection' => ['grade' => '2', 'section' => 'D', 'course' => 'escritura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 7,
                            'P2' => 8,
                            'P3' => 0,
                            'P4' => 7,
                            'P5' => 0,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 6,
                            'P2' => 5,
                            'P3' => 12,
                            'P4' => 10,
                            'P5' => 13,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 22,
                            'P2' => 22,
                            'P3' => 23,
                            'P4' => 18,
                            'P5' => 22,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                        ],
                    ],
                ],
            ],
            '2|B|lectura' => [
                'selection' => ['grade' => '2', 'section' => 'B', 'course' => 'lectura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 28,
                            'P2' => 27,
                            'P3' => 32,
                            'P4' => 24,
                            'P5' => 21,
                            'P6' => 21,
                            'P7' => 25,
                            'P8' => 19,
                            'P9' => 19,
                            'P10' => 17,
                            'P11' => 17,
                            'P12' => 15,
                            'P13' => 14,
                            'P14' => 11,
                            'P15' => 19,
                            'P16' => 12,
                            'P17' => 17,
                            'P18' => 15,
                            'P19' => 9,
                            'P20' => 7,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 0,
                            'P9' => 0,
                            'P10' => 0,
                            'P11' => 0,
                            'P12' => 0,
                            'P13' => 0,
                            'P14' => 0,
                            'P15' => 0,
                            'P16' => 0,
                            'P17' => 0,
                            'P18' => 0,
                            'P19' => 0,
                            'P20' => 0,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 3,
                            'P2' => 3,
                            'P3' => 0,
                            'P4' => 6,
                            'P5' => 6,
                            'P6' => 5,
                            'P7' => 5,
                            'P8' => 11,
                            'P9' => 10,
                            'P10' => 12,
                            'P11' => 11,
                            'P12' => 14,
                            'P13' => 17,
                            'P14' => 20,
                            'P15' => 12,
                            'P16' => 18,
                            'P17' => 13,
                            'P18' => 15,
                            'P19' => 21,
                            'P20' => 22,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 3,
                            'P2' => 4,
                            'P3' => 2,
                            'P4' => 4,
                            'P5' => 7,
                            'P6' => 8,
                            'P7' => 4,
                            'P8' => 4,
                            'P9' => 5,
                            'P10' => 5,
                            'P11' => 6,
                            'P12' => 5,
                            'P13' => 3,
                            'P14' => 3,
                            'P15' => 3,
                            'P16' => 4,
                            'P17' => 4,
                            'P18' => 4,
                            'P19' => 4,
                            'P20' => 5,
                        ],
                    ],
                ],
            ],
            '2|C|lectura' => [
                'selection' => ['grade' => '2', 'section' => 'C', 'course' => 'lectura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 27,
                            'P2' => 25,
                            'P3' => 26,
                            'P4' => 29,
                            'P5' => 24,
                            'P6' => 23,
                            'P7' => 23,
                            'P8' => 18,
                            'P9' => 25,
                            'P10' => 26,
                            'P11' => 18,
                            'P12' => 17,
                            'P13' => 20,
                            'P14' => 13,
                            'P15' => 15,
                            'P16' => 15,
                            'P17' => 20,
                            'P18' => 16,
                            'P19' => 19,
                            'P20' => 6,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 0,
                            'P9' => 0,
                            'P10' => 0,
                            'P11' => 0,
                            'P12' => 0,
                            'P13' => 0,
                            'P14' => 0,
                            'P15' => 0,
                            'P16' => 0,
                            'P17' => 0,
                            'P18' => 0,
                            'P19' => 0,
                            'P20' => 0,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 2,
                            'P2' => 4,
                            'P3' => 3,
                            'P4' => 0,
                            'P5' => 4,
                            'P6' => 5,
                            'P7' => 6,
                            'P8' => 11,
                            'P9' => 4,
                            'P10' => 3,
                            'P11' => 11,
                            'P12' => 12,
                            'P13' => 5,
                            'P14' => 12,
                            'P15' => 10,
                            'P16' => 10,
                            'P17' => 8,
                            'P18' => 12,
                            'P19' => 9,
                            'P20' => 21,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 1,
                            'P6' => 1,
                            'P7' => 0,
                            'P8' => 0,
                            'P9' => 0,
                            'P10' => 0,
                            'P11' => 0,
                            'P12' => 0,
                            'P13' => 4,
                            'P14' => 4,
                            'P15' => 4,
                            'P16' => 4,
                            'P17' => 1,
                            'P18' => 1,
                            'P19' => 1,
                            'P20' => 1,
                        ],
                    ],
                ],
            ],
            '2|D|lectura' => [
                'selection' => ['grade' => '2', 'section' => 'D', 'course' => 'lectura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 32,
                            'P2' => 30,
                            'P3' => 30,
                            'P4' => 32,
                            'P5' => 21,
                            'P6' => 24,
                            'P7' => 19,
                            'P8' => 19,
                            'P9' => 18,
                            'P10' => 18,
                            'P11' => 14,
                            'P12' => 20,
                            'P13' => 19,
                            'P14' => 14,
                            'P15' => 12,
                            'P16' => 14,
                            'P17' => 17,
                            'P18' => 12,
                            'P19' => 13,
                            'P20' => 6,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 0,
                            'P9' => 0,
                            'P10' => 0,
                            'P11' => 0,
                            'P12' => 0,
                            'P13' => 0,
                            'P14' => 0,
                            'P15' => 0,
                            'P16' => 0,
                            'P17' => 0,
                            'P18' => 0,
                            'P19' => 0,
                            'P20' => 0,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 1,
                            'P2' => 3,
                            'P3' => 3,
                            'P4' => 1,
                            'P5' => 10,
                            'P6' => 6,
                            'P7' => 12,
                            'P8' => 12,
                            'P9' => 12,
                            'P10' => 12,
                            'P11' => 16,
                            'P12' => 10,
                            'P13' => 13,
                            'P14' => 16,
                            'P15' => 17,
                            'P16' => 15,
                            'P17' => 13,
                            'P18' => 17,
                            'P19' => 16,
                            'P20' => 23,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 1,
                            'P2' => 1,
                            'P3' => 1,
                            'P4' => 1,
                            'P5' => 3,
                            'P6' => 4,
                            'P7' => 3,
                            'P8' => 3,
                            'P9' => 4,
                            'P10' => 4,
                            'P11' => 4,
                            'P12' => 4,
                            'P13' => 2,
                            'P14' => 4,
                            'P15' => 5,
                            'P16' => 5,
                            'P17' => 4,
                            'P18' => 5,
                            'P19' => 5,
                            'P20' => 5,
                        ],
                    ],
                ],
            ],
            '3|A|escritura' => [
                'selection' => ['grade' => '3', 'section' => 'A', 'course' => 'escritura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 0,
                            'P2' => 1,
                            'P3' => 2,
                            'P4' => 3,
                            'P5' => 3,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 13,
                            'P2' => 13,
                            'P3' => 8,
                            'P4' => 17,
                            'P5' => 11,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 17,
                            'P2' => 16,
                            'P3' => 20,
                            'P4' => 10,
                            'P5' => 16,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                        ],
                    ],
                ],
            ],
            '3|B|escritura' => [
                'selection' => ['grade' => '3', 'section' => 'B', 'course' => 'escritura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 2,
                            'P2' => 0,
                            'P3' => 1,
                            'P4' => 1,
                            'P5' => 1,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 11,
                            'P2' => 13,
                            'P3' => 13,
                            'P4' => 12,
                            'P5' => 8,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 12,
                            'P2' => 12,
                            'P3' => 11,
                            'P4' => 12,
                            'P5' => 16,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                        ],
                    ],
                ],
            ],
            '3|C|escritura' => [
                'selection' => ['grade' => '3', 'section' => 'C', 'course' => 'escritura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 13,
                            'P2' => 11,
                            'P3' => 8,
                            'P4' => 6,
                            'P5' => 24,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 7,
                            'P2' => 9,
                            'P3' => 11,
                            'P4' => 18,
                            'P5' => 1,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 7,
                            'P2' => 7,
                            'P3' => 8,
                            'P4' => 3,
                            'P5' => 2,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                        ],
                    ],
                ],
            ],
            '3|D|escritura' => [
                'selection' => ['grade' => '3', 'section' => 'D', 'course' => 'escritura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                        ],
                    ],
                ],
            ],
            '3|A|lectura' => [
                'selection' => ['grade' => '3', 'section' => 'A', 'course' => 'lectura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 26,
                            'P2' => 22,
                            'P3' => 18,
                            'P4' => 18,
                            'P5' => 23,
                            'P6' => 16,
                            'P7' => 20,
                            'P8' => 13,
                            'P9' => 13,
                            'P10' => 14,
                            'P11' => 16,
                            'P12' => 14,
                            'P13' => 21,
                            'P14' => 18,
                            'P15' => 11,
                            'P16' => 19,
                            'P17' => 20,
                            'P18' => 9,
                            'P19' => 23,
                            'P20' => 8,
                            'P21' => 18,
                            'P22' => 20,
                            'P23' => 19,
                            'P24' => 16,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 0,
                            'P9' => 0,
                            'P10' => 0,
                            'P11' => 0,
                            'P12' => 0,
                            'P13' => 0,
                            'P14' => 0,
                            'P15' => 0,
                            'P16' => 0,
                            'P17' => 0,
                            'P18' => 0,
                            'P19' => 0,
                            'P20' => 0,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 2,
                            'P2' => 6,
                            'P3' => 10,
                            'P4' => 11,
                            'P5' => 6,
                            'P6' => 13,
                            'P7' => 9,
                            'P8' => 15,
                            'P9' => 15,
                            'P10' => 14,
                            'P11' => 12,
                            'P12' => 14,
                            'P13' => 7,
                            'P14' => 10,
                            'P15' => 17,
                            'P16' => 9,
                            'P17' => 8,
                            'P18' => 18,
                            'P19' => 4,
                            'P20' => 7,
                            'P21' => 10,
                            'P22' => 8,
                            'P23' => 9,
                            'P24' => 12,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 2,
                            'P2' => 2,
                            'P3' => 2,
                            'P4' => 1,
                            'P5' => 1,
                            'P6' => 1,
                            'P7' => 1,
                            'P8' => 2,
                            'P9' => 2,
                            'P10' => 2,
                            'P11' => 2,
                            'P12' => 2,
                            'P13' => 2,
                            'P14' => 2,
                            'P15' => 2,
                            'P16' => 2,
                            'P17' => 2,
                            'P18' => 3,
                            'P19' => 3,
                            'P20' => 15,
                            'P21' => 2,
                            'P22' => 2,
                            'P23' => 2,
                            'P24' => 2,
                        ],
                    ],
                ],
            ],
            '3|B|lectura' => [
                'selection' => ['grade' => '3', 'section' => 'B', 'course' => 'lectura'],
                'series' => [
                    [
                        'label' => 'Adecuados',
                        'tone' => 'success',
                        'values' => [
                            'P1' => 25,
                            'P2' => 22,
                            'P3' => 14,
                            'P4' => 16,
                            'P5' => 18,
                            'P6' => 11,
                            'P7' => 13,
                            'P8' => 16,
                            'P9' => 9,
                            'P10' => 14,
                            'P11' => 21,
                            'P12' => 15,
                            'P13' => 16,
                            'P14' => 16,
                            'P15' => 16,
                            'P16' => 19,
                            'P17' => 20,
                            'P18' => 11,
                            'P19' => 20,
                            'P20' => 8,
                            'P21' => 19,
                            'P22' => 20,
                            'P23' => 14,
                            'P24' => 17,
                        ],
                    ],
                    [
                        'label' => 'Parciales',
                        'tone' => 'lavender',
                        'values' => [
                            'P1' => 0,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 0,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 0,
                            'P9' => 0,
                            'P10' => 0,
                            'P11' => 0,
                            'P12' => 0,
                            'P13' => 0,
                            'P14' => 0,
                            'P15' => 0,
                            'P16' => 0,
                            'P17' => 0,
                            'P18' => 0,
                            'P19' => 0,
                            'P20' => 0,
                            'P21' => 0,
                            'P22' => 0,
                            'P23' => 0,
                            'P24' => 0,
                        ],
                    ],
                    [
                        'label' => 'Inadecuados',
                        'tone' => 'violet',
                        'values' => [
                            'P1' => 1,
                            'P2' => 5,
                            'P3' => 13,
                            'P4' => 11,
                            'P5' => 8,
                            'P6' => 16,
                            'P7' => 14,
                            'P8' => 9,
                            'P9' => 15,
                            'P10' => 12,
                            'P11' => 5,
                            'P12' => 11,
                            'P13' => 9,
                            'P14' => 9,
                            'P15' => 9,
                            'P16' => 6,
                            'P17' => 5,
                            'P18' => 14,
                            'P19' => 5,
                            'P20' => 9,
                            'P21' => 4,
                            'P22' => 3,
                            'P23' => 8,
                            'P24' => 4,
                        ],
                    ],
                    [
                        'label' => 'Omitidos',
                        'tone' => 'dark',
                        'values' => [
                            'P1' => 1,
                            'P2' => 0,
                            'P3' => 0,
                            'P4' => 0,
                            'P5' => 1,
                            'P6' => 0,
                            'P7' => 0,
                            'P8' => 2,
                            'P9' => 3,
                            'P10' => 1,
                            'P11' => 1,
                            'P12' => 1,
                            'P13' => 2,
                            'P14' => 2,
                            'P15' => 2,
                            'P16' => 2,
                            'P17' => 2,
                            'P18' => 2,
                            'P19' => 2,
                            'P20' => 10,
                            'P21' => 4,
                            'P22' => 4,
                            'P23' => 5,
                            'P24' => 6,
                        ],
                    ],
                ],
            ],
        ];
    }

    private static function renderHeatmapDatasets(array $datasets): array
    {
        $rendered = [];

        foreach ($datasets as $key => $dataset) {
            $selection = $dataset['selection'];

            $rendered[$key] = [
                'selection' => self::formatSelectionLabel($selection),
                'rows' => self::renderHeatmapRows($dataset['series']),
                'hasData' => true,
                'emptyMessage' => '',
            ];
        }

        return $rendered;
    }

    private static function renderHeatmapRows(array $series): array
    {
        return array_map(
            fn (array $row): array => [
                'label' => $row['label'],
                'count' => number_format(array_sum($row['values'])).' respuestas',
                'tone' => $row['tone'],
                'cells' => self::itemCells($row['values']),
            ],
            $series
        );
    }

    private static function emptyHeatmapDataset(array $selection): array
    {
        return [
            'selection' => self::formatSelectionLabel($selection),
            'rows' => [],
            'hasData' => false,
            'emptyMessage' => 'No hay datos cargados para '.self::formatSelectionLabel($selection).'.',
        ];
    }

    private static function formatSelectionLabel(array $selection): string
    {
        return sprintf(
            '%s / %s / %s',
            self::selectedOptionLabel($selection['grade'], self::gradeOptions()),
            self::selectedOptionLabel($selection['section'], self::sectionOptions()),
            self::selectedOptionLabel($selection['course'], self::courseOptions()),
        );
    }

    private static function gradeOptions(): array
    {
        return [
            ['value' => 'all', 'label' => 'Todos los grados'],
            ['value' => '1', 'label' => '1er grado'],
            ['value' => '2', 'label' => '2do grado'],
            ['value' => '3', 'label' => '3er grado'],
            ['value' => '4', 'label' => '4to grado'],
            ['value' => '5', 'label' => '5to grado'],
            ['value' => '6', 'label' => '6to grado'],
        ];
    }

    private static function sectionOptions(): array
    {
        return [
            ['value' => 'all', 'label' => 'Todas las secciones'],
            ['value' => 'A', 'label' => 'Seccion A'],
            ['value' => 'B', 'label' => 'Seccion B'],
            ['value' => 'C', 'label' => 'Seccion C'],
            ['value' => 'D', 'label' => 'Seccion D'],
            ['value' => 'E', 'label' => 'Seccion E'],
        ];
    }

    private static function courseOptions(): array
    {
        return [
            ['value' => 'all', 'label' => 'Todos los cursos'],
            ['value' => 'lectura', 'label' => 'Lectura'],
            ['value' => 'escritura', 'label' => 'Escritura'],
            ['value' => 'matematica', 'label' => 'Matematica'],
        ];
    }

    private static function getRealPerformanceData(): array
    {
        // Simulación de datos reales por grado para cada dimensión
        // En producción, esto debería extraerse de la base de datos real
        return [
            [
                'dimension' => '1° Grado',
                'logro' => '45% En Desarrollo',
                'nivel_dominante' => 'Inicio',
                'brecha' => '25 puntos',
                'prioridad' => 'BAJO',
                'tone' => 'dark'
            ],
            [
                'dimension' => '2° Grado',
                'logro' => '58% Proceso',
                'nivel_dominante' => 'Proceso Guiado',
                'brecha' => '18 puntos',
                'prioridad' => 'MEDIO',
                'tone' => 'lavender'
            ],
            [
                'dimension' => '3° Grado',
                'logro' => '62% Adecuado',
                'nivel_dominante' => 'Adecuado',
                'brecha' => '12 puntos',
                'prioridad' => 'MEDIO',
                'tone' => 'success'
            ],
            [
                'dimension' => '4° Grado',
                'logro' => '71% Logrado',
                'nivel_dominante' => 'Logro',
                'brecha' => '8 puntos',
                'prioridad' => 'ALTO',
                'tone' => 'gold'
            ],
            [
                'dimension' => '5° Grado',
                'logro' => '76% Logrado',
                'nivel_dominante' => 'Logro',
                'brecha' => '5 puntos',
                'prioridad' => 'ALTO',
                'tone' => 'gold'
            ],
            [
                'dimension' => '6° Grado',
                'logro' => '82% Destacado',
                'nivel_dominante' => 'Destacado',
                'brecha' => '2 puntos',
                'prioridad' => 'ALTO',
                'tone' => 'gold'
            ]
        ];
    }

    private static function getIndividualGradePerformanceData(): array
    {
        // Datos para vista individual por grado (funcionalidad existente)
        return [
            ['dimension' => 'Escritura', 'achievement' => '68%', 'dominant' => 'Proceso guiado', 'gap' => '9 pts', 'priority' => 'Alta', 'tone' => 'lavender'],
            ['dimension' => 'Lectura', 'achievement' => '61%', 'dominant' => 'En desarrollo', 'gap' => '14 pts', 'priority' => 'Alta', 'tone' => 'violet'],
            ['dimension' => 'Matematica', 'achievement' => '64%', 'dominant' => 'Proceso sostenido', 'gap' => '11 pts', 'priority' => 'Media', 'tone' => 'success'],
            ['dimension' => 'Autorregulacion', 'achievement' => '72%', 'dominant' => 'Adecuado', 'gap' => '7 pts', 'priority' => 'Media', 'tone' => 'neutral'],
            ['dimension' => 'Participacion', 'achievement' => '81%', 'dominant' => 'Destacado', 'gap' => '4 pts', 'priority' => 'Baja', 'tone' => 'gold'],
        ];
    }

    private static function selectedOptionLabel(string $value, array $options): string
    {
        foreach ($options as $option) {
            if ($option['value'] === $value) {
                return $option['label'];
            }
        }

        return '';
    }

    private static function resources(): array
    {
        return [
            [
                'title' => 'Bootstrap Icons',
                'url' => 'https://icons.getbootstrap.com/',
                'description' => 'Catalogo oficial de iconos SVG y web font para acciones, paneles y estados.',
                'icon' => 'bi-bootstrap-fill',
            ],
            [
                'title' => 'Lucide',
                'url' => 'https://lucide.dev/icons/',
                'description' => 'Biblioteca lineal limpia para futuras variantes y modulos internos del tablero.',
                'icon' => 'bi-stars',
            ],
        ];
    }

    private static function modules(): array
    {
        return [
            [
                'title' => 'Motor de calculos',
                'description' => 'Bloque listo para inyectar totales, porcentajes, brechas y reglas de conversion.',
                'icon' => 'bi-calculator-fill',
            ],
            [
                'title' => 'Alertas automatizadas',
                'description' => 'Espacio reservado para reglas por aula, nivel de riesgo y seguimiento de omisiones.',
                'icon' => 'bi-bell-fill',
            ],
            [
                'title' => 'Comparativo historico',
                'description' => 'Panel preparado para cortes, cohortes y evolucion entre diagnosticos.',
                'icon' => 'bi-bar-chart-steps',
            ],
        ];
    }

    private static function itemRatioCaption(int $value, int $itemTotal): string
    {
        if ($itemTotal === 0) {
            return 'Sin datos';
        }

        return number_format(($value / $itemTotal) * 100, 1).' % de criterios';
    }

}

