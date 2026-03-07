@extends('layouts.vertical', ['title' => 'Intervenciones y Seguimiento'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Deserción Escolar', 'title' => 'Línea de Tiempo de Intervenciones'] )

    <div class="grid lg:grid-cols-3 grid-cols-1 gap-5">
        <div class="lg:col-span-2 card h-full">
            <div class="card-header bg-default-50 border-b border-default-200 flex justify-between items-center">
                <h6 class="card-title text-primary font-bold"><i data-lucide="activity" class="size-4 inline mr-2"></i> Seguimiento Histórico</h6>
            </div>
            <div class="card-body p-6">
                <div>
                    @forelse(\App\Models\IntervencionMultisectorial::with(['alerta.matricula.estudiante', 'especialista', 'aliado'])->latest()->get() as $intervencion)
                    <div class="relative before:absolute before:border-s-2 before:border-default-200 before:start-3.5 before:end-3.5 before:top-1.5 before:-bottom-1.5 pb-6">
                        <div class="relative flex gap-4">
                            <div class="size-8 rounded-full flex items-center justify-center shrink-0 z-10 ring-4 ring-white
                                {{ $intervencion->estado == 'Abierto' ? 'bg-danger text-white' : ($intervencion->estado == 'Seguimiento' ? 'bg-warning text-white' : 'bg-success text-white') }}">
                                <i data-lucide="{{ $intervencion->estado == 'Abierto' ? 'alert-circle' : ($intervencion->estado == 'Seguimiento' ? 'loader' : 'check') }}" class="size-4"></i>
                            </div>
                            <div class="bg-white p-5 rounded-lg flex-grow border border-default-200 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h6 class="text-base text-heading font-bold text-default-800">{{ $intervencion->alerta->matricula->estudiante->nombre_completo }}</h6>
                                        <p class="text-xs text-default-500 font-mono mt-0.5">DNI: {{ $intervencion->alerta->matricula->estudiante->dni }}</p>
                                    </div>
                                    <span class="text-xs text-default-500 font-medium bg-default-100 px-2 py-1 rounded">
                                        {{ $intervencion->fecha_intervencion->format('d M, Y') }}
                                    </span>
                                </div>
                                <p class="mb-4 text-default-600 text-sm leading-relaxed">
                                    {{ $intervencion->descripcion_accion }}
                                </p>
                                <div class="flex flex-wrap gap-2 text-xs border-t border-default-100 pt-3">
                                    <span class="inline-flex items-center gap-1.5 bg-default-100 px-2.5 py-1 rounded-full text-default-700 font-medium">
                                        <i data-lucide="user" class="size-3.5"></i> {{ $intervencion->especialista->name }}
                                    </span>
                                    @if($intervencion->aliado)
                                    <span class="inline-flex items-center gap-1.5 bg-info/10 text-info border border-info/20 px-2.5 py-1 rounded-full font-medium">
                                        <i data-lucide="handshake" class="size-3.5"></i> {{ $intervencion->aliado->nombre }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 text-default-400">
                        <i data-lucide="shield-check" class="size-16 mx-auto mb-4 opacity-20 text-success"></i>
                        <h6 class="text-lg font-medium text-default-600">No hay intervenciones</h6>
                        <p class="text-sm mt-1">Aún no se ha registrado ninguna acción multisectorial en el sistema.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div class="col-span-1 space-y-5">
            <div class="card sticky top-24">
                <div class="card-header bg-default-50 border-b border-default-200">
                    <h6 class="card-title font-bold text-default-800">Registrar Intervención</h6>
                </div>
                <div class="card-body">
                    <form action="#" method="POST">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <label class="text-sm font-semibold mb-1.5 block text-default-800">Alerta Asociada</label>
                                <select class="form-input text-sm">
                                    <option value="">Seleccione un caso pendiente...</option>
                                    @foreach(\App\Models\AlertaTemprana::where('estado_atencion', '!=', 'Atendido')->with('matricula.estudiante')->get() as $alerta)
                                        <option value="{{ $alerta->id }}">{{ $alerta->matricula->estudiante->nombres }} ({{ $alerta->nivel_riesgo }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-sm font-semibold mb-1.5 block text-default-800">Aliado Estratégico (Opcional)</label>
                                <select class="form-input text-sm">
                                    <option value="">Ninguno (Atención Interna)</option>
                                    @foreach(\App\Models\AliadoEstrategico::all() as $aliado)
                                        <option value="{{ $aliado->id }}">{{ $aliado->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-sm font-semibold mb-1.5 block text-default-800">Descripción de Acciones</label>
                                <textarea class="form-input text-sm" rows="4" placeholder="Detalle la visita, la llamada o la coordinación realizada..."></textarea>
                            </div>
                            <div>
                                <label class="text-sm font-semibold mb-1.5 block text-default-800">Estado Post-Intervención</label>
                                <div class="flex gap-4 mt-2">
                                    <label class="flex items-center gap-2 text-sm"><input type="radio" name="estado" value="Seguimiento" checked class="form-radio text-warning"> En Seguimiento</label>
                                    <label class="flex items-center gap-2 text-sm"><input type="radio" name="estado" value="Cerrado" class="form-radio text-success"> Caso Cerrado</label>
                                </div>
                            </div>
                            <div class="pt-4 border-t border-default-200">
                                <button type="submit" class="btn bg-primary text-white w-full py-2.5 font-bold"><i data-lucide="plus-circle" class="size-4 mr-2"></i> Añadir al Historial</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
