# Documentación Técnica del Proyecto HabilProf

## Introducción

Esta documentación describe las decisiones técnicas, validaciones y lógica de negocio implementadas en el proyecto HabilProf, una aplicación Laravel para la gestión de habilitaciones académicas.

## Arquitectura General

### Framework y Tecnologías

- **Framework**: Laravel 10, aprovechando su estructura MVC, middleware de autenticación y validaciones integradas.
- **Base de Datos**: PostgreSQL, con migraciones para definir esquemas y relaciones.
- **Autenticación**: Sistema de autenticación integrado de Laravel, con middleware para proteger rutas.
- **Frontend**: Blade templates con Tailwind CSS para estilos, y JavaScript vanilla para interacciones dinámicas.
- **Validaciones**: Uso de Form Requests para centralizar reglas de validación y mensajes de error.
- **Lógica de Negocio**: Implementada en el controlador, con métodos privados para reutilización y separación de responsabilidades.

### Patrón de Diseño

- **MVC (Model-View-Controller)**: Separación clara de responsabilidades.
- **Repository Pattern**: No implementado, pero lógica de BD centralizada en controladores.
- **Service Layer**: No implementado, pero métodos privados en controladores para lógica reutilizable.
- **Form Requests**: Para validación y sanitización de datos de entrada.

## Estructura de Rutas

### Organización de Rutas

- Las rutas están organizadas en grupos protegidos por autenticación (`auth` middleware).
- Uso de resource routes para CRUD estándar, con rutas adicionales para funcionalidades específicas como verificación de límites.
- Prefijo consistente en nombres de rutas (`habilitaciones.*`) para claridad.

### Rutas Implementadas

```php
// Rutas públicas
Route::get('/', function () { return view('welcome'); });
require __DIR__.'/auth.php';

// Rutas protegidas
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD Habilitaciones
    Route::get('/ingreso', [HabilitacionController::class, 'create'])->name('habilitaciones.create');
    Route::post('/ingreso', [HabilitacionController::class, 'store'])->name('habilitaciones.store');
    Route::post('/habilitaciones/check-limit', [HabilitacionController::class, 'checkLimit'])->name('habilitaciones.checkLimit');

    // Actualizar/Eliminar Habilitaciones
    Route::get('/actualizar_eliminar', [HabilitacionController::class, 'index'])->name('habilitaciones.index');
    Route::get('/actualizar_eliminar/{alumno}/edit', [HabilitacionController::class, 'edit'])->name('habilitaciones.edit');
    Route::put('/actualizar_eliminar/{alumno}', [HabilitacionController::class, 'update'])->name('habilitaciones.update');
    Route::delete('/actualizar_eliminar/{alumno}', [HabilitacionController::class, 'destroy'])->name('habilitaciones.destroy');

    // Generar Listados
    Route::get('/listados', [ListadoController::class, 'index'])->name('listados');
    Route::any('/listados/generar', [ListadoController::class, 'generar'])->name('listados.generar');
});
```

## Modelo de Datos

### Estructura de Base de Datos

- **Habilitacion**: Entidad principal, relacionada con Alumno, Proyecto y PrTut.
- **Proyecto**: Para habilitaciones PrIng/PrInv, con roles de profesor (guía, co-guía, comisión).
- **PrTut**: Para prácticas tuteladas, con empresa, supervisor y tutor.
- Relaciones a través de `rut_alumno` como clave foránea.

### Relaciones

```php
// Habilitacion.php
public function alumno() { return $this->belongsTo(Alumno::class, 'rut_alumno', 'rut_alumno'); }
public function proyecto() { return $this->hasOne(Proyecto::class, 'rut_alumno', 'rut_alumno'); }
public function prTut() { return $this->hasOne(PrTut::class, 'rut_alumno', 'rut_alumno'); }

// Alumno.php
public function habilitacion() { return $this->hasOne(Habilitacion::class, 'rut_alumno', 'rut_alumno'); }

// Proyecto.php y PrTut.php
public function habilitacion() { return $this->belongsTo(Habilitacion::class, 'rut_alumno', 'rut_alumno'); }
```

## Validaciones

### StoreHabilitacionRequest

**Campos Comunes:**

- `selector_alumno_rut`: Obligatorio, debe existir en tabla `alumno`.
- `tipo_habilitacion`: Obligatorio, uno de PrIng, PrInv, PrTut.
- `semestre_inicio`: Obligatorio, string (formato YYYY-S).
- `titulo`: Obligatorio, 6-50 chars, regex permite alfanumérico y símbolos específicos.
- `descripcion`: Obligatorio, 30-500 chars, mismo regex.

**Condicionales PrIng/PrInv:**

- `seleccion_guia_rut`: Obligatorio, existe en `profesor`.
- `seleccion_co_guia_rut`: Opcional, existe en `profesor`.
- `seleccion_comision_rut`: Obligatorio, existe en `profesor`.

**Condicionales PrTut:**

- `nombre_empresa`: Obligatorio, 50 chars máx, regex alfanumérico.
- `nombre_supervisor`: Obligatorio, 50 chars máx, regex letras con acentos.
- `seleccion_tutor_rut`: Obligatorio, existe en `profesor`.

**Mensajes:** Personalizados para claridad, e.g., "Debe seleccionar un Profesor Guía."

### UpdateHabilitacionRequest

- Similar a Store, pero `titulo` hasta 80 chars.
- Reglas adicionales para evitar conflictos en roles (aunque no implementadas en rules(), se validan en controlador).
- Mensajes incluyen "El Co-Guía no puede ser el mismo que el Guía o el de Comisión."

### Validaciones en Controlador

- **Límite de Profesores**: Máximo 5 habilitaciones por profesor por semestre, verificado antes de crear/actualizar.
- **Roles Únicos**: Un profesor no puede tener múltiples roles en la misma habilitación.
- **Transacciones**: Uso de DB::transaction para actualizaciones que afectan múltiples tablas.

## Lógica de las Funcionalidades

### Creación de Habilitaciones (store)

1. Valida datos con StoreHabilitacionRequest.
2. Verifica roles únicos y límite de profesores.
3. Crea Habilitacion con nota_final=0.0.
4. Crea Proyecto o PrTut según tipo.

### Actualización de Habilitaciones (update)

1. Valida con UpdateHabilitacionRequest.
2. Verifica roles y límites (excluyendo actual).
3. En transacción: actualiza Habilitacion, elimina/crea Proyecto/PrTut si cambia tipo.

### Eliminación (destroy)

1. Elimina Proyecto/PrTut relacionados.
2. Elimina Habilitacion.

### Índice (index)

1. Lista alumnos con habilitaciones.
2. Si rut_alumno en request, busca habilitación específica.
3. Ajusta semestres para update si hay habilitación.

### Creación (create)

1. Alumnos sin habilitación, profesores, próximos semestres.

### Verificación de Límite (checkLimit)

1. Verifica límite para profesores en semestre, usado en frontend para validación asíncrona.

## Vista actualizar_eliminar.blade.php

### Estructura

- Formulario de búsqueda por alumno.
- Sección condicional para modificar/eliminar.
- JavaScript para mostrar/ocultar secciones, validación, confirmaciones.
- Modal para confirmar updates.

### Funcionalidades JavaScript

- `toggleSections()`: Muestra/oculta campos según tipo de habilitación.
- Validación en tiempo real con AJAX (`checkLimit`).
- Scroll automático a errores.
- Confirmaciones para eliminación.

## Mejoras Futuras

- Cambiar PK de la tabla Habilitacion por {rut_alumno, semestre_inicio}.
- Optimizar consultas.
- Reestructurar validaciones.
- Agregar logs más detallados.
- Realizar testeo de forma más completa.
- Agregar un registro de auditoría con los últimos cambios que hace un usuario.
- Implementar autenticación de usuarios via email.
- Agregar notificaciones por email para cambios importantes.
