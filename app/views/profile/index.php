<?php
/*
 * Vista: perfil personal del operador
 * Modo configuración inicial: muestra ambas tarjetas (contraseña y PIN) en paralelo.
 * La tarjeta de PIN permanece bloqueada hasta que se completa el Paso 1 (contraseña).
 */
$hasPin        = !empty(Auth::user()['has_pin']);
$hasDefaultPin = Auth::user()['has_default_pin'] ?? false;
$mustPwd       = (Auth::user()['must_change_password'] ?? 0) === 1;
$avatarUrl     = !empty($userRecord['avatar'])
    ? url('users/' . $userRecord['id'] . '/avatar') . '?v=' . time()
    : null;

/* Determina en qué etapa de la configuración inicial está el usuario */
$isStep1 = $mustPwd;                                   // Paso 1: cambiar contraseña
$isStep2 = !$mustPwd && ($hasDefaultPin || !$hasPin);  // Paso 2: configurar PIN
$isSetup = $isStep1 || $isStep2;                       // ¿Está en modo configuración?
?>

<?php if ($isSetup): ?>
<!-- ══════════════════════════════════════════════════════
     MODO CONFIGURACIÓN INICIAL OBLIGATORIA
     Ambas tarjetas visibles; PIN bloqueado en el Paso 1.
     ══════════════════════════════════════════════════════ -->

<style>
/* Dos columnas de configuración: se apilan en pantallas pequeñas */
.setup-cols {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px;
    align-items: start;
}
@media (max-width: 720px) {
    .setup-cols { grid-template-columns: 1fr; }
}
</style>

<!-- ── Cabecera del asistente ── -->
<div style="margin-bottom:26px;border:1px solid rgba(74,130,190,.28);
            background:rgba(4,8,14,.85)">

    <!-- Título -->
    <div style="display:flex;align-items:center;gap:10px;padding:13px 18px;
                border-bottom:1px solid rgba(74,130,190,.18);background:rgba(0,0,0,.38)">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#4a82be"
             stroke-width="2.5" stroke-linecap="round" style="flex-shrink:0">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <div>
            <span style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.22em;
                         color:#4a82be;text-transform:uppercase">
                Configuración Inicial — Acceso Clasificado ARCANUM
            </span>
            <span style="display:block;font:11px/1.5 ui-monospace,monospace;
                         color:rgba(74,130,190,.42);margin-top:3px">
                Completa los dos pasos obligatorios antes de operar en el sistema.
            </span>
        </div>
    </div>

    <!-- Barra de pasos -->
    <div style="display:flex;align-items:stretch">

        <!-- Paso 1 -->
        <div style="flex:1;display:flex;align-items:center;gap:12px;padding:13px 18px;
                    background:<?= $isStep1 ? 'rgba(74,130,190,.07)' : 'rgba(74,222,128,.04)' ?>;
                    border-right:1px solid rgba(74,130,190,.14)">
            <?php if ($isStep1): ?>
                <div style="flex-shrink:0;width:26px;height:26px;border-radius:50%;
                            background:rgba(74,130,190,.18);border:2px solid #4a82be;
                            display:flex;align-items:center;justify-content:center;
                            font:700 11px/1 ui-monospace,monospace;color:#4a82be">1</div>
            <?php else: ?>
                <div style="flex-shrink:0;width:26px;height:26px;border-radius:50%;
                            background:rgba(74,222,128,.12);border:2px solid rgba(74,222,128,.55);
                            display:flex;align-items:center;justify-content:center">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                         stroke="#4ade80" stroke-width="3" stroke-linecap="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
            <?php endif; ?>
            <div>
                <div style="font:700 9px/1 ui-monospace,monospace;letter-spacing:.16em;
                            text-transform:uppercase;
                            color:<?= $isStep1 ? '#4a82be' : '#4ade80' ?>">
                    <?= $isStep1 ? 'EN CURSO' : 'COMPLETADO' ?>
                </div>
                <div style="font:600 11px/1.4 ui-monospace,monospace;margin-top:3px;
                            color:<?= $isStep1 ? '#a8c4e0' : 'rgba(74,222,128,.55)' ?>">
                    Paso 1 — Contraseña de acceso
                </div>
            </div>
        </div>

        <!-- Separador con flecha -->
        <div style="display:flex;align-items:center;padding:0 10px;
                    background:rgba(0,0,0,.22);
                    color:rgba(74,130,190,.28);
                    font:600 16px/1 ui-monospace,monospace">›</div>

        <!-- Paso 2 -->
        <div style="flex:1;display:flex;align-items:center;gap:12px;padding:13px 18px;
                    background:<?= $isStep2 ? 'rgba(74,130,190,.07)' : 'rgba(0,0,0,.18)' ?>">
            <?php if ($isStep2): ?>
                <div style="flex-shrink:0;width:26px;height:26px;border-radius:50%;
                            background:rgba(74,130,190,.18);border:2px solid #4a82be;
                            display:flex;align-items:center;justify-content:center;
                            font:700 11px/1 ui-monospace,monospace;color:#4a82be">2</div>
            <?php else: ?>
                <div style="flex-shrink:0;width:26px;height:26px;border-radius:50%;
                            background:rgba(30,50,70,.35);border:2px solid rgba(60,90,120,.22);
                            display:flex;align-items:center;justify-content:center;
                            font:700 11px/1 ui-monospace,monospace;
                            color:rgba(74,130,190,.25)">2</div>
            <?php endif; ?>
            <div>
                <div style="font:700 9px/1 ui-monospace,monospace;letter-spacing:.16em;
                            text-transform:uppercase;
                            color:<?= $isStep2 ? '#4a82be' : 'rgba(74,130,190,.22)' ?>">
                    <?= $isStep2 ? 'EN CURSO' : 'PENDIENTE' ?>
                </div>
                <div style="font:600 11px/1.4 ui-monospace,monospace;margin-top:3px;
                            color:<?= $isStep2 ? '#a8c4e0' : 'rgba(74,130,190,.2)' ?>">
                    Paso 2 — PIN de seguridad
                </div>
            </div>
        </div>

    </div>
</div><!-- /cabecera asistente -->


<!-- ═══════════════════════════════════════════════════════
     DOS TARJETAS LADO A LADO
     ═══════════════════════════════════════════════════════ -->
<div class="setup-cols">

    <!-- ────────────────────────────────────────────────────
         TARJETA IZQUIERDA — Contraseña de acceso
         Activa en Paso 1, vista de "completado" en Paso 2.
         ──────────────────────────────────────────────────── -->
    <div class="profile-card"
         style="<?= $isStep1
             ? 'border-color:rgba(74,130,190,.45)'
             : 'border-color:rgba(74,222,128,.28);opacity:.52;pointer-events:none;user-select:none' ?>">

        <!-- Cabecera de la tarjeta -->
        <div class="profile-card-head"
             style="<?= $isStep1
                 ? 'background:rgba(74,130,190,.07);border-color:rgba(74,130,190,.25)'
                 : 'background:rgba(74,222,128,.04);border-color:rgba(74,222,128,.18)' ?>">
            <div style="display:flex;align-items:center;gap:8px">
                <?php if ($isStep1): ?>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                         stroke="#4a82be" stroke-width="2.5" stroke-linecap="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <strong style="color:#a8c4e0">Contraseña de acceso</strong>
                <?php else: ?>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                         stroke="rgba(74,222,128,.6)" stroke-width="2.5" stroke-linecap="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <strong style="color:rgba(74,222,128,.65)">Contraseña de acceso</strong>
                <?php endif; ?>
            </div>
            <span class="badge" style="font-size:9px;
                  <?= $isStep1
                      ? 'background:rgba(74,130,190,.14);border-color:rgba(74,130,190,.5);color:#4a82be'
                      : 'background:rgba(74,222,128,.1);border-color:rgba(74,222,128,.35);color:#4ade80' ?>">
                <?= $isStep1 ? 'PASO 1 DE 2' : 'COMPLETADO' ?>
            </span>
        </div>

        <!-- Cuerpo de la tarjeta -->
        <div class="profile-card-body">
            <?php if ($isStep1): ?>
            <!-- ── Formulario de contraseña (Paso 1 activo) ── -->
            <p style="font:12px/1.7 ui-monospace,monospace;color:rgba(74,130,190,.5);margin:0 0 18px">
                Se te asignó una contraseña provisional al crear tu cuenta.<br>
                Establece una contraseña personal segura para continuar.
            </p>
            <form method="post" action="<?= e(url('profile/password')) ?>"
                  class="form-grid profile-form">
                <?= Csrf::field() ?>
                <label class="wide">Contraseña provisional
                    <input type="password" name="current_password" required
                           autocomplete="current-password"
                           placeholder="La contraseña que te asignaron">
                </label>
                <label>Nueva contraseña
                    <input type="password" name="new_password" required
                           minlength="8" autocomplete="new-password"
                           placeholder="Mínimo 8 caracteres">
                </label>
                <label>Confirmar contraseña
                    <input type="password" name="confirm_password" required
                           autocomplete="new-password"
                           placeholder="Repite la nueva contraseña">
                </label>
                <div class="wide profile-card-actions">
                    <button class="btn primary" type="submit"
                            style="width:100%;justify-content:center">
                        Establecer contraseña y continuar →
                    </button>
                </div>
            </form>

            <?php else: ?>
            <!-- ── Estado completado (Paso 2 activo) ── -->
            <div style="display:flex;flex-direction:column;align-items:center;
                        justify-content:center;gap:16px;padding:36px 22px;text-align:center">
                <div style="width:52px;height:52px;border-radius:50%;
                            background:rgba(74,222,128,.1);
                            border:2px solid rgba(74,222,128,.42);
                            display:flex;align-items:center;justify-content:center">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                         stroke="#4ade80" stroke-width="2.5" stroke-linecap="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <div>
                    <div style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.16em;
                                color:rgba(74,222,128,.65);text-transform:uppercase;margin-bottom:8px">
                        Contraseña configurada
                    </div>
                    <div style="font:11px/1.7 ui-monospace,monospace;
                                color:rgba(74,222,128,.35)">
                        Tu contraseña fue establecida<br>correctamente.
                    </div>
                </div>
                <div style="font:700 9px/1.6 ui-monospace,monospace;letter-spacing:.1em;
                            text-transform:uppercase;color:rgba(74,130,190,.4);
                            padding:6px 14px;border:1px solid rgba(74,130,190,.16);
                            background:rgba(74,130,190,.04)">
                    Configura tu PIN en el panel derecho →
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div><!-- /tarjeta contraseña -->


    <!-- ────────────────────────────────────────────────────
         TARJETA DERECHA — PIN de seguridad
         Bloqueada (overlay + opacidad) en Paso 1.
         Activa en Paso 2.
         ──────────────────────────────────────────────────── -->
    <div style="position:relative">

        <div class="profile-card"
             style="<?= $isStep2
                 ? 'border-color:rgba(74,130,190,.45)'
                 : 'border-color:rgba(60,90,120,.16);opacity:.36' ?>">

            <!-- Cabecera de la tarjeta -->
            <div class="profile-card-head"
                 style="<?= $isStep2
                     ? 'background:rgba(74,130,190,.07);border-color:rgba(74,130,190,.25)'
                     : 'background:rgba(16,28,48,.55);border-color:rgba(60,90,120,.14)' ?>">
                <div style="display:flex;align-items:center;gap:8px">
                    <?php if ($isStep2): ?>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                             stroke="#4a82be" stroke-width="2.5" stroke-linecap="round">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 9.9-1"/>
                        </svg>
                        <strong style="color:#a8c4e0">PIN de seguridad</strong>
                    <?php else: ?>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                             stroke="rgba(74,130,190,.28)" stroke-width="2.5" stroke-linecap="round">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <strong style="color:rgba(74,130,190,.26)">PIN de seguridad</strong>
                    <?php endif; ?>
                </div>
                <span class="badge" style="font-size:9px;
                      <?= $isStep2
                          ? 'background:rgba(74,130,190,.14);border-color:rgba(74,130,190,.5);color:#4a82be'
                          : 'background:rgba(30,50,80,.28);border-color:rgba(60,90,120,.18);color:rgba(74,130,190,.28)' ?>">
                    <?= $isStep2 ? 'PASO 2 DE 2' : 'PENDIENTE' ?>
                </span>
            </div>

            <!-- Cuerpo de la tarjeta -->
            <div class="profile-card-body">
                <?php if ($isStep2): ?>
                <!-- ── Formulario de PIN (Paso 2 activo) ── -->
                <p style="font:12px/1.7 ui-monospace,monospace;color:rgba(74,130,190,.5);margin:0 0 18px">
                    El sistema te asignó el PIN predeterminado
                    <code style="background:rgba(74,130,190,.12);padding:1px 6px;color:#6a9ac0">123456</code>.
                    Establece un PIN personal para habilitar todas las operaciones.
                </p>
                <form method="post" action="<?= e(url('profile/pin')) ?>"
                      class="form-grid profile-form">
                    <?= Csrf::field() ?>
                    <label class="wide">PIN actual (predeterminado del sistema)
                        <input type="password" name="current_pin" inputmode="numeric"
                               maxlength="8" required autocomplete="off"
                               placeholder="Ingresa 123456 para confirmar">
                    </label>
                    <label>Nuevo PIN
                        <input type="password" name="new_pin" inputmode="numeric"
                               maxlength="8" required autocomplete="new-password"
                               placeholder="4–8 dígitos numéricos">
                    </label>
                    <label>Confirmar PIN
                        <input type="password" name="confirm_pin" inputmode="numeric"
                               maxlength="8" required autocomplete="new-password"
                               placeholder="Repite tu nuevo PIN">
                    </label>
                    <div class="wide profile-card-actions">
                        <button class="btn primary" type="submit"
                                style="width:100%;justify-content:center">
                            Configurar PIN y acceder al sistema →
                        </button>
                    </div>
                </form>

                <?php else: ?>
                <!-- ── Vista previa bloqueada (Paso 1 aún no completado) ── -->
                <fieldset disabled aria-hidden="true"
                          style="border:none;padding:0;margin:0">
                    <div class="form-grid profile-form">
                        <label class="wide">PIN actual (predeterminado del sistema)
                            <input type="password" inputmode="numeric" maxlength="8"
                                   placeholder="Ingresa 123456 para confirmar"
                                   tabindex="-1" autocomplete="off">
                        </label>
                        <label>Nuevo PIN
                            <input type="password" inputmode="numeric" maxlength="8"
                                   placeholder="4–8 dígitos numéricos"
                                   tabindex="-1" autocomplete="new-password">
                        </label>
                        <label>Confirmar PIN
                            <input type="password" inputmode="numeric" maxlength="8"
                                   placeholder="Repite tu nuevo PIN"
                                   tabindex="-1" autocomplete="new-password">
                        </label>
                        <div class="wide profile-card-actions">
                            <button type="button" class="btn primary"
                                    style="width:100%;justify-content:center"
                                    tabindex="-1" disabled>
                                Configurar PIN y acceder al sistema →
                            </button>
                        </div>
                    </div>
                </fieldset>
                <?php endif; ?>
            </div>
        </div><!-- /profile-card PIN -->

        <!-- Candado superpuesto: solo visible cuando el PIN está pendiente (Paso 1 activo) -->
        <?php if ($isStep1): ?>
        <div style="position:absolute;inset:0;display:flex;align-items:center;
                    justify-content:center;pointer-events:none;z-index:2">
            <div style="display:flex;flex-direction:column;align-items:center;gap:12px;
                        padding:20px 28px;
                        background:rgba(4,9,18,.88);
                        border:1px solid rgba(60,95,140,.32)">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                     stroke="rgba(74,130,190,.4)" stroke-width="1.6" stroke-linecap="round">
                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <div style="font:700 9px/1.65 ui-monospace,monospace;letter-spacing:.17em;
                            text-transform:uppercase;text-align:center;
                            color:rgba(74,130,190,.45)">
                    Disponible al<br>completar el Paso 1
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /wrapper PIN -->

</div><!-- /setup-cols -->


<?php else: ?>
<!-- ══════════════════════════════════════════════════════
     MODO NORMAL: vista de perfil completa
     ══════════════════════════════════════════════════════ -->

<div class="page-head">
    <div>
        <h1><?= e($title) ?></h1>
        <p>// Personaliza tus credenciales y datos de acceso.</p>
    </div>
</div>

<?php if ($hasDefaultPin): ?>
<!-- Alerta de PIN predeterminado — bloquea todas las operaciones de escritura -->
<div class="alert alert-error" style="margin-bottom:18px;border-color:rgba(232,152,32,.55);
     background:rgba(232,152,32,.07);color:#e89820">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="2.5" stroke-linecap="round" style="flex-shrink:0;margin-top:1px">
        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <div>
        <strong style="font:700 12px/1.4 ui-monospace,monospace;letter-spacing:.04em">
            PIN PREDETERMINADO ACTIVO — OPERACIONES BLOQUEADAS
        </strong><br>
        <span style="font:11px/1.6 ui-monospace,monospace">
            Tu PIN actual es el predeterminado del sistema
            (<code style="background:rgba(232,152,32,.15);padding:1px 5px;border-radius:3px;
                          color:#e89820">123456</code>).
            Mientras no lo cambies, no podrás agregar, editar ni eliminar ningún registro.
            Dirígete a la sección <strong>PIN de seguridad</strong> en esta página.
        </span>
    </div>
</div>
<?php endif; ?>

<div class="profile-grid">

    <!-- ── Foto de perfil ── -->
    <section class="profile-card" aria-label="Foto de perfil">
        <div class="profile-card-head"><strong>Foto de perfil</strong></div>
        <form method="post"
              action="<?= e(url('profile/avatar')) ?>"
              enctype="multipart/form-data"
              data-avatar-form
              class="profile-card-body">
            <?= Csrf::field() ?>
            <div class="avatar-preview-wrap">
                <img src="<?= $avatarUrl ? e($avatarUrl) : e(url('assets/img/avatar-default.svg')) ?>"
                     alt="Avatar"
                     class="avatar-preview-img"
                     data-avatar-preview>
                <span class="avatar-preview-name" data-avatar-name>
                    <?= $avatarUrl ? 'Foto actual' : 'Sin foto asignada' ?>
                </span>
                <div class="file-field-row" style="justify-content:center">
                    <button type="button" class="btn file-btn" data-avatar-pick>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        Subir foto
                    </button>
                </div>
                <input type="file" name="avatar"
                       accept="image/jpeg,image/png,image/gif,image/webp"
                       data-avatar-input style="display:none">
                <p class="avatar-hint">JPG · PNG · GIF · WEBP &nbsp;|&nbsp; Máx. 2 MB</p>
            </div>
            <div class="profile-card-actions">
                <button class="btn primary" type="submit">Guardar foto</button>
            </div>
        </form>
    </section>

    <!-- ── Cambiar contraseña ── -->
    <section class="profile-card" aria-label="Cambiar contraseña">
        <div class="profile-card-head">
            <strong>Contraseña de acceso</strong>
        </div>
        <form method="post" action="<?= e(url('profile/password')) ?>"
              class="profile-card-body form-grid profile-form">
            <?= Csrf::field() ?>
            <label class="wide">Contraseña actual
                <input type="password" name="current_password" required
                       autocomplete="current-password"
                       placeholder="Tu contraseña vigente">
            </label>
            <label>Nueva contraseña
                <input type="password" name="new_password" required
                       minlength="8" autocomplete="new-password"
                       placeholder="Mínimo 8 caracteres">
            </label>
            <label>Confirmar contraseña
                <input type="password" name="confirm_password" required
                       autocomplete="new-password"
                       placeholder="Repite la nueva contraseña">
            </label>
            <div class="wide profile-card-actions">
                <button class="btn primary" type="submit">Actualizar contraseña</button>
            </div>
        </form>
    </section>

    <!-- ── PIN de seguridad ── -->
    <section class="profile-card" aria-label="PIN de seguridad">
        <div class="profile-card-head">
            <strong>PIN de seguridad</strong>
            <?php if (!$hasPin): ?>
                <span class="badge" style="background:rgba(248,113,113,.12);
                      border-color:rgba(248,113,113,.35);color:#f87171">REQUERIDO</span>
            <?php elseif ($hasDefaultPin): ?>
                <span class="badge" style="background:rgba(232,152,32,.12);
                      border-color:rgba(232,152,32,.45);color:#e89820">PREDETERMINADO</span>
            <?php else: ?>
                <span class="badge" style="background:rgba(74,222,128,.08);
                      border-color:rgba(74,222,128,.3);color:#4ade80">CONFIGURADO</span>
            <?php endif; ?>
        </div>
        <form method="post" action="<?= e(url('profile/pin')) ?>"
              class="profile-card-body form-grid profile-form">
            <?= Csrf::field() ?>
            <p class="profile-hint wide">
                El PIN se solicitará cada vez que agregues, edites o elimines un registro.<br>
                Debe ser numérico, entre 4 y 8 dígitos.
            </p>
            <?php if ($hasPin): ?>
            <label class="wide">
                <?= $hasDefaultPin ? 'PIN actual (predeterminado: 123456)' : 'PIN actual' ?>
                <input type="password" name="current_pin" inputmode="numeric"
                       maxlength="8" required autocomplete="off"
                       placeholder="<?= $hasDefaultPin
                           ? 'Ingresa 123456 para confirmar'
                           : 'Tu PIN vigente' ?>">
            </label>
            <?php endif; ?>
            <label>Nuevo PIN
                <input type="password" name="new_pin" inputmode="numeric"
                       maxlength="8" required autocomplete="new-password"
                       placeholder="4–8 dígitos">
            </label>
            <label>Confirmar PIN
                <input type="password" name="confirm_pin" inputmode="numeric"
                       maxlength="8" required autocomplete="new-password"
                       placeholder="Repite el PIN">
            </label>
            <div class="wide profile-card-actions">
                <button class="btn primary" type="submit">
                    <?= $hasPin ? 'Cambiar PIN' : 'Configurar PIN' ?>
                </button>
            </div>
        </form>
    </section>

</div>
<?php endif; ?>
