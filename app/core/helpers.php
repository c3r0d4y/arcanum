<?php

/*
 * Archivo: app/core/helpers.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Define funciones de utilidad (helpers) disponibles en toda la aplicación.
 * Son funciones pequeñas que se usan frecuentemente en controladores,
 * modelos y vistas para tareas comunes como generar URLs, escapar texto
 * para HTML, redirigir al usuario y manejar mensajes temporales.
 */

declare(strict_types=1);

/**
 * Genera una URL completa a partir de una ruta relativa.
 *
 * Antepone la BASE_URL configurada para que los enlaces funcionen
 * sin importar en qué carpeta esté instalada la aplicación.
 *
 * Ejemplo: url('documents/create') => '/archivo/documents/create'
 *
 * @param string $path Ruta relativa dentro de la aplicación
 * @return string URL completa lista para usar en href= o redirect()
 */
function url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Escapa texto para mostrarlo de forma segura en HTML.
 *
 * Convierte caracteres especiales (<, >, ", &, ') en sus entidades HTML.
 * Esto previene ataques XSS (Cross-Site Scripting), donde un atacante
 * podría inyectar código HTML o JavaScript malicioso en la página.
 *
 * SIEMPRE usa esta función antes de imprimir datos del usuario en HTML.
 *
 * Ejemplo: e('<script>alert(1)</script>') => '&lt;script&gt;alert(1)&lt;/script&gt;'
 *
 * @param string|null $value El texto a escapar (acepta null y lo trata como '')
 * @return string Texto seguro para mostrar en HTML
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirige al usuario a otra página y detiene la ejecución.
 *
 * Envía el encabezado HTTP Location con la URL generada por url()
 * y termina el script para que no se ejecute nada más después.
 *
 * Ejemplo: redirect('login') redirige a /archivo/login
 *
 * @param string $path Ruta relativa a donde redirigir
 * @return never Esta función nunca regresa (detiene la ejecución)
 */
function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

/**
 * Guarda un mensaje temporal en la sesión para mostrarlo en la siguiente página.
 *
 * Los mensajes flash se muestran una sola vez (en el header.php) y
 * luego se eliminan automáticamente. Se usan para avisos después
 * de guardar, actualizar o eliminar registros.
 *
 * Ejemplo: flash('success', 'Documento guardado.') muestra un cuadro verde.
 *          flash('error', 'Error al guardar.') muestra un cuadro rojo.
 *
 * @param string $type    Tipo de alerta: 'success' (verde) o 'error' (rojo)
 * @param string $message Texto del mensaje a mostrar
 */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

/**
 * Recupera el valor anterior de un campo de formulario.
 *
 * Cuando un formulario tiene errores y se recarga, esta función
 * devuelve el valor que el usuario ya había escrito para no
 * obligarlo a escribirlo de nuevo.
 *
 * Los datos se guardan en $_SESSION['old'] antes de redirigir al formulario.
 *
 * Ejemplo: old('email') devuelve el correo que se escribió antes del error.
 *
 * @param string $key     Nombre del campo del formulario
 * @param mixed  $default Valor por defecto si no hay dato previo
 * @return mixed El valor anterior o el valor por defecto
 */
function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['old'][$key] ?? $default;
}
