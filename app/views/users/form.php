<?php
/*
 * Vista: formulario de operador (crear / editar)
 * El administrador puede cambiar nombre, correo, rol, contraseña y avatar.
 * El PIN lo gestiona cada operador desde su propio perfil.
 */
$isEdit      = $userRecord !== null;
$value       = static fn(string $key): string => e(old($key, $userRecord[$key] ?? ''));
$roleValue   = old('role', $userRecord['role'] ?? 'operador');
$activeValue = (int) old('active', $userRecord['active'] ?? 1);

// URL del avatar actual o null si no tiene
$avatarUrl = ($isEdit && !empty($userRecord['avatar']))
    ? url('users/' . $userRecord['id'] . '/avatar') . '?v=' . time()
    : null;

// Parte local del correo (antes del @) para el campo partido
$emailLocal = old('email_user', '');
if ($emailLocal === '' && $isEdit && !empty($userRecord['email'] ?? '')) {
    $emailLocal = explode('@', $userRecord['email'])[0];
}
?>
<div class="page-head">
    <div>
        <h1><?= e($title) ?></h1>
        <p><?= $isEdit
            ? '// Actualiza credenciales y nivel de acceso.'
            : '// Registra un nuevo operador autorizado.' ?></p>
    </div>
    <a class="btn muted" href="<?= e(url('users')) ?>">← Volver</a>
</div>

<form class="user-form-layout"
      method="post"
      action="<?= e($action) ?>"
      enctype="multipart/form-data"
      data-avatar-form
      data-pin-required>

    <?= Csrf::field() ?>

    <!-- Panel de avatar -->
    <section class="avatar-panel" aria-label="Foto del operador">
        <div class="avatar-panel-head">
            <strong>Foto de perfil</strong>
        </div>
        <div class="avatar-preview-wrap">
            <img src="<?= $avatarUrl ? e($avatarUrl) : e(url('assets/img/avatar-default.svg')) ?>"
                 alt="Avatar"
                 class="avatar-preview-img"
                 data-avatar-preview>
            <span class="avatar-preview-name" data-avatar-name>
                <?= $isEdit && !empty($userRecord['avatar']) ? 'Foto actual' : 'Sin foto asignada' ?>
            </span>
            <div class="file-field-row" style="justify-content:center">
                <button type="button" class="btn file-btn" data-avatar-pick>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                    Subir foto
                </button>
            </div>
            <input type="file"
                   name="avatar"
                   accept="image/jpeg,image/png,image/gif,image/webp"
                   data-avatar-input
                   style="display:none">
            <p class="avatar-hint">JPG · PNG · GIF · WEBP<br>Máx. 2 MB</p>
        </div>
    </section>

    <!-- Campos del operador -->
    <div class="form-grid user-fields">
        <label class="wide">Nombre completo
            <input name="name" value="<?= $value('name') ?>" required placeholder="Ej. Agente Ramírez">
        </label>

        <!-- Campo de correo con dominio fijo @c3r0d4y.com — solo la parte local es editable -->
        <label class="wide">Identificador (correo)
            <div style="display:flex;align-items:stretch;
                        border:1px solid var(--line2);
                        background:var(--bg2);
                        overflow:hidden">
                <input type="text"
                       name="email_user"
                       value="<?= e($emailLocal) ?>"
                       required
                       autocomplete="off"
                       autocapitalize="none"
                       spellcheck="false"
                       placeholder="nombre.apellido"
                       pattern="[a-zA-Z0-9._%+\-]+"
                       title="Solo letras, números y los caracteres . _ % + -"
                       style="flex:1;border:none;background:transparent;
                              padding:0 10px;min-width:0;
                              font:inherit;color:inherit;outline:none;
                              height:38px">
                <span aria-hidden="true"
                      style="display:flex;align-items:center;
                             padding:0 13px;
                             font:700 11px/1 ui-monospace,monospace;
                             color:var(--sub);
                             background:rgba(0,0,0,.22);
                             border-left:1px solid var(--line2);
                             white-space:nowrap;
                             user-select:none;
                             pointer-events:none">
                    @c3r0d4y.com
                </span>
            </div>
        </label>

        <label>Nivel de acceso
            <select name="role" required>
                <option value="operador" <?= $roleValue === 'operador' ? 'selected' : '' ?>>Operador</option>
                <option value="admin"    <?= $roleValue === 'admin'    ? 'selected' : '' ?>>Administrador</option>
            </select>
        </label>

        <!-- ── Contraseña de acceso ── -->
        <label>
            Clave de acceso
            <?php if (!$requiresPassword): ?>
                <span style="color:var(--sub);font-size:9px;letter-spacing:.06em">(OPCIONAL — dejar vacío para conservar)</span>
            <?php endif; ?>
            <input type="password"
                   name="password"
                   minlength="8"
                   autocomplete="new-password"
                   <?= $requiresPassword ? 'required' : '' ?>
                   placeholder="Mínimo 8 caracteres">
            <?php if ($isEdit): ?>
            <!-- Aviso: cambiar la contraseña obliga al operador a renovarla y reconfigurar PIN -->
            <span style="display:block;margin-top:5px;
                         font:10px/1.55 ui-monospace,monospace;
                         color:rgba(232,152,32,.8)">
                Al guardar una nueva contraseña, el operador deberá renovarla en su próximo ingreso
                y volver a configurar su PIN de seguridad.
            </span>
            <?php endif; ?>
        </label>

        <label class="checkbox-row wide" style="flex-direction:row;align-items:center;gap:12px;
               font-size:12px;padding:10px 12px;background:rgba(74,130,190,.05);border:1px solid var(--line2)">
            <input type="checkbox" name="active" value="1"
                   <?= $activeValue === 1 ? 'checked' : '' ?>
                   style="width:auto;min-height:auto">
            <span style="text-transform:none;letter-spacing:0;font-family:ui-sans-serif">
                Operador activo — puede iniciar sesión
            </span>
        </label>

        <div class="wide form-actions">
            <button class="btn primary" type="submit">
                <?= $isEdit ? 'Actualizar operador' : 'Registrar operador' ?>
            </button>
            <a class="btn muted" href="<?= e(url('users')) ?>">Cancelar</a>
        </div>
    </div>
</form>
