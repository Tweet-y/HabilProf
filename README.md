# **HabilProf \- Sistema de Gestión de Habilitaciones Profesionales**

**HabilProf** es una aplicación web desarrollada en Laravel 10 para la gestión, seguimiento y reporte del proceso de Habilitación Profesional (Proyectos de Título y Prácticas Profesionales) de la carrera de Ingeniería Civil Informática de la UCSC.

## **Creadores**

Este proyecto fue desarrollado por:

* Vicente Alarcón  
* Benjamín Bizama  
* Brandon Martínez  
* Rodrigo Sandoval

## **Índice**

* [Requisitos Previos](https://www.google.com/search?q=%23requisitos-previos)  
* [Instalación y Puesta en Marcha](https://www.google.com/search?q=%23instalaci%C3%B3n-y-puesta-en-marcha)  
* [Ejecución del Proyecto](https://www.google.com/search?q=%23ejecuci%C3%B3n-del-proyecto)  
* [Funcionalidades Principales](https://www.google.com/search?q=%23funcionalidades-principales)

## **Requisitos Previos**

Para ejecutar este proyecto en un entorno de desarrollo local, necesitarás tener instalado el siguiente software:

* **PHP:** ^8.1 (Tu composer.json especifica esto. La versión 8.2.12 mencionada es compatible).  
* **Composer:** Gestor de dependencias de PHP.  
* **Node.js:** Entorno de ejecución para JavaScript (v18+ recomendado).  
* **NPM:** Gestor de paquetes de Node.  
* **Base de Datos:** El proyecto está configurado para usar **PostgreSQL** (ver config/database.php).

## **Instalación y Puesta en Marcha**

Sigue estos pasos para configurar el proyecto localmente:

1. **Clonar el repositorio:**  
   git clone \[URL-DEL-REPOSITORIO\]  
   cd HabilProf-qa

2. **Instalar dependencias de PHP:**  
   composer install

3. **Instalar dependencias de Node.js:**  
   npm install

4. Configurar el entorno:  
   Copia el archivo de ejemplo .env.example para crear tu propio archivo de configuración .env.  
   cp .env.example .env

5. Configurar la base de datos en .env:  
   Abre el archivo .env y asegúrate de que las variables DB\_ apunten a tu base de datos PostgreSQL local:  
   DB\_CONNECTION=pgsql  
   DB\_HOST=127.0.0.1  
   DB\_PORT=5432  
   DB\_DATABASE=habilprof  
   DB\_USERNAME=tu\_usuario\_postgres  
   DB\_PASSWORD=tu\_contraseña\_postgres

6. **Generar la clave de la aplicación:**  
   php artisan key:generate

7. Ejecutar migraciones y seeders (¡Importante\!):  
   Este comando creará la estructura de la base de datos y la llenará con los datos de simulación (profesores, alumnos, etc.) definidos en database/seeders/CargaSeeder.php.  
   php artisan migrate:fresh \--seed

   *(Este es el comando contenido en seed.bat)*

## **Ejecución del Proyecto**

Para que la aplicación funcione correctamente, **necesitas ejecutar tres procesos en paralelo**, cada uno en su propia terminal.

### **Terminal 1: Servidor de Laravel (Backend)**

Inicia el servidor de desarrollo de PHP.

php artisan serve

*(Comando de run.bat)*

La aplicación estará disponible en http://localhost:8000.

### **Terminal 2: Servidor de Vite (Frontend)**

Inicia el servidor de Vite para compilar los assets de frontend (Tailwind CSS, Alpine.js).

npm run dev

*(Comando de login.bat)*

### **Terminal 3: Tareas Programadas (Schedule)**

Inicia el "scheduler" de Laravel. Este proceso es **crucial**, ya que simula la carga automática de datos (RF1) desde los sistemas UCSC (mock) cada minuto, actualizando profesores y notas de alumnos.

php artisan schedule:work

*(Comando de schedule.bat)*

## **Funcionalidades Principales**

Una vez que la aplicación esté corriendo y hayas iniciado sesión (puedes registrar un nuevo usuario):

* **Dashboard (/dashboard):** Menú principal.  
* **Ingresar Habilitación (/habilitaciones/ingreso):** Registra un nuevo proyecto (PrIng, PrInv) o práctica (PrTut) para un alumno.  
* **Actualizar/Eliminar (/actualizar\_eliminar):** Busca habilitaciones existentes para modificarlas o eliminarlas.  
* **Generar Listados (/listados):** Crea reportes semestrales o históricos de las habilitaciones.  
* **Sincronización Automática (RF1):** La tarea schedule:work (Terminal 3\) ejecuta cada minuto el CargaUCSCService.php para simular la carga de datos de profesores y la actualización de notas de alumnos.