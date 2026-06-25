<?php
/*
 * Encabezado HTML común a todas las páginas de ARCANUM.
 * Incluye barra de estado táctica, topbar de navegación y mensajes flash.
 */

$user = Auth::user();

$scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
$canonical = htmlspecialchars(
    $scheme . '://' . $host . strtok($_SERVER['REQUEST_URI'] ?? '/', '?'),
    ENT_QUOTES, 'UTF-8'
);

$pageTitle   = "Arcanum";
$description = e('Sistema cifrado de gestión de expedientes clasificados. Acceso restringido a personal autorizado.');
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= $description ?>">
    <meta name="author"  content="C3r0d4y">
    <meta name="robots"  content="noindex, nofollow">
    <link rel="canonical" href="<?= $canonical ?>">
    <meta name="theme-color" content="#06090f">
    <link rel="icon" type="image/png" href="<?= e(url('assets/img/favicon.png')) ?>">
    <link rel="apple-touch-icon" href="<?= e(url('assets/img/icon.png')) ?>">
    <link rel="preload" as="image" href="<?= e(url('assets/img/logo.webp')) ?>" type="image/webp">
    <link rel="stylesheet" href="<?= e(url('assets/css/app.min.css')) ?>">
</head>
<body>

<!-- Barra de estado táctica: indicadores del sistema y reloj -->
<div class="statusbar">
    <div class="statusbar-left">
        <span class="status-pill blue"><span class="dot"></span>CLASIFICADO</span>
        <span class="status-pill gold"><span class="dot"></span>CIFRADO AES-256</span>
        <span class="status-pill">ACCESO RESTRINGIDO</span>
        <button id="btn-diagram" class="status-pill" style="cursor:pointer;background:none;font:inherit;color:inherit">DIAGRAMA DE CIFRADO</button>
    </div>
    <div class="statusbar-right">
        <?php if ($user): ?>
            <span style="font-size:10px;color:var(--muted);">OP: <strong style="color:var(--accent)"><?= e($user['name']) ?></strong></span>
        <?php endif; ?>
        <span style="color:var(--gold);font-weight:700;letter-spacing:.14em">ARCANUM</span>
        <span class="clock" id="clk">--:--:--</span>
    </div>
</div>

<!-- Barra de navegación principal -->
<header class="topbar-outer">
<div class="topbar">
    <a class="brand" href="<?= e(url('documents')) ?>"><span>ARC</span>ANUM</a>

    <?php if ($user): ?>
        <nav class="nav" aria-label="Navegación principal">
            <a class="nav-link" href="<?= e(url('documents')) ?>">
                <svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 2h8l5 5v15H6z"/><path d="M14 2v6h5"/><path d="M9 13h6"/><path d="M9 17h6"/>
                </svg>
                <span>Expedientes</span>
            </a>
            <?php if ($user['role'] === 'admin'): ?>
                <a class="nav-link" href="<?= e(url('users')) ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>Operadores</span>
                </a>
                <a class="nav-link" href="<?= e(url('logs')) ?>">
                    <svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M3 3v18h18"/><path d="M7 14l3-3 4 4 5-7"/>
                    </svg>
                    <span>Bitácora</span>
                </a>
            <?php endif; ?>
        </nav>

        <div class="user-menu">
            <div class="user-summary">
                <svg class="user-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 21a8 8 0 0 1 16 0"/>
                </svg>
                <span class="user-name"><?= e($user['name']) ?></span>
                <span class="user-role"><?= e($user['role']) ?></span>
            </div>
            <a class="logout-link" href="<?= e(url('logout')) ?>" title="Cerrar sesión" aria-label="Cerrar sesión">
                <svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>
                </svg>
                <span>Salir</span>
            </a>
        </div>
    <?php endif; ?>

    <a class="brand-personal" href="/" aria-label="C3r0D4y Cyber Defense">
        <span><strong>C3r0D4y</strong><small>Cyber Defense</small></span>
        <span class="brand-logo-img">
            <picture>
                <source srcset="<?= e(url('assets/img/logo.webp')) ?>" type="image/webp">
                <img src="<?= e(url('assets/img/logo.jpeg')) ?>" alt="C3r0D4y" width="160" height="160" fetchpriority="high">
            </picture>
        </span>
    </a>
</div>
</header>

<!-- Contenido principal -->
<main class="container">
    <?php foreach ($_SESSION['flash'] ?? [] as $type => $message): ?>
        <div class="alert alert-<?= e($type) ?>"><?= e($message) ?></div>
    <?php endforeach; unset($_SESSION['flash']); ?>
