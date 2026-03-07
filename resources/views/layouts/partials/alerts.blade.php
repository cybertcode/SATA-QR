{{-- GESTOR GLOBAL DE ALERTAS SATA-QR --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('success') || session('error') || session('info') || $errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}",
                    background: '#ffffff',
                    color: '#1e293b',
                    iconColor: '#22c55e'
                });
            @endif

            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}",
                    background: '#ffffff',
                    color: '#1e293b',
                    iconColor: '#ef4444'
                });
            @endif

            @if (session('info'))
                Toast.fire({
                    icon: 'info',
                    title: "{{ session('info') }}",
                    background: '#ffffff',
                    color: '#1e293b',
                    iconColor: '#3b82f6'
                });
            @endif

            {{-- Mostrar el primer error de validación de forma elegante si no se manejó en el modal --}}
            @if ($errors->any() && !old('modal_id'))
                Toast.fire({
                    icon: 'warning',
                    title: 'Información incompleta',
                    text: 'Revise los campos marcados en el formulario.',
                    background: '#ffffff',
                    color: '#1e293b',
                    iconColor: '#f59e0b'
                });
            @endif
        });
    </script>
@endif
