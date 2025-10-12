            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Simple DataTables -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
    
    <!-- Custom JS -->
    <script src="/assets/js/app.js"></script>
    
    <!-- Initialize DataTables -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tables = document.querySelectorAll('.datatable');
        tables.forEach(table => {
            if (table && !table.classList.contains('dataTable-table')) {
                new simpleDatatables.DataTable(table, {
                    searchable: true,
                    fixedHeight: false,
                    perPage: 10,
                    perPageSelect: [5, 10, 20, 50],
                    labels: {
                        placeholder: "Buscar...",
                        perPage: "registros por página",
                        noRows: "No se encontraron registros",
                        info: "Mostrando {start} a {end} de {rows} registros",
                    }
                });
            }
        });
    });
    </script>
    
    <?php if (isset($additionalScripts)): ?>
        <?= $additionalScripts ?>
    <?php endif; ?>
</body>
</html>
