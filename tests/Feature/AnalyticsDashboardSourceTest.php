<?php

namespace Tests\Feature;

use App\Support\AnalyticsDashboardSource;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AnalyticsDashboardSourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.connections.analytics', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => false,
        ]);

        DB::purge('analytics');
        $this->createAnalyticsTables();
    }

    public function test_it_builds_dataset_keys_with_canonical_filter_values(): void
    {
        DB::connection('analytics')->table('vw_indicador_item')->insert([
            [
                'grado' => '2do grado',
                'seccion' => 'Sección A',
                'curso' => 'Matemática',
                'item' => 'P1',
                'orden_item' => 1,
                'adecuados_item' => 3,
                'parciales_item' => 2,
                'inadecuados_item' => 1,
                'omitidos_item' => 0,
            ],
            [
                'grado' => '2do grado',
                'seccion' => 'Seccion A',
                'curso' => 'Lectura',
                'item' => 'P1',
                'orden_item' => 1,
                'adecuados_item' => 4,
                'parciales_item' => 1,
                'inadecuados_item' => 0,
                'omitidos_item' => 1,
            ],
        ]);

        DB::connection('analytics')->table('vw_indicador_estudiante')->insert([
            [
                'alumno' => 'Ana Perez',
                'grado' => '2do grado',
                'seccion' => 'Sección A',
                'curso' => 'Matemática',
                'nivel_logro_raw' => 'Previo al inicio',
                'nivel_logro_panel' => null,
                'hipotesis_escritura' => 'Silábico-Alfabético',
                'hipotesis_panel' => null,
            ],
            [
                'alumno' => 'Luis Rojas',
                'grado' => '2do grado',
                'seccion' => 'Seccion A',
                'curso' => 'Lectura',
                'nivel_logro_raw' => 'Logrado',
                'nivel_logro_panel' => null,
                'hipotesis_escritura' => 'Alfabético',
                'hipotesis_panel' => null,
            ],
        ]);

        $source = AnalyticsDashboardSource::load();

        $this->assertTrue($source['connected']);
        $this->assertArrayHasKey('2|A|matematica', $source['heatmapDatasets']);
        $this->assertArrayHasKey('2|A|matematica', $source['metricDatasets']);
        $this->assertArrayHasKey('2|A|matematica', $source['analyticsDatasets']);
        $this->assertArrayHasKey('2|A|lectura', $source['heatmapDatasets']);
        $this->assertArrayNotHasKey('2|SECCION A|matemática', $source['heatmapDatasets']);

        $this->assertSame(3, $source['metricDatasets']['2|A|matematica']['adequate']);
        $this->assertSame(1, $source['metricDatasets']['2|A|matematica']['students']);
        $this->assertSame(1, $source['analyticsDatasets']['2|A|matematica']['riskBars'][0]['value']);
    }

    private function createAnalyticsTables(): void
    {
        Schema::connection('analytics')->create('vw_indicador_item', function (Blueprint $table): void {
            $table->string('grado');
            $table->string('seccion');
            $table->string('curso');
            $table->string('item');
            $table->integer('orden_item')->nullable();
            $table->integer('adecuados_item');
            $table->integer('parciales_item');
            $table->integer('inadecuados_item');
            $table->integer('omitidos_item');
        });

        Schema::connection('analytics')->create('vw_indicador_estudiante', function (Blueprint $table): void {
            $table->string('alumno');
            $table->string('grado');
            $table->string('seccion');
            $table->string('curso');
            $table->string('nivel_logro_raw')->nullable();
            $table->string('nivel_logro_panel')->nullable();
            $table->string('hipotesis_escritura')->nullable();
            $table->string('hipotesis_panel')->nullable();
        });
    }
}
