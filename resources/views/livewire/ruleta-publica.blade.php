<!-- resources/views/livewire/ruleta-publica.blade.php -->
<div
    class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 p-4">

    <h1 class="text-5xl font-black mb-2 bg-gradient-to-b from-yellow-200 via-yellow-400 to-yellow-600 bg-clip-text text-transparent drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)] tracking-wider"
        style="font-family: 'Georgia', serif;">
        🎰 ¡GIRA LA RULETA! 🎰
    </h1>
    <p class="text-slate-300 mb-8 tracking-wide">Completa tus datos y prueba tu suerte</p>

    {{-- Modal --}}
    @if ($mostrarModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm">
            {{-- ✅ Modal más ancho: max-w-lg en vez de max-w-md --}}
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 border-2 border-yellow-500/40">
                <h2 class="text-2xl font-bold text-slate-800 mb-6 text-center">Ingresa tus datos</h2>

                <form wire:submit="girar" class="space-y-4">
                    <div>
                        <input type="text" wire:model="nick" placeholder="Nick"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-yellow-500 focus:outline-none">
                        @error('nick')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <input type="text" wire:model="nombre" placeholder="Nombre"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-yellow-500 focus:outline-none">
                        @error('nombre')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <input type="text" wire:model="apellido" placeholder="Apellido"
                            class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-yellow-500 focus:outline-none">
                        @error('apellido')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ✅ Captcha con layout grid robusto --}}
                    <div>
                        <label class="block text-sm text-slate-600 mb-1">Código de verificación</label>
                        <div class="grid grid-cols-[auto,1fr] gap-3 items-center">
                            <img src="{{ captcha_src() }}" alt="captcha" id="captcha-img"
                                class="h-12 w-auto border rounded-lg cursor-pointer bg-slate-50"
                                onclick="this.src='{{ captcha_src() }}?'+Math.random()">
                            <input type="text" wire:model="captcha" placeholder="Escribe el código"
                                class="w-full min-w-0 border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-yellow-500 focus:outline-none">
                        </div>
                        @error('captcha')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    @if ($mensajeError)
                        <div class="bg-red-100 text-red-700 p-3 rounded-lg text-sm">{{ $mensajeError }}</div>
                    @endif

                    <button type="submit"
                        class="w-full bg-gradient-to-b from-yellow-400 to-yellow-600 hover:from-yellow-500 hover:to-yellow-700 text-slate-900 font-black py-3 rounded-lg shadow-lg shadow-yellow-500/30 transition tracking-wide">
                        🎯 INICIAR GIRO
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- Contenedor de la ruleta --}}
    <div id="wheel-container" class="{{ $mostrarModal ? 'hidden' : '' }} relative">
        <div id="pointer" class="absolute top-[-22px] left-1/2 -translate-x-1/2 z-20">
            <svg width="48" height="60" viewBox="0 0 48 60">
                <defs>
                    <linearGradient id="pointerGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#fef3c7" />
                        <stop offset="40%" stop-color="#facc15" />
                        <stop offset="70%" stop-color="#a16207" />
                        <stop offset="100%" stop-color="#422006" />
                    </linearGradient>
                </defs>
                <path d="M24 58 L8 12 Q24 2 40 12 Z" fill="url(#pointerGrad)" stroke="#78350f" stroke-width="2" />
                <ellipse cx="24" cy="14" rx="6" ry="3" fill="white" opacity="0.35" />
            </svg>
        </div>

        <canvas id="wheel" width="600" height="600"
            class="rounded-full shadow-2xl drop-shadow-[0_0_40px_rgba(250,204,21,0.25)]"></canvas>
    </div>

    {{-- ✅ DESPUÉS --}}
    @if ($premioGanador && $mostrarResultado)
        <div class="mt-8 text-center animate-fade-in">
            <div
                class="text-5xl font-black bg-gradient-to-b from-yellow-200 via-yellow-400 to-yellow-600 bg-clip-text text-transparent drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)]">
                🎉 ¡GANASTE! 🎉
            </div>
            <div class="text-3xl font-bold text-white mt-2">
                {{ $premioGanador->nombre }}
            </div>
        </div>
    @endif
</div>

{{-- ============================================================ --}}
{{-- Script global: se ejecuta UNA VEZ y escucha el evento Livewire --}}
{{-- ============================================================ --}}
<script>
    (function() {
        'use strict';

        // ===== Datos del servidor (fijos durante la sesión del componente) =====
        const premios = @json($premios);
        const numPremios = premios.length;
        const arc = (2 * Math.PI) / numPremios;

        let canvas, ctx, CX, CY, RADIO_EXTERIOR, RADIO_RIM;
        let anguloActual = 0;
        let audioCtx = null;
        let dibujado = false;

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

        function playTick() {
            const a = getAudioCtx();
            if (!a) return;
            const t = a.currentTime;
            const osc = a.createOscillator();
            const gain = a.createGain();
            osc.type = 'square';
            osc.frequency.setValueAtTime(1200, t);
            osc.frequency.exponentialRampToValueAtTime(400, t + 0.03);
            gain.gain.setValueAtTime(0.12, t);
            gain.gain.exponentialRampToValueAtTime(0.001, t + 0.04);
            osc.connect(gain).connect(a.destination);
            osc.start(t);
            osc.stop(t + 0.05);
        }

        function playWinFanfare() {
            const a = getAudioCtx();
            if (!a) return;
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

        // ===== Dibujo =====
        function initCanvas() {
            canvas = document.getElementById('wheel');
            if (!canvas) return false;
            ctx = canvas.getContext('2d');
            CX = canvas.width / 2;
            CY = canvas.height / 2;
            RADIO_EXTERIOR = Math.min(CX, CY) - 30;
            RADIO_RIM = Math.min(CX, CY) - 8;
            return true;
        }

        function dibujar() {
            if (!ctx) return;
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Aro exterior dorado
            ctx.save();
            ctx.beginPath();
            ctx.arc(CX, CY, RADIO_RIM, 0, 2 * Math.PI);
            if (ctx.createConicGradient) {
                const rimGrad = ctx.createConicGradient(anguloActual, CX, CY);
                rimGrad.addColorStop(0.00, '#fef3c7');
                rimGrad.addColorStop(0.15, '#facc15');
                rimGrad.addColorStop(0.30, '#a16207');
                rimGrad.addColorStop(0.50, '#facc15');
                rimGrad.addColorStop(0.65, '#fde68a');
                rimGrad.addColorStop(0.80, '#a16207');
                rimGrad.addColorStop(1.00, '#fef3c7');
                ctx.fillStyle = rimGrad;
            } else {
                ctx.fillStyle = '#facc15';
            }
            ctx.shadowColor = 'rgba(0,0,0,0.6)';
            ctx.shadowBlur = 25;
            ctx.fill();
            ctx.restore();

            // Círculo interior oscuro
            ctx.beginPath();
            ctx.arc(CX, CY, RADIO_EXTERIOR + 6, 0, 2 * Math.PI);
            ctx.fillStyle = '#1e293b';
            ctx.fill();

            // Segmentos
            premios.forEach((p, i) => {
                const inicio = i * arc + anguloActual;
                const fin = inicio + arc;

                ctx.beginPath();
                ctx.moveTo(CX, CY);
                ctx.arc(CX, CY, RADIO_EXTERIOR, inicio, fin);
                ctx.closePath();
                ctx.fillStyle = p.color || '#999';
                ctx.fill();

                const gradRadial = ctx.createRadialGradient(CX, CY, 0, CX, CY, RADIO_EXTERIOR);
                gradRadial.addColorStop(0, 'rgba(0,0,0,0)');
                gradRadial.addColorStop(0.7, 'rgba(0,0,0,0.05)');
                gradRadial.addColorStop(1, 'rgba(0,0,0,0.35)');
                ctx.fillStyle = gradRadial;
                ctx.fill();

                ctx.strokeStyle = 'rgba(0,0,0,0.35)';
                ctx.lineWidth = 1.5;
                ctx.beginPath();
                ctx.moveTo(CX, CY);
                ctx.lineTo(CX + Math.cos(inicio) * RADIO_EXTERIOR, CY + Math.sin(inicio) * RADIO_EXTERIOR);
                ctx.stroke();
            });

            // Aro interior dorado
            ctx.beginPath();
            ctx.arc(CX, CY, RADIO_EXTERIOR, 0, 2 * Math.PI);
            ctx.strokeStyle = 'rgba(250, 204, 21, 0.6)';
            ctx.lineWidth = 3;
            ctx.stroke();

            // Pernos
            for (let i = 0; i < numPremios; i++) {
                const angulo = i * arc + anguloActual;
                const px = CX + Math.cos(angulo) * (RADIO_EXTERIOR - 12);
                const py = CY + Math.sin(angulo) * (RADIO_EXTERIOR - 12);

                ctx.beginPath();
                ctx.arc(px, py, 5, 0, 2 * Math.PI);
                ctx.fillStyle = '#78350f';
                ctx.fill();

                const pernoGrad = ctx.createRadialGradient(px - 1.5, py - 1.5, 0, px, py, 4);
                pernoGrad.addColorStop(0, '#fef9c3');
                pernoGrad.addColorStop(0.5, '#facc15');
                pernoGrad.addColorStop(1, '#a16207');
                ctx.beginPath();
                ctx.arc(px, py, 4, 0, 2 * Math.PI);
                ctx.fillStyle = pernoGrad;
                ctx.fill();
            }

            // Texto
            premios.forEach((p, i) => {
                const centro = i * arc + anguloActual + arc / 2;
                ctx.save();
                ctx.translate(CX, CY);
                ctx.rotate(centro);

                ctx.fillStyle = 'rgba(0,0,0,0.5)';
                ctx.font = 'bold 16px "Georgia", serif';
                ctx.textAlign = 'right';
                ctx.textBaseline = 'middle';
                ctx.fillText(p.nombre, RADIO_EXTERIOR - 40 + 1, 1);

                ctx.fillStyle = '#ffffff';
                ctx.strokeStyle = 'rgba(0,0,0,0.4)';
                ctx.lineWidth = 3;
                ctx.strokeText(p.nombre, RADIO_EXTERIOR - 40, 0);
                ctx.fillText(p.nombre, RADIO_EXTERIOR - 40, 0);

                ctx.restore();
            });

            // Hub central
            ctx.beginPath();
            ctx.arc(CX, CY, 52, 0, 2 * Math.PI);
            ctx.fillStyle = '#1e293b';
            ctx.fill();

            const hubGrad = ctx.createRadialGradient(CX - 8, CY - 8, 4, CX, CY, 50);
            hubGrad.addColorStop(0, '#fef9c3');
            hubGrad.addColorStop(0.4, '#facc15');
            hubGrad.addColorStop(0.8, '#a16207');
            hubGrad.addColorStop(1, '#422006');
            ctx.beginPath();
            ctx.arc(CX, CY, 50, 0, 2 * Math.PI);
            ctx.fillStyle = hubGrad;
            ctx.fill();

            const innerGrad = ctx.createRadialGradient(CX - 8, CY - 8, 2, CX, CY, 38);
            innerGrad.addColorStop(0, '#ffffff');
            innerGrad.addColorStop(0.7, '#e5e7eb');
            innerGrad.addColorStop(1, '#9ca3af');
            ctx.beginPath();
            ctx.arc(CX, CY, 38, 0, 2 * Math.PI);
            ctx.fillStyle = innerGrad;
            ctx.fill();

            ctx.font = 'bold 32px sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#facc15';
            ctx.strokeStyle = '#78350f';
            ctx.lineWidth = 2;
            ctx.strokeText('★', CX, CY + 2);
            ctx.fillText('★', CX, CY + 2);

            dibujado = true;
        }

        // ===== Animación =====
        let segmentoPrevio = -1;

        function segmentoBajoPuntero() {
            const anguloFlecha = 3 * Math.PI / 2;
            const anguloNorm = ((anguloFlecha - anguloActual) % (2 * Math.PI) + 2 * Math.PI) % (2 * Math.PI);
            return Math.floor(anguloNorm / arc) % numPremios;
        }

        function girarHacia(indiceGanador) {
            const anguloFlecha = 3 * Math.PI / 2;
            const centroPremio = indiceGanador * arc + arc / 2;
            let target = anguloFlecha - centroPremio + 2 * Math.PI * (10 + Math.floor(Math.random() * 4));
            while (target <= anguloActual + 2 * Math.PI * 8) target += 2 * Math.PI;

            const duracion = 6500;
            const inicio = performance.now();
            const anguloInicial = anguloActual;
            segmentoPrevio = -1;

            function animar(tiempo) {
                const progreso = Math.min((tiempo - inicio) / duracion, 1);
                const easing = 1 - Math.pow(1 - progreso, 4);
                anguloActual = anguloInicial + (target - anguloInicial) * easing;
                dibujar();

                const segActual = segmentoBajoPuntero();
                if (segActual !== segmentoPrevio) {
                    playTick();
                    segmentoPrevio = segActual;
                }

                if (progreso < 1) {
                    requestAnimationFrame(animar);
                } else {
                    playWinFanfare();
                    // ✅ Avisar a Livewire que la animación terminó
                    Livewire.dispatch('spin-finished');
                }
            }
            requestAnimationFrame(animar);
        }

        // ===== Inicialización cuando Livewire esté listo =====
        function initAll() {
            if (!initCanvas()) {
                // El canvas no existe todavía (modal visible). Reintentar.
                setTimeout(initAll, 150);
                return;
            }
            if (!dibujado) {
                dibujar();
            }
        }

        // Ejecutar cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAll);
        } else {
            initAll();
        }

        // ✅ Reaccionar al evento que emite Livewire
        document.addEventListener('livewire:init', () => {
            Livewire.on('spin-to', (payload) => {
                // Livewire 3 envía un array si el evento tiene parámetros
                const data = Array.isArray(payload) ? payload[0] : payload;
                const ganadorId = data?.ganadorId;
                if (!ganadorId) return;

                // Asegurarse que el canvas está listo
                if (!ctx) initCanvas();

                // Esperar un frame a que Livewire termine de actualizar el DOM
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        if (!dibujado) dibujar();
                        const idx = premios.findIndex(p => p.id === ganadorId);
                        if (idx >= 0) girarHacia(idx);
                    });
                });
            });
        });
    })();
</script>
