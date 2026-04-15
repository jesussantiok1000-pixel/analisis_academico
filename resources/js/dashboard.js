const numberFormatter = new Intl.NumberFormat('es-ES');

const animateNumber = (element, target, prefersReducedMotion) => {
    if (!element) {
        return;
    }

    const startValue = Number(element.dataset.currentValue ?? 0);

    if (prefersReducedMotion) {
        element.textContent = numberFormatter.format(target);
        element.dataset.currentValue = String(target);
        return;
    }

    const start = performance.now();
    const duration = 420;

    const frame = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        const value = Math.round(startValue + (target - startValue) * eased);

        element.textContent = numberFormatter.format(value);

        if (progress < 1) {
            requestAnimationFrame(frame);
            return;
        }

        element.dataset.currentValue = String(target);
    };

    requestAnimationFrame(frame);
};

const buildDonutGradient = (segments) => {
    const total = segments.reduce((sum, segment) => sum + Number(segment.value ?? 0), 0);
    let offset = 0;

    return segments
        .map((segment) => {
            const start = offset;
            offset += total > 0 ? (Number(segment.value ?? 0) / total) * 100 : 0;
            return `${segment.color} ${start}% ${offset}%`;
        })
        .join(', ');
};

const metricCaption = (key, value, metrics) => {
    if (key === 'students') {
        return 'Rindieron el examen';
    }

    const total = Number(metrics.itemTotal ?? 0);

    if (!total) {
        return 'Sin datos';
    }

    return `${((value / total) * 100).toFixed(1)} % de criterios`;
};

const syncFilterState = (app, select) => {
    const key = select.dataset.filterKey;
    const field = select.closest('[data-filter-field]');
    const option = select.options[select.selectedIndex];
    const label = option ? option.textContent.trim() : '';
    const isFiltered = select.value !== 'all';
    const clearButton = app.querySelector(`[data-filter-clear][data-filter-target="${key}"]`);
    const chip = app.querySelector(`[data-filter-chip="${key}"]`);

    if (field) {
        field.classList.toggle('filter-field--filled', isFiltered);
    }

    app.querySelectorAll(`[data-filter-preview="${key}"], [data-filter-badge="${key}"]`).forEach((node) => {
        node.textContent = label;
    });

    if (clearButton) {
        clearButton.disabled = !isFiltered;
    }

    if (chip) {
        chip.hidden = !isFiltered;
    }

    const chipGroup = app.querySelector('[data-filter-chip-group]');

    if (chipGroup) {
        const visibleChips = app.querySelectorAll('[data-filter-chip]:not([hidden])').length;
        chipGroup.hidden = visibleChips === 0;
    }
};

const buildHeatmapKey = (selection) => `${selection.grade}|${selection.section}|${selection.course}`;

const getFilterSelection = (app) => {
    const read = (key) => {
        const select = app.querySelector(`[data-filter-key="${key}"]`);
        const option = select?.options[select.selectedIndex];

        return {
            value: select?.value ?? 'all',
            label: option ? option.textContent.trim() : '',
        };
    };

    const grade = read('grade');
    const section = read('section');
    const course = read('course');

    return {
        grade: grade.value,
        section: section.value,
        course: course.value,
        gradeLabel: grade.label,
        sectionLabel: section.label,
        courseLabel: course.label,
    };
};

const renderHeatmapRow = (row) => {
    const cells = row.cells
        .map((cell) => `
            <div class="heatmap-cell heatmap-cell--${row.tone}">
                <span class="heatmap-cell__label">${cell.label}</span>
                <strong class="heatmap-cell__value">${numberFormatter.format(cell.value)}</strong>
            </div>
        `)
        .join('');

    return `
        <div class="heatmap-row">
            <div class="heatmap-row__label heatmap-row__label--${row.tone}">
                <strong>${row.label}</strong>
                <span>${row.count}</span>
            </div>

            <div class="heatmap-row__grid-wrap">
                <div class="heatmap-row__grid" style="--heatmap-columns: ${row.cells.length};">
                    ${cells}
                </div>
            </div>
        </div>
    `;
};

const emptyAnalyticsDataset = {
    riskBars: [
        { key: 'previo', label: 'Previo al inicio', value: 0 },
        { key: 'inicio', label: 'Inicio', value: 0 },
    ],
    learningDistribution: [
        { key: 'previo', label: 'Previo al inicio', value: 0, color: '#c85d5d' },
        { key: 'inicio', label: 'Inicio', value: 0, color: '#d89aaf' },
        { key: 'proceso', label: 'Proceso', value: 0, color: '#c7a23a' },
        { key: 'logro', label: 'Logrado', value: 0, color: '#5f9870' },
    ],
    writingHypotheses: [
        { key: 'presilabico', label: 'Presilabico', value: 0 },
        { key: 'silabico', label: 'Silabico', value: 0 },
        { key: 'silabico_alfabetico', label: 'Silabico-Alfabetico', value: 0 },
        { key: 'alfabetico', label: 'Alfabetico', value: 0 },
    ],
};

const emptyMetricDataset = {
    adequate: 0,
    partial: 0,
    inadequate: 0,
    omitted: 0,
    students: 0,
    itemTotal: 0,
};


const DASHBOARD_THEME_STORAGE_KEY = 'dashboardTheme';
const DEFAULT_DASHBOARD_THEME = 'ivory';

const applyGlobalStyle = (style) => {
    const body = document.body;

    if (!body) {
        return;
    }

    const supportedStyles = new Set(['ivory', 'aurora', 'graphite', 'verdant', 'ember']);
    const selectedStyle = supportedStyles.has(style) ? style : DEFAULT_DASHBOARD_THEME;

    body.dataset.dashboardTheme = selectedStyle;
    localStorage.setItem(DASHBOARD_THEME_STORAGE_KEY, selectedStyle);
};

const wireGlobalStyleSelector = () => {
    const select = document.getElementById('globalStyleSelect');

    if (!select) {
        return;
    }

    const availableStyles = new Set(Array.from(select.options).map((option) => option.value));
    const savedStyle = localStorage.getItem(DASHBOARD_THEME_STORAGE_KEY);
    const initialStyle = availableStyles.has(savedStyle) ? savedStyle : DEFAULT_DASHBOARD_THEME;

    select.value = initialStyle;
    applyGlobalStyle(initialStyle);
    select.addEventListener('change', (event) => applyGlobalStyle(event.target.value));
    window.applyGlobalStyle = applyGlobalStyle;
};
const wireDashboard = () => {
    const app = document.querySelector('[data-dashboard-app]');

    if (!app) {
        return;
    }

    wireGlobalStyleSelector();

    const snapshotNode = document.getElementById('dashboard-snapshots');

    if (!snapshotNode) {
        return;
    }

    const snapshots = JSON.parse(snapshotNode.textContent);
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const snapshot = snapshots.general ?? Object.values(snapshots)[0];
    const donut = app.querySelector('[data-donut]');
    const donutTotal = app.querySelector('[data-distribution-total]');
    const filterSelects = app.querySelectorAll('[data-filter-select]');
    const filterClearButtons = app.querySelectorAll('[data-filter-clear]');
    const metricNode = document.getElementById('dashboard-metric-datasets');
    const analyticsNode = document.getElementById('dashboard-analytics-datasets');
    const heatmapNode = document.getElementById('dashboard-heatmap-datasets');
    const heatmapSection = app.querySelector('[data-heatmap-section]');
    const heatmapBoard = app.querySelector('[data-heatmap-board]');
    const heatmapEmpty = app.querySelector('[data-heatmap-empty]');
    const heatmapSelection = app.querySelector('[data-heatmap-selection-value]');
    const metricDatasets = metricNode ? JSON.parse(metricNode.textContent) : {};
    const analyticsDatasets = analyticsNode ? JSON.parse(analyticsNode.textContent) : {};
    const heatmapDatasets = heatmapNode ? JSON.parse(heatmapNode.textContent) : {};

    if (!snapshot) {
        return;
    }

    const resolveMetrics = (selection) => metricDatasets[buildHeatmapKey(selection)] ?? emptyMetricDataset;
    const resolveAnalytics = (selection) => analyticsDatasets[buildHeatmapKey(selection)] ?? emptyAnalyticsDataset;

    const updateMetrics = (selection) => {
        const metrics = resolveMetrics(selection);

        Object.entries(metrics).forEach(([key, value]) => {
            if (key === 'itemTotal') {
                return;
            }

            const valueNode = app.querySelector(`[data-metric-key="${key}"]`);
            const captionNode = app.querySelector(`[data-metric-caption="${key}"]`);

            animateNumber(valueNode, value, prefersReducedMotion);

            if (captionNode) {
                captionNode.textContent = metricCaption(key, value, metrics);
            }
        });
    };

    const updateRiskBars = (bars) => {
        const max = Math.max(0, ...bars.map(({ value }) => Number(value)));

        bars.forEach(({ key, value }) => {
            const barNode = app.querySelector(`[data-risk-key="${key}"]`);
            const valueNode = app.querySelector(`[data-risk-value="${key}"]`);

            if (barNode) {
                barNode.style.height = `${max > 0 ? (value / max) * 100 : 0}%`;
            }

            if (valueNode) {
                valueNode.textContent = value;
            }
        });
    };

    const updateDistribution = (segments) => {
        const total = segments.reduce((sum, { value }) => sum + Number(value), 0);

        if (donut) {
            donut.style.setProperty('--dashboard-donut', buildDonutGradient(segments));
        }

        if (donutTotal) {
            donutTotal.textContent = `${total}%`;
        }

        segments.forEach(({ key, value }) => {
            const valueNode = app.querySelector(`[data-distribution-value="${key}"]`);

            if (valueNode) {
                valueNode.textContent = `${value}%`;
            }
        });
    };

    const updateWriting = (hypotheses) => {
        const max = Math.max(0, ...hypotheses.map(({ value }) => Number(value)));

        hypotheses.forEach(({ key, value }) => {
            const barNode = app.querySelector(`[data-writing-key="${key}"]`);
            const valueNode = app.querySelector(`[data-writing-value="${key}"]`);

            if (barNode) {
                barNode.style.width = `${max > 0 ? (value / max) * 100 : 0}%`;
            }

            if (valueNode) {
                valueNode.textContent = value;
            }
        });
    };

    const renderSnapshot = () => {
        const selection = getFilterSelection(app);
        const analytics = resolveAnalytics(selection);

        updateMetrics(selection);
        updateRiskBars(analytics.riskBars);
        updateDistribution(analytics.learningDistribution);
        updateWriting(analytics.writingHypotheses);
    };

    const updateHeatmap = () => {
        if (!heatmapSection || !heatmapBoard || !heatmapEmpty) {
            return;
        }

        const selection = getFilterSelection(app);
        const selectionLabel = `${selection.gradeLabel} / ${selection.sectionLabel} / ${selection.courseLabel}`;
        const dataset = heatmapDatasets[buildHeatmapKey(selection)];

        if (heatmapSelection) {
            heatmapSelection.textContent = selectionLabel;
        }

        if (!dataset) {
            heatmapBoard.innerHTML = '';
            heatmapBoard.hidden = true;
            heatmapEmpty.textContent = `No hay datos cargados para ${selectionLabel}.`;
            heatmapEmpty.hidden = false;
            return;
        }

        heatmapBoard.innerHTML = dataset.rows.map(renderHeatmapRow).join('');
        heatmapBoard.hidden = false;
        heatmapEmpty.hidden = true;
    };

    renderSnapshot();
    updateHeatmap();

    filterSelects.forEach((select) => {
        syncFilterState(app, select);

        select.addEventListener('change', () => {
            syncFilterState(app, select);
            renderSnapshot();
            updateHeatmap();
        });
    });

    filterClearButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const key = button.dataset.filterTarget;
            const select = app.querySelector(`[data-filter-key="${key}"]`);

            if (!select || select.value === 'all') {
                return;
            }

            select.value = 'all';
            select.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', wireDashboard);
} else {
    wireDashboard();
}





