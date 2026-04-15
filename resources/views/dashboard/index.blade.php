<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $header['title'] }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="dashboard-body">
        <div class="dashboard-app" data-dashboard-app>
            <script id="dashboard-snapshots" type="application/json">@json($snapshots)</script>
            <script id="dashboard-metric-datasets" type="application/json">@json($metricDatasets)</script>
            <script id="dashboard-analytics-datasets" type="application/json">@json($analyticsDatasets)</script>

            <div class="dashboard-shell">
                <aside class="dashboard-sidebar">
                    @include('dashboard.partials.filters', ['filters' => $filters])
                </aside>

                <div class="dashboard-content">
                    @include('dashboard.partials.header', [
                        'header' => $header,
                        'filters' => $filters,
                    ])

                    <main class="dashboard-main">
                        @include('dashboard.partials.summary-cards', ['metrics' => $metrics])
                        @include('dashboard.partials.analytics', [
                            'riskBars' => $riskBars,
                            'learningDistribution' => $learningDistribution,
                            'writingHypotheses' => $writingHypotheses,
                        ])

                        <section class="dashboard-lower" id="tables-section">
                            <div class="dashboard-lower__main">
                                @include('dashboard.partials.tables', [
                                    'performanceTable' => $performanceTable,
                                    'alertTable' => $alertTable,
                                ])
                            </div>

                            <aside class="dashboard-lower__side">
                                @include('dashboard.partials.resources', [
                                    'resources' => $resources,
                                    'modules' => $modules,
                                ])
                            </aside>
                        </section>

                        @include('dashboard.partials.heatmap', ['heatmap' => $heatmap])
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
