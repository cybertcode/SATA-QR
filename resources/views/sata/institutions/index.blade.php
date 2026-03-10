@extends('layouts.vertical', ['title' => 'Instituciones Educativas'])

@section('content')
    @include('layouts.partials/page-title', [
        'subtitle' => 'Administración',
        'title' => 'Instituciones Educativas',
    ])

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- TARJETAS DE RESUMEN                                    --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="grid xl:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-5 mb-5">
        <div class="card hover:shadow-md transition-shadow">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-violet-500/10 flex items-center justify-center shrink-0">
                    <i data-lucide="school" class="size-6 text-violet-500"></i>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Total I.E.</p>
                    <h4 class="text-xl font-bold text-violet-600">{{ $institutions->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="card hover:shadow-md transition-shadow">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <i data-lucide="users" class="size-6 text-primary"></i>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Total Estudiantes</p>
                    <h4 class="text-xl font-bold text-primary">{{ $institutions->sum('estudiantes_count') }}</h4>
                </div>
            </div>
        </div>
        <div class="card hover:shadow-md transition-shadow">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-amber-500/10 flex items-center justify-center shrink-0">
                    <i data-lucide="book-open" class="size-6 text-amber-500"></i>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Con Primaria</p>
                    <h4 class="text-xl font-bold text-amber-600">
                        {{ $institutions->filter(fn($ie) => $ie->niveles->contains('nivel', 'Primaria'))->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="card hover:shadow-md transition-shadow">
            <div class="card-body flex items-center gap-3">
                <div class="size-12 rounded-lg bg-indigo-500/10 flex items-center justify-center shrink-0">
                    <i data-lucide="graduation-cap" class="size-6 text-indigo-500"></i>
                </div>
                <div>
                    <p class="text-default-500 text-xs uppercase tracking-wide">Con Secundaria</p>
                    <h4 class="text-xl font-bold text-indigo-600">
                        {{ $institutions->filter(fn($ie) => $ie->niveles->contains('nivel', 'Secundaria'))->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- TABLA DE INSTITUCIONES                                 --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">Directorio de Instituciones Educativas</h6>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-default-200">
                <thead class="bg-default-100/50">
                    <tr>
                        <th class="px-3.5 py-3 text-start text-xs font-medium text-default-500 uppercase tracking-wider">
                            Institución Educativa</th>
                        <th class="px-3.5 py-3 text-start text-xs font-medium text-default-500 uppercase tracking-wider">
                            Código</th>
                        <th class="px-3.5 py-3 text-start text-xs font-medium text-default-500 uppercase tracking-wider">
                            UGEL</th>
                        <th class="px-3.5 py-3 text-start text-xs font-medium text-default-500 uppercase tracking-wider">
                            Niveles</th>
                        <th class="px-3.5 py-3 text-start text-xs font-medium text-default-500 uppercase tracking-wider">
                            Director Responsable</th>
                        <th class="px-3.5 py-3 text-center text-xs font-medium text-default-500 uppercase tracking-wider">
                            Estudiantes</th>
                        <th class="px-3.5 py-3 text-center text-xs font-medium text-default-500 uppercase tracking-wider">
                            Asistencia</th>
                        <th class="px-3.5 py-3 text-center text-xs font-medium text-default-500 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-default-100">
                    @forelse($institutions as $ie)
                        @php
                            $director = $ie->users->first();
                            $hasConfig = $ie->configuracionAsistencia !== null;
                        @endphp
                        <tr class="transition-colors hover:bg-default-50">
                            {{-- Institución --}}
                            <td class="px-3.5 py-3 whitespace-nowrap text-sm">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="size-10 rounded-lg bg-violet-500/10 flex items-center justify-center shrink-0">
                                        <i data-lucide="school" class="size-5 text-violet-500"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-default-800 font-semibold text-sm">{{ $ie->nombre }}</h6>
                                    </div>
                                </div>
                            </td>

                            {{-- Código Modular --}}
                            <td class="px-3.5 py-3 whitespace-nowrap text-sm">
                                <span class="font-mono text-primary font-medium">{{ $ie->id }}</span>
                            </td>

                            {{-- UGEL --}}
                            <td class="px-3.5 py-3 whitespace-nowrap text-sm text-default-600">
                                {{ $ie->ugel ?? '—' }}
                            </td>

                            {{-- Niveles --}}
                            <td class="px-3.5 py-3 whitespace-nowrap text-sm">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($ie->niveles as $nivel)
                                        @php
                                            $nivelColor =
                                                $nivel->nivel === 'Primaria'
                                                    ? 'bg-amber-500/10 text-amber-600 border-amber-200'
                                                    : 'bg-indigo-500/10 text-indigo-600 border-indigo-200';
                                            $nivelIcon = $nivel->nivel === 'Primaria' ? 'book-open' : 'graduation-cap';
                                        @endphp
                                        <span
                                            class="inline-flex items-center gap-1 py-0.5 px-2 rounded text-[11px] font-semibold border {{ $nivelColor }}">
                                            <i data-lucide="{{ $nivelIcon }}" class="size-3"></i>
                                            {{ $nivel->nivel }}
                                        </span>
                                    @empty
                                        <span class="text-default-400 text-xs italic">Sin niveles</span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Director --}}
                            <td class="px-3.5 py-3 whitespace-nowrap text-sm">
                                @if ($director)
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="size-8 rounded-full bg-success/10 flex items-center justify-center shrink-0">
                                            <i data-lucide="user-check" class="size-4 text-success"></i>
                                        </div>
                                        <div>
                                            <p class="text-default-800 font-medium text-sm leading-tight">
                                                {{ $director->name }}</p>
                                            <p class="text-default-400 text-[10px]">{{ $director->email }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="size-8 rounded-full bg-warning/10 flex items-center justify-center shrink-0">
                                            <i data-lucide="user-x" class="size-4 text-warning"></i>
                                        </div>
                                        <span class="text-warning text-xs font-medium">Sin director asignado</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Estudiantes --}}
                            <td class="px-3.5 py-3 text-center">
                                <span
                                    class="inline-flex items-center justify-center min-w-[2.5rem] py-1 px-2.5 rounded-full text-xs font-bold {{ $ie->estudiantes_count > 0 ? 'bg-primary/10 text-primary' : 'bg-default-100 text-default-400' }}">
                                    {{ $ie->estudiantes_count }}
                                </span>
                            </td>

                            {{-- Config Asistencia --}}
                            <td class="px-3.5 py-3 text-center">
                                @if ($hasConfig)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-success">
                                        <i data-lucide="check-circle" class="size-4"></i>
                                        Configurado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-default-400">
                                        <i data-lucide="minus-circle" class="size-4"></i>
                                        Pendiente
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-3.5 py-3 text-center">
                                <a href="{{ route('institutions.carnets', $ie->id) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-primary/10 text-primary hover:bg-primary hover:text-white transition-colors"
                                    title="Generar Carnets QR masivos">
                                    <i data-lucide="qr-code" class="size-3.5"></i>
                                    Carnets QR
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12">
                                <div class="flex flex-col items-center">
                                    <div class="size-16 rounded-full bg-default-100 flex items-center justify-center mb-4">
                                        <i data-lucide="building-2" class="size-8 text-default-300"></i>
                                    </div>
                                    <h6 class="text-default-600 font-medium mb-1">No hay instituciones registradas</h6>
                                    <p class="text-default-400 text-xs">Contacte al administrador para registrar
                                        instituciones.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
