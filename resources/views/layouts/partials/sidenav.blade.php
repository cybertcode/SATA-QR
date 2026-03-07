@php
    $user = auth()->user();
@endphp

<aside class="app-menu" id="app-menu">
    <!-- Logo Maestro -->
    <a class="logo-box sticky top-0 flex min-h-topbar-height items-center justify-start px-6 backdrop-blur-xs" href="{{ route('root') }}">
        <div class="logo-light">
            <img alt="SATA QR" class="logo-lg h-6" src="/images/logo-light.png" />
            <img alt="SATA QR" class="logo-sm h-6" src="/images/logo-sm.png" />
        </div>
        <div class="logo-dark">
            <img alt="SATA QR" class="logo-lg h-6" src="/images/logo-dark.png" />
            <img alt="SATA QR" class="logo-sm h-6" src="/images/logo-sm.png" />
        </div>
    </a>

    <div class="relative min-h-0 flex-grow" data-simplebar="">
        <ul class="side-nav p-3 hs-accordion-group">
            
            {{-- SECCIÓN OPERATIVA --}}
            <li class="menu-title"><span>Operación</span></li>
            
            <li class="menu-item">
                <a class="menu-link {{ Route::is('root') ? 'active' : '' }}" href="{{ route('root') }}">
                    <span class="menu-icon"><i data-lucide="qr-code"></i></span>
                    <span class="menu-text"> Escáner QR </span>
                </a>
            </li>

            {{-- SECCIÓN DE ANÁLISIS --}}
            <li class="menu-title"><span>Estrategia</span></li>
            
            <li class="menu-item">
                <a class="menu-link {{ Route::is('dashboard*') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <span class="menu-icon"><i data-lucide="monitor-dot"></i></span>
                    <span class="menu-text"> Panel Analítico </span>
                </a>
            </li>

            {{-- GESTIÓN (ACORDEÓN PROFESIONAL) --}}
            <li class="menu-title"><span>Administración</span></li>

            <li class="hs-accordion menu-item">
                <a class="hs-accordion-toggle menu-link" href="javascript:void(0)">
                    <span class="menu-icon"><i data-lucide="school"></i></span>
                    <span class="menu-text"> Institución </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="hs-accordion-content w-full overflow-hidden hidden transition-[height] duration-300">
                    <ul class="sub-menu">
                        <li class="menu-item">
                            <a class="menu-link {{ Route::is('institution.settings') ? 'active' : '' }}" href="{{ route('institution.settings') }}">
                                <span class="menu-text">Configuración Horarios</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a class="menu-link {{ Route::is('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                <span class="menu-text">Directorio Personal</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="hs-accordion menu-item">
                <a class="hs-accordion-toggle menu-link" href="javascript:void(0)">
                    <span class="menu-icon"><i data-lucide="users"></i></span>
                    <span class="menu-text"> Alumnado </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="hs-accordion-content w-full overflow-hidden hidden transition-[height] duration-300">
                    <ul class="sub-menu">
                        <li class="menu-item">
                            <a class="menu-link {{ Route::is('students.index') ? 'active' : '' }}" href="{{ route('students.index') }}">
                                <span class="menu-text">Listado de Alumnos</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a class="menu-link {{ Route::is('students.import') ? 'active' : '' }}" href="{{ route('students.import') }}">
                                <span class="menu-text">Importación SIAGIE</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="hs-accordion menu-item">
                <a class="hs-accordion-toggle menu-link" href="javascript:void(0)">
                    <span class="menu-icon"><i data-lucide="alert-triangle"></i></span>
                    <span class="menu-text"> Control de Deserción </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="hs-accordion-content w-full overflow-hidden hidden transition-[height] duration-300">
                    <ul class="sub-menu">
                        <li class="menu-item">
                            <a class="menu-link {{ Route::is('alerts.index') ? 'active' : '' }}" href="{{ route('alerts.index') }}">
                                <span class="menu-text">Alertas Críticas</span>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a class="menu-link {{ Route::is('interventions.index') ? 'active' : '' }}" href="{{ route('interventions.index') }}">
                                <span class="menu-text">Intervenciones (Timeline)</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- REFERENCIA DE DESARROLLO --}}
            <li class="menu-title"><span>Referencia Visual</span></li>
            <li class="menu-item">
                <a class="menu-link" href="{{ route('demo.root') }}" target="_blank">
                    <span class="menu-icon"><i data-lucide="library"></i></span>
                    <span class="menu-text text-[10px] italic"> Biblioteca Original </span>
                </a>
            </li>

        </ul>
    </div>
</aside>
