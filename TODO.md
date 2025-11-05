# TODO: Unify Breeze Layout with HabilProf Pages

## Steps to Complete

- [x] Edit `resources/views/layouts/app.blade.php` to add `{{ $header_styles ?? '' }}` before `</head>`.
- [x] Convert `resources/views/habilitacion_create.blade.php` to use `<x-app-layout>` with header and header_styles slots.
- [x] Convert `resources/views/actualizar_eliminar.blade.php` to use `<x-app-layout>` with header and header_styles slots.
- [x] Convert `resources/views/listados.blade.php` to use `<x-app-layout>` with header and header_styles slots.
- [x] Test the application to ensure pages load within the layout without navigation disappearing.
- [x] Verify CSS and JS assets load correctly.
