/*
 * Archivo: public/assets/js/app.js
 * Autor:   C3r0d4y
 *
 * ¿Qué hace este archivo?
 * Agrega comportamientos interactivos a la interfaz del usuario:
 *
 *  1. Confirmación antes de eliminar registros.
 *  2. Validación en tiempo real del tipo de archivo al seleccionar PDF.
 *  3. Envío automático del formulario de filtros al cambiar cualquier campo.
 *  4. Vista previa del PDF seleccionado antes de guardar el formulario.
 *
 * Todo el código se envuelve en DOMContentLoaded para asegurarse de que
 * el HTML esté completamente cargado antes de buscar elementos en la página.
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ---------------------------------------------------------------
     * 1. Confirmación personalizada antes de enviar formularios peligrosos
     *
     * Busca todos los formularios que tengan el atributo data-confirm.
     * En lugar del diálogo nativo del navegador, muestra el modal táctico
     * #modal-confirm con el mensaje del atributo data-confirm.
     *
     * Al confirmar, marca el formulario con data-confirmed="1" y lo reenvía.
     * En el segundo disparo el listener detecta la marca y deja pasar el envío.
     *
     * Ejemplo de uso en HTML:
     * <form data-confirm="¿Eliminar este documento?"> ... </form>
     * --------------------------------------------------------------- */
    const modalConfirm       = document.getElementById('modal-confirm');
    const modalConfirmMsg    = document.getElementById('modal-confirm-msg');
    const modalConfirmOk     = document.getElementById('modal-confirm-ok');
    const modalConfirmCancel = document.getElementById('modal-confirm-cancel');

    // Muestra el modal y llama a onConfirm() si el usuario acepta
    function showConfirmModal(message, onConfirm) {

        // Fallback al diálogo nativo si el modal no está en el DOM
        if (!modalConfirm) {
            if (window.confirm(message)) { onConfirm(); }
            return;
        }

        if (modalConfirmMsg) { modalConfirmMsg.textContent = message; }
        modalConfirm.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        function cerrar() {
            modalConfirm.style.display = 'none';
            document.body.style.overflow = '';
            if (modalConfirmOk)     { modalConfirmOk.removeEventListener('click', aceptar); }
            if (modalConfirmCancel) { modalConfirmCancel.removeEventListener('click', cerrar); }
            modalConfirm.removeEventListener('click', clic_fondo);
            document.removeEventListener('keydown', tecla_escape);
        }

        function aceptar()       { cerrar(); onConfirm(); }
        function clic_fondo(e)   { if (e.target === modalConfirm) { cerrar(); } }
        function tecla_escape(e) { if (e.key === 'Escape') { cerrar(); } }

        if (modalConfirmOk)     { modalConfirmOk.addEventListener('click', aceptar); }
        if (modalConfirmCancel) { modalConfirmCancel.addEventListener('click', cerrar); }
        modalConfirm.addEventListener('click', clic_fondo);
        document.addEventListener('keydown', tecla_escape);
    }

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {

            // Segunda pasada: el usuario ya confirmó, se deja pasar el envío
            if (form.dataset.confirmed === '1') {
                delete form.dataset.confirmed;
                return;
            }

            // Primera pasada: interceptar y mostrar el modal de confirmación
            event.preventDefault();
            const message = form.getAttribute('data-confirm') || '¿Confirmar operación?';

            showConfirmModal(message, () => {
                // Marca el formulario y lo reenvía para que pase el listener
                form.dataset.confirmed = '1';
                form.requestSubmit();
            });
        });
    });

    /* ---------------------------------------------------------------
     * 2. Validación del tipo de archivo al seleccionar un PDF
     *
     * Busca todos los campos <input type="file" accept="application/pdf">.
     * Cuando el usuario selecciona un archivo, verifica que sea realmente
     * un PDF (por su tipo MIME o por su extensión .pdf).
     * Si no es PDF, muestra una alerta y limpia el campo para que el
     * usuario seleccione un archivo correcto.
     *
     * Esta validación es del lado del cliente (navegador) y es
     * complementaria a la validación del servidor en PHP.
     * --------------------------------------------------------------- */
    document.querySelectorAll('input[type="file"][accept="application/pdf"]').forEach((input) => {
        input.addEventListener('change', () => {
            const file = input.files[0];

            // Si no hay archivo seleccionado, no hacemos nada
            if (!file) {
                return;
            }

            // Verifica por tipo MIME y por extensión del nombre del archivo
            if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                window.alert('Seleccione un archivo PDF valido.');
                input.value = ''; // Limpia el campo para forzar una nueva selección
            }
        });
    });

    /* ---------------------------------------------------------------
     * 3. Filtros con envío automático (auto-filter)
     *
     * Busca formularios con el atributo data-auto-filter.
     * Cuando el usuario escribe en un campo o cambia un selector,
     * el formulario se envía automáticamente después de 450ms.
     *
     * El retraso de 450ms evita enviar el formulario con cada tecla
     * presionada; espera a que el usuario deje de escribir.
     *
     * Se usa para que la lista de documentos se actualice en tiempo real
     * mientras el usuario escribe en los campos de búsqueda.
     * --------------------------------------------------------------- */
    document.querySelectorAll('form[data-auto-filter]').forEach((form) => {
        const fields = form.querySelectorAll('input, select');
        let timer = null; // Guarda el temporizador para poder cancelarlo

        const submitFilters = () => {
            // Cancela cualquier envío pendiente antes de iniciar uno nuevo
            window.clearTimeout(timer);
            timer = window.setTimeout(() => {
                form.requestSubmit(); // Envía el formulario respetando validaciones HTML
            }, 450); // Espera 450ms desde el último cambio antes de enviar
        };

        fields.forEach((field) => {
            /* Para selectores y campos de fecha se usa el evento 'change'
             * (solo se dispara cuando el valor cambia al salir del campo).
             * Para campos de texto se usa 'input'
             * (se dispara con cada tecla presionada). */
            const eventName = field.tagName === 'SELECT' || field.type === 'date' ? 'change' : 'input';
            field.addEventListener(eventName, submitFilters);
        });
    });

    /* ---------------------------------------------------------------
     * 4. Vista previa del PDF al seleccionar el archivo
     *
     * Busca formularios con el atributo data-pdf-preview-form.
     * Cuando el usuario selecciona un archivo PDF, crea una URL temporal
     * del archivo en memoria (blob URL) y la carga en el elemento <object>
     * que actúa como visor de PDF.
     *
     * También actualiza el texto del título con el nombre del archivo.
     * Cuando se navega fuera de la página, libera la memoria del blob URL.
     *
     * Se usa en el formulario de creación/edición de documentos
     * para que el usuario pueda verificar el PDF antes de guardarlo.
     * --------------------------------------------------------------- */
    document.querySelectorAll('[data-pdf-preview-form]').forEach((form) => {
        // Busca los elementos clave dentro del formulario usando data-attributes
        const input      = form.querySelector('[data-pdf-preview-input]');   // El <input type="file">
        const preview    = form.querySelector('[data-pdf-preview]');          // El <object> visor de PDF
        const emptyState = form.querySelector('[data-pdf-preview-empty]');    // Mensaje "sin PDF"
        const title      = form.querySelector('[data-pdf-preview-title]');    // Título del archivo

        let objectUrl = null; // Guarda la URL blob actual para poder limpiarla después

        // Si no encuentra los elementos necesarios, no hace nada
        if (!input || !preview) {
            return;
        }

        /* Cuando el usuario selecciona un archivo en el campo PDF */
        input.addEventListener('change', () => {
            const file = input.files[0];

            // Libera la URL blob anterior para liberar memoria del navegador
            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
                objectUrl = null;
            }

            // Si el campo fue limpiado o no hay archivo válido, no continua
            if (!file || input.value === '') {
                return;
            }

            // Verifica que el archivo sea PDF antes de intentar mostrarlo
            if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                return;
            }

            /* Crea una URL temporal que apunta al archivo en memoria del navegador.
             * Esta URL solo existe mientras la página esté abierta. */
            objectUrl = URL.createObjectURL(file);

            // Carga el PDF en el elemento <object> asignando su URL
            preview.setAttribute('data', objectUrl);

            // Quita la clase que lo hacía invisible
            preview.classList.remove('is-empty');

            // Oculta el mensaje "Seleccione un PDF para visualizarlo aquí"
            if (emptyState) {
                emptyState.hidden = true;
            }

            // Actualiza el título con el nombre real del archivo seleccionado
            if (title) {
                title.textContent = file.name;
            }
        });

        /* Cuando el usuario navega fuera de la página (beforeunload),
         * se libera la URL blob para evitar fugas de memoria. */
        window.addEventListener('beforeunload', () => {
            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
            }
        });
    });
});
