<?php /* Vista: gestión de catálogos del sistema — solo administrador */ ?>
<div class="page-head">
    <div>
        <h1>Catálogos del Sistema</h1>
        <p>// Gestión de tipos de documento y remitentes autorizados</p>
    </div>
</div>

<div class="catalog-grid">

    <!-- ═══════════════════════════════════════════
         SECCIÓN 1: Clasificación / Tipo de documento
         ═══════════════════════════════════════════ -->
    <div class="catalog-section">
        <div class="catalog-section-head">
            <svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true" style="width:16px;height:16px;flex-shrink:0">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <path d="M14 2v6h6"/><path d="M9 13h6"/><path d="M9 17h4"/>
            </svg>
            <span>Clasificación / Tipo de Documento</span>
            <span class="catalog-count"><?= count($types) ?></span>
        </div>

        <div class="table-wrap" style="border-top:none">
            <table style="min-width:0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th style="width:90px">Estado</th>
                        <th style="width:160px" class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($types as $entry): ?>
                    <tr>
                        <td data-label="Nombre" style="font-weight:600"><?= e(ucfirst($entry['name'])) ?></td>
                        <td data-label="Estado">
                            <?php if ((int)$entry['active'] === 1): ?>
                                <span style="color:#4ade80;font:600 10px/1 ui-monospace,monospace">&#9679; ACTIVO</span>
                            <?php else: ?>
                                <span style="color:var(--danger);font:600 10px/1 ui-monospace,monospace">&#9679; INACTIVO</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <a class="btn soft" href="<?= e(url('catalogs/types/' . $entry['id'] . '/edit')) ?>">Editar</a>
                            <form method="post"
                                  action="<?= e(url('catalogs/types/' . $entry['id'] . '/delete')) ?>"
                                  data-confirm="¿Eliminar el tipo «<?= e(ucfirst($entry['name'])) ?>»?"
                                  data-pin-required>
                                <?= Csrf::field() ?>
                                <button class="btn soft" type="submit"
                                        style="color:var(--danger);border-color:rgba(192,57,43,.25)">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$types): ?>
                    <tr><td colspan="3" class="empty">// Sin tipos registrados.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Formulario para agregar nuevo tipo -->
        <form class="catalog-add-form" method="post" action="<?= e(url('catalogs/types/store')) ?>" data-pin-required>
            <?= Csrf::field() ?>
            <span class="catalog-add-label">+ Nuevo tipo</span>
            <input type="text"
                   name="name"
                   placeholder="Ej: resolución, decreto…"
                   maxlength="100"
                   required
                   style="flex:1">
            <button class="btn primary" type="submit" style="white-space:nowrap">Agregar</button>
        </form>
    </div>

    <!-- ═══════════════════════════════════════════
         SECCIÓN 2: Remitentes autorizados
         ═══════════════════════════════════════════ -->
    <div class="catalog-section">
        <div class="catalog-section-head">
            <svg class="nav-icon" viewBox="0 0 24 24" aria-hidden="true" style="width:16px;height:16px;flex-shrink:0">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            <span>Remitentes Autorizados</span>
            <span class="catalog-count"><?= count($senders) ?></span>
        </div>

        <div class="table-wrap" style="border-top:none">
            <table style="min-width:0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th style="width:90px">Estado</th>
                        <th style="width:160px" class="col-actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($senders as $entry): ?>
                    <tr>
                        <td data-label="Nombre" style="font-weight:600"><?= e($entry['name']) ?></td>
                        <td data-label="Estado">
                            <?php if ((int)$entry['active'] === 1): ?>
                                <span style="color:#4ade80;font:600 10px/1 ui-monospace,monospace">&#9679; ACTIVO</span>
                            <?php else: ?>
                                <span style="color:var(--danger);font:600 10px/1 ui-monospace,monospace">&#9679; INACTIVO</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <a class="btn soft" href="<?= e(url('catalogs/senders/' . $entry['id'] . '/edit')) ?>">Editar</a>
                            <form method="post"
                                  action="<?= e(url('catalogs/senders/' . $entry['id'] . '/delete')) ?>"
                                  data-confirm="¿Eliminar el remitente «<?= e($entry['name']) ?>»?"
                                  data-pin-required>
                                <?= Csrf::field() ?>
                                <button class="btn soft" type="submit"
                                        style="color:var(--danger);border-color:rgba(192,57,43,.25)">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$senders): ?>
                    <tr><td colspan="3" class="empty">// Sin remitentes registrados.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Formulario para agregar nuevo remitente -->
        <form class="catalog-add-form" method="post" action="<?= e(url('catalogs/senders/store')) ?>" data-pin-required>
            <?= Csrf::field() ?>
            <span class="catalog-add-label">+ Nuevo remitente</span>
            <input type="text"
                   name="name"
                   placeholder="Nombre completo del remitente"
                   maxlength="200"
                   required
                   style="flex:1">
            <button class="btn primary" type="submit" style="white-space:nowrap">Agregar</button>
        </form>
    </div>

</div>
