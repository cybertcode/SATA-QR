<!DOCTYPE html>
<html lang="es-PE" @yield('html_attribute')>

<head>
    @include('layouts.partials/title-meta')
    @include('layouts.partials/head-css')
    @yield('css')
</head>

<body>
    @yield('content')

    @include('layouts.partials/customizer')

    {{-- SCRIPTS MAESTROS DE LA PLANTILLA --}}
    @vite(['resources/js/app.js', 'resources/js/vendor.js'])
    @yield('scripts')
</body>

</html>
