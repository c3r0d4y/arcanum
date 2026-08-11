<?php
/* Vista: lista de operadores del sistema con indicador de cuentas bloqueadas */

// Cuenta cuántas cuentas están bloqueadas para mostrar la alerta superior
$lockedCount = count(array_filter($users, fn($u) => $u['locked_at'] !== null));
?>

<?php if ($lockedCount > 0): ?>
<!-- Alerta de cuentas bloqueadas -->
<div class="alert alert-error" style="margin-bottom:16px;font:600 11px/1.5 ui-monospace,monospace">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="flex-shrink:0">
        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
    </svg>
    <?= $lockedCount === 1
        ? '1 cuenta bloqueada por múltiples intentos fallidos de autenticación.'
        : "{$lockedCount} cuentas bloqueadas por múltiples intentos fallidos de autenticación." ?>
    &nbsp;— Revise la tabla y use el botón <strong>Desbloquear</strong> según corresponda.
</div>
<?php endif; ?>

<div class="page-head">
    <div>
        <h1>Operadores del Sistema</h1>
        <p>// Control de acceso — administradores y operadores autorizados</p>
    </div>
    <a class="btn primary" href="<?= e(url('users/create')) ?>">+ Nuevo operador</a>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Identificador</th>
                <th>Nivel</th>
                <th>Estado</th>
                <th>Alta</th>
                <th class="col-actions">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $userRecord): ?>
            <?php $isLocked = $userRecord['locked_at'] !== null; ?>
            <tr>
                <!-- Nombre con avatar -->
                <td data-label="Nombre">
                    <div style="display:flex;align-items:center;gap:10px">
                        <img src="<?= !empty($userRecord['avatar'])
                            ? e(url('users/' . $userRecord['id'] . '/avatar'))
                            : e(url('assets/img/avatar-default.svg')) ?>"
                             alt=""
                             class="user-avatar-thumb"
                             style="<?= $isLocked ? 'opacity:.45;filter:grayscale(1)' : '' ?>">
                        <span style="font-weight:600;<?= $isLocked ? 'color:var(--muted)' : '' ?>">
                            <?= e($userRecord['name']) ?>
                        </span>
                    </div>
                </td>

                <!-- Correo electrónico -->
                <td data-label="Identificador" style="font:12px/1 ui-monospace,monospace;color:var(--muted)">
                    <?= e($userRecord['email']) ?>
                </td>

                <!-- Rol -->
                <td data-label="Nivel"><span class="badge"><?= e(ucfirst($userRecord['role'])) ?></span></td>

                <!-- Estado: Activo / Inactivo / Bloqueada -->
                <td data-label="Estado">
                    <?php if ($isLocked): ?>
                        <!-- Cuenta bloqueada por intentos fallidos -->
                        <div style="display:flex;flex-direction:column;gap:4px">
                            <span style="color:#e89820;font:700 10px/1 ui-monospace,monospace">
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="vertical-align:middle;margin-right:2px">
                                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                BLOQUEADA
                            </span>
                            <span style="color:var(--sub);font:9px/1 ui-monospace,monospace">
                                <?= e(date('d/m/Y H:i', strtotime($userRecord['locked_at']))) ?>
                            </span>
                            <span style="color:var(--sub);font:9px/1 ui-monospace,monospace">
                                <?= (int)$userRecord['failed_attempts'] ?> intentos fallidos
                            </span>
                        </div>
                    <?php elseif ((int)$userRecord['active'] === 1): ?>
                        <span style="color:#4ade80;font:600 10px/1 ui-monospace,monospace">&#9679; ACTIVO</span>
                    <?php else: ?>
                        <span style="color:var(--danger);font:600 10px/1 ui-monospace,monospace">&#9679; INACTIVO</span>
                    <?php endif; ?>
                </td>

                <!-- Fecha de alta -->
                <td data-label="Alta" style="font:11px/1 ui-monospace,monospace;color:var(--muted)">
                    <?= e($userRecord['created_at']) ?>
                </td>

                <!-- Acciones -->
                <td class="actions">
                    <a class="btn soft" href="<?= e(url('users/' . $userRecord['id'] . '/edit')) ?>">Editar</a>

                    <?php if ($isLocked): ?>
                        <!-- Botón de desbloqueo — requiere PIN -->
                        <form method="post"
                              action="<?= e(url('users/' . $userRecord['id'] . '/unlock')) ?>"
                              data-confirm="¿Desbloquear la cuenta de <?= e($userRecord['name']) ?>? Se restablecerán los intentos fallidos."
                              data-pin-required>
                            <?= Csrf::field() ?>
                            <button class="btn soft" type="submit"
                                    style="color:#e89820;border-color:rgba(232,152,32,.3)">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 9.9-1"/>
                                </svg>
                                Desbloquear
                            </button>
                        </form>
                    <?php endif; ?>

                    <?php if ((int)$userRecord['id'] !== Auth::id()): ?>
                        <!-- Botón de revocar acceso -->
                        <form method="post"
                              action="<?= e(url('users/' . $userRecord['id'] . '/delete')) ?>"
                              data-confirm="¿Revocar acceso de este operador?"
                              data-pin-required>
                            <?= Csrf::field() ?>
                            <button class="btn soft" type="submit"
                                    style="color:var(--danger);border-color:rgba(192,57,43,.25)">
                                Revocar
                            </button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$users): ?>
            <tr><td colspan="6" class="empty">// No hay operadores registrados.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
