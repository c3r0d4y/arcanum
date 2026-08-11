<?php

/*
 * Archivo: app/core/Controller.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Define la clase base Controller de la que heredan todos los
 * controladores de la aplicación. Por ahora su única función es
 * cargar y mostrar vistas (las páginas HTML que ve el usuario).
 * Al usar herencia, cualquier controlador que extienda esta clase
 * tendrá automáticamente el método view() disponible.
 */

declare(strict_types=1);

class Controller
{
    /**
     * Carga y muestra una vista al navegador.
     *
     * El proceso es siempre el mismo:
     *   1. Incluye el encabezado HTML (layouts/header.php)
     *   2. Incluye la vista solicitada (p.ej. documents/index.php)
     *   3. Incluye el pie de página HTML (layouts/footer.php)
     *
     * Los datos del arreglo $data se convierten en variables locales
     * para que la vista pueda usarlos directamente.
     * Por ejemplo: ['title' => 'Documentos'] permite usar $title en la vista.
     *
     * Al terminar, se limpia la variable de sesión 'old' que guarda
     * los valores anteriores del formulario en caso de error.
     *
     * @param string $view Ruta relativa a la vista, sin extensión .php
     *                     (p.ej. 'documents/index', 'auth/login')
     * @param array  $data Variables que se pasarán a la vista
     */
    public function view(string $view, array $data = []): void
    {
        // Convierte el arreglo $data en variables individuales
        // EXTR_SKIP evita sobreescribir variables ya existentes
        extract($data, EXTR_SKIP);

        // Incluye el encabezado HTML común a todas las páginas
        require APP_ROOT . '/app/views/layouts/header.php';

        // Incluye la vista específica que se solicitó
        require APP_ROOT . "/app/views/{$view}.php";

        // Incluye el pie de página HTML común a todas las páginas
        require APP_ROOT . '/app/views/layouts/footer.php';

        // Limpia los datos del formulario anterior para que no
        // aparezcan en el próximo formulario que se muestre
        unset($_SESSION['old']);
    }
}
