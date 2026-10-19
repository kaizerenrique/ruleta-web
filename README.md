# 🎰 Ruleta de Premios

Aplicación web de ruleta interactiva con panel de administración, estadísticas en tiempo real y control de participación por IP/nick. Construida con Laravel 13, Jetstream, Livewire 3 y Tailwind CSS.

Los usuarios completan un formulario (nick, nombre, apellido, aplicación de origen, captcha) y giran la ruleta. El sistema registra cada giro y permite al administrador gestionar premios, aplicaciones, límites y visualizar estadísticas detalladas.

---

## 📑 Índice

- [Características](#-características)
- [Stack técnico](#-stack-técnico)
- [Requisitos](#-requisitos)
- [Instalación local](#-instalación-local)
- [Configuración](#-configuración)
- [Uso](#-uso)
- [Estructura del proyecto](#-estructura-del-proyecto)
- [Base de datos](#-base-de-datos)
- [Seeders disponibles](#-seeders-disponibles)
- [Despliegue en producción](#-despliegue-en-producción)
- [Mantenimiento](#-mantenimiento)
- [Contribuir](#-contribuir)
- [Licencia](#-licencia)

---

## ✨ Características

### 🎡 Ruleta pública
- Ruleta animada estilo casino con canvas HTML5 (render dorado, pins, rim metálico).
- Puntero con física simulada: se "patea" al pasar cada segmento.
- Audio generado con Web Audio API (ticks al girar, fanfarria al ganar, sonido al perder).
- Formulario con validación en tiempo real vía Livewire.
- Captcha (Mews Captcha) configurable.
- Selección de aplicación de origen.

### 🔒 Control de participación
- Límite de giros por IP, por nick o ambos.
- Ventana temporal configurable:
  - Últimas 24 horas (por defecto)
  - Últimos 7 días
  - Últimos 30 días
  - Sin límite temporal
- Mensajes claros al usuario cuando alcanza el límite.

### 🛠️ Panel de administración
- **Premios**: CRUD completo con color hex, probabilidad, tipo (premio/sin premio) y estado activo.
- **Aplicaciones**: CRUD de las apps de origen (para saber de dónde viene cada usuario).
- **Resultados**: listado paginado con filtros (nick, premio, aplicación, fechas) y exportación a CSV.
- **Configuración**: activar/desactivar la ruleta, límites de giros, periodo, captcha.

### 📊 Dashboard de estadísticas
- KPIs: total giros, premios entregados, tasa de conversión, participantes únicos, giros hoy/7d/30d.
- Gráfico de línea con los giros de los últimos 14 días.
- Gráfico dona con distribución premios vs sin premio.
- Top 5 premios más entregados.
- Top 5 aplicaciones con más participación.
- Tabla con los últimos 10 giros en tiempo real.

### 🔐 Autenticación
- Laravel Fortify + Jetstream con Livewire.
- Login, registro, recuperación de contraseña, verificación de email, 2FA.
- Passkeys (WebAuthn).
- Sesiones en base de datos.
- API tokens (Sanctum).

---

## 🧰 Stack técnico

| Capa | Tecnología |
|---|---|
| Framework | Laravel 13.x |
| PHP | ^8.3 |
| Autenticación | Laravel Fortify + Jetstream 5 |
| Componentes reactivos | Livewire 3 |
| Estilos | Tailwind CSS 3 + plugins (forms, typography) |
| Build | Vite 8 |
| Base de datos | MySQL / MariaDB / SQLite |
| Captcha | mews/captcha |
| Gráficos | Chart.js 4 (vía CDN) |
| Internacionalización | laravel-lang (es / en) |

---

## 📦 Requisitos

- **PHP** >= 8.3 con extensiones: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`
- **Composer** 2.x
- **Node.js** >= 20 + npm
- **MySQL** 8+ / MariaDB 10.6+ (o SQLite para desarrollo local)
- Servidor web: Nginx (recomendado) o Apache

---

## 🚀 Instalación local

```bash
# 1. Clonar el repositorio
git clone <url-del-repo> ruleta
cd ruleta

# 2. Instalar dependencias PHP
composer install

# 3. Copiar el archivo de entorno
cp .env.example .env

# 4. Generar la clave de aplicación
php artisan key:generate

# 5. Configurar la base de datos en .env
#    (ver sección "Configuración" abajo)

# 6. Ejecutar migraciones
php artisan migrate

# 7. Cargar datos de demo (opcional pero recomendado)
php artisan db:seed --class=AplicacionesSeeder
php artisan db:seed --class=PremiosDemoSeeder

# 8. Instalar dependencias JS y compilar
npm install
npm run build

# 9. Crear el enlace simbólico de storage
php artisan storage:link

# 10. Levantar el servidor de desarrollo
php artisan serve
# → http://localhost:8000