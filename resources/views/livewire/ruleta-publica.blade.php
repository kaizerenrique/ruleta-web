<!-- resources/views/livewire/ruleta-publica.blade.php -->
<div
    class="min-h-screen flex flex-col items-center justify-center
            bg-gradient-to-b from-black via-slate-950 to-black
            px-3 py-6 sm:p-6">

    @if (!$ruletaActiva)
        <div class="max-w-md w-full text-center px-4">
            <div class="text-6xl sm:text-7xl md:text-8xl mb-4 sm:mb-6">🚫</div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black mb-4
                       bg-gradient-to-b from-yellow-200 via-yellow-400 to-yellow-600
                       bg-clip-text text-transparent
                       drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)] tracking-wider leading-tight"
                style="font-family: 'Georgia', serif;">
                Ruleta Inactiva
            </h1>
            <p class="text-slate-200 text-base sm:text-lg md:text-xl mb-3">
                La ruleta no está disponible en este momento.
            </p>
            <p class="text-slate-400 text-sm sm:text-base">
                Por favor, intenta más tarde o contacta al organizador del evento.
            </p>
        </div>
    @else
        <h1 class="text-2xl sm:text-4xl md:text-5xl font-black mb-2
                   text-center leading-tight px-2
                   bg-gradient-to-b from-yellow-100 via-yellow-400 to-amber-700
                   bg-clip-text text-transparent
                   drop-shadow-[0_2px_6px_rgba(212,175,55,0.5)] tracking-wider"
            style="font-family: 'Georgia', serif;">
            🎰 ¡GIRA LA RULETA! 🎰
        </h1>
        <p
            class="text-amber-200/70 mb-5 sm:mb-8 tracking-widest
                  text-xs sm:text-sm text-center px-4 uppercase">
            Completa tus datos y prueba tu suerte
        </p>

        {{-- Modal --}}
        @if ($mostrarModal)
            <div
                class="fixed inset-0 z-50 flex items-center justify-center
                        bg-black/80 p-3 sm:p-4 backdrop-blur-md
                        overflow-y-auto">
                <div
                    class="bg-gradient-to-b from-zinc-900 to-black
                            rounded-2xl shadow-2xl w-full max-w-lg
                            p-4 sm:p-6 border-2 border-amber-500/50
                            max-h-[95vh] overflow-y-auto my-auto">

                    <h2 class="text-xl sm:text-2xl font-bold text-amber-300 mb-4 sm:mb-6 text-center"
                        style="font-family: 'Georgia', serif;">
                        ✦ Ingresa tus datos ✦
                    </h2>

                    <form wire:submit="girar" class="space-y-3 sm:space-y-4">

                        <div>
                            <input type="text" wire:model="nick" placeholder="Nick" autocomplete="off"
                                inputmode="text"
                                class="w-full bg-zinc-800/60 border border-amber-500/30
                                          text-amber-50 placeholder-amber-200/40
                                          rounded-lg px-3 py-3 sm:px-4 sm:py-2
                                          text-base focus:ring-2 focus:ring-amber-500
                                          focus:border-amber-500 focus:outline-none">
                            @error('nick')
                                <span class="text-red-400 text-xs sm:text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <input type="text" wire:model="nombre" placeholder="Nombre" autocomplete="given-name"
                                class="w-full bg-zinc-800/60 border border-amber-500/30
                                          text-amber-50 placeholder-amber-200/40
                                          rounded-lg px-3 py-3 sm:px-4 sm:py-2
                                          text-base focus:ring-2 focus:ring-amber-500
                                          focus:border-amber-500 focus:outline-none">
                            @error('nombre')
                                <span class="text-red-400 text-xs sm:text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <input type="text" wire:model="apellido" placeholder="Apellido"
                                autocomplete="family-name"
                                class="w-full bg-zinc-800/60 border border-amber-500/30
                                          text-amber-50 placeholder-amber-200/40
                                          rounded-lg px-3 py-3 sm:px-4 sm:py-2
                                          text-base focus:ring-2 focus:ring-amber-500
                                          focus:border-amber-500 focus:outline-none">
                            @error('apellido')
                                <span class="text-red-400 text-xs sm:text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm text-amber-200/70 mb-1">
                                ¿De qué aplicación vienes? <span class="text-red-400">*</span>
                            </label>
                            <select wire:model="aplicacion_id" required
                                class="w-full bg-zinc-800/60 border border-amber-500/30
                                           text-amber-50
                                           rounded-lg px-3 py-3 sm:px-4 sm:py-2
                                           text-base focus:ring-2 focus:ring-amber-500
                                           focus:border-amber-500 focus:outline-none">
                                <option value="">— Selecciona una opción —</option>
                                @foreach ($aplicaciones as $app)
                                    <option value="{{ $app->id }}">{{ $app->nombre }}</option>
                                @endforeach
                            </select>
                            @error('aplicacion_id')
                                <span class="text-red-400 text-xs sm:text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm text-amber-200/70 mb-1">
                                Código de verificación
                            </label>
                            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 sm:items-center">
                                <img src="{{ captcha_src() }}" alt="captcha" id="captcha-img"
                                    class="h-12 sm:h-12 w-auto self-center sm:self-auto
                                            border border-amber-500/30 rounded-lg cursor-pointer bg-white"
                                    onclick="this.src='{{ captcha_src() }}?'+Math.random()">
                                <input type="text" wire:model="captcha" placeholder="Escribe el código"
                                    autocomplete="off" inputmode="text"
                                    class="w-full min-w-0 bg-zinc-800/60 border border-amber-500/30
                                              text-amber-50 placeholder-amber-200/40
                                              rounded-lg px-3 py-3 sm:px-4 sm:py-2
                                              text-base focus:ring-2 focus:ring-amber-500
                                              focus:border-amber-500 focus:outline-none">
                            </div>
                            @error('captcha')
                                <span class="text-red-400 text-xs sm:text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        @if ($mensajeError)
                            <div
                                class="bg-red-950/60 border border-red-500/40 text-red-300
                                        p-3 rounded-lg text-xs sm:text-sm">
                                {{ $mensajeError }}
                            </div>
                        @endif

                        <button type="submit"
                            class="w-full bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700
                                       hover:from-amber-200 hover:via-amber-400 hover:to-amber-600
                                       active:from-amber-400 active:to-amber-700
                                       text-black font-black py-3.5 sm:py-3 rounded-lg
                                       shadow-lg shadow-amber-500/40 transition tracking-widest
                                       text-base sm:text-lg touch-manipulation
                                       border border-amber-700/50">
                            🎯 INICIAR GIRO
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Contenedor de la ruleta --}}
        <div id="wheel-container"
            class="{{ $mostrarModal ? 'hidden' : '' }} relative
                    w-full max-w-[600px] mx-auto
                    drop-shadow-[0_0_60px_rgba(212,175,55,0.35)]">

            {{-- ✅ Puntero con pivote superior: se patea al pasar segmento --}}
            <div
                class="absolute -top-6 sm:-top-8 left-1/2 -translate-x-1/2 z-20
                        w-12 h-16 sm:w-16 sm:h-20 pointer-events-none">
                <svg id="pointer-svg" viewBox="0 0 60 80" class="w-full h-full"
                    style="transform-origin: 50% 15%; transition: transform 0.09s ease-out;">
                    <defs>
                        <linearGradient id="pointerGold" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#fef9c3" />
                            <stop offset="30%" stop-color="#f4d97a" />
                            <stop offset="70%" stop-color="#d4af37" />
                            <stop offset="100%" stop-color="#8b6914" />
                        </linearGradient>
                        <linearGradient id="pointerDark" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#2a2a2a" />
                            <stop offset="100%" stop-color="#000000" />
                        </linearGradient>
                    </defs>

                    {{-- Wings laterales --}}
                    <ellipse cx="14" cy="32" rx="12" ry="9" fill="url(#pointerGold)"
                        opacity="0.85" />
                    <ellipse cx="46" cy="32" rx="12" ry="9" fill="url(#pointerGold)"
                        opacity="0.85" />

                    {{-- Cuerpo principal --}}
                    <path d="M30 76 L14 32 Q30 14 46 32 Z" fill="url(#pointerGold)" stroke="#5c4a1a"
                        stroke-width="1.5" />

                    {{-- Detalle interior oscuro --}}
                    <path d="M30 70 L20 34 Q30 22 40 34 Z" fill="url(#pointerDark)" />

                    {{-- Tornillo/centro --}}
                    <circle cx="30" cy="30" r="7" fill="url(#pointerGold)" stroke="#5c4a1a"
                        stroke-width="1.2" />
                    <circle cx="30" cy="30" r="3" fill="#2a2a2a" />
                    <ellipse cx="28.5" cy="28.5" rx="1.5" ry="0.8" fill="#fff"
                        opacity="0.5" />
                </svg>
            </div>

            {{-- Canvas --}}
            <canvas id="wheel" width="700" height="700"
                class="block w-full h-auto aspect-square rounded-full
                           touch-manipulation"></canvas>
        </div>

        {{-- Mensaje de resultado --}}
        @if ($premioGanador && $mostrarResultado)
            <div class="mt-5 sm:mt-8 text-center animate-fade-in px-3 max-w-2xl">

                @if ($premioGanador->es_premio)
                    <div class="text-2xl sm:text-4xl md:text-5xl font-black
                                bg-gradient-to-b from-yellow-100 via-amber-300 to-amber-600
                                bg-clip-text text-transparent
                                drop-shadow-[0_2px_6px_rgba(212,175,55,0.5)]
                                leading-tight break-words"
                        style="font-family: 'Georgia', serif;">
                        🏆 ¡FELICIDADES, {{ strtoupper($nick) }}! 🏆
                    </div>

                    <div
                        class="text-sm sm:text-lg text-amber-300/80 mt-3 sm:mt-4
                                tracking-[0.3em] uppercase font-semibold">
                        Has ganado
                    </div>

                    <div class="text-xl sm:text-3xl md:text-4xl font-black text-amber-100
                                mt-2 sm:mt-3 break-words px-2
                                drop-shadow-[0_2px_8px_rgba(212,175,55,0.8)]"
                        style="font-family: 'Georgia', serif;">
                        ✦ {{ $premioGanador->nombre }} ✦
                    </div>

                    <div
                        class="mt-4 sm:mt-6 inline-block px-3 py-2 sm:px-4 rounded-full
                                bg-amber-500/20 border border-amber-400/50">
                        <p class="text-amber-200 text-xs sm:text-sm font-medium">
                            📸 Muestra esta pantalla al organizador para reclamar tu premio
                        </p>
                    </div>
                @else
                    <div class="text-2xl sm:text-4xl md:text-5xl font-black
                                bg-gradient-to-b from-slate-300 via-slate-400 to-slate-600
                                bg-clip-text text-transparent
                                drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)]
                                leading-tight break-words"
                        style="font-family: 'Georgia', serif;">
                        😔 ¡CASI, {{ strtoupper($nick) }}!
                    </div>

                    <div
                        class="text-xl sm:text-3xl font-bold text-slate-300 mt-3 sm:mt-4
                                break-words px-2">
                        {{ $premioGanador->nombre }}
                    </div>

                    <div class="mt-4 sm:mt-6">
                        <p class="text-slate-500 text-sm sm:text-base">
                            No fue esta vez. ¡Gracias por participar!
                        </p>
                    </div>
                @endif
            </div>
        @endif
    @endif
</div>

{{-- ============================================================ --}}
{{-- Script: dibujo estilo casino + animación + puntero con kick --}}
{{-- ============================================================ --}}
<script>
    (function() {
        'use strict';

        const premios = @json($premios);
        const numPremios = premios.length;
        const arc = numPremios > 0 ? (2 * Math.PI) / numPremios : 0;

        let canvas, ctx, CX, CY, RADIO_EXTERIOR, RADIO_RIM;
        let anguloActual = 0;
        let audioCtx = null;
        let dibujado = false;

        // ============================================================
        // UTILIDADES DE COLOR - convertir color a versión oscura tipo casino
        // ============================================================
        function darkTint(hex, saturation = 25, lightness = 8) {
            if (!hex || hex.length < 7) return `hsl(0, 0%, ${lightness}%)`;
            const r = parseInt(hex.slice(1, 3), 16) / 255;
            const g = parseInt(hex.slice(3, 5), 16) / 255;
            const b = parseInt(hex.slice(5, 7), 16) / 255;

            const max = Math.max(r, g, b);
            const min = Math.min(r, g, b);
            let h, s, l = (max + min) / 2;

            if (max === min) {
                h = s = 0;
            } else {
                const d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r:
                        h = ((g - b) / d + (g < b ? 6 : 0)) / 6;
                        break;
                    case g:
                        h = ((b - r) / d + 2) / 6;
                        break;
                    case b:
                        h = ((r - g) / d + 4) / 6;
                        break;
                }
            }

            return `hsl(${Math.round(h * 360)}, ${saturation}%, ${lightness}%)`;
        }

        function lighterTint(hex, saturation = 45, lightness = 15) {
            if (!hex || hex.length < 7) return `hsl(0, 0%, ${lightness}%)`;
            const r = parseInt(hex.slice(1, 3), 16) / 255;
            const g = parseInt(hex.slice(3, 5), 16) / 255;
            const b = parseInt(hex.slice(5, 7), 16) / 255;

            const max = Math.max(r, g, b);
            const min = Math.min(r, g, b);
            let h, s, l = (max + min) / 2;

            if (max === min) {
                h = s = 0;
            } else {
                const d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r:
                        h = ((g - b) / d + (g < b ? 6 : 0)) / 6;
                        break;
                    case g:
                        h = ((b - r) / d + 2) / 6;
                        break;
                    case b:
                        h = ((r - g) / d + 4) / 6;
                        break;
                }
            }

            return `hsl(${Math.round(h * 360)}, ${saturation}%, ${lightness}%)`;
        }

        // ===== Audio =====
        function getAudioCtx() {
            if (!audioCtx) {
                try {
                    audioCtx = new(window.AudioContext || window.webkitAudioContext)();
                } catch (e) {
                    console.warn('Audio no disponible', e);
                }
            }
            return audioCtx;
        }

        function unlockAudio() {
            const a = getAudioCtx();
            if (a && a.state === 'suspended') {
                a.resume().catch(() => {});
            }
        }
        document.addEventListener('touchstart', unlockAudio, {
            once: true,
            passive: true
        });
        document.addEventListener('click', unlockAudio, {
            once: true
        });

        function playTick() {
            const a = getAudioCtx();
            if (!a || a.state !== 'running') return;
            const t = a.currentTime;
            const osc = a.createOscillator();
            const gain = a.createGain();
            osc.type = 'square';
            osc.frequency.setValueAtTime(1400, t);
            osc.frequency.exponentialRampToValueAtTime(500, t + 0.025);
            gain.gain.setValueAtTime(0.10, t);
            gain.gain.exponentialRampToValueAtTime(0.001, t + 0.035);
            osc.connect(gain).connect(a.destination);
            osc.start(t);
            osc.stop(t + 0.04);
        }

        function playWinFanfare() {
            const a = getAudioCtx();
            if (!a || a.state !== 'running') return;
            const t0 = a.currentTime;
            [523.25, 659.25, 783.99, 1046.50].forEach((freq, i) => {
                const t = t0 + i * 0.15;
                const osc = a.createOscillator();
                const gain = a.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(freq, t);
                gain.gain.setValueAtTime(0, t);
                gain.gain.linearRampToValueAtTime(0.15, t + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.001, t + 0.5);
                osc.connect(gain).connect(a.destination);
                osc.start(t);
                osc.stop(t + 0.55);
            });
        }

        function playLoseSound() {
            const a = getAudioCtx();
            if (!a || a.state !== 'running') return;
            const t0 = a.currentTime;
            [783.99, 659.25, 523.25].forEach((freq, i) => {
                const t = t0 + i * 0.18;
                const osc = a.createOscillator();
                const gain = a.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, t);
                gain.gain.setValueAtTime(0, t);
                gain.gain.linearRampToValueAtTime(0.10, t + 0.03);
                gain.gain.exponentialRampToValueAtTime(0.001, t + 0.35);
                osc.connect(gain).connect(a.destination);
                osc.start(t);
                osc.stop(t + 0.4);
            });
        }

        // ===== Pointer kick =====
        const pointerSvg = () => document.getElementById('pointer-svg');
        let pointerKickTimer = null;

        function kickPointer() {
            const p = pointerSvg();
            if (!p) return;
            // Cancela el timer anterior para evitar parpadeos si los kicks se solapan
            if (pointerKickTimer) clearTimeout(pointerKickTimer);

            // Patea hacia un lado, luego rebota
            p.style.transform = 'rotate(-14deg)';
            pointerKickTimer = setTimeout(() => {
                p.style.transform = 'rotate(0deg)';
            }, 70);
        }

        // ===== Canvas =====
        function initCanvas() {
            canvas = document.getElementById('wheel');
            if (!canvas) return false;
            ctx = canvas.getContext('2d');
            CX = canvas.width / 2;
            CY = canvas.height / 2;
            RADIO_RIM = Math.min(CX, CY) - 12;
            RADIO_EXTERIOR = RADIO_RIM - 42; // Rim ancho estilo casino
            return true;
        }

        function dibujar() {
            if (!ctx || numPremios === 0) return;
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // ============================================
            // 1. FONDO NEGRO EXTERIOR
            // ============================================
            ctx.beginPath();
            ctx.arc(CX, CY, RADIO_RIM + 20, 0, 2 * Math.PI);
            ctx.fillStyle = '#000000';
            ctx.fill();

            // ============================================
            // 2. ARO DORADO PRINCIPAL (conic gradient metálico estático)
            // ============================================
            ctx.save();
            ctx.beginPath();
            ctx.arc(CX, CY, RADIO_RIM, 0, 2 * Math.PI);

            if (ctx.createConicGradient) {
                const rimGrad = ctx.createConicGradient(0, CX, CY);
                rimGrad.addColorStop(0.000, '#8b6914');
                rimGrad.addColorStop(0.083, '#fef9c3');
                rimGrad.addColorStop(0.167, '#d4af37');
                rimGrad.addColorStop(0.250, '#8b6914');
                rimGrad.addColorStop(0.333, '#f4d97a');
                rimGrad.addColorStop(0.417, '#d4af37');
                rimGrad.addColorStop(0.500, '#8b6914');
                rimGrad.addColorStop(0.583, '#fef9c3');
                rimGrad.addColorStop(0.667, '#d4af37');
                rimGrad.addColorStop(0.750, '#8b6914');
                rimGrad.addColorStop(0.833, '#f4d97a');
                rimGrad.addColorStop(0.917, '#d4af37');
                rimGrad.addColorStop(1.000, '#8b6914');
                ctx.fillStyle = rimGrad;
            } else {
                ctx.fillStyle = '#d4af37';
            }
            ctx.shadowColor = 'rgba(212,175,55,0.6)';
            ctx.shadowBlur = 30;
            ctx.fill();
            ctx.restore();

            // ============================================
            // 3. SEGUNDO ARO (línea dorada fina)
            // ============================================
            ctx.beginPath();
            ctx.arc(CX, CY, RADIO_RIM - 10, 0, 2 * Math.PI);
            ctx.strokeStyle = '#5c4a1a';
            ctx.lineWidth = 2;
            ctx.stroke();

            // ============================================
            // 4. FONDO INTERIOR NEGRO
            // ============================================
            ctx.beginPath();
            ctx.arc(CX, CY, RADIO_EXTERIOR + 4, 0, 2 * Math.PI);
            ctx.fillStyle = '#050505';
            ctx.fill();

            // ============================================
            // 5. SEGMENTOS OSCUROS CON TINTE SUTIL
            // ============================================
            premios.forEach((p, i) => {
                const inicio = i * arc + anguloActual;
                const fin = inicio + arc;
                const esImpar = i % 2 === 1;

                ctx.beginPath();
                ctx.moveTo(CX, CY);
                ctx.arc(CX, CY, RADIO_EXTERIOR, inicio, fin);
                ctx.closePath();

                // Gradiente radial: más oscuro en el centro, tinte del color en el borde
                const grad = ctx.createRadialGradient(CX, CY, 0, CX, CY, RADIO_EXTERIOR);
                if (esImpar) {
                    grad.addColorStop(0, darkTint(p.color || '#000000', 15, 4));
                    grad.addColorStop(0.7, darkTint(p.color || '#000000', 25, 7));
                    grad.addColorStop(1, lighterTint(p.color || '#000000', 45, 12));
                } else {
                    grad.addColorStop(0, '#000000');
                    grad.addColorStop(0.7, darkTint(p.color || '#000000', 12, 3));
                    grad.addColorStop(1, darkTint(p.color || '#000000', 30, 8));
                }
                ctx.fillStyle = grad;
                ctx.fill();

                // Línea separadora dorada
                ctx.strokeStyle = 'rgba(212, 175, 55, 0.55)';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(CX, CY);
                ctx.lineTo(CX + Math.cos(inicio) * RADIO_EXTERIOR,
                    CY + Math.sin(inicio) * RADIO_EXTERIOR);
                ctx.stroke();
            });

            // ============================================
            // 6. ARO INTERNO DORADO
            // ============================================
            ctx.beginPath();
            ctx.arc(CX, CY, RADIO_EXTERIOR, 0, 2 * Math.PI);
            ctx.strokeStyle = '#d4af37';
            ctx.lineWidth = 4;
            ctx.stroke();

            // Línea interior fina
            ctx.beginPath();
            ctx.arc(CX, CY, RADIO_EXTERIOR - 3, 0, 2 * Math.PI);
            ctx.strokeStyle = 'rgba(139, 105, 20, 0.8)';
            ctx.lineWidth = 1;
            ctx.stroke();

            // ============================================
            // 7. PINS DORADOS EN BORDES DE SEGMENTO
            // ============================================
            for (let i = 0; i < numPremios; i++) {
                const angulo = i * arc + anguloActual;
                const px = CX + Math.cos(angulo) * (RADIO_EXTERIOR - 14);
                const py = CY + Math.sin(angulo) * (RADIO_EXTERIOR - 14);

                // Base oscura
                ctx.beginPath();
                ctx.arc(px, py, 5.5, 0, 2 * Math.PI);
                ctx.fillStyle = '#000';
                ctx.fill();

                // Anillo dorado
                ctx.beginPath();
                ctx.arc(px, py, 5, 0, 2 * Math.PI);
                const pinGrad = ctx.createRadialGradient(px - 1.5, py - 1.5, 0, px, py, 5);
                pinGrad.addColorStop(0, '#fef9c3');
                pinGrad.addColorStop(0.4, '#f4d97a');
                pinGrad.addColorStop(0.8, '#d4af37');
                pinGrad.addColorStop(1, '#5c4a1a');
                ctx.fillStyle = pinGrad;
                ctx.fill();

                // Highlight
                ctx.beginPath();
                ctx.arc(px - 1.2, py - 1.2, 1.5, 0, 2 * Math.PI);
                ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
                ctx.fill();
            }

            // ============================================
            // 8. TEXTO DORADO SERIF
            // ============================================
            premios.forEach((p, i) => {
                const centro = i * arc + anguloActual + arc / 2;
                ctx.save();
                ctx.translate(CX, CY);
                ctx.rotate(centro);

                // Sombra negra
                ctx.fillStyle = 'rgba(0,0,0,0.9)';
                ctx.font = 'bold 26px "Georgia", serif';
                ctx.textAlign = 'right';
                ctx.textBaseline = 'middle';
                ctx.fillText(p.nombre, RADIO_EXTERIOR - 60 + 2, 2);

                // Texto dorado con gradiente lineal
                const textGrad = ctx.createLinearGradient(RADIO_EXTERIOR - 260, 0, RADIO_EXTERIOR - 60, 0);
                textGrad.addColorStop(0, '#8b6914');
                textGrad.addColorStop(0.4, '#fef9c3');
                textGrad.addColorStop(0.7, '#f4d97a');
                textGrad.addColorStop(1, '#d4af37');
                ctx.fillStyle = textGrad;

                // Contorno dorado oscuro
                ctx.strokeStyle = '#5c4a1a';
                ctx.lineWidth = 3;
                ctx.strokeText(p.nombre, RADIO_EXTERIOR - 60, 0);
                ctx.fillText(p.nombre, RADIO_EXTERIOR - 60, 0);

                ctx.restore();
            });

            // ============================================
            // 9. HUB CENTRAL "SPIN & GOLD"
            // ============================================
            // Anillo exterior negro
            ctx.beginPath();
            ctx.arc(CX, CY, 68, 0, 2 * Math.PI);
            ctx.fillStyle = '#000';
            ctx.fill();

            // Anillo dorado
            ctx.beginPath();
            ctx.arc(CX, CY, 64, 0, 2 * Math.PI);
            const hubRingGrad = ctx.createRadialGradient(CX - 20, CY - 20, 5, CX, CY, 64);
            hubRingGrad.addColorStop(0, '#fef9c3');
            hubRingGrad.addColorStop(0.4, '#f4d97a');
            hubRingGrad.addColorStop(0.7, '#d4af37');
            hubRingGrad.addColorStop(1, '#5c4a1a');
            ctx.fillStyle = hubRingGrad;
            ctx.fill();

            // Círculo interior negro
            ctx.beginPath();
            ctx.arc(CX, CY, 56, 0, 2 * Math.PI);
            ctx.fillStyle = '#0a0a0a';
            ctx.fill();

            // Anillo dorado fino interior
            ctx.beginPath();
            ctx.arc(CX, CY, 53, 0, 2 * Math.PI);
            ctx.strokeStyle = '#d4af37';
            ctx.lineWidth = 1;
            ctx.stroke();

            // Texto "SPIN" arriba
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            ctx.font = 'bold italic 17px "Georgia", serif';
            const spinGrad = ctx.createLinearGradient(CX - 30, CY - 15, CX + 30, CY - 5);
            spinGrad.addColorStop(0, '#8b6914');
            spinGrad.addColorStop(0.5, '#fef9c3');
            spinGrad.addColorStop(1, '#d4af37');
            ctx.fillStyle = spinGrad;
            ctx.fillText('SPIN', CX, CY - 12);

            // Línea "&"
            ctx.font = 'bold 12px "Georgia", serif';
            ctx.fillStyle = '#d4af37';
            ctx.fillText('&', CX, CY + 1);

            // Texto "GOLD" abajo
            ctx.font = 'bold 15px "Georgia", serif';
            const goldGrad = ctx.createLinearGradient(CX - 30, CY + 8, CX + 30, CY + 20);
            goldGrad.addColorStop(0, '#8b6914');
            goldGrad.addColorStop(0.5, '#fef9c3');
            goldGrad.addColorStop(1, '#d4af37');
            ctx.fillStyle = goldGrad;
            ctx.fillText('GOLD', CX, CY + 15);

            // Estrella central
            ctx.font = 'bold 14px sans-serif';
            ctx.fillStyle = '#f4d97a';
            ctx.fillText('★', CX, CY + 32);

            dibujado = true;
        }

        // ===== Animación =====
        let segmentoPrevio = -1;

        function segmentoBajoPuntero() {
            const anguloFlecha = 3 * Math.PI / 2;
            const anguloNorm = ((anguloFlecha - anguloActual) % (2 * Math.PI) + 2 * Math.PI) % (2 * Math.PI);
            return Math.floor(anguloNorm / arc) % numPremios;
        }

        function girarHacia(indiceGanador, esPremio = true) {
            if (numPremios === 0) return;

            const anguloFlecha = 3 * Math.PI / 2;
            const centroPremio = indiceGanador * arc + arc / 2;
            let target = anguloFlecha - centroPremio + 2 * Math.PI * (12 + Math.floor(Math.random() * 5));
            while (target <= anguloActual + 2 * Math.PI * 10) target += 2 * Math.PI;

            // ✅ Duración extendida para mayor suspenso
            const esMovil = window.matchMedia('(max-width: 640px)').matches;
            const duracion = esMovil ? 7500 : 9000;

            const inicio = performance.now();
            const anguloInicial = anguloActual;
            segmentoPrevio = -1;

            function animar(tiempo) {
                const progreso = Math.min((tiempo - inicio) / duracion, 1);

                // ✅ easeOutQuint: arranque rápido con larga desaceleración
                const easing = 1 - Math.pow(1 - progreso, 5);

                anguloActual = anguloInicial + (target - anguloInicial) * easing;
                dibujar();

                const segActual = segmentoBajoPuntero();
                if (segActual !== segmentoPrevio) {
                    playTick();
                    kickPointer();
                    segmentoPrevio = segActual;
                }

                if (progreso < 1) {
                    requestAnimationFrame(animar);
                } else {
                    if (esPremio) {
                        playWinFanfare();
                    } else {
                        playLoseSound();
                    }
                    // Reset del pointer
                    const p = pointerSvg();
                    if (p) p.style.transform = 'rotate(0deg)';
                    Livewire.dispatch('spin-finished');
                }
            }
            requestAnimationFrame(animar);
        }

        // ===== Init =====
        function initAll() {
            if (!initCanvas()) {
                setTimeout(initAll, 150);
                return;
            }
            if (!dibujado) {
                dibujar();
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAll);
        } else {
            initAll();
        }

        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (ctx && dibujado) dibujar();
            }, 200);
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('spin-to', (payload) => {
                const data = Array.isArray(payload) ? payload[0] : payload;
                const ganadorId = data?.ganadorId;
                const esPremio = data?.esPremio ?? true;
                if (!ganadorId) return;

                unlockAudio();
                if (!ctx) initCanvas();

                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        if (!dibujado) dibujar();
                        const idx = premios.findIndex(p => p.id === ganadorId);
                        if (idx >= 0) girarHacia(idx, esPremio);
                    });
                });
            });
        });
    })();
</script>
