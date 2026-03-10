<!-- Topbar Start -->
<div class="app-header min-h-topbar-height flex items-center sticky top-0 z-30 bg-(--topbar-background) border-b border-default-200">
    <div class="w-full flex items-center justify-between px-6">
        <div class="flex items-center gap-5">
            <!-- Sidenav Menu Toggle Button -->
            <button class="btn btn-icon size-8 hover:bg-default-150 rounded" id="button-toggle-menu">
                <i class="iconify lucide--align-left text-xl"></i>
            </button>
            <div class="lg:flex hidden items-center relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <i class="iconify tabler--search text-base"></i>
                </div>
                <input class="form-input px-12 text-sm rounded border-transparent focus:border-transparent w-60"
                       id="topbar-search" placeholder="Buscar alumno por DNI..." type="search"/>
                <button class="absolute inset-y-0 end-0 flex items-center pe-4" type="button">
                    <span class="ms-auto font-medium text-[10px]">⌘ K</span>
                </button>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="topbar-item hs-dropdown [--placement:bottom-right] relative inline-flex">
                <button aria-expanded="false" aria-haspopup="menu" aria-label="Dropdown"
                        class="hs-dropdown-toggle btn btn-icon size-8 hover:bg-default-150 rounded-full relative"
                        type="button">
                    <img alt="" class="size-4.5 rounded" src="/images/flags/spain.jpg"/>
                </button>
                <div aria-labelledby="dropdown-menu" aria-orientation="vertical" class="hs-dropdown-menu" role="menu">
                    <a class="flex items-center gap-x-3.5 py-1.5 font-medium px-3 text-default-600 hover:bg-default-150 rounded"
                       href="#">
                        <img alt="" class="size-4 rounded-full" src="/images/flags/spain.jpg"/>
                        Español
                    </a>
                </div>
            </div>

            <div class="topbar-item">
                <button class="btn btn-icon size-8 hover:bg-default-150 transition-[scale,background] rounded-full"
                        id="light-dark-mode" type="button">
                    <i class="iconify tabler--moon text-xl absolute dark:scale-0 dark:-rotate-90 scale-100 rotate-0 transition-all duration-200"></i>
                    <i class="iconify tabler--sun text-xl absolute dark:scale-100 dark:rotate-0 scale-0 rotate-90 transition-all duration-200"></i>
                </button>
            </div>

            <div class="topbar-item hs-dropdown [--auto-close:inside] relative inline-flex">
                <button aria-expanded="false" aria-haspopup="menu" aria-label="Dropdown"
                        class="hs-dropdown-toggle btn btn-icon size-8 hover:bg-default-150 rounded-full relative"
                        type="button">
                    <i class="size-4.5" data-lucide="bell-ring"></i>
                    @php $alertas = \App\Models\AlertaTemprana::where('nivel_riesgo', 'Crítico')->count(); @endphp
                    @if($alertas > 0)
                        <span class="absolute end-0 top-0 size-1.5 bg-primary/90 rounded-full"></span>
                    @endif
                </button>
                <div class="hs-dropdown-menu max-w-100 p-0" role="menu">
                    <div class="p-4 border-b border-default-200">
                        <div class="flex items-center gap-2">
                            <h3 class="text-base text-default-800">Notificaciones</h3>
                            <span class="size-5 font-semibold bg-orange-500 rounded text-white flex items-center justify-center text-xs">{{ $alertas }}</span>
                        </div>
                    </div>
                    <div class="p-4 text-center text-sm text-default-500">
                        @if($alertas > 0)
                            Tiene {{ $alertas }} alertas críticas de deserción.
                        @else
                            No hay alertas nuevas.
                        @endif
                    </div>
                </div>
            </div>

            <div class="topbar-item">
                <button aria-controls="theme-customization" aria-expanded="false" aria-haspopup="dialog"
                        class="btn btn-icon size-8 hover:bg-default-150 rounded-full"
                        data-hs-overlay="#theme-customization" type="button">
                    <i class="size-4.5" data-lucide="settings"></i>
                </button>
            </div>

            <div class="topbar-item hs-dropdown relative inline-flex">
                <button aria-expanded="false" aria-haspopup="menu" aria-label="Dropdown"
                        class="cursor-pointer bg-pink-100 rounded-full p-0.5">
                    <img alt="user-image" class="hs-dropdown-toggle rounded-full size-9"
                         src="/images/user/avatar-1.png"/>
                </button>
                <div aria-labelledby="hs-dropdown-with-icons" aria-orientation="vertical"
                     class="hs-dropdown-menu min-w-48 p-2" role="menu">
                    <div class="p-2">
                        <h6 class="mb-2 text-default-500 uppercase text-[10px] font-bold">Bienvenido</h6>
                        <a class="flex gap-3" href="#!">
                            <div class="relative inline-block">
                                <div class="rounded bg-default-200">
                                    <img alt="" class="size-10 rounded" src="/images/user/avatar-1.png"/>
                                </div>
                                <span class="-top-1 -end-1 absolute w-2.5 h-2.5 bg-green-400 border-2 border-white rounded-full"></span>
                            </div>
                            <div>
                                <h6 class="mb-1 text-sm font-semibold text-default-800">{{ auth()->user()->name }}</h6>
                                <p class="text-[10px] text-primary font-medium">{{ auth()->user()->role }}</p>
                            </div>
                        </a>
                    </div>
                    <div class="border-t border-t-default-200 -mx-2 my-2"></div>
                    <div class="flex flex-col gap-y-1">
                        <a class="flex items-center gap-x-3.5 py-1.5 font-medium px-3 text-default-600 hover:bg-default-150 rounded text-sm"
                           href="{{ route('profile') }}">
                            <i class="size-4" data-lucide="user"></i>
                            Mi Perfil
                        </a>
                        <a class="flex items-center gap-x-3.5 py-1.5 font-medium px-3 text-default-600 hover:bg-default-150 rounded text-sm"
                           href="{{ route('institution.settings') }}">
                            <i class="size-4" data-lucide="building"></i>
                            Mi Institución
                        </a>
                        <div class="border-t border-default-200 -mx-2 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-x-3.5 py-1.5 font-medium px-3 text-danger hover:bg-danger/10 rounded text-sm text-left">
                                <i class="size-4" data-lucide="log-out"></i>
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Topbar End -->
