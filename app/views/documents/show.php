<?php
/*
 * Vista: detalle de expediente con visor de PDF integrado
 */
?>
<div class="page-head">
    <div>
        <h1 style="font-family:ui-monospace,monospace;letter-spacing:.05em"><?= e($document['number']) ?></h1>
        <p><?= e($document['subject']) ?></p>
    </div>
    <div class="button-row">
        <a class="btn primary"
           href="<?= e(url('documents/' . $document['id'] . '/file')) ?>"
           target="_blank" rel="noopener">Abrir PDF</a>
        <a class="btn" href="<?= e(url('documents/' . $document['id'] . '/edit')) ?>">Editar</a>
        <?php if ((Auth::user()['role'] ?? '') === 'admin'): ?>
            <form method="post"
                  action="<?= e(url('documents/' . $document['id'] . '/delete')) ?>"
                  data-confirm="¿Eliminar este expediente y su archivo cifrado?">
                <?= Csrf::field() ?>
                <button class="btn danger" type="submit">Eliminar</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<section class="detail-grid">
    <div>
        <strong>Fecha</strong>
        <span><?= e($document['document_date']) ?></span>
    </div>
    <div>
        <strong>Remitente</strong>
        <span><?= e($document['sender']) ?></span>
    </div>
    <div>
        <strong>Clasificación</strong>
        <span><span class="badge"><?= e(ucfirst($document['type'])) ?></span></span>
    </div>
    <div>
        <strong>Registrado por</strong>
        <span><?= e($document['created_by_name'] ?? 'Sistema') ?></span>
    </div>
</section>

<div style="font:10px/1.5 ui-monospace,monospace;color:var(--muted);padding:8px 14px;
            background:rgba(184,147,90,.05);border:1px solid rgba(184,147,90,.18);
            margin-bottom:16px;display:flex;align-items:center;gap:12px">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2.2" stroke-linecap="round">
        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
    </svg>
    <span>ARCHIVO CIFRADO AES-256-GCM — DESCIFRADO EN MEMORIA — <?= e($document['original_file_name']) ?></span>
</div>

<iframe class="pdf-viewer"
        src="<?= e(url('documents/' . $document['id'] . '/file')) ?>"
        title="Expediente <?= e($document['number']) ?>"></iframe>
