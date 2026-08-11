/*
 * Archivo: public/assets/js/app.js
 * Autor:   C3r0d4y
 *
 * Comportamientos interactivos de ARCANUM:
 *
 *  1. Modal de confirmación unificado con PIN de seguridad.
 *     Formularios con data-confirm muestran confirmación antes de enviar.
 *     Formularios con data-pin-required solicitan PIN al usuario.
 *     Si el formulario tiene ambos atributos: confirmación → PIN → envío.
 *
 *  2. Validación del tipo de archivo al seleccionar un PDF.
 *  3. Envío automático del formulario de filtros.
 *  4. Vista previa del PDF seleccionado antes de guardar.
 *  5. Vista previa de la foto de perfil/avatar al seleccionar.
 *  6. Campos de fecha: el calendario se abre al hacer clic en el campo.
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ── Utilidades ── */

    /* URL base para el endpoint de verificación de PIN */
    const PIN_VERIFY_URL = (function () {
        const base = document.querySelector('base')?.href ||
            window.location.origin + (window.location.pathname.split('/').slice(0, -1).join('/'));
        // Construye la URL tomando el origen + la ruta hasta /arcanum/
        const m = window.location.pathname.match(/^(\/arcanum\/?)/);
        return window.location.origin + (m ? m[1] : '/') + 'pin/verify';
    })();

    /* Lee el token CSRF de la meta etiqueta */
    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    /* ── Modal de confirmación (reutilizable) ── */
    const modalConfirm       = document.getElementById('modal-confirm');
    const modalConfirmMsg    = document.getElementById('modal-confirm-msg');
    const modalConfirmOk     = document.getElementById('modal-confirm-ok');
    const modalConfirmCancel = document.getElementById('modal-confirm-cancel');

    function showConfirmModal(message, onConfirm) {
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
            modalConfirm.removeEventListener('click', fondo);
            document.removeEventListener('keydown', escape);
        }
        function aceptar()    { cerrar(); onConfirm(); }
        function fondo(e)     { if (e.target === modalConfirm) cerrar(); }
        function escape(e)    { if (e.key === 'Escape') cerrar(); }

        if (modalConfirmOk)     { modalConfirmOk.addEventListener('click', aceptar); }
        if (modalConfirmCancel) { modalConfirmCancel.addEventListener('click', cerrar); }
        modalConfirm.addEventListener('click', fondo);
        document.addEventListener('keydown', escape);
    }

    /* ── Modal de PIN ── */
    const modalPin   = document.getElementById('modal-pin');
    const pinInput   = document.getElementById('pin-input');
    const pinSubmit  = document.getElementById('pin-submit');
    const pinError   = document.getElementById('pin-error');
    const pinClose   = document.getElementById('pin-modal-close');

    function showPinModal(onSuccess) {
        if (!modalPin) {
            /* Sin modal disponible: no bloquear (el servidor valida de todas formas) */
            onSuccess();
            return;
        }
        if (pinInput)  { pinInput.value = ''; }
        if (pinError)  { pinError.style.display = 'none'; pinError.textContent = ''; }
        modalPin.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(() => pinInput?.focus(), 80);

        function cerrar() {
            modalPin.style.display = 'none';
            document.body.style.overflow = '';
            if (pinSubmit) { pinSubmit.removeEventListener('click', verificar); }
            if (pinClose)  { pinClose.removeEventListener('click', cerrar); }
            if (pinInput)  { pinInput.removeEventListener('keydown', tecla); }
            document.removeEventListener('keydown', escape);
        }

        async function verificar() {
            const pin = pinInput?.value?.trim() || '';
            if (!pin) {
                mostrarError('Ingresa tu PIN.');
                return;
            }
            pinSubmit.disabled = true;
            pinSubmit.textContent = '...';

            try {
                const res  = await fetch(PIN_VERIFY_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: '_token=' + encodeURIComponent(csrfToken()) + '&pin=' + encodeURIComponent(pin),
                    credentials: 'same-origin',
                });
                const data = await res.json();

                if (data.ok) {
                    cerrar();
                    onSuccess();
                } else if (data.locked) {
                    /* Cuenta bloqueada por PIN fallidos: el servidor ya cerró
                       la sesión; se muestra el aviso y se regresa al login */
                    mostrarError(data.error || 'Cuenta bloqueada. Sesión cerrada.');
                    setTimeout(function () {
                        window.location.href = window.location.origin +
                            (window.location.pathname.match(/^(\/arcanum\/?)/)?.[1] || '/') + 'login';
                    }, 2500);
                } else if (data.setup) {
                    /* PIN no configurado: ir a perfil */
                    cerrar();
                    window.location.href = window.location.origin +
                        window.location.pathname.match(/^(\/arcanum\/?)/)?.[1] + 'profile';
                } else {
                    mostrarError(data.error || 'PIN incorrecto.');
                    if (pinInput) { pinInput.value = ''; pinInput.focus(); }
                }
            } catch {
                mostrarError('Error de red. Intenta de nuevo.');
            } finally {
                if (pinSubmit) {
                    pinSubmit.disabled = false;
                    pinSubmit.textContent = 'Verificar';
                }
            }
        }

        function mostrarError(msg) {
            if (!pinError) { return; }
            pinError.textContent = msg;
            pinError.style.display = 'block';
        }

        function tecla(e) { if (e.key === 'Enter') verificar(); }
        function escape(e) { if (e.key === 'Escape') cerrar(); }

        if (pinSubmit) { pinSubmit.addEventListener('click', verificar); }
        if (pinClose)  { pinClose.addEventListener('click', cerrar); }
        if (pinInput)  { pinInput.addEventListener('keydown', tecla); }
        document.addEventListener('keydown', escape);
    }

    /* ── Pipeline unificado: confirmación → PIN → envío ── */
    document.querySelectorAll('form[data-confirm], form[data-pin-required]').forEach((form) => {
        const needsConfirm = form.hasAttribute('data-confirm');
        const needsPin     = form.hasAttribute('data-pin-required');

        form.addEventListener('submit', (e) => {
            /* Si ya pasó por todo el pipeline, dejar pasar */
            if (form.dataset.formReady === '1') {
                delete form.dataset.formReady;
                return;
            }
            e.preventDefault();

            /* Función final: marcar y reenviar */
            const proceed = () => {
                form.dataset.formReady = '1';
                form.requestSubmit();
            };

            if (needsConfirm && needsPin) {
                showConfirmModal(
                    form.getAttribute('data-confirm') || '¿Confirmar operación?',
                    () => showPinModal(proceed)
                );
            } else if (needsConfirm) {
                showConfirmModal(
                    form.getAttribute('data-confirm') || '¿Confirmar operación?',
                    proceed
                );
            } else {
                showPinModal(proceed);
            }
        });
    });

    /* ---------------------------------------------------------------
     * 2. Validación del tipo de archivo al seleccionar un PDF
     * --------------------------------------------------------------- */
    document.querySelectorAll('input[type="file"][accept="application/pdf"]').forEach((input) => {
        input.addEventListener('change', () => {
            const file = input.files[0];
            if (!file) return;
            if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                window.alert('Seleccione un archivo PDF valido.');
                input.value = '';
            }
        });
    });

    /* ---------------------------------------------------------------
     * 3. Filtros con envío automático (auto-filter)
     * --------------------------------------------------------------- */
    document.querySelectorAll('form[data-auto-filter]').forEach((form) => {
        const fields = form.querySelectorAll('input, select');
        let timer = null;
        const submitFilters = () => {
            window.clearTimeout(timer);
            timer = window.setTimeout(() => form.requestSubmit(), 450);
        };
        fields.forEach((field) => {
            const eventName = field.tagName === 'SELECT' || field.type === 'date' ? 'change' : 'input';
            field.addEventListener(eventName, submitFilters);
        });
    });

    /* ---------------------------------------------------------------
     * 4. Vista previa del PDF al seleccionar el archivo
     * --------------------------------------------------------------- */
    document.querySelectorAll('[data-pdf-preview-form]').forEach((form) => {
        const input      = form.querySelector('[data-pdf-preview-input]');
        const preview    = form.querySelector('[data-pdf-preview]');
        const emptyState = form.querySelector('[data-pdf-preview-empty]');
        const title      = form.querySelector('[data-pdf-preview-title]');
        const trigger    = form.querySelector('[data-pdf-pick]');
        const fileNameEl = form.querySelector('[data-file-name]');

        let objectUrl = null;
        if (!input || !preview) return;

        if (trigger) { trigger.addEventListener('click', () => input.click()); }
        if (fileNameEl) {
            input.addEventListener('change', () => {
                fileNameEl.textContent = input.files[0]?.name ?? 'Sin archivo seleccionado';
            });
        }

        input.addEventListener('change', () => {
            const file = input.files[0];
            if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = null; }
            if (!file || input.value === '') return;
            if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) return;

            objectUrl = URL.createObjectURL(file);
            const frame = preview.parentElement;
            const prev  = frame.querySelector('.pdf-dyn-obj');
            if (prev) prev.remove();

            const dynObj = document.createElement('object');
            dynObj.className = 'pdf-preview-object pdf-dyn-obj';
            dynObj.type = 'application/pdf';
            dynObj.setAttribute('data', objectUrl + '#navpanes=0');
            frame.appendChild(dynObj);

            preview.style.display = 'none';
            if (emptyState) emptyState.style.display = 'none';
            if (title) title.textContent = file.name;
        });

        window.addEventListener('beforeunload', () => {
            if (objectUrl) URL.revokeObjectURL(objectUrl);
        });
    });

    /* ---------------------------------------------------------------
     * 5. Vista previa de la foto de perfil/avatar
     * --------------------------------------------------------------- */
    document.querySelectorAll('[data-avatar-form]').forEach((form) => {
        const input   = form.querySelector('[data-avatar-input]');
        const img     = form.querySelector('[data-avatar-preview]');
        const nameEl  = form.querySelector('[data-avatar-name]');
        const trigger = form.querySelector('[data-avatar-pick]');

        if (!input || !img) return;
        if (trigger) { trigger.addEventListener('click', () => input.click()); }

        let avatarUrl = null;
        input.addEventListener('change', () => {
            const file = input.files[0];
            if (nameEl) nameEl.textContent = file ? file.name : 'Sin foto asignada';
            if (!file || !file.type.startsWith('image/')) return;
            if (avatarUrl) URL.revokeObjectURL(avatarUrl);
            avatarUrl = URL.createObjectURL(file);
            img.src = avatarUrl;
        });

        window.addEventListener('beforeunload', () => {
            if (avatarUrl) URL.revokeObjectURL(avatarUrl);
        });
    });

    /* ---------------------------------------------------------------
     * 6. Campos de fecha: abrir el calendario al hacer clic en
     *    cualquier parte del campo, no solo en el icono pequeño
     * --------------------------------------------------------------- */
    document.querySelectorAll('input[type="date"]').forEach((field) => {
        field.addEventListener('click', () => {
            // showPicker() abre el calendario nativo en navegadores modernos;
            // si el navegador no lo soporta, el icono nativo sigue funcionando
            if (typeof field.showPicker === 'function') {
                try {
                    field.showPicker();
                } catch (e) {
                    // Algunos navegadores lo bloquean sin gesto del usuario; se ignora
                }
            }
        });
    });
});
