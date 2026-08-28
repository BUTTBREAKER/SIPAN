/**
 * SIPAN - Grid.js Initializer
 * Standardizes table look & feel across the app.
 */

function initSipanGrid({
    element,
    data,
    columns,
    search = true,
    pagination = true,
    sort = true,
    language = 'es',
}) {
    if (!element) return

    // Custom "Pan Dorado" Styles injected via generic config or CSS
    // but here we configure the instance.

    const grid = new gridjs.Grid({
        className: {
            container: 'sipan-grid-container',
            paginationButton: 'sipan-grid-btn',
            search: 'sipan-grid-search',
            table: 'sipan-grid-table',
            td: 'sipan-grid-td',
            th: 'sipan-grid-th',
        },
        columns: columns,
        data: data,
        language: {
            loading: 'Cargando...',
            noRecordsFound: 'No se encontraron registros',
            pagination: {
                next: 'Siguiente',
                previous: 'Anterior',
                results: () => 'resultados',
                showing: 'Mostrando',
            },
            search: {
                placeholder: '🔍 Buscar...',
            },
        },
        pagination: pagination
            ? {
                  enabled: true,
                  limit: 10,
                  summary: true,
              }
            : false,
        search: search
            ? {
                  enabled: true,
                  placeholder: 'Buscar...',
              }
            : false,
        sort: sort,
        style: {
            container: {
                background: 'transparent',
                'box-shadow': 'none',
                padding: '0',
            },
            footer: {
                'background-color': 'transparent',
            },
            table: {
                background: 'transparent',
                'border-collapse': 'separate',
                'border-spacing': '0 0.5rem', // Row spacing
                width: '100%',
            },
            td: {
                'background-color': 'var(--bg-card)',
                'border-bottom': '1px solid var(--border-color)',
                'border-top': '1px solid var(--border-color)',
                color: 'var(--text-main)',
                'font-size': '0.9rem',
                padding: '1rem',
            },
            th: {
                'background-color': 'rgba(255, 255, 255, 0.05)',
                border: 'none',
                color: 'var(--primary)',
                'font-size': '0.75rem',
                'font-weight': '700',
                padding: '1rem',
                'text-transform': 'uppercase',
            },
        },
    }).render(element)

    return grid
}
