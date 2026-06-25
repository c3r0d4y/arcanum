<?php /* Pie de página: cierra main, modal de diagrama, JS y reloj */ ?>
</main>

<!-- ══════════════════════════════════════════════════════
     MODAL: Diagrama de Cifrado AES-256-GCM — ARCANUM
     ══════════════════════════════════════════════════════ -->
<div id="modal-diagram" style="display:none;position:fixed;inset:0;z-index:9999;overflow-y:auto;background:rgba(0,0,0,.82);backdrop-filter:blur(4px)">
<div style="max-width:960px;margin:32px auto;padding:0 16px 48px">

    <!-- Cabecera del modal -->
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 24px;background:rgba(0,0,0,.6);border-bottom:1px solid rgba(120,155,210,.28);margin-bottom:0">
        <div style="display:flex;align-items:center;gap:12px">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="2" stroke-linecap="round">
                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <span style="font:700 14px/1 ui-monospace,monospace;letter-spacing:.18em;text-transform:uppercase;color:#fff">ARCANUM</span>
            <span style="font:600 10px/1 ui-monospace,monospace;letter-spacing:.14em;color:#5e7498">/&nbsp; ARQUITECTURA DE CIFRADO AES-256-GCM</span>
        </div>
        <button id="btn-close-diagram" style="background:none;border:1px solid rgba(120,155,210,.28);color:#5e7498;cursor:pointer;padding:6px 12px;font:600 10px/1 ui-monospace,monospace;letter-spacing:.1em">✕ CERRAR</button>
    </div>

    <!-- Contenido del modal -->
    <div style="background:#0a0e1a;border:1px solid rgba(120,155,210,.14);border-top:none;padding:28px 28px 32px">

        <!-- Descripción general -->
        <p style="font:13.5px/1.8 ui-sans-serif,sans-serif;color:#8aa4c0;margin-bottom:28px;max-width:780px">
            <strong style="color:#d8e0ef">ARCANUM</strong> implementa cifrado simétrico
            <strong style="color:#4a82be">AES-256-GCM</strong> (Advanced Encryption Standard en modo Galois/Counter Mode)
            sobre cada expediente. Cada operación genera un vector de inicialización (IV) aleatorio de 96 bits,
            produciendo un criptograma estadísticamente único incluso para documentos idénticos.
            El TAG de autenticación de 128 bits garantiza que cualquier alteración del archivo sea detectada antes de descifrar.
        </p>

        <!-- ── DIAGRAMA 1: Flujo de Cifrado ── -->
        <div style="margin-bottom:32px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <span style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.2em;color:#4ade80;text-transform:uppercase">&#9650; Flujo de Cifrado — Subida de Expediente</span>
                <div style="flex:1;height:1px;background:linear-gradient(90deg,rgba(74,222,128,.3),transparent)"></div>
            </div>

            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:0">
                <!-- Paso 1 -->
                <div style="background:#06090f;border:1px solid rgba(74,222,128,.25);padding:12px 14px;min-width:120px;text-align:center">
                    <div style="font:700 10px/1 ui-monospace,monospace;color:#4ade80;letter-spacing:.1em;margin-bottom:6px">01</div>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 6px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <div style="font:600 10px/1.4 ui-monospace,monospace;color:#d8e0ef">PDF<br>Usuario</div>
                </div>
                <div style="font:700 18px ui-monospace,monospace;color:rgba(74,222,128,.5);padding:0 4px">→</div>

                <!-- Paso 2 -->
                <div style="background:#06090f;border:1px solid rgba(74,222,128,.25);padding:12px 14px;min-width:130px;text-align:center">
                    <div style="font:700 10px/1 ui-monospace,monospace;color:#4ade80;letter-spacing:.1em;margin-bottom:6px">02</div>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 6px"><polyline points="20 6 9 17 4 12"/></svg>
                    <div style="font:600 10px/1.4 ui-monospace,monospace;color:#d8e0ef">Validación<br>MIME + Tamaño</div>
                </div>
                <div style="font:700 18px ui-monospace,monospace;color:rgba(74,222,128,.5);padding:0 4px">→</div>

                <!-- Paso 3 -->
                <div style="background:#06090f;border:1px solid rgba(74,222,128,.25);padding:12px 14px;min-width:130px;text-align:center">
                    <div style="font:700 10px/1 ui-monospace,monospace;color:#4ade80;letter-spacing:.1em;margin-bottom:6px">03</div>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 6px"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                    <div style="font:600 10px/1.4 ui-monospace,monospace;color:#d8e0ef">Leer bytes<br>en memoria</div>
                </div>
                <div style="font:700 18px ui-monospace,monospace;color:rgba(74,222,128,.5);padding:0 4px">→</div>

                <!-- Paso 4 -->
                <div style="background:rgba(74,130,190,.08);border:1px solid rgba(74,130,190,.35);padding:12px 14px;min-width:140px;text-align:center">
                    <div style="font:700 10px/1 ui-monospace,monospace;color:#4a82be;letter-spacing:.1em;margin-bottom:6px">04</div>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 6px"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <div style="font:600 10px/1.4 ui-monospace,monospace;color:#d8e0ef">Generar IV<br><span style="color:#4a82be">96-bit aleatorio</span></div>
                </div>
                <div style="font:700 18px ui-monospace,monospace;color:rgba(74,130,190,.5);padding:0 4px">→</div>

                <!-- Paso 5 — núcleo de cifrado -->
                <div style="background:rgba(74,130,190,.13);border:2px solid #4a82be;padding:12px 16px;min-width:150px;text-align:center;position:relative">
                    <div style="font:700 10px/1 ui-monospace,monospace;color:#4a82be;letter-spacing:.1em;margin-bottom:6px">05</div>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 6px"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <div style="font:700 11px/1.4 ui-monospace,monospace;color:#fff">AES-256-GCM<br><span style="color:#4a82be;font-size:9px">Clave 256-bit</span></div>
                    <div style="position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:#4a82be;color:#fff;font:700 8px/1 ui-monospace,monospace;padding:2px 7px;letter-spacing:.1em">NÚCLEO</div>
                </div>
                <div style="font:700 18px ui-monospace,monospace;color:rgba(74,130,190,.5);padding:0 4px">→</div>

                <!-- Paso 6 -->
                <div style="background:#06090f;border:1px solid rgba(184,147,90,.3);padding:12px 14px;min-width:130px;text-align:center">
                    <div style="font:700 10px/1 ui-monospace,monospace;color:#b8935a;letter-spacing:.1em;margin-bottom:6px">06</div>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#b8935a" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 6px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <div style="font:600 10px/1.4 ui-monospace,monospace;color:#d8e0ef">Guardar<br><span style="color:#b8935a">.enc en disco</span></div>
                </div>
            </div>

            <!-- Formato del archivo cifrado -->
            <div style="margin-top:12px;padding:10px 14px;background:#06090f;border:1px solid rgba(120,155,210,.14);font:10.5px/1.8 ui-monospace,monospace;color:#5e7498">
                <strong style="color:#4a82be">Formato binario .enc:</strong>
                &nbsp;
                <span style="color:#4ade80">IV [12 bytes]</span>
                &nbsp;+&nbsp;
                <span style="color:#f59e0b">TAG GCM [16 bytes]</span>
                &nbsp;+&nbsp;
                <span style="color:#d8e0ef">Ciphertext [N bytes]</span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <strong style="color:#b8935a">Metadatos BD:</strong>
                &nbsp;
                <span style="color:#d8e0ef">ENC:<span style="color:#4a82be">base64(IV + TAG + CT)</span></span>
            </div>
        </div>

        <!-- ── DIAGRAMA 2: Flujo de Descifrado ── -->
        <div style="margin-bottom:28px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <span style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.2em;color:#f87171;text-transform:uppercase">&#9660; Flujo de Descifrado — Visualización de Expediente</span>
                <div style="flex:1;height:1px;background:linear-gradient(90deg,rgba(248,113,113,.3),transparent)"></div>
            </div>

            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:0">
                <!-- Paso A -->
                <div style="background:#06090f;border:1px solid rgba(248,113,113,.25);padding:12px 14px;min-width:120px;text-align:center">
                    <div style="font:700 10px/1 ui-monospace,monospace;color:#f87171;letter-spacing:.1em;margin-bottom:6px">A</div>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 6px"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                    <div style="font:600 10px/1.4 ui-monospace,monospace;color:#d8e0ef">Solicitud<br>GET /file</div>
                </div>
                <div style="font:700 18px ui-monospace,monospace;color:rgba(248,113,113,.5);padding:0 4px">→</div>

                <!-- Paso B -->
                <div style="background:#06090f;border:1px solid rgba(248,113,113,.25);padding:12px 14px;min-width:130px;text-align:center">
                    <div style="font:700 10px/1 ui-monospace,monospace;color:#f87171;letter-spacing:.1em;margin-bottom:6px">B</div>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 6px"><path d="M12 2 4 6v5c0 5.5 3.3 9.7 8 11.5 4.7-1.8 8-6 8-11.5V6Z"/><polyline points="9 12 11 14 15 10"/></svg>
                    <div style="font:600 10px/1.4 ui-monospace,monospace;color:#d8e0ef">Verificar<br>sesión activa</div>
                </div>
                <div style="font:700 18px ui-monospace,monospace;color:rgba(248,113,113,.5);padding:0 4px">→</div>

                <!-- Paso C -->
                <div style="background:#06090f;border:1px solid rgba(248,113,113,.25);padding:12px 14px;min-width:130px;text-align:center">
                    <div style="font:700 10px/1 ui-monospace,monospace;color:#f87171;letter-spacing:.1em;margin-bottom:6px">C</div>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 6px"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                    <div style="font:600 10px/1.4 ui-monospace,monospace;color:#d8e0ef">Leer BD +<br>Descifrar campos</div>
                </div>
                <div style="font:700 18px ui-monospace,monospace;color:rgba(248,113,113,.5);padding:0 4px">→</div>

                <!-- Paso D -->
                <div style="background:#06090f;border:1px solid rgba(248,113,113,.25);padding:12px 14px;min-width:130px;text-align:center">
                    <div style="font:700 10px/1 ui-monospace,monospace;color:#f87171;letter-spacing:.1em;margin-bottom:6px">D</div>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 6px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    <div style="font:600 10px/1.4 ui-monospace,monospace;color:#d8e0ef">Leer archivo<br><span style="color:#f87171">.enc del disco</span></div>
                </div>
                <div style="font:700 18px ui-monospace,monospace;color:rgba(248,113,113,.5);padding:0 4px">→</div>

                <!-- Paso E — núcleo de descifrado -->
                <div style="background:rgba(74,130,190,.13);border:2px solid #4a82be;padding:12px 16px;min-width:150px;text-align:center;position:relative">
                    <div style="font:700 10px/1 ui-monospace,monospace;color:#4a82be;letter-spacing:.1em;margin-bottom:6px">E</div>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 6px"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><line x1="12" y1="16" x2="12" y2="16" stroke-width="3"/></svg>
                    <div style="font:700 11px/1.4 ui-monospace,monospace;color:#fff">AES-256-GCM<br><span style="color:#4ade80;font-size:9px">Verifica TAG</span></div>
                    <div style="position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:#4a82be;color:#fff;font:700 8px/1 ui-monospace,monospace;padding:2px 7px;letter-spacing:.1em">NÚCLEO</div>
                </div>
                <div style="font:700 18px ui-monospace,monospace;color:rgba(74,130,190,.5);padding:0 4px">→</div>

                <!-- Paso F -->
                <div style="background:#06090f;border:1px solid rgba(74,222,128,.25);padding:12px 14px;min-width:130px;text-align:center">
                    <div style="font:700 10px/1 ui-monospace,monospace;color:#4ade80;letter-spacing:.1em;margin-bottom:6px">F</div>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 6px"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <div style="font:600 10px/1.4 ui-monospace,monospace;color:#d8e0ef">Stream PDF<br><span style="color:#4ade80">al navegador</span></div>
                </div>
            </div>

            <div style="margin-top:12px;padding:10px 14px;background:rgba(74,222,128,.05);border:1px solid rgba(74,222,128,.18);font:10.5px/1.8 ui-monospace,monospace;color:#5e7498">
                <strong style="color:#4ade80">Garantía de integridad:</strong>
                &nbsp; El TAG GCM falla si el archivo fue alterado — el sistema rechaza el descifrado y no transmite nada.
                &nbsp;&nbsp;
                <strong style="color:#4ade80">Sin claro en disco:</strong>
                &nbsp; El PDF descifrado existe únicamente en RAM durante la transmisión HTTP.
            </div>
        </div>

        <!-- ── Tabla de parámetros técnicos ── -->
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                <span style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.2em;color:#b8935a;text-transform:uppercase">&#9632; Parámetros Criptográficos</span>
                <div style="flex:1;height:1px;background:linear-gradient(90deg,rgba(184,147,90,.3),transparent)"></div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1px;background:rgba(120,155,210,.14);border:1px solid rgba(120,155,210,.14)">
                <?php
                $params = [
                    ['Algoritmo',       'AES-256-GCM'],
                    ['Longitud de clave','256 bits (32 bytes)'],
                    ['IV / Nonce',      '96 bits (12 bytes) — aleatorio por operación'],
                    ['TAG de autenticación','128 bits (16 bytes)'],
                    ['Función PHP',     'openssl_encrypt() OPENSSL_RAW_DATA'],
                    ['Extensión cifrada','.enc (binario: IV + TAG + CT)'],
                    ['Campos en BD',    'ENC:base64(IV + TAG + CT)'],
                    ['Plaintext en disco','Nunca — descifrado solo en RAM'],
                ];
                foreach ($params as [$label, $val]): ?>
                <div style="background:#0a0e1a;padding:11px 14px">
                    <div style="font:600 9px/1 ui-monospace,monospace;letter-spacing:.14em;text-transform:uppercase;color:#2c3a52;margin-bottom:5px"><?= $label ?></div>
                    <div style="font:600 12px/1.4 ui-monospace,monospace;color:#d8e0ef"><?= $val ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div><!-- /contenido -->
</div><!-- /inner -->
</div><!-- /modal -->

<!-- ══════════════════════════════════════════════════════
     MODAL: Confirmación de operación destructiva
     ══════════════════════════════════════════════════════ -->
<div id="modal-confirm" style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.88);backdrop-filter:blur(4px);align-items:center;justify-content:center">
    <div style="max-width:440px;width:calc(100% - 32px);background:#0a0e1a;border:1px solid rgba(220,60,60,.35);box-shadow:0 0 60px rgba(0,0,0,.9)">

        <!-- Cabecera del modal -->
        <div style="display:flex;align-items:center;gap:12px;padding:16px 20px;background:rgba(0,0,0,.5);border-bottom:1px solid rgba(220,60,60,.25)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc3c3c" stroke-width="2" stroke-linecap="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <span style="font:700 11px/1 ui-monospace,monospace;letter-spacing:.18em;text-transform:uppercase;color:#dc3c3c">OPERACIÓN IRREVERSIBLE</span>
        </div>

        <!-- Mensaje y botones -->
        <div style="padding:24px 20px">
            <p id="modal-confirm-msg" style="font:13px/1.75 ui-monospace,monospace;color:#8aa4c0;margin:0 0 24px;word-break:break-word"></p>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <button id="modal-confirm-cancel" style="background:none;border:1px solid rgba(120,155,210,.3);color:#5e7498;cursor:pointer;padding:9px 20px;font:600 10px/1 ui-monospace,monospace;letter-spacing:.14em;text-transform:uppercase;transition:border-color .15s,color .15s">CANCELAR</button>
                <button id="modal-confirm-ok" style="background:rgba(180,40,40,.18);border:1px solid rgba(220,60,60,.6);color:#f87171;cursor:pointer;padding:9px 20px;font:700 10px/1 ui-monospace,monospace;letter-spacing:.14em;text-transform:uppercase;transition:background .15s,border-color .15s">ELIMINAR</button>
            </div>
        </div>

    </div>
</div>

<script src="<?= e(url('assets/js/app.min.js')) ?>"></script>
<script>
/* Reloj táctico en tiempo real */
(function(){
    function tick(){
        var p=function(n){return String(n).padStart(2,'0');};
        var d=new Date();
        var el=document.getElementById('clk');
        if(el) el.textContent=p(d.getHours())+':'+p(d.getMinutes())+':'+p(d.getSeconds());
    }
    tick(); setInterval(tick,1000);
})();

/* Modal de diagrama de cifrado */
(function(){
    var modal = document.getElementById('modal-diagram');
    var btnOpen  = document.getElementById('btn-diagram');
    var btnClose = document.getElementById('btn-close-diagram');
    if(!modal || !btnOpen) return;

    function open()  { modal.style.display='block'; document.body.style.overflow='hidden'; }
    function close() { modal.style.display='none';  document.body.style.overflow=''; }

    btnOpen.addEventListener('click', open);
    if(btnClose) btnClose.addEventListener('click', close);

    /* Cerrar al hacer clic en el fondo oscuro */
    modal.addEventListener('click', function(e){ if(e.target === modal) close(); });

    /* Cerrar con Escape */
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') close(); });
})();
</script>
</body>
</html>
