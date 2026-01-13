/**
 * SIPAN - Application Logic
 * Handles Sidebar toggle, persistence, and mobile interactions.
 */

document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("appSidebar");
    const toggleBtn = document.getElementById("sidebarToggle");
    const toggleHeaderBtn = document.getElementById("sidebarToggleHeader");
    const mobileToggleBtn = document.getElementById("sidebarToggleMobile");
    const overlay = document.getElementById("sidebarOverlay");
    const mainContent = document.querySelector(".main-content");

    // 1. Persistence Logic
    const isExpanded = localStorage.getItem("sidebar-state") === "expanded";
    if (isExpanded) {
        sidebar.classList.add("expanded");
    }

    // 2. Toggle Handler (Desktop & Mobile)
    function toggleSidebar() {
        if (!sidebar) return;

        const isNowExpanded = sidebar.classList.toggle("expanded");
        // Add manual 'active' class as backup for some CSS selectors
        sidebar.classList.toggle("active", isNowExpanded);

        localStorage.setItem(
            "sidebar-state",
            isNowExpanded ? "expanded" : "collapsed",
        );

        console.log("[SIPAN] Sidebar toggled. Expanded:", isNowExpanded);

        // Toggle overlay in mobile
        if (window.innerWidth <= 991) {
            if (overlay) {
                overlay.classList.toggle("active", isNowExpanded);
                console.log(
                    "[SIPAN] Mobile Overlay:",
                    isNowExpanded ? "ON" : "OFF",
                );
            }
            // Lock body scroll ONLY if menu is open on mobile
            document.body.style.overflow = isNowExpanded ? "hidden" : "";
        }

        window.dispatchEvent(new Event("resize"));
    }

    if (toggleBtn) {
        toggleBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (toggleHeaderBtn) {
        toggleHeaderBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (mobileToggleBtn) {
        mobileToggleBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (overlay) {
        overlay.addEventListener("click", () => {
            if (sidebar.classList.contains("expanded")) {
                toggleSidebar();
            }
        });
    }

    // 3. Close on Outside Click (Mobile mainly)
    document.addEventListener("click", (e) => {
        if (window.innerWidth <= 991) {
            if (
                sidebar.classList.contains("expanded") &&
                !sidebar.contains(e.target) &&
                !toggleHeaderBtn.contains(e.target) &&
                !mobileToggleBtn.contains(e.target)
            ) {
                sidebar.classList.remove("expanded");
                if (overlay) overlay.classList.remove("active");
                document.body.style.overflow = "";
            }
        }
    });

    // 4. Group Tooltip Logic for Collapsed State (Accessibility)
    const navGroups = document.querySelectorAll(".nav-group");
    navGroups.forEach((group) => {
        const header = group.querySelector(".nav-group-header");
        if (header) {
            // Ensure aria-expanded aligns with sidebar state could be complex,
            // but we can add title/aria-label dynamically if needed.
            // Currently CSS handles visibility.
        }
    });
    // 5. Global Helpers (SIPAN Object)
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
    };

    /* End of app.js */
});
