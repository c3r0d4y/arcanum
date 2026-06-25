<?php

/*
 * Archivo: app/controllers/DocumentsController.php
 * Autor:   C3r0d4y
 *
 * Controlador de documentos con cifrado AES-256-GCM de archivos.
 * Los PDF se cifran al subirse y se descifran en memoria al servirse;
 * nunca existe una copia en claro en el sistema de archivos.
 */

declare(strict_types=1);

final class DocumentsController extends Controller
{
    private Document $documents;

    public function __construct()
    {
        $this->documents = new Document();
    }

    public function index(): void
    {
        Auth::requireLogin();

        $filters = [
            'number'  => trim((string) ($_GET['number']  ?? '')),
            'subject' => trim((string) ($_GET['subject'] ?? '')),
            'date'    => trim((string) ($_GET['date']    ?? '')),
            'type'    => trim((string) ($_GET['type']    ?? '')),
        ];

        $this->view('documents/index', [
            'title'     => 'Expedientes',
            'documents' => $this->documents->search($filters),
            'types'     => $this->documents->types(),
            'filters'   => $filters,
        ]);
    }

    public function create(): void
    {
        Auth::requireLogin();
        $this->view('documents/form', [
            'title'        => 'Nuevo expediente',
            'document'     => null,
            'types'        => $this->documents->types(),
            'action'       => url('documents/store'),
            'requiresFile' => true,
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        Csrf::verify();

        $data = $this->validatedData();
        $file = $this->validatedPdf(true);

        if (!$data || !$file) {
            redirect('documents/create');
        }

        $payload = array_merge($data, $file, ['created_by' => Auth::id()]);
        $this->documents->create($payload);
        Logger::write('document_created', 'Expediente creado: ' . $data['number']);
        flash('success', 'Expediente registrado y cifrado correctamente.');
        redirect('documents');
    }

    public function show(int $id): void
    {
        Auth::requireLogin();
        $document = $this->findOrFail($id);
        $this->view('documents/show', [
            'title'    => 'Detalle de expediente',
            'document' => $document,
        ]);
    }

    public function edit(int $id): void
    {
        Auth::requireLogin();
        $document = $this->findOrFail($id);
        $this->view('documents/form', [
            'title'        => 'Editar expediente',
            'document'     => $document,
            'types'        => $this->documents->types(),
            'action'       => url('documents/' . $id . '/update'),
            'requiresFile' => false,
        ]);
    }

    public function update(int $id): void
    {
        Auth::requireLogin();
        Csrf::verify();

        $document = $this->findOrFail($id);
        $data     = $this->validatedData();

        if (!$data) {
            redirect('documents/' . $id . '/edit');
        }

        $file = $this->validatedPdf(false);

        if ($file === false) {
            redirect('documents/' . $id . '/edit');
        }

        if (is_array($file)) {
            // Elimina el archivo cifrado anterior del disco
            $oldPath = STORAGE_PATH . '/' . $document['file_name'];
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
            $data = array_merge($data, $file);
        }

        $this->documents->update($id, $data);
        Logger::write('document_updated', 'Expediente actualizado: ' . $data['number']);
        flash('success', 'Expediente actualizado correctamente.');
        redirect('documents');
    }

    public function delete(int $id): void
    {
        Auth::requireLogin();
        Csrf::verify();

        $document = $this->findOrFail($id);
        $path     = STORAGE_PATH . '/' . $document['file_name'];

        $this->documents->delete($id);

        if (is_file($path)) {
            unlink($path);
        }

        Logger::write('document_deleted', 'Expediente eliminado: ' . $document['number']);
        flash('success', 'Expediente eliminado.');
        redirect('documents');
    }

    /*
     * Sirve el PDF descifrado en memoria — nunca escribe el claro en disco.
     * Archivos legacy (.pdf sin cifrado) se sirven directamente.
     */
    public function file(int $id): void
    {
        Auth::requireLogin();
        $document = $this->findOrFail($id);
        $path     = STORAGE_PATH . '/' . $document['file_name'];

        if (!is_file($path)) {
            http_response_code(404);
            exit('Archivo no encontrado.');
        }

        Logger::write('document_viewed', 'PDF visualizado: ' . $document['number']);

        // Cabeceras anti-caché para documentos clasificados
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' .
            rawurlencode(basename((string) $document['original_file_name'])) . '"');
        header('X-Content-Type-Options: nosniff');
        // Permite embeber el PDF en <object> e <iframe> del mismo origen
        header('X-Frame-Options: SAMEORIGIN');

        if (str_ends_with((string) $document['file_name'], '.enc')) {
            // Descifra en memoria y transmite sin tocar disco
            $content = Crypto::decryptFile((string) file_get_contents($path));
            header('Content-Length: ' . strlen($content));
            echo $content;
        } else {
            // Archivo legado sin cifrado
            header('Content-Length: ' . filesize($path));
            readfile($path);
        }

        exit;
    }

    /* ── Validación privada ── */

    private function validatedData(): ?array
    {
        $data = [
            'number'        => trim((string) ($_POST['number']        ?? '')),
            'subject'       => trim((string) ($_POST['subject']       ?? '')),
            'document_date' => trim((string) ($_POST['document_date'] ?? '')),
            'sender'        => trim((string) ($_POST['sender']        ?? '')),
            'type'          => trim((string) ($_POST['type']          ?? '')),
        ];

        $_SESSION['old'] = $data;

        if (in_array('', $data, true)) {
            flash('error', 'Todos los campos son obligatorios.');
            return null;
        }

        if (!in_array($data['type'], $this->documents->types(), true)) {
            flash('error', 'Tipo de documento inválido.');
            return null;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['document_date'])) {
            flash('error', 'Fecha inválida.');
            return null;
        }

        return $data;
    }

    /*
     * Valida el PDF subido, lo cifra con AES-256-GCM y lo guarda
     * en STORAGE_PATH con extensión .enc. Nunca queda el claro en disco.
     */
    private function validatedPdf(bool $required): array|false|null
    {
        $upload = $_FILES['pdf'] ?? null;

        if (!$upload || ($upload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            if ($required) {
                flash('error', 'Debe adjuntar un archivo PDF.');
                return false;
            }
            return null;
        }

        if ($upload['error'] !== UPLOAD_ERR_OK) {
            flash('error', $this->uploadErrorMessage((int) $upload['error']));
            return false;
        }

        if (($upload['size'] ?? 0) > 15 * 1024 * 1024) {
            flash('error', 'El PDF no debe exceder 15 MB.');
            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($upload['tmp_name']);
        if ($mime !== 'application/pdf') {
            flash('error', 'Solo se permiten archivos PDF válidos.');
            return false;
        }

        if (!is_dir(STORAGE_PATH)) {
            mkdir(STORAGE_PATH, 0750, true);
        }

        // Lee el PDF en claro desde el directorio temporal de PHP
        $plaintext = file_get_contents($upload['tmp_name']);
        if ($plaintext === false) {
            flash('error', 'No fue posible leer el archivo temporal.');
            return false;
        }

        // Cifra con AES-256-GCM y guarda como .enc
        $encName = bin2hex(random_bytes(16)) . '.enc';
        $saved   = file_put_contents(
            STORAGE_PATH . '/' . $encName,
            Crypto::encryptFile($plaintext)
        );

        if ($saved === false) {
            flash('error', 'No fue posible guardar el expediente cifrado.');
            return false;
        }

        return [
            'file_name'          => $encName,
            'original_file_name' => basename((string) $upload['name']),
            'mime_type'          => $mime,
            'file_size'          => (int) $upload['size'],
        ];
    }

    private function uploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'El PDF excede el límite permitido por el servidor.',
            UPLOAD_ERR_PARTIAL                         => 'El PDF se subió de forma incompleta. Intente nuevamente.',
            UPLOAD_ERR_NO_TMP_DIR                      => 'El servidor no tiene directorio temporal configurado.',
            UPLOAD_ERR_CANT_WRITE                      => 'El servidor no pudo escribir el archivo temporal.',
            UPLOAD_ERR_EXTENSION                       => 'Una extensión de PHP bloqueó la carga del PDF.',
            default                                    => 'No fue posible subir el PDF.',
        };
    }

    private function findOrFail(int $id): array
    {
        $document = $this->documents->find($id);
        if (!$document) {
            http_response_code(404);
            $this->view('errors/404');
            exit;
        }
        return $document;
    }
}
