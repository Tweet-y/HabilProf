/**
 * Propósito: Controlar la visibilidad de secciones del formulario según el tipo de habilitación seleccionada.
 * Funcionalidad: Maneja la interfaz de usuario para mostrar/ocultar campos específicos de PrIng/PrInv vs PrTut.
 */

/**
 * Función principal para mostrar/ocultar secciones según tipo de habilitación.
 * Se ejecuta cuando cambia la selección en el dropdown de tipo_habilitacion.
 */
function toggleHabilitacionSections() {
    // Obtener referencia al elemento selector de tipo
    const tipoHabilitacion = document.getElementById('tipo_habilitacion');

    // Verificar que el elemento existe antes de agregar listener
    if (tipoHabilitacion) {
        // Agregar event listener para cambios en la selección
        tipoHabilitacion.addEventListener('change', function() {
            // Obtener el valor seleccionado
            const tipo = this.value;

            // Obtener referencias a las secciones del formulario
            const seccionPringPrinv = document.getElementById('seccion-pring-prinv');
            const seccionPrtut = document.getElementById('seccion-prtut');

            // Lógica de visibilidad basada en el tipo seleccionado
            if (tipo === 'PrTut') {
                // Para Práctica Tutelada: ocultar sección proyecto, mostrar sección práctica
                if (seccionPringPrinv) seccionPringPrinv.classList.add('hidden');
                if (seccionPrtut) seccionPrtut.classList.remove('hidden');
            } else {
                // Para PrIng/PrInv: mostrar sección proyecto, ocultar sección práctica
                if (seccionPringPrinv) seccionPringPrinv.classList.remove('hidden');
                if (seccionPrtut) seccionPrtut.classList.add('hidden');
            }
        });
    }
}

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
    // Configurar el listener para cambios en tipo de habilitación
    toggleHabilitacionSections();

    // Inicializar la visibilidad de secciones
    initializeSections();
});
