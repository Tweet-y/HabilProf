// Función para mostrar/ocultar secciones según tipo de habilitación
function toggleHabilitacionSections() {
    const tipoHabilitacion = document.getElementById('tipo_habilitacion');
    if (tipoHabilitacion) {
        tipoHabilitacion.addEventListener('change', function() {
            const tipo = this.value;
            const seccionPringPrinv = document.getElementById('seccion-pring-prinv');
            const seccionPrtut = document.getElementById('seccion-prtut');

            if (tipo === 'PrTut') {
                if (seccionPringPrinv) seccionPringPrinv.classList.add('hidden');
                if (seccionPrtut) seccionPrtut.classList.remove('hidden');
            } else {
                if (seccionPringPrinv) seccionPringPrinv.classList.remove('hidden');
                if (seccionPrtut) seccionPrtut.classList.add('hidden');
            }
        });
    }
}

// Función para inicializar secciones ocultas
function initializeSections() {
    // Trigger change event for tipo_habilitacion to show/hide sections
    setTimeout(function() {
        const tipoHabilitacion = document.getElementById('tipo_habilitacion');
        if (tipoHabilitacion) {
            tipoHabilitacion.dispatchEvent(new Event('change'));
        }
    }, 100);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    toggleHabilitacionSections();
    initializeSections();
});
