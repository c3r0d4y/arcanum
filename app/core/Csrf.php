<?php

/*
 * Archivo: app/core/Csrf.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Implementa protección contra ataques CSRF (Cross-Site Request Forgery).
 * Un ataque CSRF ocurre cuando una página maliciosa engaña al navegador
 * del usuario para que envíe peticiones no autorizadas a este sistema.
 *
 * La solución es incluir un "token secreto" en cada formulario.
 * Ese token solo lo conoce este servidor, así que si una petición
 * llega sin el token correcto, se rechaza de inmediato.
 */

declare(strict_types=1);

final class Csrf
{
    /**
     * Obtiene (o crea) el token CSRF de la sesión actual.
     *
     * El token es una cadena aleatoria de 64 caracteres hexadecimales.
     * Se genera una sola vez por sesión y se guarda en $_SESSION.
     * Las siguientes llamadas devuelven el mismo token ya guardado.
     *
     * @return string El token CSRF de 64 caracteres
     */
    public static function token(): string
    {
        // Si todavía no existe el token en esta sesión, se genera uno nuevo
        if (empty($_SESSION['csrf_token'])) {
            // random_bytes(32) genera 32 bytes aleatorios seguros
            // bin2hex los convierte a 64 caracteres hexadecimales
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Genera el campo oculto HTML para incluir en los formularios.
     *
     * Al agregar <?= Csrf::field() ?> dentro de un <form>,
     * se inserta automáticamente:
     * <input type="hidden" name="_token" value="abc123...">
     *
     * @return string El HTML del campo oculto con el token
     */
    public static function field(): string
    {
        // e() escapa el valor para evitar XSS al imprimirlo en HTML
        return '<input type="hidden" name="_token" value="' . e(self::token()) . '">';
    }

    /**
     * Verifica que el token enviado con el formulario sea válido.
     *
     * Se llama al inicio de cualquier acción POST para asegurarse
     * de que la petición proviene del propio sistema y no de un
     * sitio externo. Si el token no coincide, detiene la ejecución
     * con un error HTTP 419.
     *
     * hash_equals() hace la comparación de forma segura para evitar
     * ataques de temporización (timing attacks).
     */
    public static function verify(): void
    {
        $token = $_POST['_token'] ?? '';

        // Se verifica que sea string y que coincida con el token de la sesión
        if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(419); // 419 = Token expirado / inválido
            exit('Token CSRF invalido.');
        }
    }
}
