<header class="hero-card">
    <div class="hero-card__grid">
        <div class="hero-brand">
            <div class="hero-logo">
                <i class="bi bi-mortarboard-fill"></i>
            </div>

            <div class="hero-copy">
                @if (! empty($header['institution']))
                    <p class="hero-eyebrow">{{ $header['institution'] }}</p>
                @endif
                <h1 class="hero-title">{{ $header['title'] }}</h1>
                <p class="hero-subtitle">
                    {{ $header['subtitle'] }}
                    @if (! empty($header['cohort']))
                        <span>{{ $header['cohort'] }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="hero-style-selector">
            <label class="filter-field" data-filter-field>
                <span class="filter-field__label">
                    <i class="bi bi-palette-fill"></i>
                    Tema
                </span>
                <span class="filter-select-wrap">
                    <select class="filter-select" id="globalStyleSelect">
                        <option value="ivory">Marfil Editorial</option>
                        <option value="aurora">Aurora Cian</option>
                        <option value="graphite">Grafito Luxe</option>
                        <option value="verdant">Verde Atelier</option>
                        <option value="ember">Cobre Solar</option>
                    </select>
                    <i class="bi bi-chevron-down filter-select__icon"></i>
                </span>
            </label>
        </div>
    </div>
</header>