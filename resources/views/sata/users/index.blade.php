@extends('layouts.vertical', ['title' => 'Gestión de Usuarios'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Administración', 'title' => 'Directorio de Personal'] )

    <div class="grid grid-cols-1 gap-6">
        <div class="card">
            <div class="card-header flex flex-wrap items-center justify-between gap-4">
                <h6 class="card-title">Usuarios del Sistema</h6>
                <button class="btn btn-sm bg-primary text-white">
                    <i class="size-4 me-1" data-lucide="user-plus"></i>Nuevo Usuario
                </button>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-default-200">
                        <thead class="bg-default-100">
                            <tr class="text-xs font-semibold text-default-600 uppercase">
                                <th class="px-4 py-4 text-start">Usuario</th>
                                <th class="px-4 py-4 text-start">Institución / Cargo</th>
                                <th class="px-4 py-4 text-start">Rol</th>
                                <th class="px-4 py-4 text-start">DNI</th>
                                <th class="px-4 py-4 text-center">Estado</th>
                                <th class="px-4 py-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-default-200">
                            @foreach(\App\Models\User::with('tenant')->get() as $u)
                            <tr class="hover:bg-default-50 transition-colors">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                                            {{ substr($u->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="font-bold text-default-800 leading-none mb-1">{{ $u->name }}</h6>
                                            <p class="text-xs text-default-500">{{ $u->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-default-800 font-medium">{{ $u->tenant->nombre ?? 'UGEL HUACAYBAMBA' }}</div>
                                    <p class="text-[10px] text-default-500 uppercase">{{ $u->cargo ?? 'Sin cargo' }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase border
                                        {{ $u->role == 'SuperAdmin' ? 'bg-danger/10 text-danger border-danger/20' : ($u->role == 'Director' ? 'bg-primary/10 text-primary border-primary/20' : 'bg-success/10 text-success border-success/20') }}">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm font-mono text-default-600">{{ $u->dni ?? '--------' }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-success">
                                        <span class="size-1.5 rounded-full bg-success"></span> Activo
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button class="btn btn-icon size-8 bg-default-100 text-default-600 hover:bg-primary/10 hover:text-primary">
                                            <i class="size-4" data-lucide="edit"></i>
                                        </button>
                                        <button class="btn btn-icon size-8 bg-default-100 text-default-600 hover:bg-danger/10 hover:text-danger">
                                            <i class="size-4" data-lucide="trash-2"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
