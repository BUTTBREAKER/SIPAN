/**
 * SIPAN - DataTables Initialization
 * Centralized configuration for all app tables
 */

document.addEventListener('DOMContentLoaded', () => {
    initDataTables()
})

function initDataTables() {
    const tables = document.querySelectorAll('.datatable')

    tables.forEach(table => {
        // Prevent double init
        if (table.classList.contains('dataTable-table')) return

        new simpleDatatables.DataTable(table, {
            classes: {
                active: 'datatable-active',
                ascending: 'datatable-ascending',
                bottom: 'datatable-bottom',
                container: 'datatable-container',
                cursor: 'datatable-cursor',
                descending: 'datatable-descending',
                disabled: 'datatable-disabled',
                dropdown: 'datatable-dropdown',
                ellipsis: 'datatable-ellipsis',
                empty: 'datatable-empty',
                filter: 'datatable-filter',
                filterActive: 'datatable-filter-active',
                header: 'datatable-header',
                hidden: 'datatable-hidden',
                input: 'datatable-input',
                loading: 'datatable-loading',
                pagination: 'datatable-pagination',
                paginationList: 'datatable-pagination-list',
                search: 'datatable-search',
                selector: 'datatable-selector',
                sorter: 'datatable-sorter',
                table: 'datatable-table',
                top: 'datatable-top',
                wrapper: 'datatable-wrapper',
            },
            fixedHeight: false,
            labels: {
                info: 'Mostrando {start} a {end} de {rows} registros',
                infoFiltered: ' (filtrado de {max} registros)',
                loading: 'Cargando...',
                noRows: 'No se encontraron registros',
                perPage: 'registros por página',
                placeholder: 'Buscar...',
            },
            perPage: 10,
            perPageSelect: [5, 10, 20, 50, 100],
            searchable: true,
        })
    })
}
