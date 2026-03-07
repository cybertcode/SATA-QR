@extends('layouts.vertical', ['title' => 'Mi Perfil'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Usuario', 'title' => 'Perfil del Sistema'] )

    <div class="grid lg:grid-cols-12 grid-cols-1 gap-6">
        <div class="lg:col-span-4 col-span-1">
            <div class="card overflow-hidden">
                <div class="card-body p-0">
                    <div class="h-32 bg-primary/10 relative">
                        <div class="absolute -bottom-12 left-6">
                            <div class="size-24 rounded-full bg-white p-1 shadow-md">
                                <div class="size-full rounded-full bg-primary/20 flex items-center justify-center text-primary text-3xl font-bold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pt-16 pb-6 px-6">
                        <h5 class="text-xl font-bold text-default-800 mb-1">{{ auth()->user()->name }}</h5>
                        <p class="text-sm text-default-500 mb-4">{{ auth()->user()->cargo ?? 'Especialista en Educación' }}</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-2 py-1 rounded bg-primary/10 text-primary text-[10px] font-bold uppercase tracking-wider">{{ auth()->user()->role }}</span>
                            <span class="px-2 py-1 rounded bg-success/10 text-success text-[10px] font-bold uppercase tracking-wider">Activo</span>
                        </div>
                    </div>
                    <div class="border-t border-default-200 p-6">
                        <h6 class="text-xs text-default-400 uppercase font-bold mb-4">Información de Contacto</h6>
                        <ul class="space-y-4">
                            <li class="flex items-center gap-3">
                                <i data-lucide="mail" class="size-4 text-default-400"></i>
                                <span class="text-sm text-default-700">{{ auth()->user()->email }}</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i data-lucide="credit-card" class="size-4 text-default-400"></i>
                                <span class="text-sm text-default-700 font-mono">DNI: {{ auth()->user()->dni ?? '--------' }}</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i data-lucide="building-2" class="size-4 text-default-400"></i>
                                <span class="text-sm text-default-700">{{ auth()->user()->tenant->nombre ?? 'UGEL HUACAYBAMBA' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8 col-span-1">
            <div class="card h-full">
                <div class="card-header border-b border-default-200">
                    <h6 class="card-title">Configuración de Cuenta</h6>
                </div>
                <div class="card-body">
                    <form action="#" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block mb-2 text-sm font-semibold text-default-800">Nombre Completo</label>
                                <input type="text" class="form-input" value="{{ auth()->user()->name }}">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-semibold text-default-800">Correo Electrónico</label>
                                <input type="email" class="form-input" value="{{ auth()->user()->email }}" disabled>
                                <p class="mt-1 text-[10px] text-default-400">El correo no puede ser modificado por el usuario.</p>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-semibold text-default-800">DNI</label>
                                <input type="text" class="form-input" value="{{ auth()->user()->dni }}">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-semibold text-default-800">Cargo / Función</label>
                                <input type="text" class="form-input" value="{{ auth()->user()->cargo }}">
                            </div>
                        </div>

                        <div class="pt-6 border-t border-default-200">
                            <h6 class="text-sm font-bold text-danger mb-4">Seguridad</h6>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block mb-2 text-sm font-semibold text-default-800">Nueva Contraseña</label>
                                    <input type="password" class="form-input" placeholder="Dejar en blanco para no cambiar">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-semibold text-default-800">Confirmar Contraseña</label>
                                    <input type="password" class="form-input" placeholder="Dejar en blanco para no cambiar">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="btn bg-primary text-white py-2 px-6 font-bold">
                                <i data-lucide="save" class="size-4 mr-2"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
