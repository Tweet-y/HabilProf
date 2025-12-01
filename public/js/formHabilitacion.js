/**
 * Propósito: Controlar la visibilidad de secciones del formulario según el tipo de habilitación seleccionada.
 * Funcionalidad: Maneja la interfaz de usuario para mostrar/ocultar campos específicos de PrIng/PrInv vs PrTut.
 */

/**
 * Función para inicializar las secciones al cargar la página.
 * Asegura que las secciones correctas estén visibles según el valor inicial.
 */
function initializeSections() {
    // Usar setTimeout para asegurar que el DOM esté completamente cargado
    setTimeout(function() {
        // Obtener referencia al selector de tipo
        const tipoHabilitacion = document.getElementById('tipo_habilitacion');

        // Si existe el elemento, disparar evento change para inicializar visibilidad
        if (tipoHabilitacion) {
            tipoHabilitacion.dispatchEvent(new Event('change'));
        }
    }, 100); // Pequeño delay para asegurar carga completa
}

// Inicializar funcionalidades cuando el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar la visibilidad de secciones
    initializeSections();
});
