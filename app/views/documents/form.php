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
      data-pdf-preview-form>

    <?= Csrf::field() ?>

    <!-- Vista previa del PDF -->
    <section class="pdf-preview-panel" aria-label="Vista previa del expediente">
        <div class="pdf-preview-head">
            <strong>Vista previa</strong>
            <span data-pdf-preview-title><?= e($previewTitle) ?></span>
        </div>
        <div class="pdf-preview-frame">
            <?php if ($previewUrl !== ''): ?>
                <object data="<?= e($previewUrl) ?>"
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
            <input name="sender" value="<?= $value('sender') ?>" required>
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

        <label class="wide">Archivo PDF <?= $requiresFile ? '' : '<span style="color:var(--sub);font-size:9px">(OPCIONAL)</span>' ?>
            <input type="file"
                   name="pdf"
                   accept="application/pdf"
                   <?= $requiresFile ? 'required' : '' ?>
                   data-pdf-preview-input
                   style="padding:10px;cursor:pointer">
        </label>

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
