@extends('layouts.vertical', ['title' => 'Mi Perfil'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Usuario', 'title' => 'Perfil del Sistema'] )

    {{-- CABECERA DE PERFIL: CLONADO DE ORDER OVERVIEW / CUSTOMER INFO --}}
    <div class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-5 mb-5">
        <div class="card lg:col-span-3 md:col-span-2 col-span-1">
            <div class="card-body">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="size-20 bg-primary/10 rounded-md flex items-center justify-center text-primary text-3xl font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h6 class="mb-1 text-default-800 font-semibold text-lg uppercase">{{ $user->name }}</h6>
                            <p class="mb-1 text-default-500 text-sm">Rol: <span class="text-primary font-medium">{{ $user->role }}</span></p>
                            <p class="text-default-500 text-sm">Institución: <span class="font-medium text-default-800">{{ $user->tenant->nombre ?? 'UGEL HUACAYBAMBA' }}</span></p>
                        </div>
                    </div>
                    <div class="btn bg-primary/10 text-primary size-12 hidden md:flex items-center justify-center rounded-full">
                        <i class="size-6" data-lucide="user"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="flex justify-between">
                    <div>
                        <h6 class="mb-4 card-title">Información de Contacto</h6>
                        <h6 class="mb-1 text-default-800 font-semibold text-sm">{{ $user->email }}</h6>
                        <p class="mb-1 text-default-500 text-sm italic">Correo Institucional</p>
                        <p class="text-default-500 text-sm font-mono uppercase">DNI: {{ $user->dni ?? 'Pendiente' }}</p>
                    </div>
                    <div class="btn bg-info/10 text-info size-12 float-end flex items-center justify-center rounded-full">
                        <i class="size-6" data-lucide="mail"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FORMULARIO DE EDICIÓN: CLONADO DE PRODUCT CREATE --}}
    <div class="grid lg:grid-cols-1 grid-cols-1 gap-6">
        <div class="col-span-1">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-4 card-title">Actualizar Información de Cuenta</h6>
                        
                        <div class="grid lg:grid-cols-2 grid-cols-1 gap-5 mb-5">
                            <div class="col-span-1">
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium" for="nameInput">Nombre Completo</label>
                                <input class="form-input @error('name') border-danger @enderror" id="nameInput" name="name" value="{{ old('name', $user->name) }}" required type="text"/>
                                @error('name') <p class="mt-1 text-danger text-xs font-semibold">{{ $message }}</p> @enderror
                            </div>
                            <div class="col-span-1">
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium opacity-70" for="emailInput">Correo Electrónico</label>
                                <input class="form-input bg-default-100 cursor-not-allowed" id="emailInput" value="{{ $user->email }}" disabled type="email"/>
                                <p class="mt-1 text-default-400 text-xs italic">El correo institucional es gestionado por el administrador.</p>
                            </div>
                        </div>

                        <div class="grid lg:grid-cols-2 grid-cols-1 gap-5 mb-5">
                            <div class="col-span-1">
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium" for="dniInput">Número de DNI</label>
                                <input class="form-input @error('dni') border-danger @enderror" id="dniInput" name="dni" value="{{ old('dni', $user->dni) }}" maxlength="8" placeholder="8 dígitos" type="text"/>
                                @error('dni') <p class="mt-1 text-danger text-xs font-semibold">{{ $message }}</p> @enderror
                            </div>
                            <div class="col-span-1">
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium" for="cargoInput">Cargo / Función</label>
                                <input class="form-input" id="cargoInput" name="cargo" value="{{ old('cargo', $user->cargo) }}" placeholder="Ej: Director General" type="text"/>
                            </div>
                        </div>

                        {{-- SECCIÓN SEGURIDAD: DIVISOR DE PRODUCT CREATE --}}
                        <div class="grid grid-cols-1 mb-5 pt-5 border-t border-dashed border-default-200">
                            <h6 class="mb-2 font-semibold text-sm text-danger flex items-center gap-2 uppercase tracking-wider">
                                <i class="size-4" data-lucide="lock"></i> Seguridad y Acceso
                            </h6>
                            <p class="mb-4 text-default-500 text-xs italic">Complete estos campos solo si desea renovar su clave de acceso al sistema.</p>
                            
                            <div class="grid lg:grid-cols-2 grid-cols-1 gap-5">
                                <div class="col-span-1">
                                    <label class="inline-block mb-2 text-sm text-default-800 font-medium" for="passwordInput">Nueva Contraseña</label>
                                    <input class="form-input" id="passwordInput" name="password" placeholder="••••••••" type="password"/>
                                </div>
                                <div class="col-span-1">
                                    <label class="inline-block mb-2 text-sm text-default-800 font-medium" for="passwordConfirmInput">Confirmar Contraseña</label>
                                    <input class="form-input" id="passwordConfirmInput" name="password_confirmation" placeholder="••••••••" type="password"/>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-2 md:justify-end">
                            <button type="reset" class="bg-transparent text-danger btn border-0 hover:bg-danger/15 font-medium px-6 uppercase text-xs">Restablecer Formulario</button>
                            <button type="submit" class="text-white btn bg-primary px-10 font-bold uppercase text-xs shadow-sm hover:shadow-md transition-all">
                                <i class="size-4 me-2" data-lucide="save"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
