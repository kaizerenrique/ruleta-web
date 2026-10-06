<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruleta de Premios</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        #wheel-container { position: relative; width: 500px; height: 500px; margin: 20px auto; }
        #wheel { border: 6px solid #1e293b; border-radius: 50%; display: block; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        #pointer {
            position: absolute; top: -15px; left: 50%; transform: translateX(-50%);
            width: 0; height: 0;
            border-left: 20px solid transparent; border-right: 20px solid transparent;
            border-top: 40px solid #ef4444; z-index: 10;
        }
        .hidden { display: none; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center p-4">

    <h1 class="text-4xl font-bold text-slate-800 mb-2">🎡 ¡Gira la Ruleta!</h1>
    <p class="text-slate-600 mb-6">Completa tus datos y prueba tu suerte</p>

    <!-- Botón que abre el modal -->
    <button id="btn-jugar"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-full text-lg shadow-lg transition">
        🎯 ¡Jugar Ahora!
    </button>

    <!-- Contenedor de la ruleta (oculto inicialmente) -->
    <div id="wheel-container" class="hidden">
        <div id="pointer"></div>
        <canvas id="wheel" width="500" height="500"></canvas>
    </div>

    <div id="resultado" class="mt-6 text-2xl font-bold text-slate-800"></div>

    <!-- Modal (oculto por defecto) -->
    <div id="modal-overlay" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h2 class="text-2xl font-bold text-slate-800 mb-4">Ingresa tus datos</h2>

            <form id="formulario-datos">
                <div class="mb-3">
                    <input type="text" id="nick" placeholder="Nick (sin espacios)"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div class="mb-3">
                    <input type="text" id="nombre" placeholder="Nombre"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div class="mb-3">
                    <input type="text" id="apellido" placeholder="Apellido"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-slate-600 mb-1">Código de verificación</label>
                    <div class="flex items-center gap-3">
                        <img src="{{ captcha_src() }}" alt="captcha" id="captcha-img"
                             class="border rounded-lg cursor-pointer" title="Clic para refrescar">
                        <input type="text" id="captcha" placeholder="Escribe el código"
                               class="flex-1 border border-slate-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                </div>
                <div id="error-msg" class="hidden text-red-500 text-sm mb-3"></div>
                <div class="flex gap-3">
                    <button type="button" id="btn-cancelar"
                            class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold py-2 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit" id="btn-iniciar"
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-lg transition">
                        Iniciar Giro
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const premios = @json($premios);
        const canvas = document.getElementById('wheel');
        const ctx = canvas.getContext('2d');
        const numPremios = premios.length;
        const arc = numPremios > 0 ? 2 * Math.PI / numPremios : 0;
        let anguloActual = 0;
        let girando = false;

        function dibujarRuleta() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            const cx = canvas.width / 2;
            const cy = canvas.height / 2;
            const radio = Math.min(cx, cy) - 10;

            if (numPremios === 0) {
                ctx.fillStyle = '#e2e8f0';
                ctx.beginPath();
                ctx.arc(cx, cy, radio, 0, 2 * Math.PI);
                ctx.fill();
                ctx.fillStyle = '#64748b';
                ctx.font = 'bold 18px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('Sin premios configurados', cx, cy);
                return;
            }

            for (let i = 0; i < numPremios; i++) {
                const inicio = i * arc + anguloActual;
                const fin = inicio + arc;

                ctx.beginPath();
                ctx.moveTo(cx, cy);
                ctx.arc(cx, cy, radio, inicio, fin);
                ctx.fillStyle = premios[i].color || '#ccc';
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
                ctx.fillText(premios[i].nombre, radio - 20, 5);
                ctx.restore();
            }
        }

        function girar(indiceGanador) {
            if (girando) return;
            girando = true;
            document.getElementById('resultado').innerText = '';

            const anguloFlecha = 3 * Math.PI / 2;
            const centroPremio = (indiceGanador * arc) + (arc / 2);
            let target = anguloFlecha - centroPremio;
            target += 2 * Math.PI * (8 + Math.floor(Math.random() * 4));
            while (target <= anguloActual + 2 * Math.PI) {
                target += 2 * Math.PI;
            }

            const duracion = 5000;
            const inicio = performance.now();
            const anguloInicial = anguloActual;

            function animar(tiempo) {
                const progreso = Math.min((tiempo - inicio) / duracion, 1);
                const easing = 1 - Math.pow(1 - progreso, 3);
                anguloActual = anguloInicial + (target - anguloInicial) * easing;
                dibujarRuleta();

                if (progreso < 1) {
                    requestAnimationFrame(animar);
                } else {
                    girando = false;
                    document.getElementById('resultado').innerText = `🎉 ¡Ganaste: ${premios[indiceGanador].nombre}!`;
                }
            }
            requestAnimationFrame(animar);
        }

        function refrescarCaptcha() {
            document.getElementById('captcha-img').src = '{{ captcha_src() }}?' + Date.now();
        }

        // Mostrar / ocultar modal
        document.getElementById('btn-jugar').addEventListener('click', () => {
            document.getElementById('modal-overlay').classList.remove('hidden');
        });
        document.getElementById('btn-cancelar').addEventListener('click', () => {
            document.getElementById('modal-overlay').classList.add('hidden');
        });
        document.getElementById('captcha-img').addEventListener('click', refrescarCaptcha);

        // Enviar formulario
        document.getElementById('formulario-datos').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btn-iniciar');
            btn.disabled = true;
            btn.innerText = 'Verificando...';
            document.getElementById('error-msg').classList.add('hidden');

            const data = {
                nick: document.getElementById('nick').value,
                nombre: document.getElementById('nombre').value,
                apellido: document.getElementById('apellido').value,
                captcha: document.getElementById('captcha').value,
            };

            try {
                const res = await fetch('{{ route("ruleta.girar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(data),
                });

                const json = await res.json();

                if (!res.ok) {
                    document.getElementById('error-msg').innerText = json.error || json.message || 'Error al procesar.';
                    document.getElementById('error-msg').classList.remove('hidden');
                    refrescarCaptcha();
                    return;
                }

                // Cerrar modal y mostrar ruleta
                document.getElementById('modal-overlay').classList.add('hidden');
                document.getElementById('wheel-container').classList.remove('hidden');
                document.getElementById('btn-jugar').classList.add('hidden');

                const indice = premios.findIndex(p => p.id === json.premio.id);
                girar(indice >= 0 ? indice : 0);

            } catch (err) {
                console.error(err);
                document.getElementById('error-msg').innerText = 'Error de conexión. Intenta de nuevo.';
                document.getElementById('error-msg').classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Iniciar Giro';
            }
        });

        // Dibujar al cargar
        document.addEventListener('DOMContentLoaded', dibujarRuleta);
    </script>
</body>
</html>