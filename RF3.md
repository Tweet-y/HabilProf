# Documentación de Validaciones y Lógica de Negocio - Funcionalidad Actualizar/Eliminar

    Benjamín Bizama Ramírez

## Introducción

Este documento describe específicamente las validaciones y lógica de negocio implementadas en la funcionalidad de actualizar y eliminar habilitaciones del proyecto HabilProf. Incluye análisis detallado de los archivos JavaScript `formHabilitacion.js` y `validacion.js`, el controlador `HabilitacionController.php`, las requests de validación, y la vista `actualizar_eliminar.blade.php`.

## Validaciones en UpdateHabilitacionRequest

### Campos Obligatorios y Reglas de Validación

- **tipo_habilitacion**: Obligatorio, valores permitidos: PrIng, PrInv, PrTut
- **semestre_inicio**: Obligatorio, formato string (ej: "2025-1")
- **titulo**: Obligatorio, 6-80 caracteres, regex: `/^[a-zA-Z0-9\s.,;:\'\"\&\-_()áéíóúñÁÉÍÓÚ]+$/u`
- **descripcion**: Obligatorio, 30-500 caracteres, mismo regex que título

### Validaciones Condicionales por Tipo de Habilitación

#### Para PrIng/PrInv (Proyecto de Investigación/Ingeniería)

- **seleccion_guia_rut**: Obligatorio, debe existir en tabla `profesor` y departamento DINF
- **seleccion_co_guia_rut**: Opcional, debe existir en tabla `profesor` (cualquier departamento)
- **seleccion_comision_rut**: Obligatorio, debe existir en tabla `profesor` y departamento DINF

#### Para PrTut (Práctica Tutelada)

- **nombre_empresa**: Obligatorio, máximo 50 caracteres, regex: `/^[a-zA-Z0-9\sñÑáéíóúÁÉÍÓÚ]+$/u`
- **nombre_supervisor**: Obligatorio, máximo 50 caracteres, regex: `/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u`
- **seleccion_tutor_rut**: Obligatorio, debe existir en tabla `profesor` y departamento DINF

### Mensajes de Error Personalizados

- Campos obligatorios: "Este campo es obligatorio."
- Campos condicionales: "Este campo es obligatorio para la modalidad seleccionada."
- Existencia en BD: "El valor seleccionado no es válido o no existe."
- Regex: "El formato del campo no es válido."
- Longitud: "El campo no puede tener más de :max caracteres." / "El campo debe tener al menos :min caracteres."

## Lógica de Negocio en HabilitacionController

### Método index()

1. **Recuperación de Datos**: Obtiene alumnos con habilitaciones activas, incluyendo relaciones con proyecto o prTut
2. **Cálculo de Semestres**: Recopila semestres únicos de habilitaciones existentes. En caso de no haber, no hay semestres para mostrar.
3. **Búsqueda Específica**: Si se recibe rut_alumno en la request, busca y carga la habilitación específica con relaciones, limitando semestres a anterior, actual y siguiente para edición
4. **Preparación de Vista**: Pasa alumnos, profesores (DINF y UCSC), semestres, y habilitación (si aplicable) a la vista actualizar_eliminar.blade.php

### Método edit()

1. **Propósito**: Muestra el formulario de edición para una habilitación específica, permitiendo edición directa sin pasar por búsqueda inicial
2. **Recuperación de Datos**: Obtiene alumnos con habilitaciones (para selector de búsqueda), profesores DINF/UCSC, y la habilitación específica por RUT
3. **Cálculo de Semestres**: Limita semestres a anterior, actual y siguiente para la habilitación encontrada, evitando cambios drásticos
4. **Diferencia con index()**: index() maneja búsqueda general y opcional; edit() carga directamente una habilitación específica para edición inmediata
5. **Preparación de Vista**: Pasa datos similares a index(), pero con habilitación siempre presente y semestres restringidos

### Método update()

1. **Validación Inicial**: Usa UpdateHabilitacionRequest para validar datos entrantes
2. **Verificación de Roles Únicos**: Asegura que un profesor no tenga múltiples roles en la misma habilitación
3. **Verificación de Límites**: Confirma que ningún profesor exceda 5 habilitaciones por semestre (excluyendo la actual en updates)
4. **Transacción de Base de Datos**: Garantiza atomicidad en operaciones que afectan múltiples tablas

#### Proceso de Actualización

- Actualiza siempre la tabla `habilitacion` con semestre, título y descripción
- Si cambia el tipo: elimina el registro relacionado (Proyecto/PrTut) y crea el nuevo usando updateOrCreate
- Si no cambia: actualiza el registro relacionado existente
- Maneja errores con try-catch y redirige con mensajes

### Método destroy()

1. **Eliminación en Cascada**: Primero elimina Proyecto o PrTut relacionado (si existe)
2. **Eliminación Principal**: Luego elimina el registro de Habilitacion
3. **Integridad Referencial**: Mantiene consistencia de base de datos
4. **Búsqueda por RUT**: Encuentra la habilitación usando rut_alumno como parámetro

### Método checkLimit()

- **Verificación Asíncrona**: Endpoint AJAX para validar límites antes de guardar
- **Exclusión en Updates**: Para actualizaciones, excluye la habilitación actual del conteo usando `exclude_rut_alumno`
- **Respuesta JSON**: Retorna array de errores o mensaje OK

## JavaScript - formHabilitacion.js

### Función toggleHabilitacionSections()

```javascript
// Función para mostrar/ocultar secciones según tipo de habilitación
function toggleHabilitacionSections() {
    // Agrega event listener al selector de tipo
    // Muestra/oculta secciones usando clases CSS 'hidden'
}
```

- **Propósito**: Controla la visibilidad de secciones del formulario basándose en el tipo de habilitación seleccionado
- **Implementación**: Usa `classList.add/remove('hidden')` para mostrar/ocultar elementos
- **Inicialización**: Se ejecuta en `DOMContentLoaded` y se dispara manualmente con `dispatchEvent`

### Función initializeSections()

- **Propósito**: Asegura que las secciones correctas estén visibles al cargar la página
- **Implementación**: Usa `setTimeout` para ejecutar después de que el DOM esté listo
- **Trigger**: Dispara evento 'change' en el selector de tipo para activar toggleSections

## JavaScript - validacion.js

### Función validarFormulario() - Validaciones Frontend

#### 1. Validación de Campos Básicos

```javascript
// Verifica campos requeridos: alumno, tipo, semestre, título, descripción
// Usa validación manual con regex y longitudes
```

- **Alumno**: Verifica que se haya seleccionado un alumno
- **Tipo**: Confirma selección de tipo de habilitación
- **Semestre**: Asegura selección de semestre de inicio
- **Título**: Valida longitud (6-80) y regex manual
- **Descripción**: Valida longitud (30-500) y regex manual

#### 2. Validación de Roles Duplicados

```javascript
// Previene que un profesor tenga múltiples roles en la misma habilitación
const profesores = [guia, coGuia, comision].filter(Boolean);
const unicos = new Set(profesores);
if (profesores.length !== unicos.size) {
    // Mostrar error de duplicados
}
```

- **Lógica**: Recopila RUTs de profesores según tipo de habilitación
- **Verificación**: Usa Set para detectar duplicados
- **Feedback**: Muestra mensaje de error y resalta campos afectados

#### 3. Validación de Campos Requeridos por Tipo

- **PrIng/PrInv**: Verifica Guía y Comisión obligatorios
- **PrTut**: Verifica Empresa, Supervisor y Tutor obligatorios
- **Visual**: Resalta campos faltantes con clase 'input-error'

#### 4. Validación de Límite de Profesores (AJAX)

```javascript
// Llamada asíncrona al endpoint /habilitaciones/check-limit
const response = await fetch('/habilitaciones/check-limit', {
    method: 'POST',
    body: formData
});
```

- **Propósito**: Verifica límite de 5 habilitaciones por profesor por semestre
- **Datos Enviados**: Semestre, tipo, RUTs de profesores, token CSRF, exclude_rut_alumno
- **Manejo de Errores**: Procesa respuesta JSON y muestra errores específicos

### Manejo de Errores y UX

- **Mensajes de Error**: Se muestran en div dedicado con ID 'js-validation-error'
- **Resaltado Visual**: Campos inválidos reciben clase 'field-error' o 'input-error'
- **Limpieza**: Se remueven clases de error al inicio de cada validación

## Vista - actualizar_eliminar.blade.php

### Interacciones Dinámicas

- **Búsqueda de Habilitación**: Selector de alumno para cargar datos existentes
- **Secciones Condicionales**: Usa JavaScript para mostrar/ocultar campos según tipo
- **Modal de Confirmación**: Para guardar cambios, usando componente x-modal
- **Confirmación de Eliminación**: Diálogo separado con botones Cancelar/Eliminar
- **Scroll Automático**: A mensajes de error/éxito del servidor

### Funciones JavaScript en la Vista

- **mostrarModificar()**: Muestra formulario de edición y dispara toggleSections
- **mostrarEliminar()**: Muestra confirmación de eliminación con nombre del alumno
- **guardarCambios()**: Ejecuta validación y abre modal de confirmación
- **confirmarGuardarCambios()**: Envía formulario de update
- **confirmarEliminar()**: Crea y envía formulario de delete

## Flujo de Validación Completo

### En Frontend (JavaScript)

1. **Campos Básicos** → 2. **Roles Duplicados** → 3. **Campos por Tipo** → 4. **Límite Profesores (AJAX)**

### En Backend (Laravel)

1. **UpdateHabilitacionRequest** → 2. **Validación de Roles** → 3. **Verificación de Límites** → 4. **Transacción de BD**

### Integración Frontend-Backend

- Frontend pre-valida para mejor UX
- Backend valida definitivamente para seguridad
- Comunicación AJAX para validaciones complejas (límites de profesores)
- Mensajes de error consistentes entre capas

## Consideraciones de Seguridad y Performance

### Seguridad

- **Validación en Múltiples Capas**: Frontend + Backend
- **Protección CSRF**: Tokens en formularios y AJAX
- **Sanitización**: Regex patterns para prevenir inyección
- **Autorización**: Middleware de autenticación en rutas

### Performance

- **Validación Asíncrona**: AJAX para límites evita recargas de página
- **Queries Optimizadas**: Uso de whereHas y with para relaciones
- **Transacciones**: Atomicidad sin afectar otras operaciones
- **Caching**: No implementado, pero recomendado para listas grandes
