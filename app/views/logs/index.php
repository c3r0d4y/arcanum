<?php /* Vista: bitácora táctica del sistema */ ?>
<div class="page-head">
    <div>
        <h1>Bitácora de Operaciones</h1>
        <p>// Registro de accesos y actividad sobre expedientes clasificados</p>
    </div>
</div>

<form class="filters logs-filters" method="get" action="<?= e(url('logs')) ?>" data-auto-filter>
    <label>Desde
        <input type="date" name="from" value="<?= e($filters['from']) ?>">
    </label>
    <label>Hasta
        <input type="date" name="to" value="<?= e($filters['to']) ?>">
    </label>
    <label>Nivel
        <select name="role">
            <option value="">Todos</option>
            <option value="admin"    <?= $filters['role'] === 'admin'    ? 'selected' : '' ?>>Administrador</option>
            <option value="operador" <?= $filters['role'] === 'operador' ? 'selected' : '' ?>>Operador</option>
        </select>
    </label>
    <div class="filters-actions">
        <a class="btn muted" href="<?= e(url('logs')) ?>">Limpiar</a>
    </div>
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="width:14%">Fecha/Hora</th>
                <th style="width:16%">Operador</th>
                <th style="width:9%">Nivel</th>
                <th style="width:13%">Acción</th>
                <th>Descripción</th>
                <th style="width:10%">IP</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td data-label="Fecha/Hora" style="font:11px/1 ui-monospace,monospace;color:var(--muted)"><?= e($log['created_at']) ?></td>
                <td data-label="Operador" style="font-weight:600"><?= e($log['user_name'] ?? 'No autenticado') ?></td>
                <td data-label="Nivel">
                    <?= $log['user_role']
                        ? '<span class="badge">' . e(ucfirst($log['user_role'])) . '</span>'
                        : '<span style="color:var(--sub);font:10px ui-monospace,monospace">—</span>' ?>
                </td>
                <td data-label="Acción">
                    <?php
                    $actionColor = match(true) {
                        str_contains($log['action'], 'login_failed') => 'var(--danger)',
                        str_contains($log['action'], 'deleted')      => '#f87171',
                        str_contains($log['action'], 'created')      => '#4ade80',
                        str_contains($log['action'], 'login')        => 'var(--accent)',
                        default => 'var(--muted)',
                    };
                    ?>
                    <span style="font:600 10px/1 ui-monospace,monospace;color:<?= $actionColor ?>;letter-spacing:.08em;text-transform:uppercase">
                        <?= e($log['action']) ?>
                    </span>
                </td>
                <td data-label="Descripción" style="font-size:13px"><?= e($log['description']) ?></td>
                <td data-label="IP" style="font:11px/1 ui-monospace,monospace;color:var(--sub)"><?= e($log['ip_address']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$logs): ?>
            <tr><td colspan="6" class="empty">// Sin registros con los filtros aplicados.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
