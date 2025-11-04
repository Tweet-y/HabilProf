// Función para validar formulario
function validarFormulario() {
    let isValid = true;

    // Limpiar errores previos
    document.querySelectorAll('.error-message').forEach(function(el) {
        el.style.display = 'none';
        el.textContent = '';
    });
    document.getElementById('form-error').style.display = 'none';
    document.querySelectorAll('input, textarea, select').forEach(function(el) {
        el.classList.remove('input-error');
        el.classList.remove('field-error');
    });

    // Validar título
    const titulo = document.getElementById('titulo');
    if (titulo && !titulo.checkValidity()) {
        titulo.classList.add('field-error');
        isValid = false;
    }

    // Validar descripción
    const descripcion = document.getElementById('descripcion');
    if (descripcion && !descripcion.checkValidity()) {
        descripcion.classList.add('field-error');
        isValid = false;
    }

    // Validar que no se repitan profesores en PrIng/PrInv
    const tipo = document.getElementById('tipo_habilitacion').value;
    if (tipo === 'PrIng' || tipo === 'PrInv') {
        const guia = document.getElementById('seleccion_guia').value;
        const coGuia = document.getElementById('seleccion_co_guia').value;
        const comision = document.getElementById('seleccion_comision').value;

        const profesores = [guia, coGuia, comision].filter(function(rut) { return rut !== ''; });

        if (profesores.length !== new Set(profesores).size) {
            document.getElementById('form-error').textContent = 'Un profesor no puede tener múltiples roles en la misma habilitación.';
            document.getElementById('form-error').style.display = 'block';
            document.getElementById('seleccion_guia').classList.add('input-error');
            document.getElementById('seleccion_co_guia').classList.add('input-error');
            document.getElementById('seleccion_comision').classList.add('input-error');
            isValid = false;
        }
    }

    return isValid;
}
