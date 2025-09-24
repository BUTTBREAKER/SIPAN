import "@fontsource/poppins/latin-400.css";
import "@fontsource/poppins/latin-700.css";
import "@fontsource/volkhov/latin-700.css";
import "@fortawesome/fontawesome-free/js/all.min";
import "bootstrap/dist/css/bootstrap.min.css";
import "../styles/landing.css";

import "bootstrap/dist/js/bootstrap.bundle.min";
import { Tooltip } from "bootstrap";
import "alpinejs/dist/cdn.min";

for (const $node of document.querySelectorAll('[data-bs-toggle="tooltip"]')) {
  new Tooltip($node);
}
