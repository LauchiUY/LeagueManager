# 🏆 League Manager

League Manager es una plataforma web profesional para la gestión integral de ligas y competiciones deportivas. Desarrollada con **Laravel**, la aplicación automatiza la creación de torneos, la gestión de equipos, las actas arbitrales digitales y el control disciplinario mediante un robusto sistema de roles y permisos.

## ✨ Características Principales

*   **Autenticación y Roles:** Sistema seguro de acceso para Administradores, Árbitros, Capitanes y Jugadores, con paneles de control (dashboards) personalizados y protección de rutas.
*   **Gestión de Torneos (Round-Robin):** Algoritmo automatizado para la generación de calendarios y jornadas (todos contra todos) con asignación de pistas y árbitros.
*   **Acta Digital en Tiempo Real:** Panel interactivo para que los árbitros registren eventos (goles, tarjetas amarillas/rojas, observaciones) minuto a minuto durante los partidos.
*   **Gestión de Equipos:** Los capitanes pueden administrar sus plantillas, fichar a nuevos jugadores mediante correo electrónico y expulsar miembros.
*   **Sistema Disciplinario Automatizado (`SancionesService`):** Detección y aplicación automática de sanciones (ej. partidos de suspensión por tarjetas rojas) y auditoría de alineaciones indebidas.
*   **Interfaz Moderna (Glassmorphism):** Diseño premium, dinámico y responsivo, con efectos translúcidos, animaciones suaves y una paleta de colores vibrante para una experiencia de usuario de primer nivel.

## 👥 Sistema de Roles

La aplicación divide sus funcionalidades dependiendo del tipo de usuario:

1.  **👑 Administrador:** Control total. Crea competiciones, equipos, aprueba usuarios y supervisa el funcionamiento general de todas las ligas.
2.  **⚖️ Árbitro:** Accede a las Actas Digitales de los partidos que tiene asignados. Registra los eventos del encuentro y cambia el estado del partido a "finalizado".
3.  **🛡️ Capitán:** Gestiona la plantilla de su equipo (ficha/expulsa), consulta las clasificaciones y calendarios.
4.  **🏃 Jugador:** Visualiza su perfil, sus estadísticas personales, el calendario de su equipo y las clasificaciones públicas.

## 🛠️ Tecnologías Utilizadas

*   **Backend:** [Laravel 11](https://laravel.com/) (PHP 8.2+)
*   **Base de Datos:** MySQL / Eloquent ORM
*   **Frontend:** Blade Templates, HTML5, Vanilla CSS (Diseño UI/UX Custom), Bootstrap (Layout y Grid)
*   **Arquitectura:** Patrón MVC (Model-View-Controller) apoyado por Service Pattern para la lógica de negocio compleja (ej. generación de torneos y auditorías).

## 🚀 Instalación y Configuración Local

Sigue estos pasos para desplegar el proyecto en tu entorno local:

### 1. Clonar el repositorio
```bash
git clone <url-del-repositorio>
cd LeagueManager
```

### 2. Instalar dependencias
```bash
composer install
npm install
npm run build
```

### 3. Configurar variables de entorno
Copia el archivo de ejemplo y configura tu base de datos:
```bash
cp .env.example .env
```
*(Asegúrate de editar el archivo `.env` con las credenciales correctas de tu base de datos MySQL local).*

### 4. Generar clave de aplicación
```bash
php artisan key:generate
```

### 5. Migraciones y Seeders (Poblar Base de Datos)
Para crear las tablas y rellenarlas con datos de prueba (usuarios, equipos, partidos pre-jugados, eventos y sanciones):
```bash
php artisan migrate:fresh --seed
```

### 6. Iniciar el servidor local
```bash
php artisan serve
```
La aplicación estará disponible en `http://localhost:8000`.

## 🧪 Usuarios de Prueba (Seeders)

Si has ejecutado el comando `--seed`, puedes probar la aplicación con las siguientes cuentas preconfiguradas (la contraseña para todos es `1234` salvo indicación contraria):

*   **Administrador:** `admin@admin.com` (contraseña: `admin123`)
*   **Árbitro:** `sergio@leaguemanager.com`
*   **Capitán:** `carlos@leaguemanager.com` (Capitán de Tigres FC)
*   **Jugador:** `user@user.com` (contraseña: `user123`)

## 📊 Arquitectura de Base de Datos (E-R)

La base de datos está diseñada de forma relacional asegurando la máxima integridad referencial:

- **Usuarios** (Tabla principal de autenticación y roles)
- **Equipos** (Entidad deportiva principal)
- **Competiciones** (Ligas o Torneos en curso/finalizados)
- **Partidos** (Encuentros entre un equipo local y visitante con un árbitro asignado)
- **Plantilla Jugadores** (Tabla pivote fundamental que une `usuarios` con `equipos` y define quién `es_capitan`)
- **Eventos Partido** (Historial cronológico de lo ocurrido en el campo)
- **Sanciones** (Registro disciplinario con partidos de suspensión vinculados a eventos y usuarios)

---
*Desarrollado como proyecto académico / profesional de gestión deportiva.*
