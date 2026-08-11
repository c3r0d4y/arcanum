<?php /* Vista: edición de un elemento de catálogo */ ?>
<div class="page-head">
    <div>
        <h1><?= e($title) ?></h1>
        <p>// Modifica el nombre del elemento en el catálogo del sistema</p>
    </div>
    <a class="btn muted" href="<?= e(url('catalogs')) ?>">← Catálogos</a>
</div>

<div style="max-width:480px">
    <form class="form-grid" method="post" action="<?= e($action) ?>" style="padding:24px 20px;gap:18px" data-pin-required>
        <?= Csrf::field() ?>

        <label class="wide"><?= e($backLabel) ?>
            <input type="text"
                   name="name"
                   value="<?= e(old('name', $entry['name'])) ?>"
                   maxlength="200"
                   required
                   autofocus>
        </label>

        <div class="wide form-actions">
            <button class="btn primary" type="submit">Guardar cambios</button>
            <a class="btn muted" href="<?= e(url('catalogs')) ?>">Cancelar</a>
        </div>
    </form>
</div>
