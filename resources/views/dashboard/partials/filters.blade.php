<div class="filter-panel">
    <div class="filter-panel__header">
        <h2 class="filter-panel__title">Filtros</h2>
    </div>

    <div class="filter-panel__group filter-panel__group--fields">
        @foreach ($filters['items'] as $item)
            <label class="filter-field" data-filter-field>
                <span class="filter-field__label">
                    <i class="bi {{ $item['icon'] }}"></i>
                    {{ $item['label'] }}
                </span>

                <span class="filter-select-wrap">
                    <select
                        class="filter-select"
                        data-filter-select
                        data-filter-key="{{ $item['key'] }}"
                        aria-label="{{ $item['label'] }}"
                    >
                        @foreach ($item['options'] as $option)
                            <option value="{{ $option['value'] }}" @selected($option['value'] === $item['value'])>
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    </select>

                    <i class="bi bi-chevron-down filter-select__icon"></i>
                </span>
            </label>
        @endforeach
    </div>

    <div
        class="filter-panel__group filter-panel__group--chips"
        data-filter-chip-group
        @if (! collect($filters['items'])->contains(fn ($item) => $item['value'] !== 'all')) hidden @endif
    >
        <div class="filter-chip-list">
            @foreach ($filters['items'] as $item)
                <span
                    class="filter-chip"
                    data-filter-chip="{{ $item['key'] }}"
                    @if ($item['value'] === 'all') hidden @endif
                >
                    <span class="filter-chip__copy">
                        <small>{{ $item['label'] }}</small>
                        <strong data-filter-preview="{{ $item['key'] }}">{{ $item['selectedLabel'] }}</strong>
                    </span>

                    <button
                        type="button"
                        class="filter-chip__clear"
                        data-filter-clear
                        data-filter-target="{{ $item['key'] }}"
                        aria-label="Quitar seleccion de {{ strtolower($item['label']) }}"
                        @disabled($item['value'] === 'all')
                    >
                        <i class="bi bi-x-lg"></i>
                    </button>
                </span>
            @endforeach
        </div>
    </div>
</div>
