<section class="metric-grid" id="overview-section">
    @foreach ($metrics as $metric)
        <article class="metric-card metric-card--{{ $metric['tone'] }}">
            <div class="metric-card__icon">
                <i class="bi {{ $metric['icon'] }}"></i>
            </div>

            <div class="metric-card__content">
                <p class="metric-card__label">{{ $metric['label'] }}</p>
                <p class="metric-card__value" data-metric-key="{{ $metric['key'] }}">{{ number_format($metric['value']) }}</p>
            </div>
        </article>
    @endforeach
</section>
