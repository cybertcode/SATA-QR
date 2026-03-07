@extends('layouts.vertical', ['title' => 'Gestión de Estudiantes'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Alumnado', 'title' => 'Listado General'] )

    <div class="grid grid-cols-1 gap-5 mb-5">
        <div class="card">
            <div class="card-header flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative">
                        <input class="ps-11 form-input form-input-sm w-full" placeholder="Buscar alumno por DNI o nombre..." type="text"/>
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3">
                            <i class="size-3.5 flex items-center text-default-500" data-lucide="search"></i>
                        </div>
                    </div>
                    {{-- Filtro de Institución (Visible solo para SuperAdmin) --}}
                    @if(auth()->user()->isSuperAdmin())
                    <select class="form-input form-input-sm w-full">
                        <option value="">Todas las I.E.</option>
                        @foreach(\App\Models\Tenant::all() as $ie)
                            <option value="{{ $ie->id }}">{{ $ie->nombre }}</option>
                        @endforeach
                    </select>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('students.import') }}" class="btn btn-sm bg-info/10 text-info hover:bg-info hover:text-white">
                        <i class="size-4 me-1" data-lucide="upload"></i>Importar SIAGIE
                    </a>
                    <button class="btn btn-sm bg-primary text-white">
                        <i class="size-4 me-1" data-lucide="plus"></i>Nuevo Alumno
                    </button>
                </div>
            </div>
            <div class="flex flex-col">
                <div class="overflow-x-auto">
                    <div class="min-w-full inline-block align-middle">
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-default-200">
                                <thead class="bg-default-150">
                                <tr class="text-sm font-normal text-default-700">
                                    <th class="px-3.5 py-3 text-start" scope="col">DNI</th>
                                    <th class="px-3.5 py-3 text-start" scope="col">Estudiante</th>
                                    <th class="px-3.5 py-3 text-start" scope="col">Grado/Sección</th>
                                    <th class="px-3.5 py-3 text-start" scope="col">Vulnerabilidad</th>
                                    <th class="px-3.5 py-3 text-start" scope="col">Estado</th>
                                    <th class="px-3.5 py-3 text-start" scope="col">Acciones</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-default-200">
                                @forelse($students as $student)
                                <tr class="text-default-800 font-normal hover:bg-default-50 transition-colors">
                                    <td class="px-3.5 py-2.5 whitespace-nowrap text-sm font-mono text-primary">{{ $student->dni }}</td>
                                    <td class="px-3.5 py-2.5 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs uppercase">
                                                {{ substr($student->nombres, 0, 1) }}{{ substr($student->apellido_paterno, 0, 1) }}
                                            </div>
                                            <div>
                                                <h6 class="text-default-800 font-medium mb-0.5">{{ $student->nombre_completo }}</h6>
                                                <p class="text-[10px] text-default-500 uppercase">{{ $student->tenant->nombre }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3.5 py-2.5 whitespace-nowrap text-sm">
                                        @php $matricula = $student->matriculas->first(); @endphp
                                        @if($matricula)
                                            <div class="inline-flex py-0.5 px-2.5 rounded text-xs font-semibold bg-default-100 border border-default-200 text-default-600">
                                                {{ $matricula->seccion->grado }}° "{{ $matricula->seccion->letra }}" - {{ $matricula->seccion->nivel }}
                                            </div>
                                        @else
                                            <span class="text-default-400 text-xs italic">Sin Matrícula</span>
                                        @endif
                                    </td>
                                    <td class="px-3.5 py-2.5 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-x-1.5 py-0.5 px-2.5 rounded text-xs font-medium border 
                                            {{ $student->vulnerabilidad != 'Ninguna' ? 'bg-warning/10 border-warning/30 text-warning' : 'bg-success/10 border-success/30 text-success' }}">
                                            {{ $student->vulnerabilidad }}
                                        </span>
                                    </td>
                                    <td class="px-3.5 py-2.5 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-success">
                                            <span class="size-1.5 rounded-full bg-success"></span> Activo
                                        </span>
                                    </td>
                                    <td class="px-3.5 py-2.5">
                                        <div class="hs-dropdown relative inline-flex">
                                            <button aria-expanded="false" aria-haspopup="menu" aria-label="Dropdown"
                                                    class="hs-dropdown-toggle btn size-7.5 bg-default-200 hover:bg-default-600 text-default-500 hover:text-white"
                                                    hs-dropdown-placement="bottom-end" type="button">
                                                <i class="iconify lucide--ellipsis size-4"></i>
                                            </button>
                                            <div class="hs-dropdown-menu" role="menu">
                                                <a class="flex items-center gap-1.5 py-1.5 font-medium px-3 text-default-500 hover:bg-default-150 rounded"
                                                   href="{{ route('students.qr', $student->id) }}" target="_blank">
                                                    <i class="size-3 text-primary" data-lucide="qr-code"></i>
                                                    Generar Carnet QR
                                                </a>
                                                <a class="flex items-center gap-1.5 py-1.5 font-medium px-3 text-default-500 hover:bg-default-150 rounded"
                                                   href="{{ route('students.show', $student->id) }}">
                                                    <i class="size-3 text-info" data-lucide="eye"></i>
                                                    Ver Historial
                                                </a>
                                                <div class="border-t border-default-200 my-1"></div>
                                                <a class="flex items-center gap-1.5 py-1.5 font-medium px-3 text-danger hover:bg-danger/10 rounded"
                                                   href="#">
                                                    <i class="size-3" data-lucide="alert-triangle"></i>
                                                    Crear Alerta
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-10 text-default-500">
                                        <i data-lucide="inbox" class="size-10 mx-auto mb-3 opacity-20"></i>
                                        No hay estudiantes registrados. Importe desde SIAGIE.
                                    </td>
                                </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-t border-default-200 p-4">
                    {{ $students->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
@endsection
