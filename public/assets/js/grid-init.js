/**
 * SIPAN - Grid.js Initializer
 * Standardizes table look & feel across the app.
 */

function initSipanGrid({ element, data, columns, search = true, pagination = true, sort = true, language = 'es' }) {
    if (!element) return;

    // Custom "Pan Dorado" Styles injected via generic config or CSS 
    // but here we configure the instance.

    const grid = new gridjs.Grid({
        data: data,
        columns: columns,
        search: search ? {
            enabled: true,
            placeholder: 'Buscar...'
        } : false,
        sort: sort,
        pagination: pagination ? {
            enabled: true,
            limit: 10,
            summary: true
        } : false,
        language: {
            'search': {
                'placeholder': '🔍 Buscar...'
            },
            'pagination': {
                'previous': 'Anterior',
                'next': 'Siguiente',
                'showing': 'Mostrando',
                'results': () => 'resultados'
            },
            loading: 'Cargando...',
            noRecordsFound: 'No se encontraron registros'
        },
        style: {
            table: {
                'width': '100%',
                'background': 'transparent',
                'border-collapse': 'separate',
                'border-spacing': '0 0.5rem' // Row spacing
            },
            th: {
                'background-color': 'rgba(255, 255, 255, 0.05)',
                'color': 'var(--primary)',
                'font-weight': '700',
                'text-transform': 'uppercase',
                'font-size': '0.75rem',
                'border': 'none',
                'padding': '1rem'
            },
            td: {
                'background-color': 'var(--bg-card)',
                'border-top': '1px solid var(--border-color)',
                'border-bottom': '1px solid var(--border-color)',
                'color': 'var(--text-main)',
                'padding': '1rem',
                'font-size': '0.9rem'
            },
            container: {
                'padding': '0',
                'background': 'transparent',
                'box-shadow': 'none'
            },
            footer: {
                'background-color': 'transparent'
            }
        },
        className: {
            table: 'sipan-grid-table',
            th: 'sipan-grid-th',
            td: 'sipan-grid-td',
            container: 'sipan-grid-container',
            search: 'sipan-grid-search',
            paginationButton: 'sipan-grid-btn'
        }
    }).render(element);

    return grid;
}
