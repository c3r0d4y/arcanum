<?php
/*
 * Vista: lista de expedientes cifrados con filtros de búsqueda
 */
?>
<div class="page-head">
    <div>
        <h1>Expedientes Clasificados</h1>
        <p>// REPOSITORIO CIFRADO — <?= count($documents) ?> expediente(s) encontrado(s)</p>
    </div>
</div>

<form class="filters" method="get" action="<?= e(url('documents')) ?>" data-auto-filter>
    <label>Folio
        <input name="number" value="<?= e($filters['number']) ?>" placeholder="EXP-001">
    </label>
    <label>Asunto
        <input name="subject" value="<?= e($filters['subject']) ?>">
    </label>
    <label>Fecha
        <input type="date" name="date" value="<?= e($filters['date']) ?>">
    </label>
    <label>Tipo
        <select name="type">
            <option value="">Todos</option>
            <?php foreach ($types as $type): ?>
                <option value="<?= e($type) ?>" <?= $filters['type'] === $type ? 'selected' : '' ?>>
                    <?= e(ucfirst($type)) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <a class="btn muted" href="<?= e(url('documents')) ?>">Limpiar</a>
    <a class="btn primary" href="<?= e(url('documents/create')) ?>">+ Nuevo</a>
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th class="col-number">Folio</th>
                <th class="col-subject">Asunto</th>
                <th class="col-date">Fecha</th>
                <th class="col-sender">Remitente</th>
                <th class="col-type">Tipo</th>
                <th class="col-actions">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($documents as $document): ?>
            <tr>
                <td style="font:600 12px/1 ui-monospace,monospace;color:var(--accent)"><?= e($document['number']) ?></td>
                <td><?= e($document['subject']) ?></td>
                <td style="font:12px/1 ui-monospace,monospace;color:var(--muted)"><?= e($document['document_date']) ?></td>
                <td><?= e($document['sender']) ?></td>
                <td><span class="badge"><?= e(ucfirst($document['type'])) ?></span></td>
                <td class="actions">
                    <a class="btn soft"
                       href="<?= e(url('documents/' . $document['id'] . '/file')) ?>"
                       target="_blank" rel="noopener">Ver PDF</a>
                    <a class="btn soft"
                       href="<?= e(url('documents/' . $document['id'] . '/edit')) ?>">Editar</a>
                    <form method="post"
                          action="<?= e(url('documents/' . $document['id'] . '/delete')) ?>"
                          data-confirm="¿Eliminar este expediente y su archivo cifrado?">
                        <?= Csrf::field() ?>
                        <button class="btn soft" type="submit" style="color:var(--danger);border-color:rgba(192,57,43,.25)">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$documents): ?>
            <tr>
                <td colspan="6" class="empty">// No hay expedientes con los filtros aplicados.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
