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
                <span style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.2em;color:#9d8c60;text-transform:uppercase">&#9650; Flujo de Cifrado — Subida de Expediente</span>
                <div style="flex:1;height:1px;background:linear-gradient(90deg,rgba(157,140,96,.28),transparent)"></div>
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
                <strong style="color:#b8935a">Nombre original en BD:</strong>
                &nbsp;
                <span style="color:#d8e0ef">ENC:<span style="color:#4a82be">base64(IV + TAG + CT)</span></span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <strong style="color:#b8935a">Metadatos de búsqueda:</strong>
                &nbsp;
                <span style="color:#d8e0ef">en claro e indexados (number, subject, sender)</span>
            </div>
        </div>

        <!-- ── DIAGRAMA 2: Flujo de Descifrado ── -->
        <div style="margin-bottom:28px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <span style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.2em;color:#9d6e6e;text-transform:uppercase">&#9660; Flujo de Descifrado — Visualización de Expediente</span>
                <div style="flex:1;height:1px;background:linear-gradient(90deg,rgba(157,110,110,.28),transparent)"></div>
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
                    <div style="font:600 10px/1.4 ui-monospace,monospace;color:#d8e0ef">Leer BD<br>metadatos en claro</div>
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
                <span style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.2em;color:#64809f;text-transform:uppercase">&#9632; Parámetros Criptográficos</span>
                <div style="flex:1;height:1px;background:linear-gradient(90deg,rgba(100,128,159,.28),transparent)"></div>
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
                    ['Campo cifrado en BD','original_file_name — ENC:base64(IV + TAG + CT)'],
                    ['Metadatos de búsqueda','En claro e indexados — filtros 100% en SQL'],
                    ['Plaintext en disco','Nunca — el PDF se descifra solo en RAM'],
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
     MODAL: Diagrama de Gestión de Sesión — ARCANUM
     ══════════════════════════════════════════════════════ -->
<div id="modal-session" style="display:none;position:fixed;inset:0;z-index:9998;overflow-y:auto;background:rgba(0,0,0,.82);backdrop-filter:blur(4px)">
<div style="max-width:980px;margin:32px auto;padding:0 16px 48px">

    <!-- Cabecera del modal -->
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 24px;background:rgba(0,0,0,.6);border-bottom:1px solid rgba(120,155,210,.28)">
        <div style="display:flex;align-items:center;gap:12px">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="2" stroke-linecap="round">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            <span style="font:700 14px/1 ui-monospace,monospace;letter-spacing:.18em;text-transform:uppercase;color:#fff">ARCANUM</span>
            <span style="font:600 10px/1 ui-monospace,monospace;letter-spacing:.14em;color:#5e7498">/&nbsp; GESTIÓN DE SESIÓN Y AUTENTICACIÓN</span>
        </div>
        <button id="btn-close-session" style="background:none;border:1px solid rgba(120,155,210,.28);color:#5e7498;cursor:pointer;padding:6px 12px;font:600 10px/1 ui-monospace,monospace;letter-spacing:.1em">✕ CERRAR</button>
    </div>

    <!-- Cuerpo del modal -->
    <div style="background:#0a0e1a;border:1px solid rgba(120,155,210,.14);border-top:none;padding:28px 28px 32px">

        <!-- Descripción general -->
        <p style="font:13px/1.85 ui-sans-serif,sans-serif;color:#8aa4c0;margin-bottom:28px;max-width:820px">
            <strong style="color:#d8e0ef">ARCANUM</strong> implementa autenticación en <strong style="color:#4a82be">dos pasos</strong>:
            primero contraseña y luego PIN de seguridad. Ambos factores comparten el mismo contador de intentos fallidos —
            tres fallos en cualquier combinación bloquean la cuenta permanentemente.
            Tras el acceso, las operaciones de escritura exigen un <strong style="color:#4a82be">PIN de uso único</strong> con ventana de 90 segundos.
        </p>

        <!-- ── SECCIÓN 1: Flujo de Autenticación en 2 pasos ── -->
        <div style="margin-bottom:30px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                <span style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.2em;color:#64809f;text-transform:uppercase">▶ Flujo de Autenticación — Dos Pasos</span>
                <div style="flex:1;height:1px;background:linear-gradient(90deg,rgba(100,128,159,.28),transparent)"></div>
            </div>

            <!-- Aviso de contador compartido -->
            <div style="display:flex;align-items:center;gap:10px;padding:8px 13px;background:rgba(248,113,113,.05);border:1px solid rgba(248,113,113,.2);margin-bottom:14px">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5" stroke-linecap="round" style="flex-shrink:0">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <span style="font:11px/1.5 ui-monospace,monospace;color:#5e7498">
                    <strong style="color:#f87171">Contador compartido:</strong>
                    contraseña incorrecta y PIN incorrecto suman al mismo contador de 3 intentos.
                    El bloqueo se activa independientemente de en qué paso ocurre el tercer fallo.
                </span>
            </div>

            <!-- ── PASO 1: Contraseña ── -->
            <div style="border:1px solid rgba(74,130,190,.18);margin-bottom:10px">
                <div style="display:flex;align-items:center;gap:8px;padding:7px 13px;background:rgba(74,130,190,.07);border-bottom:1px solid rgba(74,130,190,.14)">
                    <div style="width:18px;height:18px;border-radius:50%;background:rgba(74,130,190,.2);border:2px solid #4a82be;display:flex;align-items:center;justify-content:center;font:700 9px/1 ui-monospace,monospace;color:#4a82be;flex-shrink:0">1</div>
                    <span style="font:700 9px/1 ui-monospace,monospace;letter-spacing:.16em;color:#4a82be;text-transform:uppercase">Verificación de Contraseña — POST /login</span>
                </div>
                <div style="padding:12px;display:flex;align-items:center;flex-wrap:wrap;gap:0">

                    <div style="background:#06090f;border:1px solid rgba(74,130,190,.22);padding:10px 11px;min-width:95px;text-align:center">
                        <div style="font:700 9px/1 ui-monospace,monospace;color:#4a82be;letter-spacing:.1em;margin-bottom:5px">01</div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18"/></svg>
                        <div style="font:600 9px/1.35 ui-monospace,monospace;color:#d8e0ef">Formulario<br>Login</div>
                    </div>
                    <div style="font:700 14px ui-monospace,monospace;color:rgba(74,130,190,.4);padding:0 2px">→</div>

                    <div style="background:#06090f;border:1px solid rgba(74,130,190,.22);padding:10px 11px;min-width:90px;text-align:center">
                        <div style="font:700 9px/1 ui-monospace,monospace;color:#4a82be;letter-spacing:.1em;margin-bottom:5px">02</div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <div style="font:600 9px/1.35 ui-monospace,monospace;color:#d8e0ef">CSRF<br>verify</div>
                    </div>
                    <div style="font:700 14px ui-monospace,monospace;color:rgba(74,130,190,.4);padding:0 2px">→</div>

                    <div style="background:#06090f;border:1px solid rgba(74,130,190,.22);padding:10px 11px;min-width:95px;text-align:center">
                        <div style="font:700 9px/1 ui-monospace,monospace;color:#4a82be;letter-spacing:.1em;margin-bottom:5px">03</div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                        <div style="font:600 9px/1.35 ui-monospace,monospace;color:#d8e0ef">Buscar<br>usuario BD</div>
                    </div>
                    <div style="font:700 14px ui-monospace,monospace;color:rgba(74,130,190,.4);padding:0 2px">→</div>

                    <div style="background:rgba(248,113,113,.04);border:1px solid rgba(248,113,113,.28);padding:10px 11px;min-width:95px;text-align:center">
                        <div style="font:700 9px/1 ui-monospace,monospace;color:#f87171;letter-spacing:.1em;margin-bottom:5px">04</div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <div style="font:600 9px/1.35 ui-monospace,monospace;color:#d8e0ef">¿Cuenta<br>bloqueada?</div>
                    </div>
                    <div style="font:700 14px ui-monospace,monospace;color:rgba(74,130,190,.4);padding:0 2px">→</div>

                    <div style="background:rgba(184,147,90,.04);border:1px solid rgba(184,147,90,.28);padding:10px 11px;min-width:100px;text-align:center">
                        <div style="font:700 9px/1 ui-monospace,monospace;color:#b8935a;letter-spacing:.1em;margin-bottom:5px">05</div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b8935a" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <div style="font:600 9px/1.35 ui-monospace,monospace;color:#d8e0ef">¿Contraseña<br>correcta?</div>
                    </div>
                    <div style="font:700 14px ui-monospace,monospace;color:rgba(74,130,190,.4);padding:0 2px">→</div>

                    <div style="background:rgba(74,130,190,.08);border:2px solid rgba(74,130,190,.45);padding:10px 11px;min-width:110px;text-align:center;position:relative">
                        <div style="font:700 9px/1 ui-monospace,monospace;color:#4a82be;letter-spacing:.1em;margin-bottom:5px">06</div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><path d="M12 2H2v10l9.29 9.29a1 1 0 0 0 1.41 0l6.3-6.3a1 1 0 0 0 0-1.41z"/><circle cx="7" cy="7" r="1.5" fill="#4a82be"/></svg>
                        <div style="font:700 9px/1.35 ui-monospace,monospace;color:#a8c4e0">pending_auth<br>→ /login/pin</div>
                        <div style="position:absolute;top:-9px;left:50%;transform:translateX(-50%);background:#4a82be;color:#000;font:700 7px/1 ui-monospace,monospace;padding:2px 6px;letter-spacing:.08em;white-space:nowrap">120 s</div>
                    </div>

                </div>
            </div>

            <!-- Separador entre pasos -->
            <div style="display:flex;align-items:center;justify-content:center;gap:10px;margin:6px 0 10px">
                <div style="height:1px;flex:1;background:rgba(74,130,190,.12)"></div>
                <div style="font:700 9px/1 ui-monospace,monospace;letter-spacing:.18em;color:rgba(74,130,190,.35);text-transform:uppercase;padding:4px 10px;border:1px solid rgba(74,130,190,.15)">
                    Redirige a /login/pin con sesión pendiente de 2 min
                </div>
                <div style="height:1px;flex:1;background:rgba(74,130,190,.12)"></div>
            </div>

            <!-- ── PASO 2: PIN ── -->
            <div style="border:1px solid rgba(74,130,190,.18);margin-bottom:10px">
                <div style="display:flex;align-items:center;gap:8px;padding:7px 13px;background:rgba(74,130,190,.07);border-bottom:1px solid rgba(74,130,190,.14)">
                    <div style="width:18px;height:18px;border-radius:50%;background:rgba(74,130,190,.2);border:2px solid #4a82be;display:flex;align-items:center;justify-content:center;font:700 9px/1 ui-monospace,monospace;color:#4a82be;flex-shrink:0">2</div>
                    <span style="font:700 9px/1 ui-monospace,monospace;letter-spacing:.16em;color:#4a82be;text-transform:uppercase">Verificación de PIN — POST /login/pin</span>
                </div>
                <div style="padding:12px;display:flex;align-items:center;flex-wrap:wrap;gap:0">

                    <div style="background:#06090f;border:1px solid rgba(74,130,190,.22);padding:10px 11px;min-width:95px;text-align:center">
                        <div style="font:700 9px/1 ui-monospace,monospace;color:#4a82be;letter-spacing:.1em;margin-bottom:5px">07</div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <div style="font:600 9px/1.35 ui-monospace,monospace;color:#d8e0ef">Ingresa<br>su PIN</div>
                    </div>
                    <div style="font:700 14px ui-monospace,monospace;color:rgba(74,130,190,.4);padding:0 2px">→</div>

                    <div style="background:#06090f;border:1px solid rgba(74,130,190,.22);padding:10px 11px;min-width:90px;text-align:center">
                        <div style="font:700 9px/1 ui-monospace,monospace;color:#4a82be;letter-spacing:.1em;margin-bottom:5px">08</div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <div style="font:600 9px/1.35 ui-monospace,monospace;color:#d8e0ef">CSRF<br>verify</div>
                    </div>
                    <div style="font:700 14px ui-monospace,monospace;color:rgba(74,130,190,.4);padding:0 2px">→</div>

                    <div style="background:rgba(184,147,90,.04);border:1px solid rgba(184,147,90,.28);padding:10px 11px;min-width:110px;text-align:center">
                        <div style="font:700 9px/1 ui-monospace,monospace;color:#b8935a;letter-spacing:.1em;margin-bottom:5px">09</div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b8935a" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <div style="font:600 9px/1.35 ui-monospace,monospace;color:#d8e0ef">¿pending_auth<br>válida (&lt;120 s)?</div>
                    </div>
                    <div style="font:700 14px ui-monospace,monospace;color:rgba(74,130,190,.4);padding:0 2px">→</div>

                    <div style="background:rgba(184,147,90,.04);border:1px solid rgba(184,147,90,.28);padding:10px 11px;min-width:95px;text-align:center">
                        <div style="font:700 9px/1 ui-monospace,monospace;color:#b8935a;letter-spacing:.1em;margin-bottom:5px">10</div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b8935a" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                        <div style="font:600 9px/1.35 ui-monospace,monospace;color:#d8e0ef">¿PIN<br>correcto?</div>
                    </div>
                    <div style="font:700 14px ui-monospace,monospace;color:rgba(74,222,128,.5);padding:0 2px">→</div>

                    <div style="background:#06090f;border:1px solid rgba(74,222,128,.22);padding:10px 11px;min-width:105px;text-align:center">
                        <div style="font:700 9px/1 ui-monospace,monospace;color:#4ade80;letter-spacing:.1em;margin-bottom:5px">11</div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><polyline points="20 6 9 17 4 12"/></svg>
                        <div style="font:600 9px/1.35 ui-monospace,monospace;color:#4ade80">reset<br>Attempts()</div>
                    </div>
                    <div style="font:700 14px ui-monospace,monospace;color:rgba(74,222,128,.5);padding:0 2px">→</div>

                    <div style="background:rgba(74,222,128,.07);border:2px solid rgba(74,222,128,.4);padding:10px 11px;min-width:100px;text-align:center;position:relative">
                        <div style="font:700 9px/1 ui-monospace,monospace;color:#4ade80;letter-spacing:.1em;margin-bottom:5px">12</div>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        <div style="font:700 9px/1.35 ui-monospace,monospace;color:#4ade80">Auth::login<br>→ /documents</div>
                        <div style="position:absolute;top:-9px;left:50%;transform:translateX(-50%);background:rgba(74,222,128,.75);color:#000;font:700 7px/1 ui-monospace,monospace;padding:2px 6px;letter-spacing:.08em;white-space:nowrap">ÉXITO</div>
                    </div>

                </div>
            </div>

            <!-- Notas de error para ambos pasos -->
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px">
                <div style="padding:9px 13px;background:#06090f;border:1px solid rgba(248,113,113,.18);border-left:3px solid rgba(248,113,113,.55);display:flex;align-items:flex-start;gap:9px">
                    <span style="color:#f87171;font:700 13px/1 ui-monospace,monospace;flex-shrink:0;margin-top:1px">✕</span>
                    <div>
                        <div style="font:700 9px/1 ui-monospace,monospace;letter-spacing:.12em;color:#f87171;text-transform:uppercase;margin-bottom:4px">Paso 04 — Bloqueada</div>
                        <div style="font:10.5px/1.6 ui-monospace,monospace;color:#5e7498"><code style="color:#f87171">locked_at ≠ null</code> — acceso denegado hasta que un administrador reactive la cuenta.</div>
                    </div>
                </div>
                <div style="padding:9px 13px;background:#06090f;border:1px solid rgba(184,147,90,.18);border-left:3px solid rgba(184,147,90,.55);display:flex;align-items:flex-start;gap:9px">
                    <span style="color:#b8935a;font:700 13px/1 ui-monospace,monospace;flex-shrink:0;margin-top:1px">!</span>
                    <div>
                        <div style="font:700 9px/1 ui-monospace,monospace;letter-spacing:.12em;color:#b8935a;text-transform:uppercase;margin-bottom:4px">Paso 05 — Clave incorrecta</div>
                        <div style="font:10.5px/1.6 ui-monospace,monospace;color:#5e7498">+1 al contador. Intento 3: <code style="color:#f87171">locked_at = NOW()</code> → cuenta bloqueada.</div>
                    </div>
                </div>
                <div style="padding:9px 13px;background:#06090f;border:1px solid rgba(184,147,90,.18);border-left:3px solid rgba(184,147,90,.55);display:flex;align-items:flex-start;gap:9px">
                    <span style="color:#b8935a;font:700 13px/1 ui-monospace,monospace;flex-shrink:0;margin-top:1px">!</span>
                    <div>
                        <div style="font:700 9px/1 ui-monospace,monospace;letter-spacing:.12em;color:#b8935a;text-transform:uppercase;margin-bottom:4px">Paso 10 — PIN incorrecto</div>
                        <div style="font:10.5px/1.6 ui-monospace,monospace;color:#5e7498">+1 al mismo contador. Intento 3: destruye <code>pending_auth</code> + bloqueo. PIN expirado (&gt;120 s): vuelve al paso 1.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── SECCIÓN 2: Configuración Inicial Obligatoria ── -->
        <div style="margin-bottom:30px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                <span style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.2em;color:#9d8c60;text-transform:uppercase">⚙ Configuración Inicial Obligatoria — Primera Sesión</span>
                <div style="flex:1;height:1px;background:linear-gradient(90deg,rgba(157,140,96,.28),transparent)"></div>
            </div>

            <div style="display:flex;align-items:stretch;gap:8px;flex-wrap:wrap">

                <div style="background:#06090f;border:1px solid rgba(184,147,90,.25);padding:12px 14px;min-width:120px;text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b8935a" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                    <div style="font:600 10px/1.35 ui-monospace,monospace;color:#d8e0ef">Login<br>Exitoso</div>
                    <div style="font:700 8px/1 ui-monospace,monospace;letter-spacing:.1em;color:#b8935a">NUEVA CUENTA</div>
                </div>
                <div style="font:700 16px ui-monospace,monospace;color:rgba(184,147,90,.5);display:flex;align-items:center;padding:0 3px">→</div>

                <div style="flex:1;min-width:175px;background:rgba(74,130,190,.06);border:1px solid rgba(74,130,190,.32);padding:13px 15px">
                    <div style="display:flex;align-items:center;gap:7px;margin-bottom:8px">
                        <div style="width:20px;height:20px;border-radius:50%;background:rgba(74,130,190,.2);border:2px solid #4a82be;display:flex;align-items:center;justify-content:center;font:700 9px/1 ui-monospace,monospace;color:#4a82be;flex-shrink:0">1</div>
                        <div style="font:700 9px/1 ui-monospace,monospace;letter-spacing:.1em;color:#4a82be;text-transform:uppercase">Cambiar contraseña</div>
                    </div>
                    <div style="font:11px/1.65 ui-monospace,monospace;color:#5e7498">
                        <code style="color:#a8c4e0">must_change_password = 1</code><br>
                        Solo accede a <code style="color:#4a82be">/profile</code> y <code style="color:#4a82be">/profile/password</code>.<br>
                        Cualquier otra ruta redirige al perfil.
                    </div>
                </div>
                <div style="font:700 16px ui-monospace,monospace;color:rgba(74,130,190,.5);display:flex;align-items:center;padding:0 3px">→</div>

                <div style="flex:1;min-width:175px;background:rgba(74,130,190,.06);border:1px solid rgba(74,130,190,.32);padding:13px 15px">
                    <div style="display:flex;align-items:center;gap:7px;margin-bottom:8px">
                        <div style="width:20px;height:20px;border-radius:50%;background:rgba(74,130,190,.2);border:2px solid #4a82be;display:flex;align-items:center;justify-content:center;font:700 9px/1 ui-monospace,monospace;color:#4a82be;flex-shrink:0">2</div>
                        <div style="font:700 9px/1 ui-monospace,monospace;letter-spacing:.1em;color:#4a82be;text-transform:uppercase">Configurar PIN</div>
                    </div>
                    <div style="font:11px/1.65 ui-monospace,monospace;color:#5e7498">
                        <code style="color:#a8c4e0">has_default_pin = true</code> o <code style="color:#a8c4e0">!has_pin</code><br>
                        Solo accede a <code style="color:#4a82be">/profile</code> y <code style="color:#4a82be">/profile/pin</code>.<br>
                        PIN provisional del sistema: <code style="color:#b8935a">123456</code>
                    </div>
                </div>
                <div style="font:700 16px ui-monospace,monospace;color:rgba(74,222,128,.5);display:flex;align-items:center;padding:0 3px">→</div>

                <div style="background:rgba(74,222,128,.06);border:1px solid rgba(74,222,128,.3);padding:12px 14px;min-width:110px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:6px">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.5" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <div style="font:700 10px/1.35 ui-monospace,monospace;color:#4ade80">ACCESO<br>COMPLETO</div>
                </div>

            </div>
        </div>

        <!-- ── SECCIÓN 3: Control de Inactividad ── -->
        <div style="margin-bottom:30px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                <span style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.2em;color:#9d6e6e;text-transform:uppercase">⏱ Control de Inactividad — Doble Capa</span>
                <div style="flex:1;height:1px;background:linear-gradient(90deg,rgba(157,110,110,.28),transparent)"></div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">

                <!-- Cliente -->
                <div style="background:#06090f;border:1px solid rgba(74,130,190,.2);padding:16px">
                    <div style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.16em;color:#4a82be;text-transform:uppercase;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid rgba(74,130,190,.14)">Cliente — Temporizador JavaScript</div>
                    <div style="display:flex;flex-direction:column;gap:9px">
                        <div style="display:flex;align-items:baseline;gap:10px">
                            <div style="font:700 11px/1 ui-monospace,monospace;color:#4a82be;min-width:42px;flex-shrink:0">0:00</div>
                            <div style="font:11px/1.5 ui-monospace,monospace;color:#5e7498">Página cargada — timers iniciados</div>
                        </div>
                        <div style="display:flex;align-items:baseline;gap:10px">
                            <div style="font:700 11px/1 ui-monospace,monospace;color:#4a82be;min-width:42px;flex-shrink:0">2:00</div>
                            <div style="font:11px/1.5 ui-monospace,monospace;color:#5e7498">Ping silencioso a <code style="color:#4a82be">/ping</code> (se repite cada 2 min)</div>
                        </div>
                        <div style="display:flex;align-items:baseline;gap:10px">
                            <div style="font:700 11px/1 ui-monospace,monospace;color:#b8935a;min-width:42px;flex-shrink:0">4:00</div>
                            <div style="font:11px/1.5 ui-monospace,monospace;color:#5e7498">⚠ Modal de aviso — cuenta regresiva de 60 s</div>
                        </div>
                        <div style="display:flex;align-items:baseline;gap:10px">
                            <div style="font:700 11px/1 ui-monospace,monospace;color:#f87171;min-width:42px;flex-shrink:0">5:00</div>
                            <div style="font:11px/1.5 ui-monospace,monospace;color:#5e7498">Redirige a <code style="color:#f87171">/logout</code> automáticamente</div>
                        </div>
                        <div style="padding:8px 10px;background:rgba(74,130,190,.04);border:1px solid rgba(74,130,190,.11);font:10.5px/1.55 ui-monospace,monospace;color:#3a5870;margin-top:2px">
                            Eventos mousemove, mousedown, keydown, touchstart, scroll o click reinician los timers desde 0:00.
                        </div>
                    </div>
                </div>

                <!-- Servidor -->
                <div style="background:#06090f;border:1px solid rgba(74,130,190,.2);padding:16px">
                    <div style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.16em;color:#4a82be;text-transform:uppercase;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid rgba(74,130,190,.14)">Servidor — Validación PHP por Request</div>
                    <div style="display:flex;flex-direction:column;gap:9px">
                        <div style="display:flex;align-items:flex-start;gap:9px">
                            <span style="color:#4a82be;font:700 11px/1 ui-monospace,monospace;flex-shrink:0;margin-top:1px">1.</span>
                            <div style="font:11px/1.6 ui-monospace,monospace;color:#5e7498"><code style="color:#4a82be">Auth::requireLogin()</code> se ejecuta en cada request protegido.</div>
                        </div>
                        <div style="display:flex;align-items:flex-start;gap:9px">
                            <span style="color:#4a82be;font:700 11px/1 ui-monospace,monospace;flex-shrink:0;margin-top:1px">2.</span>
                            <div style="font:11px/1.6 ui-monospace,monospace;color:#5e7498">Evalúa <code style="color:#a8c4e0">time() − last_activity &gt; SESSION_TIMEOUT (300 s)</code>.</div>
                        </div>
                        <div style="display:flex;align-items:flex-start;gap:9px">
                            <span style="color:#f87171;font:700 11px/1 ui-monospace,monospace;flex-shrink:0;margin-top:1px">3.</span>
                            <div style="font:11px/1.6 ui-monospace,monospace;color:#5e7498">Si expiró → <code style="color:#f87171">Auth::logout()</code> → flash <em>"Sesión cerrada por inactividad"</em> → redirige al login.</div>
                        </div>
                        <div style="display:flex;align-items:flex-start;gap:9px">
                            <span style="color:#4ade80;font:700 11px/1 ui-monospace,monospace;flex-shrink:0;margin-top:1px">4.</span>
                            <div style="font:11px/1.6 ui-monospace,monospace;color:#5e7498">Si válida → actualiza <code style="color:#4ade80">$_SESSION['last_activity'] = time()</code> y continúa.</div>
                        </div>
                        <div style="padding:8px 10px;background:rgba(74,130,190,.04);border:1px solid rgba(74,130,190,.11);font:10.5px/1.55 ui-monospace,monospace;color:#3a5870;margin-top:2px">
                            El ping JS también refresca <code>last_activity</code> en servidor, sincronizando ambos relojes.
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ── SECCIÓN 4: Verificación de PIN ── -->
        <div style="margin-bottom:28px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                <span style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.2em;color:#6f8f7c;text-transform:uppercase">🔑 Verificación de PIN — Operaciones de Escritura</span>
                <div style="flex:1;height:1px;background:linear-gradient(90deg,rgba(111,143,124,.28),transparent)"></div>
            </div>

            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:0;margin-bottom:8px">

                <div style="background:#06090f;border:1px solid rgba(74,222,128,.22);padding:11px 13px;min-width:105px;text-align:center">
                    <div style="font:700 9px/1 ui-monospace,monospace;color:#4ade80;letter-spacing:.1em;margin-bottom:5px">A</div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    <div style="font:600 10px/1.35 ui-monospace,monospace;color:#d8e0ef">Agregar /<br>Editar / Borrar</div>
                </div>
                <div style="font:700 16px ui-monospace,monospace;color:rgba(74,222,128,.5);padding:0 3px">→</div>

                <div style="background:rgba(74,130,190,.07);border:2px solid rgba(74,130,190,.42);padding:11px 13px;min-width:115px;text-align:center;position:relative">
                    <div style="font:700 9px/1 ui-monospace,monospace;color:#4a82be;letter-spacing:.1em;margin-bottom:5px">B</div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <div style="font:700 10px/1.35 ui-monospace,monospace;color:#a8c4e0">Modal PIN<br>Solicitado</div>
                </div>
                <div style="font:700 16px ui-monospace,monospace;color:rgba(74,130,190,.5);padding:0 3px">→</div>

                <div style="background:#06090f;border:1px solid rgba(74,130,190,.22);padding:11px 13px;min-width:115px;text-align:center">
                    <div style="font:700 9px/1 ui-monospace,monospace;color:#4a82be;letter-spacing:.1em;margin-bottom:5px">C</div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                    <div style="font:600 10px/1.35 ui-monospace,monospace;color:#d8e0ef">password_verify<br>hash en BD</div>
                </div>
                <div style="font:700 16px ui-monospace,monospace;color:rgba(74,222,128,.5);padding:0 3px">→</div>

                <div style="background:#06090f;border:1px solid rgba(74,222,128,.22);padding:11px 13px;min-width:125px;text-align:center">
                    <div style="font:700 9px/1 ui-monospace,monospace;color:#4ade80;letter-spacing:.1em;margin-bottom:5px">D</div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <div style="font:600 10px/1.35 ui-monospace,monospace;color:#d8e0ef"><code style="color:#4ade80;font-size:9px">pin_verified_at</code><br>= time()</div>
                </div>
                <div style="font:700 16px ui-monospace,monospace;color:rgba(74,222,128,.5);padding:0 3px">→</div>

                <div style="background:rgba(74,222,128,.06);border:2px solid rgba(74,222,128,.38);padding:11px 13px;min-width:115px;text-align:center;position:relative">
                    <div style="font:700 9px/1 ui-monospace,monospace;color:#4ade80;letter-spacing:.1em;margin-bottom:5px">E</div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.5" stroke-linecap="round" style="display:block;margin:0 auto 5px"><polyline points="20 6 9 17 4 12"/></svg>
                    <div style="font:700 10px/1.35 ui-monospace,monospace;color:#4ade80">Operación<br>Autorizada</div>
                    <div style="position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:rgba(74,222,128,.7);color:#000;font:700 8px/1 ui-monospace,monospace;padding:2px 7px;letter-spacing:.1em;white-space:nowrap">90 s</div>
                </div>

            </div>

            <div style="padding:9px 13px;background:rgba(74,222,128,.04);border:1px solid rgba(74,222,128,.14);font:10.5px/1.75 ui-monospace,monospace;color:#5e7498">
                <strong style="color:#4ade80">Uso único:</strong> &nbsp; Al ejecutar la operación, <code>pin_verified_at</code> se elimina con <code>unset()</code>. La siguiente escritura requiere un nuevo PIN.
                &nbsp;&nbsp;
                <strong style="color:#b8935a">Ventana de validez:</strong> &nbsp; 90 segundos desde la verificación.
                &nbsp;&nbsp;
                <strong style="color:#f87171">PIN predeterminado activo:</strong> &nbsp; Bloquea todas las escrituras hasta que el operador configure un PIN propio.
            </div>
        </div>

        <!-- ── Tabla de parámetros técnicos ── -->
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                <span style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.2em;color:#64809f;text-transform:uppercase">■ Parámetros de Sesión</span>
                <div style="flex:1;height:1px;background:linear-gradient(90deg,rgba(100,128,159,.28),transparent)"></div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:1px;background:rgba(120,155,210,.14);border:1px solid rgba(120,155,210,.14)">
                <?php
                $sparams = [
                    ['Pasos de autenticación','2 — contraseña + PIN (mismo contador de intentos)'],
                    ['Sesión pendiente',      '120 s — tiempo máximo para ingresar el PIN de login'],
                    ['Máx. intentos (total)', '3 entre ambos pasos — bloqueo permanente al 3°'],
                    ['Bloqueo de cuenta',     'locked_at = NOW() al tercer fallo (cualquier paso)'],
                    ['Tiempo de sesión',      '300 s (5 minutos de inactividad)'],
                    ['Aviso previo al cierre','60 s (1 minuto antes del logout)'],
                    ['Ping al servidor',      'Cada 120 s — refresca last_activity en servidor'],
                    ['Regeneración de ID',    'session_regenerate_id(true) al completar ambos pasos'],
                    ['Validez del PIN op.',   '90 s — uso único por operación de escritura'],
                    ['PIN provisional',       '123456 — bloquea escrituras hasta ser cambiado'],
                ];
                foreach ($sparams as [$slabel, $sval]): ?>
                <div style="background:#0a0e1a;padding:11px 14px">
                    <div style="font:600 9px/1 ui-monospace,monospace;letter-spacing:.14em;text-transform:uppercase;color:#2c3a52;margin-bottom:5px"><?= $slabel ?></div>
                    <div style="font:600 12px/1.4 ui-monospace,monospace;color:#d8e0ef"><?= $sval ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div><!-- /cuerpo -->
</div><!-- /inner -->
</div><!-- /modal-session -->

<!-- ══════════════════════════════════════════════════════
     MODAL: Diagrama Entidad-Relación — Base de datos ARCANUM
     ══════════════════════════════════════════════════════ -->
<div id="modal-er" style="display:none;position:fixed;inset:0;z-index:9997;overflow-y:auto;background:rgba(0,0,0,.82);backdrop-filter:blur(4px)">
<div style="max-width:1080px;margin:32px auto;padding:0 16px 48px">

    <!-- Cabecera del modal -->
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 24px;background:rgba(0,0,0,.6);border-bottom:1px solid rgba(120,155,210,.28)">
        <div style="display:flex;align-items:center;gap:12px">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="2" stroke-linecap="round">
                <ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
            </svg>
            <span style="font:700 14px/1 ui-monospace,monospace;letter-spacing:.18em;text-transform:uppercase;color:#fff">ARCANUM</span>
            <span style="font:600 10px/1 ui-monospace,monospace;letter-spacing:.14em;color:#5e7498">/&nbsp; MODELO ENTIDAD-RELACIÓN — arcanum</span>
        </div>
        <button id="btn-close-er" style="background:none;border:1px solid rgba(120,155,210,.28);color:#5e7498;cursor:pointer;padding:6px 12px;font:600 10px/1 ui-monospace,monospace;letter-spacing:.1em">✕ CERRAR</button>
    </div>

    <!-- Cuerpo del modal -->
    <div style="background:#0a0e1a;border:1px solid rgba(120,155,210,.14);border-top:none;padding:28px 28px 32px">

        <!-- Descripción general -->
        <p style="font:13px/1.85 ui-sans-serif,sans-serif;color:#8aa4c0;margin-bottom:20px;max-width:840px">
            Base de datos <strong style="color:#d8e0ef">arcanum</strong> (MySQL / InnoDB, utf8mb4).
            Cinco tablas: <strong style="color:#4a82be">users</strong> es la entidad central, de la que dependen
            <strong style="color:#4a82be">documents</strong> y <strong style="color:#4a82be">system_logs</strong> mediante claves foráneas.
            Las tablas <strong style="color:#b8935a">document_types</strong> y <strong style="color:#b8935a">document_senders</strong>
            son catálogos que alimentan el formulario de documentos.
            Los campos marcados con candado se almacenan cifrados con AES-256-GCM;
            los metadatos de búsqueda (number, subject, sender) se guardan en claro
            e indexados para que los filtros se resuelvan directamente en SQL.
        </p>

        <!-- Leyenda -->
        <div style="display:flex;flex-wrap:wrap;gap:14px;padding:11px 14px;background:#06090f;border:1px solid rgba(120,155,210,.14);margin-bottom:24px">
            <span style="display:inline-flex;align-items:center;gap:6px;font:600 10px/1 ui-monospace,monospace;color:#5e7498">
                <span style="font:700 8px/1 ui-monospace,monospace;color:#b8935a;background:rgba(184,147,90,.12);border:1px solid rgba(184,147,90,.4);padding:2px 5px">PK</span> Clave primaria
            </span>
            <span style="display:inline-flex;align-items:center;gap:6px;font:600 10px/1 ui-monospace,monospace;color:#5e7498">
                <span style="font:700 8px/1 ui-monospace,monospace;color:#4a82be;background:rgba(74,130,190,.12);border:1px solid rgba(74,130,190,.4);padding:2px 5px">FK</span> Clave foránea
            </span>
            <span style="display:inline-flex;align-items:center;gap:6px;font:600 10px/1 ui-monospace,monospace;color:#5e7498">
                <span style="font:700 8px/1 ui-monospace,monospace;color:#4ade80;background:rgba(74,222,128,.1);border:1px solid rgba(74,222,128,.35);padding:2px 5px">UQ</span> Único
            </span>
            <span style="display:inline-flex;align-items:center;gap:6px;font:600 10px/1 ui-monospace,monospace;color:#5e7498">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#c084fc" stroke-width="2.2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Cifrado AES-256-GCM
            </span>
            <span style="display:inline-flex;align-items:center;gap:6px;font:600 10px/1 ui-monospace,monospace;color:#5e7498">
                <span style="color:#3a4a60">○</span> Acepta NULL
            </span>
        </div>

        <?php
        /* ── Definición de las entidades: [columna, tipo, banderas] ── */
        $erTables = [
            'users' => [
                'icon'  => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
                'desc'  => 'Operadores del sistema',
                'cols'  => [
                    ['id',                   'INT UNSIGNED',       ['PK']],
                    ['name',                 'VARCHAR(120)',       []],
                    ['email',                'VARCHAR(160)',       ['UQ']],
                    ['password_hash',        'VARCHAR(255)',       []],
                    ['role',                 "ENUM(admin,operador)", []],
                    ['active',               'TINYINT(1)',         []],
                    ['failed_attempts',      'TINYINT UNSIGNED',   []],
                    ['locked_at',            'DATETIME',           ['NULL']],
                    ['lock_expires_at',      'DATETIME',           ['NULL']],
                    ['avatar',               'VARCHAR(255)',       ['NULL']],
                    ['pin_hash',             'VARCHAR(255)',       ['NULL']],
                    ['must_change_password', 'TINYINT(1)',         []],
                    ['created_at',           'TIMESTAMP',          []],
                ],
            ],
            'documents' => [
                'icon'  => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>',
                'desc'  => 'Expedientes clasificados',
                'cols'  => [
                    ['id',                 'INT UNSIGNED', ['PK']],
                    ['number',             'VARCHAR(120)', []],
                    ['subject',            'VARCHAR(255)', []],
                    ['document_date',      'DATE',         []],
                    ['sender',             'VARCHAR(160)', []],
                    ['type',               "ENUM(oficio,memorandum,carta)", []],
                    ['file_name',          'VARCHAR(120)', []],
                    ['original_file_name', 'MEDIUMTEXT',   ['ENC']],
                    ['mime_type',          'VARCHAR(80)',  []],
                    ['file_size',          'INT UNSIGNED', []],
                    ['created_by',         'INT UNSIGNED', ['FK', 'NULL']],
                    ['created_at',         'TIMESTAMP',    []],
                    ['updated_at',         'TIMESTAMP',    []],
                ],
            ],
            'system_logs' => [
                'icon'  => '<path d="M3 3v18h18"/><path d="M7 14l3-3 4 4 5-7"/>',
                'desc'  => 'Bitácora de auditoría',
                'cols'  => [
                    ['id',          'BIGINT UNSIGNED', ['PK']],
                    ['user_id',     'INT UNSIGNED',    ['FK', 'NULL']],
                    ['action',      'VARCHAR(80)',     []],
                    ['description', 'VARCHAR(500)',    []],
                    ['ip_address',  'VARCHAR(45)',     ['NULL']],
                    ['user_agent',  'VARCHAR(255)',    ['NULL']],
                    ['created_at',  'TIMESTAMP',       []],
                ],
            ],
            'document_types' => [
                'icon'  => '<path d="M4 6h16"/><path d="M4 10h16"/><path d="M4 14h10"/><path d="M4 18h6"/>',
                'desc'  => 'Catálogo de tipos',
                'cols'  => [
                    ['id',         'INT UNSIGNED', ['PK']],
                    ['name',       'VARCHAR(100)', []],
                    ['active',     'TINYINT(1)',   []],
                    ['created_at', 'TIMESTAMP',    []],
                ],
            ],
            'document_senders' => [
                'icon'  => '<path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4z"/>',
                'desc'  => 'Catálogo de remitentes',
                'cols'  => [
                    ['id',         'INT UNSIGNED', ['PK']],
                    ['name',       'VARCHAR(200)', []],
                    ['active',     'TINYINT(1)',   []],
                    ['created_at', 'TIMESTAMP',    []],
                ],
            ],
        ];

        /* Renderiza las banderas de una columna como pequeñas insignias */
        $erBadge = static function (array $flags): string {
            $html = '';
            foreach ($flags as $f) {
                if ($f === 'PK') {
                    $html .= '<span style="font:700 7px/1 ui-monospace,monospace;color:#b8935a;background:rgba(184,147,90,.12);border:1px solid rgba(184,147,90,.4);padding:2px 4px">PK</span>';
                } elseif ($f === 'FK') {
                    $html .= '<span style="font:700 7px/1 ui-monospace,monospace;color:#4a82be;background:rgba(74,130,190,.12);border:1px solid rgba(74,130,190,.4);padding:2px 4px">FK</span>';
                } elseif ($f === 'UQ') {
                    $html .= '<span style="font:700 7px/1 ui-monospace,monospace;color:#4ade80;background:rgba(74,222,128,.1);border:1px solid rgba(74,222,128,.35);padding:2px 4px">UQ</span>';
                } elseif ($f === 'ENC') {
                    $html .= '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#c084fc" stroke-width="2.2" stroke-linecap="round" style="flex-shrink:0"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>';
                }
            }
            return $html;
        };
        ?>

        <!-- ── Cuadrícula de entidades ── -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:14px;align-items:start;margin-bottom:26px">
            <?php foreach ($erTables as $tname => $tdata):
                /* users y documents ocupan doble ancho por tener más columnas */
                $span = in_array($tname, ['users', 'documents'], true) ? 'grid-column:span 1' : '';
            ?>
            <div style="background:#06090f;border:1px solid rgba(74,130,190,.28);<?= $span ?>">

                <!-- Cabecera de la entidad -->
                <div style="display:flex;align-items:center;gap:9px;padding:11px 13px;background:rgba(74,130,190,.09);border-bottom:1px solid rgba(74,130,190,.22)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><?= $tdata['icon'] ?></svg>
                    <div style="min-width:0">
                        <div style="font:700 11px/1 ui-monospace,monospace;letter-spacing:.08em;color:#d8e0ef"><?= $tname ?></div>
                        <div style="font:600 8.5px/1.3 ui-monospace,monospace;color:#3a5870;margin-top:3px;text-transform:uppercase;letter-spacing:.08em"><?= $tdata['desc'] ?></div>
                    </div>
                </div>

                <!-- Columnas -->
                <div>
                    <?php foreach ($tdata['cols'] as $i => [$cname, $ctype, $cflags]):
                        $isKey  = in_array('PK', $cflags, true) || in_array('FK', $cflags, true);
                        $isNull = in_array('NULL', $cflags, true);
                        $border = $i < count($tdata['cols']) - 1 ? 'border-bottom:1px solid rgba(120,155,210,.07)' : '';
                    ?>
                    <div style="display:flex;align-items:center;gap:7px;padding:7px 13px;<?= $border ?>">
                        <?php if ($isNull): ?><span style="color:#3a4a60;font-size:9px;flex-shrink:0" title="Acepta NULL">○</span><?php endif; ?>
                        <span style="font:<?= $isKey ? '700' : '600' ?> 10.5px/1.3 ui-monospace,monospace;color:<?= $isKey ? '#a8c4e0' : '#8aa4c0' ?>;<?= in_array('PK', $cflags, true) ? 'text-decoration:underline;text-decoration-color:rgba(184,147,90,.5)' : '' ?>"><?= $cname ?></span>
                        <span style="display:inline-flex;align-items:center;gap:3px;margin-left:auto;flex-shrink:0"><?= $erBadge($cflags) ?></span>
                        <span style="font:9px/1.3 ui-monospace,monospace;color:#3a5870;flex-shrink:0;min-width:0"><?= $ctype ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- ── Relaciones ── -->
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
            <span style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.2em;color:#9d8c60;text-transform:uppercase">▶ Relaciones y Cardinalidad</span>
            <div style="flex:1;height:1px;background:linear-gradient(90deg,rgba(157,140,96,.28),transparent)"></div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:22px">

            <!-- users → documents -->
            <div style="background:#06090f;border:1px solid rgba(74,130,190,.2);border-left:3px solid #4a82be;padding:13px 15px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap">
                    <span style="font:700 10px/1 ui-monospace,monospace;color:#d8e0ef">users</span>
                    <span style="font:700 11px/1 ui-monospace,monospace;color:#4a82be">1 ──&lt; N</span>
                    <span style="font:700 10px/1 ui-monospace,monospace;color:#d8e0ef">documents</span>
                </div>
                <div style="font:10.5px/1.65 ui-monospace,monospace;color:#5e7498">
                    <code style="color:#4a82be">documents.created_by</code> → <code style="color:#b8935a">users.id</code><br>
                    Un operador registra muchos documentos.
                    <span style="color:#f0a878">ON DELETE SET NULL</span>: si se elimina el operador, el documento se conserva sin autor.
                </div>
            </div>

            <!-- users → system_logs -->
            <div style="background:#06090f;border:1px solid rgba(74,130,190,.2);border-left:3px solid #4a82be;padding:13px 15px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap">
                    <span style="font:700 10px/1 ui-monospace,monospace;color:#d8e0ef">users</span>
                    <span style="font:700 11px/1 ui-monospace,monospace;color:#4a82be">1 ──&lt; N</span>
                    <span style="font:700 10px/1 ui-monospace,monospace;color:#d8e0ef">system_logs</span>
                </div>
                <div style="font:10.5px/1.65 ui-monospace,monospace;color:#5e7498">
                    <code style="color:#4a82be">system_logs.user_id</code> → <code style="color:#b8935a">users.id</code><br>
                    Un operador genera muchos eventos de bitácora.
                    <span style="color:#f0a878">ON DELETE SET NULL</span>: los logs anónimos (login fallido) llevan <code>user_id = NULL</code>.
                </div>
            </div>

            <!-- document_types → documents (lógica) -->
            <div style="background:#06090f;border:1px solid rgba(184,147,90,.2);border-left:3px solid #b8935a;padding:13px 15px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap">
                    <span style="font:700 10px/1 ui-monospace,monospace;color:#d8e0ef">document_types</span>
                    <span style="font:700 11px/1 ui-monospace,monospace;color:#b8935a">catálogo ┄&gt;</span>
                    <span style="font:700 10px/1 ui-monospace,monospace;color:#d8e0ef">documents.type</span>
                </div>
                <div style="font:10.5px/1.65 ui-monospace,monospace;color:#5e7498">
                    Relación <strong style="color:#b8935a">lógica sin FK</strong>: alimenta el desplegable de tipos.
                    Al guardar, el <code>name</code> del catálogo se copia como texto en <code>documents.type</code>.
                </div>
            </div>

            <!-- document_senders → documents (lógica) -->
            <div style="background:#06090f;border:1px solid rgba(184,147,90,.2);border-left:3px solid #b8935a;padding:13px 15px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap">
                    <span style="font:700 10px/1 ui-monospace,monospace;color:#d8e0ef">document_senders</span>
                    <span style="font:700 11px/1 ui-monospace,monospace;color:#b8935a">catálogo ┄&gt;</span>
                    <span style="font:700 10px/1 ui-monospace,monospace;color:#d8e0ef">documents.sender</span>
                </div>
                <div style="font:10.5px/1.65 ui-monospace,monospace;color:#5e7498">
                    Relación <strong style="color:#b8935a">lógica sin FK</strong>: alimenta el desplegable de remitentes.
                    Al guardar, el <code>name</code> se copia como texto en claro en <code>documents.sender</code> (indexado para búsquedas).
                </div>
            </div>

        </div>

        <!-- ── Nota sobre integridad referencial ── -->
        <div style="padding:11px 14px;background:rgba(74,130,190,.04);border:1px solid rgba(74,130,190,.14);font:10.5px/1.8 ui-monospace,monospace;color:#5e7498">
            <strong style="color:#4a82be">Motor:</strong> InnoDB (soporta claves foráneas y transacciones).
            &nbsp;&nbsp;
            <strong style="color:#4a82be">Cotejamiento:</strong> utf8mb4_unicode_ci.
            &nbsp;&nbsp;
            <strong style="color:#b8935a">Cifrado:</strong> los campos con candado guardan <code>ENC:base64(IV + TAG + CT)</code>.
            &nbsp;&nbsp;
            <strong style="color:#b8935a">Búsqueda:</strong> number, subject y sender están en claro e indexados; el PDF y su nombre original permanecen cifrados.
        </div>

    </div><!-- /cuerpo -->
</div><!-- /inner -->
</div><!-- /modal-er -->

<!-- ══════════════════════════════════════════════════════
     MODAL: Verificación de PIN de seguridad
     ══════════════════════════════════════════════════════ -->
<?php if (Auth::check()): ?>
<div id="modal-pin" style="display:none;position:fixed;inset:0;z-index:10002;background:rgba(0,0,0,.92);backdrop-filter:blur(5px);align-items:center;justify-content:center">
    <div style="max-width:380px;width:calc(100% - 32px);background:#0a0e1a;border:1px solid rgba(74,130,190,.45);box-shadow:0 0 80px rgba(0,0,0,.95)">

        <div style="display:flex;align-items:center;gap:12px;padding:16px 20px;background:rgba(0,0,0,.5);border-bottom:1px solid rgba(74,130,190,.2)">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#4a82be" stroke-width="2" stroke-linecap="round">
                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <span style="font:700 11px/1 ui-monospace,monospace;letter-spacing:.18em;text-transform:uppercase;color:#4a82be">VERIFICACIÓN DE PIN</span>
            <button id="pin-modal-close" style="margin-left:auto;background:none;border:none;color:#3a4a60;cursor:pointer;font-size:16px;line-height:1;padding:2px 6px">✕</button>
        </div>

        <div style="padding:24px 20px">
            <p id="pin-modal-desc" style="font:12px/1.7 ui-monospace,monospace;color:#5e7498;margin:0 0 20px">
                Ingresa tu PIN de seguridad para continuar con esta operación.
            </p>
            <div style="display:flex;gap:8px;margin-bottom:20px">
                <input id="pin-input"
                       type="password"
                       inputmode="numeric"
                       maxlength="8"
                       autocomplete="off"
                       placeholder="••••"
                       style="flex:1;background:#06090f;border:1px solid rgba(74,130,190,.35);color:#fff;font:700 22px/1 ui-monospace,monospace;letter-spacing:.3em;min-height:50px;padding:10px 14px;text-align:center;-webkit-appearance:none;appearance:none;transition:border-color .15s">
                <button id="pin-submit" style="background:rgba(74,130,190,.18);border:1px solid rgba(74,130,190,.55);color:#4a82be;cursor:pointer;font:700 11px/1 ui-monospace,monospace;letter-spacing:.1em;padding:0 16px;text-transform:uppercase;white-space:nowrap;transition:background .15s">
                    Verificar
                </button>
            </div>
            <p id="pin-error" style="display:none;font:700 11px/1.4 ui-monospace,monospace;color:#f87171;letter-spacing:.04em;margin:0;padding:8px 10px;background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2)"></p>
        </div>

    </div>
</div>
<?php endif; ?>

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

<!-- ══════════════════════════════════════════════════════
     MODAL: Aviso de VERSIÓN DEMO (solo lectura)
     Se muestra al intentar crear, editar o eliminar registros.
     ══════════════════════════════════════════════════════ -->
<div id="modal-demo" style="display:none;position:fixed;inset:0;z-index:10002;background:rgba(0,0,0,.9);backdrop-filter:blur(5px);align-items:center;justify-content:center">
    <div style="max-width:420px;width:calc(100% - 32px);background:#0a0e1a;border:1px solid rgba(184,147,90,.5);box-shadow:0 0 80px rgba(0,0,0,.95)">

        <!-- Cabecera del modal -->
        <div style="display:flex;align-items:center;gap:12px;padding:16px 20px;background:rgba(0,0,0,.5);border-bottom:1px solid rgba(184,147,90,.25)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b8935a" stroke-width="2" stroke-linecap="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <span style="font:700 11px/1 ui-monospace,monospace;letter-spacing:.18em;text-transform:uppercase;color:#b8935a">VERSIÓN DEMO</span>
        </div>

        <!-- Mensaje y botón -->
        <div style="padding:24px 20px">
            <p style="font:13px/1.75 ui-monospace,monospace;color:#8aa4c0;margin:0 0 8px;word-break:break-word">
                Versión DEMO, no puede realizar cambios.
            </p>
            <p style="font:11px/1.6 ui-monospace,monospace;color:#3a4a60;margin:0 0 24px">
                Puede navegar libremente por todo el sistema, pero las operaciones de crear, editar y eliminar están deshabilitadas.
            </p>
            <div style="display:flex;justify-content:flex-end">
                <button id="demo-ok" style="background:rgba(184,147,90,.15);border:1px solid rgba(184,147,90,.55);color:#b8935a;cursor:pointer;padding:10px 28px;font:700 10px/1 ui-monospace,monospace;letter-spacing:.14em;text-transform:uppercase;transition:background .15s">ENTENDIDO</button>
            </div>
        </div>

    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     MODAL: Aviso de cierre de sesión por inactividad
     ══════════════════════════════════════════════════════ -->
<?php if (Auth::check()): ?>
<div id="modal-idle" style="display:none;position:fixed;inset:0;z-index:10001;background:rgba(0,0,0,.92);backdrop-filter:blur(5px);align-items:center;justify-content:center">
    <div style="max-width:400px;width:calc(100% - 32px);background:#0a0e1a;border:1px solid rgba(184,147,90,.5);box-shadow:0 0 80px rgba(0,0,0,.95)">

        <!-- Cabecera -->
        <div style="display:flex;align-items:center;gap:12px;padding:16px 20px;background:rgba(0,0,0,.5);border-bottom:1px solid rgba(184,147,90,.25)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#b8935a" stroke-width="2" stroke-linecap="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span style="font:700 11px/1 ui-monospace,monospace;letter-spacing:.18em;text-transform:uppercase;color:#b8935a">ALERTA DE INACTIVIDAD</span>
        </div>

        <!-- Cuerpo -->
        <div style="padding:24px 20px">
            <p style="font:13px/1.75 ui-monospace,monospace;color:#8aa4c0;margin:0 0 8px">
                No se ha detectado actividad. La sesión se cerrará automáticamente en:
            </p>
            <div id="idle-countdown" style="font:700 36px/1 ui-monospace,monospace;color:#b8935a;text-align:center;padding:16px 0;letter-spacing:.1em">0:60</div>
            <p style="font:11px/1.6 ui-monospace,monospace;color:#3a4a60;margin:0 0 24px;text-align:center">
                Mueve el ratón o presiona cualquier tecla para continuar.
            </p>
            <div style="display:flex;gap:10px;justify-content:center">
                <button id="idle-stay" style="background:rgba(74,130,190,.15);border:1px solid rgba(74,130,190,.5);color:#4a82be;cursor:pointer;padding:10px 28px;font:700 10px/1 ui-monospace,monospace;letter-spacing:.14em;text-transform:uppercase">CONTINUAR SESIÓN</button>
                <button id="idle-logout" style="background:none;border:1px solid rgba(120,155,210,.25);color:#3a4a60;cursor:pointer;padding:10px 20px;font:600 10px/1 ui-monospace,monospace;letter-spacing:.12em;text-transform:uppercase">CERRAR AHORA</button>
            </div>
        </div>

    </div>
</div>
<?php endif; ?>

<script src="<?= e(url('assets/js/app.min.js')) ?>?v=<?= filemtime(APP_ROOT . '/public/assets/js/app.min.js') ?>"></script>
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

/* Modal de diagrama de sesión */
(function(){
    var modal    = document.getElementById('modal-session');
    var btnOpen  = document.getElementById('btn-session');
    var btnClose = document.getElementById('btn-close-session');
    if(!modal || !btnOpen) return;

    function open()  { modal.style.display='block'; document.body.style.overflow='hidden'; }
    function close() { modal.style.display='none';  document.body.style.overflow=''; }

    btnOpen.addEventListener('click', open);
    if(btnClose) btnClose.addEventListener('click', close);

    /* Cerrar al hacer clic en el fondo oscuro */
    modal.addEventListener('click', function(e){ if(e.target === modal) close(); });

    /* Cerrar con Escape (compartido con el otro modal) */
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') close(); });
})();

/* Modal de diagrama entidad-relación */
(function(){
    var modal    = document.getElementById('modal-er');
    var btnOpen  = document.getElementById('btn-er');
    var btnClose = document.getElementById('btn-close-er');
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

/* ── Menú de navegación en teléfono (cajón lateral) ──
   Abre y cierra el panel deslizante del menú. El cajón solo existe
   como panel en pantallas de hasta 640 px; arriba de esa medida el
   CSS lo muestra siempre desplegado en la barra y el botón se oculta,
   así que este código simplemente no se activa. */
(function(){
    var boton  = document.getElementById('nav-toggle');
    var cajon  = document.getElementById('nav-drawer');
    var fondo  = document.getElementById('nav-scrim');
    if(!boton || !cajon) return;

    var abierto = false;

    function abrir(){
        abierto = true;
        cajon.classList.add('open');
        if(fondo){ fondo.classList.add('open'); fondo.removeAttribute('hidden'); }
        boton.setAttribute('aria-expanded','true');
        boton.setAttribute('aria-label','Cerrar menú de navegación');
        /* Evita que la página de atrás se desplace mientras el menú está abierto */
        document.body.style.overflow = 'hidden';
        /* Lleva el foco al primer enlace para quien navega con teclado */
        var primero = cajon.querySelector('a, button');
        if(primero) primero.focus();
    }

    function cerrar(devolverFoco){
        if(!abierto) return;
        abierto = false;
        cajon.classList.remove('open');
        if(fondo){ fondo.classList.remove('open'); fondo.setAttribute('hidden',''); }
        boton.setAttribute('aria-expanded','false');
        boton.setAttribute('aria-label','Abrir menú de navegación');
        document.body.style.overflow = '';
        if(devolverFoco) boton.focus();
    }

    boton.addEventListener('click', function(){
        if(abierto) cerrar(false); else abrir();
    });

    /* Tocar el fondo oscuro cierra el menú */
    if(fondo) fondo.addEventListener('click', function(){ cerrar(false); });

    /* La tecla Escape también lo cierra */
    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape') cerrar(true);
    });

    /* Al elegir una opción el menú se cierra solo */
    cajon.addEventListener('click', function(e){
        if(e.target.closest('a')) cerrar(false);
    });

    /* Si la pantalla crece (girar el teléfono o pasar a tablet) el menú
       vuelve a verse completo en la barra: se cierra el panel para no
       dejar el desplazamiento de la página bloqueado */
    window.addEventListener('resize', function(){
        if(abierto && window.innerWidth > 640) cerrar(false);
    });
})();

<?php if (Auth::check()): ?>
/* ── Temporizador de inactividad ── */
(function(){
    /* Parámetros sincronizados con SESSION_TIMEOUT del servidor */
    var TIMEOUT_MS   = <?= SESSION_TIMEOUT * 1000 ?>;    // 5 minutos total
    var WARNING_MS   = 60000;                             // aviso al minuto restante
    var PING_MS      = 120000;                            // ping al servidor cada 2 min
    var LOGOUT_URL   = '<?= e(url('logout')) ?>';
    var PING_URL     = '<?= e(url('ping')) ?>';

    var modalIdle    = document.getElementById('modal-idle');
    var btnStay      = document.getElementById('idle-stay');
    var btnLogout    = document.getElementById('idle-logout');
    var countdownEl  = document.getElementById('idle-countdown');

    var warnTimer    = null;
    var logoutTimer  = null;
    var countTimer   = null;
    var lastPing     = Date.now();
    var warningShown = false;

    /* Formatea segundos como M:SS */
    function fmt(s) {
        var m = Math.floor(s / 60);
        var r = s % 60;
        return m + ':' + (r < 10 ? '0' : '') + r;
    }

    /* Muestra el modal de aviso e inicia la cuenta regresiva visual */
    function showWarning() {
        if (!modalIdle) return;
        warningShown = true;
        modalIdle.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        var secsLeft = Math.round(WARNING_MS / 1000);
        if (countdownEl) countdownEl.textContent = fmt(secsLeft);

        clearInterval(countTimer);
        countTimer = setInterval(function() {
            secsLeft--;
            if (countdownEl) countdownEl.textContent = fmt(Math.max(0, secsLeft));
        }, 1000);
    }

    /* Oculta el modal de aviso */
    function hideWarning() {
        if (!modalIdle || !warningShown) return;
        warningShown = false;
        clearInterval(countTimer);
        modalIdle.style.display = 'none';
        document.body.style.overflow = '';
    }

    /* Redirige al logout */
    function doLogout() {
        clearInterval(countTimer);
        window.location.href = LOGOUT_URL;
    }

    /* Reinicia todos los temporizadores al detectar actividad */
    function resetTimers() {
        clearTimeout(warnTimer);
        clearTimeout(logoutTimer);
        hideWarning();

        /* Ping silencioso al servidor para refrescar last_activity */
        var now = Date.now();
        if (now - lastPing >= PING_MS) {
            lastPing = now;
            fetch(PING_URL, { method: 'GET', credentials: 'same-origin' }).catch(function(){});
        }

        /* Aviso 1 minuto antes del cierre */
        warnTimer  = setTimeout(showWarning, TIMEOUT_MS - WARNING_MS);
        /* Cierre automático al cumplir el tiempo */
        logoutTimer = setTimeout(doLogout, TIMEOUT_MS);
    }

    /* Botón "Continuar sesión" en el modal */
    if (btnStay)   btnStay.addEventListener('click',   resetTimers);
    if (btnLogout) btnLogout.addEventListener('click',  doLogout);

    /* Escucha cualquier señal de actividad del usuario */
    var eventos = ['mousemove','mousedown','keydown','touchstart','scroll','click'];
    eventos.forEach(function(ev) {
        document.addEventListener(ev, resetTimers, { passive: true });
    });

    /* Arranca el temporizador desde el momento en que carga la página */
    resetTimers();
})();
<?php endif; ?>
</script>
</body>
</html>
