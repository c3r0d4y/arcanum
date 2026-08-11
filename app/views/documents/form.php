<?php
/*
 * Vista: formulario de creación y edición de expedientes
 */

$isEdit      = $document !== null;
$value       = static fn(string $key): string => e(old($key, $document[$key] ?? ''));
$previewUrl  = $isEdit ? url('documents/' . $document['id'] . '/file') : '';
$previewTitle = $isEdit
    ? 'Archivo actual: ' . ($document['original_file_name'] ?? $document['number'])
    : 'Sin archivo seleccionado';
?>
<div class="page-head">
    <div>
        <h1><?= e($title) ?></h1>
        <p><?= $isEdit
            ? '// Actualiza metadatos o reemplaza el archivo cifrado.'
            : '// Completa la ficha y adjunta el PDF — se cifrará automáticamente.' ?></p>
    </div>
    <a class="btn muted" href="<?= e(url('documents')) ?>">← Volver</a>
</div>

<form class="document-form-layout"
      method="post"
      action="<?= e($action) ?>"
      enctype="multipart/form-data"
      data-pdf-preview-form
      data-pin-required>

    <?= Csrf::field() ?>

    <!-- Vista previa del PDF -->
    <section class="pdf-preview-panel" aria-label="Vista previa del expediente">
        <div class="pdf-preview-head">
            <strong>Vista previa</strong>
            <span data-pdf-preview-title><?= e($previewTitle) ?></span>
        </div>
        <div class="pdf-preview-frame">
            <?php if ($previewUrl !== ''): ?>
                <object data="<?= e($previewUrl) ?>#navpanes=0"
                        type="application/pdf"
                        data-pdf-preview
                        class="pdf-preview-object">
                    <a class="btn muted" href="<?= e($previewUrl) ?>" target="_blank" rel="noopener">Abrir PDF</a>
                </object>
            <?php else: ?>
                <object data="" type="application/pdf" data-pdf-preview class="pdf-preview-object is-empty"></object>
                <div class="pdf-preview-empty" data-pdf-preview-empty>
                    <span>Seleccione un PDF para visualizarlo</span>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Campos del expediente -->
    <div class="form-grid document-fields">
        <label>Folio / Número
            <input name="number" value="<?= $value('number') ?>" required placeholder="EXP-2024-001">
        </label>

        <label>Fecha del documento
            <input type="date" name="document_date" value="<?= $value('document_date') ?>" required>
        </label>

        <label class="wide">Asunto / Materia
            <input name="subject" value="<?= $value('subject') ?>" required>
        </label>

        <label>Remitente
            <?php if (!empty($senders)): ?>
                <select name="sender" required>
                    <option value="">Seleccione remitente</option>
                    <?php foreach ($senders as $sender): ?>
                        <?php $sel = old('sender', $document['sender'] ?? '') === $sender ? 'selected' : ''; ?>
                        <option value="<?= e($sender) ?>" <?= $sel ?>><?= e($sender) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input name="sender" value="<?= $value('sender') ?>" required
                       placeholder="Sin remitentes en catálogo — contacte al administrador">
            <?php endif; ?>
        </label>

        <label>Clasificación / Tipo
            <select name="type" required>
                <option value="">Seleccione</option>
                <?php foreach ($types as $type): ?>
                    <?php $selected = old('type', $document['type'] ?? '') === $type ? 'selected' : ''; ?>
                    <option value="<?= e($type) ?>" <?= $selected ?>><?= e(ucfirst($type)) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <div class="wide file-field">
            <span class="file-field-lbl">
                Archivo PDF
                <?= $requiresFile ? '' : '<span class="file-optional">(OPCIONAL)</span>' ?>
            </span>
            <div class="file-field-row">
                <button type="button" class="btn file-btn" data-pdf-pick>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="12" y1="18" x2="12" y2="12"/>
                        <line x1="9" y1="15" x2="15" y2="15"/>
                    </svg>
                    Seleccionar PDF
                </button>
                <span class="file-name" data-file-name>Sin archivo seleccionado</span>
                <input type="file"
                       name="pdf"
                       accept="application/pdf"
                       data-pdf-preview-input
                       style="display:none">
            </div>
        </div>

        <?php if ($isEdit): ?>
            <div class="wide" style="font:10px/1.5 ui-monospace,monospace;color:var(--muted);padding:8px 10px;background:rgba(74,130,190,.05);border:1px solid var(--line2)">
                <strong style="color:var(--sub)">ARCHIVO ACTUAL:</strong>
                <?= e($document['original_file_name']) ?>
                &nbsp;|&nbsp; <?= number_format((int)$document['file_size'] / 1024, 1) ?> KB &nbsp;|&nbsp;
                <span style="color:var(--gold)">AES-256-GCM CIFRADO</span>
            </div>
        <?php endif; ?>

        <div class="wide form-actions">
            <button class="btn primary" type="submit">
                <?= $isEdit ? 'Actualizar expediente' : 'Registrar y cifrar' ?>
            </button>
            <a class="btn muted" href="<?= e(url('documents')) ?>">Cancelar</a>
        </div>
    </div>
</form>
