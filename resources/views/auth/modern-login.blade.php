@extends('layouts.base', ['title' => 'Acceso al Sistema'])

@section('css')
@endsection

@section('content')
    <div class="relative flex flex-row w-full overflow-hidden bg-gradient-to-r from-blue-900 h-screen to-blue-800 dark:to-blue-900 dark:from-blue-950">
        <div class="absolute inset-0 opacity-20">
            <img alt="" src="/images/modern.svg"/>
        </div>
        <div class="mx-4 m-4 w-160 py-14 px-10 bg-card flex justify-center rounded-md text-center relative z-10">
            <div class="flex flex-col h-full w-full">
                <div class="my-auto">
                    <div class="mt-10">
                        <div class="mb-10">
                            <h3 class="text-2xl font-semibold text-default-900 mb-2">SATA-QR</h3>
                            <p class="text-default-500">Sistema de Alerta Temprana y Asistencia</p>
                            <p class="text-primary font-medium">UGEL Huacaybamba</p>
                        </div>

                        <div class="mt-10 w-100 mx-auto">
                            <form action="{{ route('login') }}" method="POST" class="text-left w-full mt-10">
                                @csrf
                                <div class="mb-4">
                                    <label class="block font-medium text-default-900 text-sm mb-2" for="email">Correo Electrónico</label>
                                    <input class="form-input @error('email') border-danger @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           placeholder="nombre@ugel-hcy.edu.pe"
                                           type="email" required autofocus/>
                                    @error('email')
                                        <span class="text-danger text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block font-medium text-default-900 text-sm mb-2" for="password">Contraseña</label>
                                    <input class="form-input @error('password') border-danger @enderror" 
                                           id="password" 
                                           name="password"
                                           placeholder="********"
                                           type="password" required/>
                                    @error('password')
                                        <span class="text-danger text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <input class="form-checkbox" id="remember" name="remember" type="checkbox"/>
                                        <label class="text-default-900 text-sm font-medium" for="remember">Recordarme</label>
                                    </div>
                                    <a class="text-primary font-medium text-sm" href="#">¿Olvidaste tu contraseña?</a>
                                </div>

                                <div class="mt-10 text-center">
                                    <button class="btn bg-primary text-white w-full py-3" type="submit">Iniciar Sesión</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <span class="text-sm text-default-500">
                        <i class="iconify lucide--copyright align-middle"></i>
                        {{ date('Y') }} UGEL Huacaybamba. Desarrollado para la mejora educativa.
                    </span>
                </div>
            </div>
        </div>
        <div class="relative z-10 flex items-center justify-center min-h-screen px-10 py-14 grow">
            <div>
                <a class="" href="#">
                    <img alt="SATA Logo" class="h-16 mb-14 mx-auto block" src="/images/logo-ugel.png"/>
                </a>
                <img alt="Educación Huacaybamba" class="mx-auto rounded-xl block object-cover w-md shadow-2xl" src="/images/auth-modern.png"/>
                <div class="mt-10 text-center">
                    <h3 class="mb-3 text-blue-50 text-2xl font-semibold text-center">"Unidos contra la deserción escolar"</h3>
                    <p class="text-blue-200 text-base max-w-xl mx-auto text-center leading-relaxed">
                        SATA-QR es una herramienta estratégica diseñada para garantizar la permanencia de nuestros estudiantes mediante el monitoreo inteligente y la intervención oportuna.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
