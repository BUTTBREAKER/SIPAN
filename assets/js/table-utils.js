// Utilidades para Tablas - SIPAN

class TableUtils {
    constructor(tableId, options = {}) {
        this.table = document.getElementById(tableId);
        if (!this.table) return;
        
        this.tbody = this.table.querySelector('tbody');
        this.thead = this.table.querySelector('thead');
        this.rows = Array.from(this.tbody.querySelectorAll('tr'));
        this.currentPage = 1;
        this.rowsPerPage = options.rowsPerPage || 10;
        this.sortColumn = null;
        this.sortDirection = 'asc';
        this.filters = {};
        
        this.init();
    }
    
    init() {
        this.setupSorting();
        this.setupPagination();
        this.render();
    }
    
    // Configurar ordenamiento
    setupSorting() {
        if (!this.thead) return;
        
        const headers = this.thead.querySelectorAll('th[data-sortable]');
        headers.forEach((header, index) => {
            header.classList.add('sortable');
            header.addEventListener('click', () => this.sort(index));
        });
    }
    
    // Ordenar tabla
    sort(columnIndex) {
        const headers = this.thead.querySelectorAll('th');
        const currentHeader = headers[columnIndex];
        
        // Remover clases de otros headers
        headers.forEach(h => h.classList.remove('asc', 'desc'));
        
        // Determinar dirección
        if (this.sortColumn === columnIndex) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortDirection = 'asc';
        }
        
        this.sortColumn = columnIndex;
        currentHeader.classList.add(this.sortDirection);
        
        // Ordenar filas
        this.rows.sort((a, b) => {
            const aValue = a.cells[columnIndex]?.textContent.trim() || '';
            const bValue = b.cells[columnIndex]?.textContent.trim() || '';
            
            // Intentar comparar como números
            const aNum = parseFloat(aValue.replace(/[^\d.-]/g, ''));
            const bNum = parseFloat(bValue.replace(/[^\d.-]/g, ''));
            
            if (!isNaN(aNum) && !isNaN(bNum)) {
                return this.sortDirection === 'asc' ? aNum - bNum : bNum - aNum;
            }
            
            // Comparar como texto
            return this.sortDirection === 'asc' 
                ? aValue.localeCompare(bValue)
                : bValue.localeCompare(aValue);
        });
        
        this.currentPage = 1;
        this.render();
    }
    
    // Configurar paginación
    setupPagination() {
        const container = document.createElement('div');
        container.className = 'pagination';
        container.id = this.table.id + '-pagination';
        this.table.parentElement.appendChild(container);
        this.paginationContainer = container;
    }
    
    // Renderizar tabla
    render() {
        // Aplicar filtros
        let filteredRows = this.rows;
        
        if (Object.keys(this.filters).length > 0) {
            filteredRows = this.rows.filter(row => {
                return Object.entries(this.filters).every(([column, value]) => {
                    const cell = row.cells[column];
                    if (!cell) return true;
                    return cell.textContent.toLowerCase().includes(value.toLowerCase());
                });
            });
        }
        
        // Calcular paginación
        const totalPages = Math.ceil(filteredRows.length / this.rowsPerPage);
        const start = (this.currentPage - 1) * this.rowsPerPage;
        const end = start + this.rowsPerPage;
        const pageRows = filteredRows.slice(start, end);
        
        // Limpiar tbody
        this.tbody.innerHTML = '';
        
        // Agregar filas de la página actual
        if (pageRows.length === 0) {
            const emptyRow = document.createElement('tr');
            const emptyCell = document.createElement('td');
            emptyCell.colSpan = this.thead?.querySelectorAll('th').length || 1;
            emptyCell.textContent = 'No hay registros para mostrar';
            emptyCell.className = 'text-center';
            emptyRow.appendChild(emptyCell);
            this.tbody.appendChild(emptyRow);
        } else {
            pageRows.forEach(row => this.tbody.appendChild(row.cloneNode(true)));
        }
        
        // Renderizar controles de paginación
        this.renderPagination(totalPages, filteredRows.length);
    }
    
    // Renderizar controles de paginación
    renderPagination(totalPages, totalRows) {
        if (!this.paginationContainer) return;
        
        this.paginationContainer.innerHTML = '';
        
        if (totalPages <= 1) return;
        
        // Botón anterior
        const prevBtn = document.createElement('button');
        prevBtn.className = 'pagination-btn';
        prevBtn.textContent = '← Anterior';
        prevBtn.disabled = this.currentPage === 1;
        prevBtn.onclick = () => this.goToPage(this.currentPage - 1);
        this.paginationContainer.appendChild(prevBtn);
        
        // Números de página
        const maxButtons = 5;
        let startPage = Math.max(1, this.currentPage - Math.floor(maxButtons / 2));
        let endPage = Math.min(totalPages, startPage + maxButtons - 1);
        
        if (endPage - startPage < maxButtons - 1) {
            startPage = Math.max(1, endPage - maxButtons + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = 'pagination-btn' + (i === this.currentPage ? ' active' : '');
            pageBtn.textContent = i;
            pageBtn.onclick = () => this.goToPage(i);
            this.paginationContainer.appendChild(pageBtn);
        }
        
        // Botón siguiente
        const nextBtn = document.createElement('button');
        nextBtn.className = 'pagination-btn';
        nextBtn.textContent = 'Siguiente →';
        nextBtn.disabled = this.currentPage === totalPages;
        nextBtn.onclick = () => this.goToPage(this.currentPage + 1);
        this.paginationContainer.appendChild(nextBtn);
        
        // Información
        const info = document.createElement('div');
        info.className = 'pagination-info';
        const start = (this.currentPage - 1) * this.rowsPerPage + 1;
        const end = Math.min(this.currentPage * this.rowsPerPage, totalRows);
        info.textContent = `Mostrando ${start}-${end} de ${totalRows} registros`;
        this.paginationContainer.appendChild(info);
    }
    
    // Ir a página
    goToPage(page) {
        this.currentPage = page;
        this.render();
    }
    
    // Aplicar filtro
    applyFilter(columnIndex, value) {
        if (value === '') {
            delete this.filters[columnIndex];
        } else {
            this.filters[columnIndex] = value;
        }
        this.currentPage = 1;
        this.render();
    }
    
    // Limpiar filtros
    clearFilters() {
        this.filters = {};
        this.currentPage = 1;
        this.render();
    }
    
    // Cambiar filas por página
    setRowsPerPage(rows) {
        this.rowsPerPage = rows;
        this.currentPage = 1;
        this.render();
    }
}

// Modo Oscuro
class DarkMode {
    constructor() {
        this.theme = localStorage.getItem('theme') || 'light';
        this.init();
    }
    
    init() {
        this.applyTheme();
        this.createToggle();
    }
    
    applyTheme() {
        document.documentElement.setAttribute('data-theme', this.theme);
    }
    
    createToggle() {
        const toggle = document.getElementById('theme-toggle');
        if (!toggle) return;
        
        toggle.classList.toggle('active', this.theme === 'dark');
        
        toggle.addEventListener('click', () => {
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            localStorage.setItem('theme', this.theme);
            this.applyTheme();
            toggle.classList.toggle('active', this.theme === 'dark');
        });
    }
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar modo oscuro
    new DarkMode();
    
    // Inicializar tablas con paginación
    const tables = document.querySelectorAll('[data-table-utils]');
    tables.forEach(table => {
        new TableUtils(table.id, {
            rowsPerPage: parseInt(table.dataset.rowsPerPage) || 10
        });
    });
});

// Exportar para uso global
window.TableUtils = TableUtils;
window.DarkMode = DarkMode;
