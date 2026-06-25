<?php
/*
 * Archivo: public/sitemap.php — sitemap.xml dinamico
 * Autor: C3r0d4y
 */
require_once __DIR__ . '/../config/config.php';
require_once APP_ROOT . '/app/core/helpers.php';

header('Content-Type: application/xml; charset=utf-8');

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base   = rtrim(BASE_URL, '/');
$today  = date('Y-m-d');

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
echo "  <url>\n";
echo "    <loc>" . htmlspecialchars("{$scheme}://{$host}{$base}/login", ENT_XML1) . "</loc>\n";
echo "    <lastmod>{$today}</lastmod>\n";
echo "    <changefreq>monthly</changefreq>\n";
echo "    <priority>0.5</priority>\n";
echo "  </url>\n";
echo "</urlset>\n";
