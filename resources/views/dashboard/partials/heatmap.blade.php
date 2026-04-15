<section class="section-card heatmap-card" id="heatmap-section" data-heatmap-section>
    <div class="section-card__header">
        <div>
            <h2 class="section-card__title">Item</h2>
        </div>
    </div>

    <div class="heatmap-empty" data-heatmap-empty @if (! empty($heatmap['hasData'])) hidden @endif>
        {{ $heatmap['emptyMessage'] ?? '' }}
    </div>

    <div class="heatmap-board" data-heatmap-board @if (empty($heatmap['hasData'])) hidden @endif>
        @foreach ($heatmap['rows'] as $row)
            <div class="heatmap-row">
                <div class="heatmap-row__label heatmap-row__label--{{ $row['tone'] }}">
                    <strong>{{ $row['label'] }}</strong>
                    <span>{{ $row['count'] }}</span>
                </div>

                <div class="heatmap-row__grid-wrap">
                    <div class="heatmap-row__grid" style="--heatmap-columns: {{ count($row['cells']) }};">
                        @foreach ($row['cells'] as $cell)
                            <div class="heatmap-cell heatmap-cell--{{ $row['tone'] }}">
                                <span class="heatmap-cell__label">{{ $cell['label'] }}</span>
                                <strong class="heatmap-cell__value">{{ $cell['value'] }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script id="dashboard-heatmap-datasets" type="application/json">@json($heatmap['datasets'])</script>
</section>