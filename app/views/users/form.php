<?php
/*
 * Vista: formulario de operador (crear / editar)
 */
$isEdit          = $userRecord !== null;
$value           = static fn(string $key): string => e(old($key, $userRecord[$key] ?? ''));
$roleValue       = old('role', $userRecord['role'] ?? 'operador');
$activeValue     = (int) old('active', $userRecord['active'] ?? 1);
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

<form class="form-grid" method="post" action="<?= e($action) ?>">
    <?= Csrf::field() ?>

    <label>Nombre completo
        <input name="name" value="<?= $value('name') ?>" required placeholder="Ej. Agente Ramírez">
    </label>

    <label>Identificador (correo)
        <input type="email" name="email" value="<?= $value('email') ?>" required autocomplete="off"
               placeholder="operador@dominio.gov">
    </label>

    <label>Nivel de acceso
        <select name="role" required>
            <option value="operador" <?= $roleValue === 'operador' ? 'selected' : '' ?>>Operador</option>
            <option value="admin"    <?= $roleValue === 'admin'    ? 'selected' : '' ?>>Administrador</option>
        </select>
    </label>

    <label>Clave de acceso <?= $requiresPassword ? '' : '<span style="color:var(--sub);font-size:9px">(OPCIONAL)</span>' ?>
        <input type="password"
               name="password"
               minlength="8"
               autocomplete="new-password"
               <?= $requiresPassword ? 'required' : '' ?>
               placeholder="Mínimo 8 caracteres">
    </label>

    <label class="checkbox-row wide" style="flex-direction:row;align-items:center;gap:12px;font-size:12px;padding:10px 12px;background:rgba(74,130,190,.05);border:1px solid var(--line2)">
        <input type="checkbox" name="active" value="1" <?= $activeValue === 1 ? 'checked' : '' ?> style="width:auto;min-height:auto">
        <span style="text-transform:none;letter-spacing:0;font-family:ui-sans-serif">Operador activo — puede iniciar sesión</span>
    </label>

    <div class="wide form-actions">
        <button class="btn primary" type="submit"><?= $isEdit ? 'Actualizar operador' : 'Registrar operador' ?></button>
        <a class="btn muted" href="<?= e(url('users')) ?>">Cancelar</a>
    </div>
</form>
