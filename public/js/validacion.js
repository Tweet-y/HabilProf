/**
 * Archivo: validacion.js
 * Propósito: Validar formularios de habilitación en el frontend antes del envío.
 * Funcionalidad: Realiza validaciones síncronas y asíncronas (AJAX) para asegurar integridad de datos.
 * Integración: Trabaja con formHabilitacion.js para control de UI y con backend para validaciones complejas.
 */

/**
 * Función principal de validación del formulario.
 * Realiza validaciones en múltiples capas: campos básicos, roles duplicados, campos condicionales y límites de profesores.
 * @returns {boolean} true si todas las validaciones pasan, false si hay errores
 */
async function validarFormulario() {
    // Obtener referencia al contenedor de errores de JavaScript
    const errorDiv = document.getElementById('js-validation-error');

    // Preparar UI para nueva validación
    errorDiv.style.display = 'none'; // Ocultar errores previos
    errorDiv.innerHTML = ''; // Limpiar contenido de errores

    // Limpiar clases de error de todos los campos del formulario
    document.querySelectorAll('input, textarea, select').forEach(function(el) {
        el.classList.remove('input-error');
        el.classList.remove('field-error');
    });

    // === 1. VALIDACIÓN DE CAMPOS BÁSICOS REQUERIDOS ===

    // Validar selección de alumno
    const selectorAlumno = document.getElementById('selector_alumno_rut');
    if (selectorAlumno && !selectorAlumno.value) {
        selectorAlumno.classList.add('field-error');
        errorDiv.innerHTML = '<strong>Error de validación:</strong> Debe seleccionar un alumno.';
        errorDiv.style.display = 'block';
        return false;
    }

    // Validar selección de tipo de habilitación
    const tipoHabilitacion = document.getElementById('tipo_habilitacion');
    if (tipoHabilitacion && !tipoHabilitacion.value) {
        tipoHabilitacion.classList.add('field-error');
        errorDiv.innerHTML = '<strong>Error de validación:</strong> Debe seleccionar un tipo de habilitación.';
        errorDiv.style.display = 'block';
        return false;
    }

    // Validar selección de semestre
    const semestreInicio = document.getElementById('semestre_inicio');
    if (semestreInicio && !semestreInicio.value) {
        semestreInicio.classList.add('field-error');
        errorDiv.innerHTML = '<strong>Error de validación:</strong> Debe seleccionar un semestre de inicio.';
        errorDiv.style.display = 'block';
        return false;
    }

    // Validar título usando HTML5 validation API
    const titulo = document.getElementById('titulo');
    if (titulo && !titulo.checkValidity()) {
        titulo.classList.add('field-error');
        errorDiv.innerHTML = '<strong>Error de validación:</strong> El título no cumple con los requisitos (6-50 caracteres, solo alfanumérico y símbolos permitidos).';
        errorDiv.style.display = 'block';
        return false;
    }

    // Validar descripción usando HTML5 validation API
    const descripcion = document.getElementById('descripcion');
    if (descripcion && !descripcion.checkValidity()) {
        descripcion.classList.add('field-error');
        errorDiv.innerHTML = '<strong>Error de validación:</strong> La descripción no cumple con los requisitos (30-500 caracteres).';
        errorDiv.style.display = 'block';
        return false;
    }

    // === 2. VALIDACIÓN DE ROLES DUPLICADOS ===
    // Previene que un profesor tenga múltiples roles en la misma habilitación

    // Recopilar RUTs de profesores según el tipo de habilitación
    let profesores = [];
    const tipo = document.getElementById('tipo_habilitacion').value;

    if (tipo === 'PrIng' || tipo === 'PrInv') {
        // Para proyectos: recopilar guía, co-guía y comisión
        const guia = document.querySelector('[name="seleccion_guia_rut"]')?.value;
        const coGuia = document.querySelector('[name="seleccion_co_guia_rut"]')?.value;
        const comision = document.querySelector('[name="seleccion_comision_rut"]')?.value;
        profesores = [guia, coGuia, comision].filter(Boolean); // Filtrar valores vacíos
    } else if (tipo === 'PrTut') {
        // Para prácticas tuteladas: solo hay un profesor (tutor), no hay duplicados posibles
        profesores = [];
    }

    // Verificar duplicados usando Set para comparación eficiente
    const unicos = new Set(profesores);

    // Si hay duplicados, mostrar error y resaltar campos
    if (profesores.length !== unicos.size) {
        errorDiv.innerHTML = '<strong>Error de validación:</strong> Un profesor no puede tener múltiples roles (Guía, Co-Guía, Comisión) en la misma habilitación.';
        errorDiv.style.display = 'block';
        // Resaltar campos de profesores para indicar el error
        document.querySelector('[name="seleccion_guia_rut"]')?.classList.add('input-error');
        document.querySelector('[name="seleccion_co_guia_rut"]')?.classList.add('input-error');
        document.querySelector('[name="seleccion_comision_rut"]')?.classList.add('input-error');
        return false;
    }

    // === 3. VALIDACIÓN DE CAMPOS REQUERIDOS POR TIPO ===
    // Asegura que todos los campos obligatorios según el tipo estén completos

    const tipoValidacion = document.getElementById('tipo_habilitacion').value;

    if (tipoValidacion === 'PrIng' || tipoValidacion === 'PrInv') {
        // Validación para Proyectos de Investigación/Ingeniería
        const guia = document.querySelector('[name="seleccion_guia_rut"]')?.value;
        const comision = document.querySelector('[name="seleccion_comision_rut"]')?.value;

        // Verificar campos obligatorios: Guía y Comisión
        if (!guia || !comision) {
            errorDiv.innerHTML = '<strong>Error de validación:</strong> Debe seleccionar un Profesor Guía y un Profesor Comisión para esta modalidad.';
            errorDiv.style.display = 'block';
            // Resaltar campos faltantes
            if (!guia) document.querySelector('[name="seleccion_guia_rut"]').classList.add('input-error');
            if (!comision) document.querySelector('[name="seleccion_comision_rut"]').classList.add('input-error');
            return false;
        }
    } else if (tipoValidacion === 'PrTut') {
        // Validación para Prácticas Tuteladas
        const tutor = document.querySelector('[name="seleccion_tutor_rut"]')?.value;
        const empresa = document.getElementById('nombre_empresa').value;
        const supervisor = document.getElementById('nombre_supervisor').value;

        // Verificar campos obligatorios: Empresa, Supervisor y Tutor
        if (!empresa || !supervisor || !tutor) {
            errorDiv.innerHTML = '<strong>Error de validación:</strong> Debe completar todos los campos de la Práctica Tutelada (Empresa, Supervisor y Tutor).';
            errorDiv.style.display = 'block';
            // Resaltar campos faltantes
            if (!empresa) document.getElementById('nombre_empresa').classList.add('input-error');
            if (!supervisor) document.getElementById('nombre_supervisor').classList.add('input-error');
            if (!tutor) document.querySelector('[name="seleccion_tutor_rut"]').classList.add('input-error');
            return false;
        }
    }

    // === 4. VALIDACIÓN DE LÍMITE DE PROFESORES (AJAX) ===
    // Verifica que ningún profesor exceda el límite de 5 habilitaciones por semestre

    // Preparar datos para la petición AJAX
    const formData = new FormData();
    formData.append('semestre_inicio', document.getElementById('semestre_inicio').value);
    formData.append('tipo_habilitacion', tipoValidacion);

    // Agregar RUTs de profesores según el tipo de habilitación
    if (tipoValidacion === 'PrIng' || tipoValidacion === 'PrInv') {
        // Para proyectos: enviar guía, co-guía y comisión
        const guia = document.querySelector('[name="seleccion_guia_rut"]')?.value;
        const coGuia = document.querySelector('[name="seleccion_co_guia_rut"]')?.value;
        const comision = document.querySelector('[name="seleccion_comision_rut"]')?.value;
        formData.append('seleccion_guia_rut', guia || '');
        formData.append('seleccion_co_guia_rut', coGuia || '');
        formData.append('seleccion_comision_rut', comision || '');
        formData.append('seleccion_tutor_rut', ''); // Vacío para proyectos
    } else if (tipoValidacion === 'PrTut') {
        // Para prácticas: enviar solo tutor
        const tutor = document.querySelector('[name="seleccion_tutor_rut"]')?.value;
        formData.append('seleccion_guia_rut', ''); // Vacíos para práctica
        formData.append('seleccion_co_guia_rut', '');
        formData.append('seleccion_comision_rut', '');
        formData.append('seleccion_tutor_rut', tutor || '');
    }

    // Para actualizaciones: excluir la habilitación actual del conteo
    const buscarAlumno = document.getElementById('buscar_alumno');
    if (buscarAlumno && buscarAlumno.value) {
        formData.append('exclude_rut_alumno', buscarAlumno.value);
    }

    // Incluir token CSRF para protección
    formData.append('_token', document.querySelector('input[name="_token"]').value);

    // Realizar petición AJAX asíncrona
    try {
        const response = await fetch('/habilitaciones/check-limit', {
            method: 'POST',
            body: formData
        });

        // Procesar respuesta JSON
        const result = await response.json();

        // Si hay errores, mostrarlos y detener validación
        if (!response.ok) {
            errorDiv.innerHTML = '<strong>Error de validación:</strong> ' + result.errors.join('<br>');
            errorDiv.style.display = 'block';
            return false;
        }
    } catch (error) {
        // Manejo de errores de red o servidor
        console.error('Error checking limit:', error);
        errorDiv.innerHTML = '<strong>Error:</strong> No se pudo verificar el límite de habilitaciones. Intente nuevamente.';
        errorDiv.style.display = 'block';
        return false;
    }

    // === VALIDACIÓN COMPLETA ===
    // Si todas las validaciones pasaron, ocultar errores y retornar éxito
    errorDiv.style.display = 'none';
    return true;
}
