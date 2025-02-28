import "../styles/landing.scss";

import "@fortawesome/fontawesome-free/js/all.min.js";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import { Tooltip } from "bootstrap";

for (const node of document.querySelectorAll('[data-bs-toggle="tooltip"]')) {
  new Tooltip(node);
}
