/**
 * SIPAN - Application Logic
 * Handles Sidebar toggle, persistence, and mobile interactions.
 */

// Global Helpers (SIPAN Object) - Defined outside to be available immediately
window.SIPAN = {
    // Requires SweetAlert2 (Swal) to be loaded globally
    success: (message) => {
        if (typeof Swal !== "undefined") {
            Swal.fire({
                icon: "success",
                title: "¡Éxito!",
                text: message,
                confirmButtonColor: "#28a745",
                timer: 2000,
                timerProgressBar: true,
            });
        } else {
            console.log("SUCCESS:", message);
            alert(message);
        }
    },
    error: (message) => {
        if (typeof Swal !== "undefined") {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: message,
                confirmButtonColor: "#dc3545",
            });
        } else {
            console.error("ERROR:", message);
            alert("Error: " + message);
        }
    },
    warning: (message) => {
        if (typeof Swal !== "undefined") {
            Swal.fire({
                icon: "warning",
                title: "Atención",
                text: message,
                confirmButtonColor: "#ffc107",
                confirmButtonText: "Entendido",
            });
        } else {
            console.warn("WARNING:", message);
            alert("Aviso: " + message);
        }
    },
    confirm: (title, text, callback, confirmText = "Sí, continuar") => {
        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: title,
                text: text,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: confirmText,
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        } else {
            if (confirm(title + "\n" + text)) {
                callback();
            }
        }
    },
    formatPhone: (value) => {
        if (!value) return "";
        let digits = value.replace(/\D/g, "");
        if (digits.length > 4) {
            return digits.slice(0, 4) + "-" + digits.slice(4, 11);
        }
        if (digits.length === 4) {
            return digits + "-";
        }
        return digits;
    },
    formatDNI: (value) => {
        if (!value) return "";
        let firstChar = value.charAt(0).toUpperCase();
        if (!["V", "E", "J", "G"].includes(firstChar)) {
            return "";
        }
        let digits = value.slice(1).replace(/\D/g, "");
        // Siempre incluir guion tras la letra, incluso si no hay dígitos aún
        return firstChar + "-" + digits.slice(0, 9);
    }
};

document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("appSidebar");
    const toggleBtn = document.getElementById("sidebarToggle");
    const toggleHeaderBtn = document.getElementById("sidebarToggleHeader");
    const mobileToggleBtn = document.getElementById("sidebarToggleMobile");
    const overlay = document.getElementById("sidebarOverlay");

    if (sidebar) {
        const isExpanded = localStorage.getItem("sidebar-state") === "expanded";
        if (isExpanded) {
            sidebar.classList.add("expanded");
        }
    }

    function toggleSidebar() {
        if (!sidebar) return;
        const isNowExpanded = sidebar.classList.toggle("expanded");
        sidebar.classList.toggle("active", isNowExpanded);
        localStorage.setItem("sidebar-state", isNowExpanded ? "expanded" : "collapsed");
        if (window.innerWidth <= 991) {
            if (overlay) overlay.classList.toggle("active", isNowExpanded);
            document.body.style.overflow = isNowExpanded ? "hidden" : "";
        }
        window.dispatchEvent(new Event("resize"));
    }

    if (toggleBtn) toggleBtn.addEventListener("click", (e) => { e.stopPropagation(); toggleSidebar(); });
    if (toggleHeaderBtn) toggleHeaderBtn.addEventListener("click", (e) => { e.stopPropagation(); toggleSidebar(); });
    if (mobileToggleBtn) mobileToggleBtn.addEventListener("click", (e) => { e.stopPropagation(); toggleSidebar(); });
    if (overlay) overlay.addEventListener("click", () => { if (sidebar.classList.contains("expanded")) toggleSidebar(); });

    document.addEventListener("click", (e) => {
        if (sidebar && window.innerWidth <= 991) {
            if (sidebar.classList.contains("expanded") && !sidebar.contains(e.target) && 
                (!toggleHeaderBtn || !toggleHeaderBtn.contains(e.target)) && 
                (!mobileToggleBtn || !mobileToggleBtn.contains(e.target))) {
                sidebar.classList.remove("expanded");
                if (overlay) overlay.classList.remove("active");
                document.body.style.overflow = "";
            }
        }
    });
});
