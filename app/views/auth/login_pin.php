<?php
/*
 * Vista: segundo factor de autenticación — verificación de PIN.
 * Se muestra después de que la contraseña fue verificada correctamente.
 * El PIN fallido cuenta como intento de login y puede bloquear la cuenta.
 */

// Enmascara el correo: muestra solo los primeros 3 caracteres + *** + dominio
$emailParts    = explode('@', $userEmail ?? '');
$localMasked   = substr($emailParts[0], 0, 3) . str_repeat('*', max(2, strlen($emailParts[0]) - 3));
$emailMasked   = $localMasked . '@' . ($emailParts[1] ?? '');
?>
<div class="login-bg">

    <!-- Líneas de escaneo animadas sobre el fondo -->
    <div class="login-scan"></div>

    <!-- Contenedor principal centrado -->
    <div class="login-wrap">

        <!-- Corchetes dorados estilo HUD gubernamental -->
        <div class="login-corner tl"></div>
        <div class="login-corner tr"></div>
        <div class="login-corner bl"></div>
        <div class="login-corner br"></div>

        <!-- ── Sección héroe: emblema + ARCANUM dorado ── -->
        <div class="login-hero">

            <div class="login-seal-wrap">
                <svg class="login-seal" viewBox="0 0 80 80" fill="none" aria-hidden="true">
                    <circle cx="40" cy="40" r="37" stroke="#b8935a" stroke-width="1"
                            stroke-dasharray="2.5 2" opacity=".65"/>
                    <circle cx="40" cy="40" r="30" stroke="#b8935a" stroke-width=".6" opacity=".28"/>
                    <path d="M40 11 L57 19 L57 37 C57 51 40 60 40 60 C40 60 23 51 23 37 L23 19 Z"
                          stroke="#b8935a" stroke-width="1.3" fill="rgba(184,147,90,.07)"/>
                    <line x1="23" y1="28" x2="57" y2="28" stroke="#b8935a" stroke-width=".7" opacity=".45"/>
                    <line x1="40" y1="11" x2="40" y2="60" stroke="#b8935a" stroke-width=".35" opacity=".2"/>
                    <line x1="23" y1="36" x2="57" y2="36" stroke="#b8935a" stroke-width=".35" opacity=".2"/>
                    <circle cx="40" cy="37" r="1.8" fill="#b8935a" opacity=".75"/>
                    <circle cx="40" cy="37" r="4.2" stroke="#b8935a" stroke-width=".5" fill="none" opacity=".32"/>
                    <line x1="40" y1="2"  x2="40" y2="7"  stroke="#b8935a" stroke-width="1.5" opacity=".75"/>
                    <line x1="40" y1="73" x2="40" y2="78" stroke="#b8935a" stroke-width="1.5" opacity=".75"/>
                    <line x1="2"  y1="40" x2="7"  y2="40" stroke="#b8935a" stroke-width="1.5" opacity=".75"/>
                    <line x1="73" y1="40" x2="78" y2="40" stroke="#b8935a" stroke-width="1.5" opacity=".75"/>
                    <circle cx="13.4" cy="13.4" r="1.3" fill="#b8935a" opacity=".4"/>
                    <circle cx="66.6" cy="13.4" r="1.3" fill="#b8935a" opacity=".4"/>
                    <circle cx="13.4" cy="66.6" r="1.3" fill="#b8935a" opacity=".4"/>
                    <circle cx="66.6" cy="66.6" r="1.3" fill="#b8935a" opacity=".4"/>
                    <line x1="29" y1="42" x2="51" y2="42" stroke="#b8935a" stroke-width=".5" opacity=".3"/>
                    <line x1="29" y1="47" x2="51" y2="47" stroke="#b8935a" stroke-width=".5" opacity=".25"/>
                    <line x1="31" y1="52" x2="49" y2="52" stroke="#b8935a" stroke-width=".5" opacity=".2"/>
                </svg>
            </div>

            <div class="login-hero-pre">Sistema Clasificado</div>
            <h1 class="login-brand-name">ARCANUM</h1>
            <p class="login-hero-sub">Sistema Cifrado de Expedientes Clasificados</p>

            <div class="login-hero-tags">
                <span class="login-tag blue">PASO 2 DE 2</span>
                <span class="login-tag gold">Verificación de PIN</span>
                <span class="login-tag">C3r0d4y</span>
            </div>
        </div>

        <!-- ── Tarjeta de verificación de PIN ── -->
        <div class="login-card-v2">

            <!-- Cabecera -->
            <div class="login-card-v2-head">
                <div class="login-shield" style="background:rgba(74,130,190,.18);border-color:rgba(74,130,190,.5);color:#4a82be">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <div>
                    <div class="login-card-title">Verificación de PIN</div>
                    <div class="login-card-subtitle">Segundo factor de autenticación requerido</div>
                </div>
            </div>

            <!-- Cuerpo del formulario -->
            <div class="login-card-v2-body">

                <!-- Indicador del operador autenticado -->
                <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;
                            background:rgba(74,130,190,.06);border:1px solid rgba(74,130,190,.2);
                            margin-bottom:16px">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="rgba(74,130,190,.65)" stroke-width="2" stroke-linecap="round"
                         style="flex-shrink:0">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 21a8 8 0 0 1 16 0"/>
                    </svg>
                    <div style="min-width:0">
                        <div style="font:700 9px/1 ui-monospace,monospace;letter-spacing:.14em;
                                    text-transform:uppercase;color:rgba(74,130,190,.5);margin-bottom:3px">
                            Operador identificado
                        </div>
                        <div style="font:600 12px/1 ui-monospace,monospace;color:#a8c4e0;
                                    white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            <?= e($emailMasked) ?>
                        </div>
                    </div>
                    <!-- Indicador de paso completado -->
                    <div style="margin-left:auto;flex-shrink:0;display:flex;align-items:center;gap:5px">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                             stroke="rgba(74,222,128,.6)" stroke-width="3" stroke-linecap="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span style="font:700 8px/1 ui-monospace,monospace;letter-spacing:.1em;
                                     color:rgba(74,222,128,.55);text-transform:uppercase">Contraseña OK</span>
                    </div>
                </div>

                <form method="post" action="<?= e(url('login/pin')) ?>" autocomplete="off">
                    <?= Csrf::field() ?>

                    <!-- PIN de seguridad — valor precargado para ambiente de desarrollo -->
                    <label>PIN de seguridad
                        <input type="password"
                               name="pin"
                               inputmode="numeric"
                               maxlength="8"
                               value="12345678"
                               required
                               autofocus
                               autocomplete="off"
                               placeholder="••••"
                               style="letter-spacing:.35em;font-size:20px;text-align:center">
                    </label>

                    <button class="btn primary" type="submit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 9.9-1"/>
                        </svg>
                        Verificar PIN
                    </button>
                </form>

                <!-- Enlace para cancelar y volver al login -->
                <div style="text-align:center;margin-top:14px">
                    <a href="<?= e(url('login')) ?>"
                       style="font:11px/1 ui-monospace,monospace;color:rgba(74,130,190,.4);
                              text-decoration:none;letter-spacing:.05em"
                       onclick="fetch('<?= e(url('login/cancel')) ?>',{method:'POST',headers:{'X-CSRF-Token':'<?= e(Csrf::token()) ?>'}}).catch(()=>{})">
                        ← Cambiar usuario
                    </a>
                </div>
            </div>
        </div>

        <!-- Aviso de seguridad -->
        <div class="login-warning">
            <div class="login-warning-head">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                AUTENTICACIÓN EN DOS PASOS
            </div>
            <div class="login-warning-body">
                <p>El PIN de seguridad es el segundo factor de autenticación requerido para acceder
                   al sistema. Cada intento fallido se registra y acumula con los intentos de contraseña.
                   Tres intentos fallidos totales resultan en el <strong>bloqueo permanente</strong> de la cuenta.</p>
                <p>Esta verificación expira en <strong>2 minutos</strong>.
                   Si la sesión expira, deberás ingresar nuevamente tu contraseña.</p>
            </div>
        </div>

        <!-- Pie -->
        <div class="login-footer-v2">
            <span>C3r0d4y &copy; <?= date('Y') ?></span>
            <span>Cifrado en reposo y en tránsito</span>
        </div>

    </div><!-- /login-wrap -->
</div><!-- /login-bg -->

<!-- ══════════════════════════════════════════════════
     OVERLAY DE AMENAZA: PIN incorrecto
     Reutiliza el mismo componente del login de contraseña.
     ══════════════════════════════════════════════════ -->
<div id="login-threat-overlay"
     role="alertdialog" aria-modal="true" aria-label="PIN incorrecto"
     style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center">

    <div id="threat-bg-pulse"
         style="position:absolute;inset:0;background:radial-gradient(ellipse at 50% 40%,rgba(100,8,8,.78) 0%,rgba(55,2,2,.94) 100%)"></div>

    <div style="position:absolute;inset:0;pointer-events:none;
                background:repeating-linear-gradient(0deg,rgba(255,30,30,.025) 0px,rgba(255,30,30,.025) 1px,transparent 1px,transparent 4px)"></div>

    <div id="threat-card"
         style="position:relative;z-index:1;width:calc(100% - 40px);max-width:420px;
                background:rgba(6,2,2,.96);
                border:1px solid rgba(220,50,50,.65);
                box-shadow:0 0 0 1px rgba(180,20,20,.12),0 0 48px rgba(140,8,8,.55),0 0 100px rgba(90,2,2,.35);
                display:flex;flex-direction:column;align-items:center">

        <div style="width:100%;display:flex;align-items:center;gap:10px;
                    padding:13px 18px;border-bottom:1px solid rgba(200,30,30,.3);
                    background:rgba(160,15,15,.14)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="#f87171" stroke-width="2.5" stroke-linecap="round" style="flex-shrink:0">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <span id="threat-header-label"
                  style="font:700 10px/1 ui-monospace,monospace;letter-spacing:.22em;color:#f87171;text-transform:uppercase">
                PIN Incorrecto — Sistema ARCANUM
            </span>
        </div>

        <div id="threat-attempts-wrap"
             style="display:none;flex-direction:column;align-items:center;gap:10px;
                    padding:16px 20px 0;width:100%;border-bottom:1px solid rgba(200,30,30,.12)">
            <div style="display:flex;align-items:center;gap:14px">
                <div id="threat-dot-1" class="threat-dot"></div>
                <div id="threat-dot-2" class="threat-dot"></div>
                <div id="threat-dot-3" class="threat-dot"></div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;padding-bottom:14px">
                <span id="threat-attempt-label"
                      style="font:700 9px/1 ui-monospace,monospace;letter-spacing:.16em;
                             color:rgba(248,113,113,.55);text-transform:uppercase"></span>
                <span id="threat-locked-badge"
                      style="display:none;font:700 8px/1 ui-monospace,monospace;letter-spacing:.14em;
                             color:#f87171;background:rgba(248,113,113,.14);
                             border:1px solid rgba(248,113,113,.45);padding:2px 7px">
                    CUENTA BLOQUEADA
                </span>
            </div>
        </div>

        <p id="threat-msg"
           style="margin:16px 22px 10px;font:600 12.5px/1.7 ui-monospace,monospace;
                  color:#f0a0a0;letter-spacing:.025em;text-align:center"></p>

        <div style="position:relative;width:96px;height:96px;margin:6px 0">
            <svg width="96" height="96" viewBox="0 0 96 96"
                 style="display:block;transform:rotate(-90deg)">
                <circle cx="48" cy="48" r="40" fill="none"
                        stroke="rgba(200,30,30,.18)" stroke-width="4.5"/>
                <circle id="threat-ring" cx="48" cy="48" r="40" fill="none"
                        stroke="#f87171" stroke-width="4.5" stroke-linecap="butt"
                        stroke-dasharray="251.33" stroke-dashoffset="0"
                        style="transition:stroke-dashoffset .95s linear,stroke .6s ease"/>
            </svg>
            <div style="position:absolute;inset:0;display:flex;flex-direction:column;
                        align-items:center;justify-content:center;gap:2px">
                <span id="threat-num"
                      style="font:700 34px/1 ui-monospace,monospace;color:#f87171;letter-spacing:-.02em">10</span>
                <span style="font:600 8px/1 ui-monospace,monospace;color:rgba(248,113,113,.45);letter-spacing:.14em">SEG</span>
            </div>
        </div>

        <p style="font:600 9px/1 ui-monospace,monospace;letter-spacing:.16em;
                  color:rgba(248,113,113,.38);text-transform:uppercase;margin:0 0 16px">
            Sistema se reactiva automáticamente
        </p>

        <div style="width:100%;height:2px;background:rgba(200,30,30,.14)">
            <div id="threat-bar" style="height:100%;width:100%;background:rgba(248,113,113,.65)"></div>
        </div>

    </div>
</div>

<style>
@keyframes threatCardIn {
    from { opacity:0; transform:scale(.92) translateY(-10px); }
    to   { opacity:1; transform:scale(1)   translateY(0);     }
}
@keyframes threatBgPulse {
    0%,100% { opacity:1; }
    50%     { opacity:.82; }
}
.threat-dot {
    width:15px; height:15px; border-radius:50%;
    background:transparent;
    border:2px solid rgba(248,113,113,.18);
    transition:background .35s ease, border-color .35s ease, box-shadow .35s ease;
}
@keyframes lockedBadgePulse {
    0%,100% { opacity:1; }
    50%     { opacity:.55; }
}
#threat-bg-pulse { animation:threatBgPulse 2.4s ease-in-out infinite; }
#threat-card     { animation:threatCardIn .28s cubic-bezier(.17,.67,.42,.99) both; }
</style>

<script>
/* Overlay de bloqueo por PIN incorrecto — misma lógica que el login de contraseña */
(function () {
    var CIRCUM = 251.33;
    var TOTAL  = 10;

    var overlay      = document.getElementById('login-threat-overlay');
    var numEl        = document.getElementById('threat-num');
    var ringEl       = document.getElementById('threat-ring');
    var barEl        = document.getElementById('threat-bar');
    var msgEl        = document.getElementById('threat-msg');
    var headerLbl    = document.getElementById('threat-header-label');
    var attemptsWrap = document.getElementById('threat-attempts-wrap');
    var attemptLbl   = document.getElementById('threat-attempt-label');
    var lockedBadge  = document.getElementById('threat-locked-badge');

    function mostrarIntentos(actual, maximo, esBloqueada) {
        if (!attemptsWrap || actual < 1) return;
        attemptsWrap.style.display = 'flex';

        var colorRelleno  = esBloqueada ? 'rgba(248,113,113,1)'   : 'rgba(248,113,113,.78)';
        var sombraRelleno = esBloqueada ? '0 0 10px rgba(248,113,113,.9)' : '0 0 7px rgba(248,113,113,.55)';
        var bordeRelleno  = esBloqueada ? 'rgba(248,113,113,1)'   : 'rgba(248,113,113,.85)';

        for (var i = 1; i <= maximo; i++) {
            var dot = document.getElementById('threat-dot-' + i);
            if (!dot) continue;
            if (i <= actual) {
                dot.style.background  = colorRelleno;
                dot.style.borderColor = bordeRelleno;
                dot.style.boxShadow   = sombraRelleno;
            } else {
                dot.style.background  = 'transparent';
                dot.style.borderColor = 'rgba(248,113,113,.18)';
                dot.style.boxShadow   = 'none';
            }
        }

        if (attemptLbl) {
            if (esBloqueada) {
                attemptLbl.textContent = 'Intento ' + actual + ' de ' + maximo;
            } else {
                var restantes = maximo - actual;
                attemptLbl.textContent = 'Intento ' + actual + ' de ' + maximo
                    + ' — ' + restantes + ' ' + (restantes === 1 ? 'restante' : 'restantes');
            }
        }

        if (esBloqueada) {
            if (lockedBadge) {
                lockedBadge.style.display   = 'inline-block';
                lockedBadge.style.animation = 'lockedBadgePulse 1.4s ease-in-out infinite';
            }
            if (headerLbl) {
                headerLbl.textContent = 'Cuenta Bloqueada — Sistema ARCANUM';
            }
        }
    }

    function actualizarAnillo(segundos) {
        if (!ringEl || !numEl) return;
        numEl.textContent = segundos;
        var offset = CIRCUM * (1 - segundos / TOTAL);
        ringEl.style.strokeDashoffset = offset;
        var t = segundos / TOTAL;
        var r = Math.round(248 * t + 60  * (1 - t));
        var g = Math.round(113 * t + 18  * (1 - t));
        var b = Math.round(113 * t + 18  * (1 - t));
        ringEl.style.stroke = 'rgb(' + r + ',' + g + ',' + b + ')';
        numEl.style.color   = 'rgb(' + r + ',' + g + ',' + b + ')';
    }

    function finalizar(form) {
        if (overlay) {
            overlay.style.transition = 'opacity .55s ease';
            overlay.style.opacity    = '0';
            setTimeout(function () {
                overlay.style.display    = 'none';
                overlay.style.opacity    = '';
                overlay.style.transition = '';
                document.body.style.overflow = '';
            }, 560);
        }
        if (form) {
            form.style.pointerEvents = '';
            form.style.opacity       = '';
            /* Limpia el PIN para que el operador ingrese uno nuevo */
            var pinInput = form.querySelector('input[name="pin"]');
            if (pinInput) { pinInput.value = ''; pinInput.focus(); }
        }
    }

    function iniciarConteo(rawMsg) {
        if (!overlay) return;

        var prefijo       = rawMsg.match(/^Intento (\d+) de (\d+) — /);
        var intentoActual = prefijo ? parseInt(prefijo[1], 10) : 0;
        var intentoMax    = prefijo ? parseInt(prefijo[2], 10) : 3;
        var msg           = prefijo ? rawMsg.slice(prefijo[0].length) : rawMsg;

        var esBloqueada = intentoActual >= intentoMax
            || msg.toLowerCase().indexOf('bloqueada') !== -1
            || msg.toLowerCase().indexOf('bloqueado') !== -1
            || msg.toLowerCase().indexOf('suspendido') !== -1;

        if (esBloqueada && intentoActual === 0) {
            intentoActual = intentoMax;
        }

        if (msgEl) msgEl.textContent = msg;
        mostrarIntentos(intentoActual, intentoMax, esBloqueada);

        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        var form = document.querySelector('.login-card-v2-body form');
        if (form) {
            form.style.pointerEvents = 'none';
            form.style.opacity       = '.38';
            form.style.transition    = 'opacity .3s';
        }

        actualizarAnillo(TOTAL);

        if (barEl) {
            barEl.style.transition = '';
            barEl.style.width      = '100%';
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    barEl.style.transition = 'width ' + TOTAL + 's linear';
                    barEl.style.width      = '0%';
                });
            });
        }

        var restantes = TOTAL;
        var timer = setInterval(function () {
            restantes--;
            actualizarAnillo(Math.max(0, restantes));
            if (restantes <= 0) {
                clearInterval(timer);
                finalizar(esBloqueada ? null : form);
            }
        }, 1000);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var alertEl = document.querySelector('main .alert.alert-error');
        if (!alertEl) return;
        var msg = alertEl.textContent.trim();
        alertEl.style.display = 'none';
        iniciarConteo(msg);
    });
})();
</script>
