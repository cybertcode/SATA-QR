<!DOCTYPE html>
<html lang="en" @yield('html_attribute')>

<head>
    @include('layouts.partials/title-meta')
    @include('layouts.partials/head-css')
    @yield('css')
</head>

<body>
    <div class="wrapper">

        @include('layouts.partials/sidenav')

        <div class="page-content">

            @include('layouts.partials/topbar')

            <main>
                @yield('content')
            </main>

            @include('layouts.partials/footer')
            
        </div>

    </div>

    @include('layouts.partials/customizer')

    {{-- SCRIPTS MAESTROS DE LA PLANTILLA --}}
    @vite(['resources/js/app.js', 'resources/js/vendor.js'])
    @yield('scripts')
</body>

</html>
