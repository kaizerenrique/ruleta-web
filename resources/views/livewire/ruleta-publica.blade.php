<!-- resources/views/livewire/ruleta-publica.blade.php -->
<div class="min-h-screen flex flex-col items-center justify-center bg-slate-100 p-4"
     x-data="{ girando: @entangle('girando') }">

    <h1 class="text-4xl font-bold text-slate-800 mb-2">🎡 ¡Gira la Ruleta!</h1>
    <p class="text-slate-600 mb-8">Completa tus datos y prueba tu suerte</p>

    {{-- Modal con Tailwind --}}
    @if($mostrarModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h2 class="text-2xl font-bold text-slate-800 mb-6">Ingresa tus datos</h2>

            <form wire:submit="girar" class="space-y-4">
                <div>
                    <input type="text" wire:model="nick" placeholder="Nick"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('nick') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <input type="text" wire:model="nombre" placeholder="Nombre"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <input type="text" wire:model="apellido" placeholder="Apellido"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('apellido') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm text-slate-600 mb-1">Código de verificación</label>
                    <div class="flex items-center gap-3">
                        <img src="{{ captcha_src() }}" alt="captcha" id="captcha-img"
                             class="border rounded-lg cursor-pointer"
                             onclick="this.src='{{ captcha_src() }}?'+Math.random()">
                        <input type="text" wire:model="captcha" placeholder="Escribe el código"
                               class="flex-1 border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    @error('captcha') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                @if($mensajeError)
                    <div class="bg-red-100 text-red-700 p-3 rounded-lg text-sm">{{ $mensajeError }}</div>
                @endif

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg transition">
                    🎯 Iniciar Giro
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- Ruleta Canvas --}}
    <div id="wheel-container" class="{{ $mostrarModal ? 'hidden' : '' }} relative">
        <div id="pointer" class="absolute top-[-15px] left-1/2 -translate-x-1/2 z-10
             border-l-[20px] border-r-[20px] border-t-[40px]
             border-l-transparent border-r-transparent border-t-red-500"></div>
        <canvas id="wheel" width="500" height="500" class="border-8 border-slate-800 rounded-full shadow-2xl"></canvas>
    </div>

    @if($premioGanador && $girando)
    <div class="mt-8 text-3xl font-bold text-slate-800">
        🎉 ¡Ganaste: {{ $premioGanador->nombre }}!
    </div>
    @endif

    {{-- Script de la ruleta --}}
    @if($premioGanador)
    <script>
        document.addEventListener('livewire:initialized', () => {
            const premios = @json($premios);
            const ganadorId = {{ $premioGanador->id }};
            const canvas = document.getElementById('wheel');
            const ctx = canvas.getContext('2d');
            const numPremios = premios.length;
            const arc = 2 * Math.PI / numPremios;
            let anguloActual = 0;

            function dibujar() {
                const cx = canvas.width / 2, cy = canvas.height / 2;
                const radio = Math.min(cx, cy) - 10;
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                premios.forEach((p, i) => {
                    const inicio = i * arc + anguloActual;
                    ctx.beginPath();
                    ctx.moveTo(cx, cy);
                    ctx.arc(cx, cy, radio, inicio, inicio + arc);
                    ctx.fillStyle = p.color || '#ccc';
                    ctx.fill();
                    ctx.strokeStyle = '#fff';
                    ctx.lineWidth = 2;
                    ctx.stroke();

                    ctx.save();
                    ctx.translate(cx, cy);
                    ctx.rotate(inicio + arc / 2);
                    ctx.textAlign = 'right';
                    ctx.fillStyle = '#fff';
                    ctx.font = 'bold 13px sans-serif';
                    ctx.fillText(p.nombre, radio - 20, 5);
                    ctx.restore();
                });
            }

            const indiceGanador = premios.findIndex(p => p.id === ganadorId);
            const anguloFlecha = 3 * Math.PI / 2;
            const centroPremio = (indiceGanador * arc) + (arc / 2);
            let target = anguloFlecha - centroPremio + 2 * Math.PI * (8 + Math.floor(Math.random() * 4));
            while (target <= anguloActual + 2 * Math.PI) target += 2 * Math.PI;

            const duracion = 5000;
            const inicio = performance.now();
            const anguloInicial = anguloActual;

            function animar(tiempo) {
                const progreso = Math.min((tiempo - inicio) / duracion, 1);
                const easing = 1 - Math.pow(1 - progreso, 3);
                anguloActual = anguloInicial + (target - anguloInicial) * easing;
                dibujar();
                if (progreso < 1) requestAnimationFrame(animar);
            }
            requestAnimationFrame(animar);
        });
    </script>
    @endif
</div>
