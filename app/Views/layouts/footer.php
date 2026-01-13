            </div>
        </div>
    </div>
    
    <!-- Floating Action Button for Tour -->
    <div class="fab-help-container">
        <span class="fab-tooltip">Ayuda / Tour: <?= ucfirst($currentPage ?? 'Panel') ?></span>
        <button class="fab-help" onclick="startContextualTour('<?= $currentPage ?? 'dashboard' ?>')" aria-label="Iniciar Tour">
            <i class="fas fa-question"></i>
        </button>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Simple DataTables (JS only, CSS handled by local tables.css) -->
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JS -->
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/tables.js"></script>
    
    <?php if (isset($additionalScripts)): ?>
        <?= $additionalScripts ?>
    <?php endif; ?>
</body>
</html>
