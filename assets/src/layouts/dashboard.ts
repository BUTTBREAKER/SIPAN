import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap-icons/font/bootstrap-icons.min.css";

import "bootstrap/dist/js/bootstrap.bundle.min";
import { Tooltip } from "bootstrap";
import "alpinejs/dist/cdn.min";

for (const $node of document.querySelectorAll('[data-bs-toggle="tooltip"]')) {
  new Tooltip($node);
}
