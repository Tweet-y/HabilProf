# **HabilProf \- Sistema de Gestión de Habilitaciones Profesionales**

Para ver detalles de implementación, consulta Decisiones Técnicas.

**HabilProf** es una aplicación web desarrollada en Laravel 10 para la gestión, seguimiento y reporte del proceso de Habilitación Profesional (Proyectos de Título y Prácticas Profesionales) de la carrera de Ingeniería Civil Informática de la UCSC.

## **Creadores y Archivos Trabajados**

Este proyecto fue desarrollado por:

### **Vicente Alarcón** - RF4 (Generación de Listados), RF5 (Login) y RF6 (Registro)

- **RF4: Generación de Listados**: Interfaz y función de los Listados
  - `routes/web.php` (Rutas de listados)
  - `resources/views/listados.blade.php` (Vista principal)
  - `app/Http/Controllers/ListadoController.php` (Controlador de la función)
- **RF5: Login**: Interfaz y funcionalidad del Login
  - `routes/web.php` (Rutas de autenticación)
  - `resources/views/auth/login.blade.php` (Vista principal)
  - `database/migrations/2014_10_12_000000_create_users_table.php` (Migración tabla users)
  - `resources/views/auth/login.blade.php` (Vista principal)
  - `app/Http/Requests/Auth/LoginRequest.php` (Validaciones del Login)
- **RF6: Registro**: Interfaz y funcionalidad del Registro
  - `resources/views/auth/register.blade.php` (Vista principal)
  - `resources/lang/es/validation.php` (Validaciones)
  - `app/Models/User.php` (Modelo Base de User)

### **Benjamín Bizama** - RF3 (Actualizar/Eliminar Habilitaciones)

- **RF3: Actualizar/Eliminar Habilitaciones**: Backend y frontend de la funcionalidad
  - `routes/web.php` (Rutas relacionadas con habilitaciones)
  - `app/Http/Controllers/HabilitacionController.php` (Métodos index, edit, update, destroy, calculaSemestresActualizacion, líneas 120-341)
  - `app/Http/Requests/UpdateHabilitacionRequest.php` (Validaciones de actualización)
  - `resources/views/actualizar_eliminar.blade.php` (Vista principal)
  - `public/js/validacion.js` (Validaciones frontend)
  - `public/js/formHabilitacion.js` (Control de UI condicional)
  - `public/css/form.css` (Estilos CSS para formularios)
  - Modelos relacionados: `app/Models/Habilitacion.php`, `app/Models/Proyecto.php`, `app/Models/PrTut.php`

### **Brandon Martínez** - RF2 (Ingreso de Habilitaciones)

- **RF2: Ingreso de Habilitaciones**: Ingreso de nuevas habilitaciones
  - `routes/web.php` (Rutas relacionadas con ingreso de habilitaciones)
  - `app/Http/Controllers/HabilitacionController.php` (Métodos create, store, checkLimit, validarMultiplesRoles, validarLimitesProfesoresBackend, validarLimiteProfesorIndividual, líneas 1-118, 352-515)
  - `app/Http/Requests/StoreHabilitacionRequest.php` (Validaciones de ingreso)
  - `resources/views/habilitacion_create.blade.php` (Vista de ingreso)
  - `public/js/validacion.js` (Validaciones frontend, líneas 7-167, 175-206)
  - `public/js/formHabilitacion.js` (Control de UI condicional)
  - `public/css/form.css` (Estilos CSS para formularios)
  - Modelos relacionados: `app/Models/Habilitacion.php`, `app/Models/Proyecto.php`, `app/Models/PrTut.php`

### **Rodrigo Sandoval** - RF1 (Carga de Datos) y MR (Base de Datos)

- **RF1: Carga de Datos desde Sistemas UCSC**: Servicio de carga automática de datos
  - `routes/web.php` (Rutas relacionadas con sincronización)
  - `app/CargaUCSCService.php` (Servicio principal)
  - `app/Console/Commands/SimulacionCarga.php` (Comando para Carga Automática)
  - `app/Console/Kernel.php` (Configuración del Scheduler)
  - `app/Models/Profesor.php` (Modelo Base de Profesor)
  - `app/Models/GestionAcademica.php` (Modelo Base de Gestión Académica)
  - `app/Models/Alumno.php` (Modelo Base de Alumno)
  - `config/database.php` (Configuración de Base de Datos)
  - Migraciones de Base de Datos: ubicados en `database/migrations`
  - `database/seeders/CargaSeeder.php` (Datos de simulación)
  - Modelos relacionados: `app/Models/Alumno.php`, `app/Models/Profesor.php`, `app/Models/Habilitacion.php`, `app/Models/Proyecto.php`, `app/Models/PrTut.php`

## **Índice**

* [Requisitos Previos](https://www.google.com/search?q=%23requisitos-previos)  
* [Instalación y Puesta en Marcha](https://www.google.com/search?q=%23instalaci%C3%B3n-y-puesta-en-marcha)  
* [Ejecución del Proyecto](https://www.google.com/search?q=%23ejecuci%C3%B3n-del-proyecto)  
* [Funcionalidades Principales](https://www.google.com/search?q=%23funcionalidades-principales)

## **Requisitos Previos**

Para ejecutar este proyecto en un entorno de desarrollo local, necesitarás tener instalado el siguiente software:

* **PHP:** ^8.1.  
* **Composer:** Gestor de dependencias de PHP.  
* **Node.js:** Entorno de ejecución para JavaScript (v18+ recomendado).  
* **NPM:** Gestor de paquetes de Node.  
* **Base de Datos:** El proyecto está configurado para usar **PostgreSQL** (ver config/database.php).

## **Instalación y Puesta en Marcha**

Crea una carpeta para la aplicación, abre la terminal en la carpeta y sigue estos pasos para probarla localmente:

1. **Clonar el repositorio:**  

   ```ps
   git clone https://github.com/Tweet-y/HabilProf
   cd HabilProf
   ```

2. **Instalar dependencias de PHP:**  

   ```ps
   composer install
   ```

3. **Instalar dependencias de Node.js:**  

   ```ps
   npm install
   ```

4. Configurar el entorno:  
   Copia el archivo de ejemplo .env.example para crear tu propio archivo de configuración .env.

   ```ps
   cp .env.example .env
   ```

5. Configurar la base de datos en .env:  
   Abre el archivo .env y asegúrate de que las variables DB\_ apunten a tu base de datos PostgreSQL local:  
   DB\_CONNECTION=pgsql  
   DB\_HOST=127.0.0.1  
   DB\_PORT=5432  
   DB\_DATABASE=habilprof  
   DB\_USERNAME=tu\_usuario\_postgres  
   DB\_PASSWORD=tu\_contraseña\_postgres

6. **Generar la clave de la aplicación:**  

   ```ps
   php artisan key:generate
   ```

7. Ejecutar migraciones y seeders (¡Importante\!):  
   Este comando creará la estructura de la base de datos y la llenará con los datos de simulación (profesores, alumnos, etc.) definidos en database/seeders/CargaSeeder.php.  

   ```ps
   php artisan migrate:fresh --seed
   ```

   *(Este es el comando contenido en seed.bat)*

## **Ejecución del Proyecto**

Para que la aplicación funcione correctamente, **necesitas ejecutar tres procesos en paralelo**, cada uno en su propia terminal.

### **Terminal 1: Servidor de Laravel (Backend)**

Inicia el servidor de desarrollo de PHP.

```ps
php artisan serve
```

*(Comando de run.bat)*

La aplicación estará disponible en [http://localhost:8000].

### **Terminal 2: Servidor de Vite (Frontend)**

Inicia el servidor de Vite para compilar los assets de frontend (Tailwind CSS, Alpine.js).

```ps
npm run dev
```

*(Comando de login.bat)*

### **Terminal 3: Tareas Programadas (Schedule)**

Inicia el "scheduler" de Laravel. Este proceso es **crucial**, ya que simula la carga automática de datos (RF1) desde los sistemas UCSC (mock) cada minuto, actualizando profesores y notas de alumnos.

```ps
php artisan schedule:work
```

#### *(Comando de schedule.bat)*

## **Funcionalidades Principales**

Una vez que la aplicación esté corriendo y hayas iniciado sesión (puedes registrar un nuevo usuario):

* **Dashboard (/dashboard):** Menú principal con acceso a todas las funcionalidades del sistema.
* **Ingresar Habilitación (/ingreso):** Permite registrar un nuevo Proyecto de Título (PrIng, PrInv) o Práctica Tutelada (PrTut) para un alumno. Incluye validaciones de límites de profesores por semestre.
* **Actualizar/Eliminar (/actualizar_eliminar):** Permite buscar habilitaciones existentes por RUT de alumno para modificar datos o eliminar completamente la habilitación. Incluye validaciones de negocio y transacciones seguras.
* **Generar Listados (/listados):** Crea reportes semestrales o históricos de las habilitaciones, permitiendo filtrar por tipo, semestre y otros criterios.
* **Perfil de Usuario (/profile):** Gestión del perfil personal del usuario, incluyendo actualización de datos y eliminación de cuenta.
* **Sincronización Automática (RF1):** Proceso en segundo plano que ejecuta cada minuto el CargaUCSCService.php para simular la carga automática de datos desde sistemas UCSC, actualizando información de profesores y notas de alumnos.
