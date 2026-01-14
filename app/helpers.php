<?php

/** Función auxiliar para extraer parámetros de la ruta */
function matchRoute($pattern, $path)
{
    $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_-]+)', $pattern);
    $pattern = '#^' . $pattern . '$#';

    if (preg_match($pattern, $path, $matches)) {
        array_shift($matches);
        return $matches;
    }

    return false;
}
