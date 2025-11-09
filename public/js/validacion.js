function validarFormulario() {
    const errorDiv = document.getElementById('js-validation-error');
    errorDiv.style.display = 'none'; // Ocultar al empezar
    errorDiv.innerHTML = ''; // Limpiar mensaje anterior

    // Limpiar errores previos de campos
    document.querySelectorAll('input, textarea, select').forEach(function(el) {
        el.classList.remove('input-error');
        el.classList.remove('field-error');
    });

    // Validar campos básicos requeridos
    const selectorAlumno = document.getElementById('selector_alumno_rut');
    if (selectorAlumno && !selectorAlumno.value) {
        selectorAlumno.classList.add('field-error');
        errorDiv.innerHTML = '<strong>Error de validación:</strong> Debe seleccionar un alumno.';
        errorDiv.style.display = 'block';
        return false;
    }

    const tipoHabilitacion = document.getElementById('tipo_habilitacion');
    if (tipoHabilitacion && !tipoHabilitacion.value) {
        tipoHabilitacion.classList.add('field-error');
        errorDiv.innerHTML = '<strong>Error de validación:</strong> Debe seleccionar un tipo de habilitación.';
        errorDiv.style.display = 'block';
        return false;
    }

    const semestreInicio = document.getElementById('semestre_inicio');
    if (semestreInicio && !semestreInicio.value) {
        semestreInicio.classList.add('field-error');
        errorDiv.innerHTML = '<strong>Error de validación:</strong> Debe seleccionar un semestre de inicio.';
        errorDiv.style.display = 'block';
        return false;
    }

    // Validar título
    const titulo = document.getElementById('titulo');
    if (titulo && !titulo.checkValidity()) {
        titulo.classList.add('field-error');
        errorDiv.innerHTML = '<strong>Error de validación:</strong> El título no cumple con los requisitos (6-50 caracteres, solo alfanumérico y símbolos permitidos).';
        errorDiv.style.display = 'block';
        return false;
    }

    // Validar descripción
    const descripcion = document.getElementById('descripcion');
    if (descripcion && !descripcion.checkValidity()) {
        descripcion.classList.add('field-error');
        errorDiv.innerHTML = '<strong>Error de validación:</strong> La descripción no cumple con los requisitos (30-500 caracteres).';
        errorDiv.style.display = 'block';
        return false;
    }

    // --- 1. VALIDAR DUPLICADOS (Tu Bug 3) ---
    const guia = document.querySelector('[name="seleccion_guia_rut"]')?.value;
    const coGuia = document.querySelector('[name="seleccion_co_guia_rut"]')?.value;
    const comision = document.querySelector('[name="seleccion_comision_rut"]')?.value;
    const tutor = document.querySelector('[name="seleccion_tutor_rut"]')?.value;

    // Filtra solo los campos que tienen un valor (no están vacíos)
    const profesores = [guia, coGuia, comision, tutor].filter(Boolean);
    const unicos = new Set(profesores);

    if (profesores.length !== unicos.size) {
        errorDiv.innerHTML = '<strong>Error de validación:</strong> Un profesor no puede tener múltiples roles (Guía, Co-Guía, Comisión) en la misma habilitación.';
        errorDiv.style.display = 'block';
        document.querySelector('[name="seleccion_guia_rut"]')?.classList.add('input-error');
        document.querySelector('[name="seleccion_co_guia_rut"]')?.classList.add('input-error');
        document.querySelector('[name="seleccion_comision_rut"]')?.classList.add('input-error');
        document.querySelector('[name="seleccion_tutor_rut"]')?.classList.add('input-error');
        return false;
    }

    // --- 2. VALIDAR PROFESORES FALTANTES (Tu Bug 2) ---
    const tipo = document.getElementById('tipo_habilitacion').value;

    if (tipo === 'PrIng' || tipo === 'PrInv') {
        // Revisa que los campos requeridos para Proyecto no estén vacíos
        if (!guia || !comision) {
            errorDiv.innerHTML = '<strong>Error de validación:</strong> Debe seleccionar un Profesor Guía y un Profesor Comisión para esta modalidad.';
            errorDiv.style.display = 'block';
            if (!guia) document.querySelector('[name="seleccion_guia_rut"]').classList.add('input-error');
            if (!comision) document.querySelector('[name="seleccion_comision_rut"]').classList.add('input-error');
            return false;
        }
    } else if (tipo === 'PrTut') {
        // Revisa que los campos requeridos para Práctica no estén vacíos
        if (!document.getElementById('nombre_empresa').value || !document.getElementById('nombre_supervisor').value || !tutor) {
            errorDiv.innerHTML = '<strong>Error de validación:</strong> Debe completar todos los campos de la Práctica Tutelada (Empresa, Supervisor y Tutor).';
            errorDiv.style.display = 'block';
            if (!document.getElementById('nombre_empresa').value) document.getElementById('nombre_empresa').classList.add('input-error');
            if (!document.getElementById('nombre_supervisor').value) document.getElementById('nombre_supervisor').classList.add('input-error');
            if (!tutor) document.querySelector('[name="seleccion_tutor_rut"]').classList.add('input-error');
            return false;
        }
    }

    // Si todo está bien
    errorDiv.style.display = 'none';
    return true;
}
