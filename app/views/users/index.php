<?php /* Vista: lista de operadores del sistema */ ?>
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
            <tr>
                <td style="font-weight:600"><?= e($userRecord['name']) ?></td>
                <td style="font:12px/1 ui-monospace,monospace;color:var(--muted)"><?= e($userRecord['email']) ?></td>
                <td><span class="badge"><?= e(ucfirst($userRecord['role'])) ?></span></td>
                <td>
                    <?php if ((int)$userRecord['active'] === 1): ?>
                        <span style="color:#4ade80;font:600 10px/1 ui-monospace,monospace">&#9679; ACTIVO</span>
                    <?php else: ?>
                        <span style="color:var(--danger);font:600 10px/1 ui-monospace,monospace">&#9679; INACTIVO</span>
                    <?php endif; ?>
                </td>
                <td style="font:11px/1 ui-monospace,monospace;color:var(--muted)"><?= e($userRecord['created_at']) ?></td>
                <td class="actions">
                    <a class="btn soft" href="<?= e(url('users/' . $userRecord['id'] . '/edit')) ?>">Editar</a>
                    <?php if ((int)$userRecord['id'] !== Auth::id()): ?>
                        <form method="post"
                              action="<?= e(url('users/' . $userRecord['id'] . '/delete')) ?>"
                              data-confirm="¿Revocar acceso de este operador?">
                            <?= Csrf::field() ?>
                            <button class="btn soft" type="submit" style="color:var(--danger);border-color:rgba(192,57,43,.25)">Revocar</button>
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
