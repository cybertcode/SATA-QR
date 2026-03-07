<!DOCTYPE html>
<html lang="es-PE" @yield('html_attribute')>

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

    {{-- SISTEMA GLOBAL DE ALERTAS --}}
    @include('layouts.partials/alerts')

    {{-- SCRIPTS MAESTROS DE LA PLANTILLA --}}
    @vite(['resources/js/app.js', 'resources/js/vendor.js'])

    {{-- LIVEWIRE: Re-renderizar Lucide icons + SweetAlert2 global --}}
    <script>
        document.addEventListener('livewire:init', () => {
            // Lucide icons: re-render after every successful Livewire commit
            Livewire.hook('commit', ({
                succeed
            }) => {
                succeed(() => {
                    requestAnimationFrame(() => {
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    });
                });
            });

            // SweetAlert2 toast global (cualquier componente puede disparar 'swal')
            Livewire.on('swal', (params) => {
                const p = Array.isArray(params) ? params[0] : params;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: p.icon ?? 'success',
                        title: p.title ?? '',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                    });
                }
            });

            // SweetAlert2 confirmación para bulk actions
            Livewire.on('confirmBulkAction', (params) => {
                const p = Array.isArray(params) ? params[0] : params;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: p.title ?? '¿Confirmar acción?',
                        text: p.text ?? '',
                        icon: p.icon ?? 'question',
                        showCancelButton: true,
                        confirmButtonColor: p.confirmColor ?? '#4F46E5',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: p.confirmText ?? 'Confirmar',
                        cancelButtonText: 'Cancelar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.dispatch('executeBulkAction', {
                                action: p.action
                            });
                        }
                    });
                }
            });
        });
    </script>

    @yield('scripts')
</body>

</html>
