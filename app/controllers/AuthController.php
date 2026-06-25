<?php

/*
 * Archivo: app/controllers/AuthController.php
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Controla el proceso de autenticación del usuario:
 *  - Mostrar el formulario de ingreso (login)
 *  - Procesar el formulario y verificar credenciales
 *  - Cerrar la sesión del usuario (logout)
 *
 * Usa la clase User para buscar al usuario en la base de datos,
 * la clase Auth para gestionar la sesión, y Logger para registrar
 * los intentos de inicio y cierre de sesión en la bitácora.
 */

declare(strict_types=1);

final class AuthController extends Controller
{
    /**
     * Muestra la página de inicio de sesión.
     *
     * Si el usuario ya tiene sesión activa, lo redirige directamente
     * a la lista de documentos para que no vea el login innecesariamente.
     */
    public function showLogin(): void
    {
        // Si ya está autenticado, lo mandamos a documentos
        if (Auth::check()) {
            redirect('documents');
        }

        // Carga la vista del formulario de login
        $this->view('auth/login', ['title' => 'Ingresar']);
    }

    /**
     * Procesa el formulario de inicio de sesión (petición POST).
     *
     * Pasos que sigue:
     *  1. Verifica el token CSRF para proteger contra ataques externos.
     *  2. Lee el correo y contraseña enviados por el formulario.
     *  3. Busca al usuario activo con ese correo en la base de datos.
     *  4. Verifica que la contraseña coincida con el hash guardado.
     *  5. Si todo es correcto, inicia la sesión y redirige a documentos.
     *  6. Si algo falla, registra el intento fallido y vuelve al login.
     */
    public function login(): void
    {
        // Protección CSRF: verifica que el formulario sea legítimo
        Csrf::verify();

        // Lee y limpia los datos enviados por el formulario
        $email    = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        // Guarda el correo en sesión para rellenarlo si hay error
        $_SESSION['old'] = ['email' => $email];

        // Busca al usuario activo con ese correo
        $user = (new User())->findByEmail($email);

        // Verifica que el usuario exista y que la contraseña sea correcta
        // password_verify() compara el texto con el hash guardado de forma segura
        if (!$user || !password_verify($password, $user['password_hash'])) {
            flash('error', 'Credenciales incorrectas.');
            // Registra el intento fallido en la bitácora (sin usuario autenticado)
            Logger::write('login_failed', "Intento fallido para {$email}", null);
            redirect('login');
        }

        // Credenciales correctas: inicia la sesión y registra el evento
        Auth::login($user);
        Logger::write('login', 'Inicio de sesion correcto.');
        redirect('documents');
    }

    /**
     * Cierra la sesión del usuario actual.
     *
     * Si hay un usuario autenticado, registra el cierre en la bitácora
     * antes de destruir la sesión. Luego redirige al login.
     */
    public function logout(): void
    {
        // Solo registramos si hay alguien con sesión activa
        if (Auth::check()) {
            Logger::write('logout', 'Cierre de sesion.');
        }

        // Destruye la sesión y elimina la cookie del navegador
        Auth::logout();
        redirect('login');
    }
}
