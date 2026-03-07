@extends('layouts.vertical', ['title' => 'Importación SIAGIE'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Alumnado', 'title' => 'Carga Masiva SIAGIE'] )
    <div class="grid lg:grid-cols-12 grid-cols-1 gap-6 mb-5">
        <div class="lg:col-span-9 col-span-1">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-4 card-title text-lg">Sincronización de Base de Datos</h6>
                    
                    <div class="bg-info/10 border border-info/20 text-info p-4 rounded-md mb-6 text-sm flex gap-3 items-start">
                        <i data-lucide="info" class="size-5 shrink-0 mt-0.5"></i>
                        <p>Por favor, descargue el archivo matriz desde el sistema oficial SIAGIE de su Institución Educativa y cárguelo sin modificar las cabeceras. El sistema procesará las matrículas y generará los Códigos QR automáticamente.</p>
                    </div>

                    <form action="{{ route('students.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid lg:grid-cols-2 grid-cols-1 gap-5 mb-6">
                            <div class="col-span-1">
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium" for="anioInput">Año Lectivo Destino</label>
                                <select class="form-input" id="anioInput" name="anio_lectivo_id" required>
                                    @foreach(\App\Models\AnioLectivo::where('estado', true)->get() as $anio)
                                        <option value="{{ $anio->id }}">{{ $anio->nombre_anio }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-1">
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium" for="ieInput">Institución Educativa</label>
                                <input class="form-input bg-default-100" id="ieInput" type="text" value="{{ auth()->user()->tenant->nombre ?? 'UGEL HUACAYBAMBA (Global)' }}" disabled/>
                                <p class="mt-1 text-[10px] text-default-400">La importación se asociará estrictamente a esta I.E.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 mb-6">
                            <h6 class="mb-2 font-semibold text-sm text-default-800">Archivo Excel (SIAGIE)</h6>
                            <div class="flex items-center justify-center bg-transparent border-2 border-dashed rounded-xl cursor-pointer border-primary/30 hover:bg-primary/5 transition-colors py-12 relative group">
                                <input type="file" name="archivo_siagie" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept=".xlsx,.xls,.csv" required>
                                <div class="w-full text-center">
                                    <div class="mb-4 flex justify-center">
                                        <div class="size-16 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                            <i class="size-8" data-lucide="file-spreadsheet"></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-1 font-semibold text-base text-default-800">Arrastre y suelte su archivo aquí</h5>
                                    <p class="text-sm text-default-500">o haga clic para <span class="text-primary underline">explorar sus archivos</span></p>
                                    <p class="text-xs text-default-400 mt-2 font-mono">Formatos soportados: .xlsx, .xls</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-default-200">
                            <a href="{{ route('students.index') }}" class="btn bg-default-200 text-default-800 hover:bg-default-300">Cancelar</a>
                            <button type="submit" class="btn bg-primary text-white">
                                <i data-lucide="upload-cloud" class="size-4 mr-2"></i> Iniciar Importación Segura
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="lg:col-span-3 col-span-1">
            <div class="card h-full">
                <div class="card-header border-b border-default-200 bg-default-50">
                    <h6 class="card-title text-sm">Validaciones del Sistema</h6>
                </div>
                <div class="card-body">
                    <ul class="space-y-5 text-sm text-default-600">
                        <li class="flex gap-3">
                            <div class="size-6 rounded bg-success/10 flex items-center justify-center text-success shrink-0 mt-0.5">
                                <i data-lucide="check" class="size-4"></i>
                            </div>
                            <div>
                                <strong class="text-default-800 block mb-0.5">Unicidad por DNI</strong>
                                El sistema usa el DNI para evitar duplicados. Si el alumno existe, solo actualiza su grado.
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="size-6 rounded bg-success/10 flex items-center justify-center text-success shrink-0 mt-0.5">
                                <i data-lucide="check" class="size-4"></i>
                            </div>
                            <div>
                                <strong class="text-default-800 block mb-0.5">Autogeneración QR</strong>
                                Cada registro nuevo recibirá un identificador UUID único encriptable en código de barras bidimensional.
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="size-6 rounded bg-warning/10 flex items-center justify-center text-warning shrink-0 mt-0.5">
                                <i data-lucide="alert-triangle" class="size-4"></i>
                            </div>
                            <div>
                                <strong class="text-default-800 block mb-0.5">Aislamiento de Datos</strong>
                                Los estudiantes cargados no serán visibles para otras Instituciones Educativas.
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
