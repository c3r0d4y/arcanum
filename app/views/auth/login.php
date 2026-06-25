<?php
/*
 * Vista de inicio de sesión — ARCANUM
 * Sin credenciales expuestas. Solo acceso por credenciales autorizadas.
 */
?>
<div class="login-outer">
    <div class="login-card">
        <div class="login-card-head">
            <div class="badge-sys">
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2 4 6v5c0 5.5 3.3 9.7 8 11.5 4.7-1.8 8-6 8-11.5V6Z"/></svg>
                ARCANUM &nbsp;/&nbsp; Acceso Clasificado &nbsp;/&nbsp; C3r0d4y
            </div>
            <h1>Ingreso <span>Seguro</span></h1>
            <p>Autenticación requerida. Actividad monitoreada y registrada.</p>
        </div>

        <div class="login-card-body">
            <form method="post" action="<?= e(url('login')) ?>" autocomplete="off">
                <?= Csrf::field() ?>

                <label>Identificador de acceso
                    <input type="email"
                           name="email"
                           value="<?= e(old('email', 'admin@c3r0d4y.com')) ?>"
                           required
                           autofocus
                           autocomplete="username">
                </label>

                <label>Clave de acceso
                    <input type="password"
                           name="password"
                           value="Admin123!"
                           required
                           autocomplete="current-password">
                </label>

                <button class="btn primary" type="submit">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Autenticar
                </button>
            </form>
        </div>
    </div>
</div>
