# Documentación de Validaciones y Lógica de Negocio - Funcionalidad Actualizar/Eliminar

    Brandon Martínez Ramos


## Introducción

Este documento describe específicamente las validaciones y lógica de negocio implementadas en la funcionalidad de ingreso (crear) habilitaciones del proyecto HabilProf. Incluye análisis detallado de los archivos JavaScript `formHabilitacion.js` y `validacion.js`, además de los componentes backend relevantes.

## Validaciones en StoreHabilitacionRequest

### Campos Obligatorios y Reglas de Validación

- **selector_alumno_rut**: Obligatorio, debe existir en tabla `alumno`
- **tipo_habilitacion**: Obligatorio, valores permitidos: PrIng, PrInv, PrTut
- **semestre_inicio**: Obligatorio, formato string (ej: "2025-1")
- **titulo**: Obligatorio, 6-50 caracteres, regex: `/^[a-zA-Z0-9\s.,;:\'"&-_()]+$/`
- **descripcion**: Obligatorio, 30-500 caracteres, mismo regex que título

### Validaciones Condicionales por Tipo de Habilitación

#### Para PrIng/PrInv (Proyecto de Investigación/Ingeniería):

- **seleccion_guia_rut**: Obligatorio, debe existir en tabla `profesor`
- **seleccion_co_guia_rut**: Opcional, debe existir en tabla `profesor`
- **seleccion_comision_rut**: Obligatorio, debe existir en tabla `profesor`

#### Para PrTut (Práctica Tutelada):

- **nombre_empresa**: Obligatorio, máximo 50 caracteres, regex: `/^[a-zA-Z0-9\s]+$/u`
- **nombre_supervisor**: Obligatorio, máximo 50 caracteres, regex: `/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u`
- **seleccion_tutor_rut**: Obligatorio, debe existir en tabla `profesor`

### Mensajes de Error Personalizados

- Campos obligatorios: "El campo es obligatorio."
- Campos condicionales: "Este campo es obligatorio para la modalidad seleccionada."
- Existencia en BD: "El valor seleccionado no es válido."
- Roles duplicados: "Un profesor no puede tener múltiples roles (Guía, Co-Guía, Comisión)."

## Lógica de Negocio en HabilitacionController

### Método create()

1. **Recuperación de Datos**: Obtiene alumnos sin habilitación y todos los profesores
2. **Cálculo de Semestres**: Genera los próximos 2 semestres disponibles para creación
3. **Preparación de Vista**: Pasa datos a la vista `habilitacion_create`

#### Datos Preparados:
- **alumnos**: Alumnos que no tienen habilitación asignada
- **profesores**: Lista completa de profesores disponibles
- **semestres**: Próximos semestres calculados dinámicamente

### Método store()

1. **Validación Inicial**: Usa StoreHabilitacionRequest para validar datos entrantes
2. **Verificación de Roles Únicos**: Asegura que un profesor no tenga múltiples roles en la misma habilitación
3. **Verificación de Límites**: Confirma que ningún profesor exceda 5 habilitaciones por semestre
4. **Transacción de Base de Datos**: Garantiza atomicidad en operaciones que afectan múltiples tablas

#### Proceso de Creación:

- Crea el registro en tabla `habilitacion` con datos básicos
- Según el tipo, crea registro relacionado en `proyecto` o `pr_tut`
- Para PrIng/PrInv: Crea Proyecto con guía, co-guía (opcional), comisión
- Para PrTut: Crea PrTut con empresa, supervisor, tutor
- Retorna mensaje de éxito o errores

### Método checkLimit()

- **Verificación Asíncrona**: Endpoint AJAX para validar límites antes de guardar
- **Respuesta JSON**: Retorna errores específicos o confirmación de validez
- **Uso**: Se llama desde JavaScript antes de enviar el formulario

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
// Usa HTML5 validation API (checkValidity())
```
- **Alumno**: Verifica que se haya seleccionado un alumno
- **Tipo**: Confirma selección de tipo de habilitación
- **Semestre**: Asegura selección de semestre de inicio
- **Título**: Valida longitud (6-50) y formato usando regex
- **Descripción**: Valida longitud (30-500) caracteres

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
- **Datos Enviados**: Semestre, tipo, RUTs de profesores, token CSRF
- **Manejo de Errores**: Procesa respuesta JSON y muestra errores específicos

### Manejo de Errores y UX

- **Mensajes de Error**: Se muestran en div dedicado con ID 'js-validation-error'
- **Resaltado Visual**: Campos inválidos reciben clase 'field-error' o 'input-error'
- **Limpieza**: Se remueven clases de error al inicio de cada validación
- **Scroll Automático**: Implementado en habilitacion_create.blade.php para errores del servidor

## Flujo de Validación Completo

### En Frontend (JavaScript):

1. **Campos Básicos** → 2. **Roles Duplicados** → 3. **Campos por Tipo** → 4. **Límite Profesores (AJAX)**

### En Backend (Laravel):

1. **StoreHabilitacionRequest** → 2. **Validación de Roles** → 3. **Verificación de Límites** → 4. **Transacción de BD**

### Integración Frontend-Backend:

- Frontend pre-valida para mejor UX
- Backend valida definitivamente para seguridad
- Comunicación AJAX para validaciones complejas (límites de profesores)
- Mensajes de error consistentes entre capas

## Consideraciones de Seguridad y Performance

### Seguridad:

- **Validación en Múltiples Capas**: Frontend + Backend
- **Protección CSRF**: Tokens en formularios y AJAX
- **Sanitización**: Regex patterns para prevenir inyección
- **Autorización**: Middleware de autenticación en rutas

### Performance:

- **Validación Asíncrona**: AJAX para límites evita recargas de página
- **Queries Optimizadas**: Uso de exists para validaciones de BD
- **Transacciones**: Atomicidad sin afectar otras operaciones
- **Caching**: No implementado, pero recomendado para listas grandes

## Rutas Relacionadas

- **GET /habilitaciones/ingreso**: Muestra formulario de creación (create)
- **POST /habilitaciones/ingreso**: Procesa creación (store)
- **POST /habilitaciones/check-limit**: Validación AJAX de límites
