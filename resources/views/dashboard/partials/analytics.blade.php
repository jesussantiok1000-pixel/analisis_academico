@php
    $maxRisk = max(array_column($riskBars, 'value'));
    $maxWriting = max(array_column($writingHypotheses, 'value'));
    $distributionTotal = array_sum(array_column($learningDistribution, 'value'));
    $distributionOffset = 0;
    $distributionStops = [];

    foreach ($learningDistribution as $segment) {
        $start = $distributionOffset;
        $distributionOffset += $distributionTotal > 0 ? ($segment['value'] / $distributionTotal) * 100 : 0;
        $distributionStops[] = "{$segment['color']} {$start}% {$distributionOffset}%";
    }

    $donutGradient = implode(', ', $distributionStops);
@endphp

<section class="analytics-grid" id="analytics-section">
    <article class="section-card section-card--wide">
        <div class="section-card__header">
            <div>
                <p class="section-card__eyebrow">Riesgos generales</p>
                <h2 class="section-card__title">Concentracion en niveles de riesgo</h2>
            </div>

            <span class="section-card__icon">
                <i class="bi bi-bar-chart-fill"></i>
            </span>
        </div>

        <div class="risk-chart">
            @foreach ($riskBars as $bar)
                <div class="risk-chart__item">
                    <span class="risk-chart__value" data-risk-value="{{ $bar['key'] }}">{{ $bar['value'] }}</span>
                    <div class="risk-chart__bar">
                        <div
                            class="risk-chart__fill"
                            data-risk-key="{{ $bar['key'] }}"
                            style="height: {{ $maxRisk > 0 ? ($bar['value'] / $maxRisk) * 100 : 0 }}%;"
                        ></div>
                    </div>
                    <span class="risk-chart__label">{{ $bar['label'] }}</span>
                </div>
            @endforeach
        </div>
    </article>

    <article class="section-card">
        <div class="section-card__header">
            <div>
                <p class="section-card__eyebrow">Tasa de aprendizaje</p>
                <h2 class="section-card__title">Distribucion porcentual por nivel</h2>
            </div>

            <span class="section-card__icon">
                <i class="bi bi-pie-chart-fill"></i>
            </span>
        </div>

        <div class="donut-card">
            <div class="donut-card__chart">
                <div class="donut-card__ring" data-donut style="--dashboard-donut: {{ $donutGradient }}">
                    <div class="donut-card__center">
                        <strong data-distribution-total>{{ $distributionTotal }}%</strong>
                        <span>Distribucion</span>
                    </div>
                </div>
            </div>

            <ul class="donut-card__legend">
                @foreach ($learningDistribution as $segment)
                    <li class="legend-row">
                        <span class="legend-row__label">
                            <span class="legend-row__swatch" style="background: {{ $segment['color'] }}"></span>
                            {{ $segment['label'] }}
                        </span>
                        <strong data-distribution-value="{{ $segment['key'] }}">{{ $segment['value'] }}%</strong>
                    </li>
                @endforeach
            </ul>
        </div>
    </article>

    <article class="section-card">
        <div class="section-card__header">
            <div>
                <p class="section-card__eyebrow">Hipotesis de escritura</p>
                <h2 class="section-card__title">Frecuencia por categoria</h2>
            </div>

            <span class="section-card__icon">
                <i class="bi bi-journal-text"></i>
            </span>
        </div>

        <div class="progress-list">
            @foreach ($writingHypotheses as $hypothesis)
                <div class="progress-list__row">
                    <span class="progress-list__label">{{ $hypothesis['label'] }}</span>
                    <div class="progress-list__track">
                        <div
                            class="progress-list__fill"
                            data-writing-key="{{ $hypothesis['key'] }}"
                            style="width: {{ $maxWriting > 0 ? ($hypothesis['value'] / $maxWriting) * 100 : 0 }}%;"
                        ></div>
                    </div>
                    <strong class="progress-list__value" data-writing-value="{{ $hypothesis['key'] }}">{{ $hypothesis['value'] }}</strong>
                </div>
            @endforeach
        </div>
    </article>
</section>
