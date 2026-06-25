<?php
/*
 * Archivo: public/robots.php — robots.txt dinamico
 * Autor: C3r0d4y
 *
 * Genera el robots.txt indicando que el sitio requiere autenticacion,
 * por lo que se bloquea la indexacion del contenido privado.
 */
header('Content-Type: text/plain; charset=utf-8');

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base   = rtrim(BASE_URL, '/');

echo "User-agent: *\n";
// El contenido requiere autenticacion; los robots no pueden verlo
echo "Disallow: {$base}/\n\n";
echo "Sitemap: {$scheme}://{$host}{$base}/sitemap.xml\n";
