# Decisiones Técnicas

## Arquitectura General
- **Framework**: Laravel 10, aprovechando su estructura MVC, middleware de autenticación y validaciones integradas.
- **Base de Datos**: PostgreSQL, con migraciones para definir esquemas y relaciones.
- **Autenticación**: Sistema de autenticación integrado de Laravel, con middleware para proteger rutas.
- **Frontend**: Blade templates con Tailwind CSS para estilos, y JavaScript vanilla para interacciones dinámicas.
- **Validaciones**: Uso de Form Requests para centralizar reglas de validación y mensajes de error.
- **Lógica de Negocio**: Implementada en el controlador, con métodos privados para reutilización y separación de responsabilidades.