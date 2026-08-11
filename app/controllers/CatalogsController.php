<?php

/*
 * Archivo: app/controllers/CatalogsController.php
 * Autor:   C3r0d4y
 *
 * Gestión de catálogos editables por el administrador:
 *   - Tipos de documento (document_types)
 *   - Remitentes autorizados (document_senders)
 *
 * Todas las acciones requieren rol 'admin'.
 */

declare(strict_types=1);

final class CatalogsController extends Controller
{
    private Catalog $catalog;

    public function __construct()
    {
        $this->catalog = new Catalog();
    }

    // Muestra ambos catálogos con sus entradas actuales
    public function index(): void
    {
        Auth::requireAdmin();
        $this->view('catalogs/index', [
            'title'   => 'Catálogos',
            'types'   => $this->catalog->todos('document_types'),
            'senders' => $this->catalog->todos('document_senders'),
        ]);
    }

    /* ── Tipos de documento ── */

    // Agrega un nuevo tipo al catálogo
    public function storeType(): void
    {
        Auth::requireAdmin();
        Csrf::verify();
        Auth::blockIfDefaultPin();
        Auth::requirePin();

        $nombre = trim((string) ($_POST['name'] ?? ''));

        if ($nombre === '') {
            flash('error', 'El nombre del tipo no puede estar vacío.');
            redirect('catalogs');
        }

        if ($this->catalog->existeNombre('document_types', $nombre)) {
            flash('error', 'Ya existe un tipo con ese nombre.');
            redirect('catalogs');
        }

        $this->catalog->crear('document_types', $nombre);
        Logger::write('catalog_type_created', "Tipo de documento creado: {$nombre}");
        flash('success', "Tipo «{$nombre}» agregado al catálogo.");
        redirect('catalogs');
    }

    // Muestra el formulario de edición de un tipo
    public function editType(int $id): void
    {
        Auth::requireAdmin();
        $entry = $this->findTypeOrFail($id);
        $this->view('catalogs/edit', [
            'title'    => 'Editar tipo de documento',
            'entry'    => $entry,
            'action'   => url("catalogs/types/{$id}/update"),
            'backLabel'=> 'Tipo de documento',
        ]);
    }

    // Guarda los cambios de un tipo existente
    public function updateType(int $id): void
    {
        Auth::requireAdmin();
        Csrf::verify();
        Auth::blockIfDefaultPin();
        Auth::requirePin();

        $this->findTypeOrFail($id);
        $nombre = trim((string) ($_POST['name'] ?? ''));

        if ($nombre === '') {
            flash('error', 'El nombre no puede estar vacío.');
            redirect("catalogs/types/{$id}/edit");
        }

        if ($this->catalog->existeNombre('document_types', $nombre, $id)) {
            flash('error', 'Ya existe un tipo con ese nombre.');
            redirect("catalogs/types/{$id}/edit");
        }

        $this->catalog->actualizar('document_types', $id, $nombre);
        Logger::write('catalog_type_updated', "Tipo de documento actualizado (ID {$id}): {$nombre}");
        flash('success', 'Tipo actualizado correctamente.');
        redirect('catalogs');
    }

    // Elimina un tipo del catálogo
    public function deleteType(int $id): void
    {
        Auth::requireAdmin();
        Csrf::verify();
        Auth::blockIfDefaultPin();
        Auth::requirePin();

        $entry = $this->findTypeOrFail($id);
        $this->catalog->eliminar('document_types', $id);
        Logger::write('catalog_type_deleted', "Tipo de documento eliminado: {$entry['name']}");
        flash('success', "Tipo «{$entry['name']}» eliminado del catálogo.");
        redirect('catalogs');
    }

    /* ── Remitentes ── */

    // Agrega un nuevo remitente al catálogo
    public function storeSender(): void
    {
        Auth::requireAdmin();
        Csrf::verify();
        Auth::blockIfDefaultPin();
        Auth::requirePin();

        $nombre = trim((string) ($_POST['name'] ?? ''));

        if ($nombre === '') {
            flash('error', 'El nombre del remitente no puede estar vacío.');
            redirect('catalogs');
        }

        if ($this->catalog->existeNombre('document_senders', $nombre)) {
            flash('error', 'Ya existe un remitente con ese nombre.');
            redirect('catalogs');
        }

        $this->catalog->crear('document_senders', $nombre);
        Logger::write('catalog_sender_created', "Remitente creado: {$nombre}");
        flash('success', "Remitente «{$nombre}» agregado al catálogo.");
        redirect('catalogs');
    }

    // Muestra el formulario de edición de un remitente
    public function editSender(int $id): void
    {
        Auth::requireAdmin();
        $entry = $this->findSenderOrFail($id);
        $this->view('catalogs/edit', [
            'title'    => 'Editar remitente',
            'entry'    => $entry,
            'action'   => url("catalogs/senders/{$id}/update"),
            'backLabel'=> 'Remitente',
        ]);
    }

    // Guarda los cambios de un remitente existente
    public function updateSender(int $id): void
    {
        Auth::requireAdmin();
        Csrf::verify();
        Auth::blockIfDefaultPin();
        Auth::requirePin();

        $this->findSenderOrFail($id);
        $nombre = trim((string) ($_POST['name'] ?? ''));

        if ($nombre === '') {
            flash('error', 'El nombre no puede estar vacío.');
            redirect("catalogs/senders/{$id}/edit");
        }

        if ($this->catalog->existeNombre('document_senders', $nombre, $id)) {
            flash('error', 'Ya existe un remitente con ese nombre.');
            redirect("catalogs/senders/{$id}/edit");
        }

        $this->catalog->actualizar('document_senders', $id, $nombre);
        Logger::write('catalog_sender_updated', "Remitente actualizado (ID {$id}): {$nombre}");
        flash('success', 'Remitente actualizado correctamente.');
        redirect('catalogs');
    }

    // Elimina un remitente del catálogo
    public function deleteSender(int $id): void
    {
        Auth::requireAdmin();
        Csrf::verify();
        Auth::blockIfDefaultPin();
        Auth::requirePin();

        $entry = $this->findSenderOrFail($id);
        $this->catalog->eliminar('document_senders', $id);
        Logger::write('catalog_sender_deleted', "Remitente eliminado: {$entry['name']}");
        flash('success', "Remitente «{$entry['name']}» eliminado del catálogo.");
        redirect('catalogs');
    }

    /* ── Ayudantes privados ── */

    private function findTypeOrFail(int $id): array
    {
        $entry = $this->catalog->buscar('document_types', $id);
        if (!$entry) {
            http_response_code(404);
            $this->view('errors/404');
            exit;
        }
        return $entry;
    }

    private function findSenderOrFail(int $id): array
    {
        $entry = $this->catalog->buscar('document_senders', $id);
        if (!$entry) {
            http_response_code(404);
            $this->view('errors/404');
            exit;
        }
        return $entry;
    }
}
