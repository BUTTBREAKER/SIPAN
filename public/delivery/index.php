<?php

// Proxy de emergencia para la App Delivery
// Redirigir al index.php principal manteniendo la URI
$_SERVER['REQUEST_URI'] = str_replace('/delivery/index.php', '/delivery', $_SERVER['REQUEST_URI']);
require __DIR__ . '/../index.php';
