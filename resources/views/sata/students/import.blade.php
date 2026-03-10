@extends('layouts.vertical', ['title' => 'Importación SIAGIE'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Alumnado', 'title' => 'Carga Masiva SIAGIE'])
    <div class="grid lg:grid-cols-12 grid-cols-1 gap-6 mb-5">
        <div class="lg:col-span-9 col-span-1">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-4 card-title text-lg">Sincronización de Base de Datos</h6>

                    <div
                        class="bg-info/10 border border-info/20 text-info p-4 rounded-md mb-6 text-sm flex gap-3 items-start">
                        <i data-lucide="info" class="size-5 shrink-0 mt-0.5"></i>
                        <p>Por favor, descargue el archivo matriz desde el sistema oficial SIAGIE de su Institución
                            Educativa y cárguelo sin modificar las cabeceras. El sistema procesará las matrículas y generará
                            los Códigos QR automáticamente.</p>
                    </div>

                    @if ($errors->any())
                        <div class="bg-danger/10 border border-danger/20 text-danger p-4 rounded-md mb-6 text-sm">
                            <div class="flex gap-3 items-start">
                                <i data-lucide="alert-circle" class="size-5 shrink-0 mt-0.5"></i>
                                <div>
                                    <strong>Se encontraron errores:</strong>
                                    <ul class="list-disc list-inside mt-2 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('students.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid lg:grid-cols-2 grid-cols-1 gap-5 mb-6">
                            <div class="col-span-1">
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium" for="anioInput">Año
                                    Lectivo Destino</label>
                                <select class="form-input" id="anioInput" name="anio_lectivo_id" required>
                                    @foreach (\App\Models\AnioLectivo::where('estado', true)->get() as $anio)
                                        <option value="{{ $anio->id }}"
                                            {{ old('anio_lectivo_id') == $anio->id ? 'selected' : '' }}>
                                            {{ $anio->nombre_anio }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if (auth()->user()->isSuperAdmin())
                                <div class="col-span-1">
                                    <label class="inline-block mb-2 text-sm text-default-800 font-medium"
                                        for="tenantInput">Institución Educativa Destino</label>
                                    <select class="form-input" id="tenantInput" name="tenant_id" required>
                                        <option value="">— Seleccione una I.E. —</option>
                                        @foreach ($instituciones as $ie)
                                            <option value="{{ $ie->id }}"
                                                {{ old('tenant_id') == $ie->id ? 'selected' : '' }}>{{ $ie->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-[10px] text-default-400">Como SuperAdmin, seleccione la I.E. destino
                                        para esta importación.</p>
                                </div>
                            @else
                                <div class="col-span-1">
                                    <label class="inline-block mb-2 text-sm text-default-800 font-medium"
                                        for="ieInput">Institución Educativa</label>
                                    <input class="form-input bg-default-100" id="ieInput" type="text"
                                        value="{{ auth()->user()->tenant->nombre ?? 'Sin asignar' }}" disabled />
                                    <p class="mt-1 text-[10px] text-default-400">La importación se asociará estrictamente a
                                        esta I.E.</p>
                                </div>
                            @endif
                        </div>

                        <div class="grid lg:grid-cols-2 grid-cols-1 gap-5 mb-6">
                            <div class="col-span-1">
                                <label class="inline-block mb-2 text-sm text-default-800 font-medium" for="nivelInput">Nivel
                                    Educativo</label>
                                <select class="form-input" id="nivelInput" name="nivel" required>
                                    <option value="Primaria" {{ old('nivel') == 'Primaria' ? 'selected' : '' }}>Primaria
                                    </option>
                                    <option value="Secundaria"
                                        {{ old('nivel', 'Secundaria') == 'Secundaria' ? 'selected' : '' }}>Secundaria
                                    </option>
                                </select>
                                <p class="mt-1 text-[10px] text-default-400">Seleccione el nivel que corresponde al archivo
                                    SIAGIE a importar.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 mb-6">
                            <h6 class="mb-2 font-semibold text-sm text-default-800">Archivo Excel (SIAGIE)</h6>
                            <div
                                class="flex items-center justify-center bg-transparent border-2 border-dashed rounded-xl cursor-pointer border-primary/30 hover:bg-primary/5 transition-colors py-12 relative group">
                                <input type="file" name="archivo_siagie" id="fileInput"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    accept=".xlsx,.xls,.csv" required>
                                <div class="w-full text-center" id="fileDropZone">
                                    <div class="mb-4 flex justify-center">
                                        <div
                                            class="size-16 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                            <i class="size-8" data-lucide="file-spreadsheet"></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-1 font-semibold text-base text-default-800" id="fileName">Arrastre y
                                        suelte su archivo aquí</h5>
                                    <p class="text-sm text-default-500">o haga clic para <span
                                            class="text-primary underline">explorar sus archivos</span></p>
                                    <p class="text-xs text-default-400 mt-2 font-mono">Formatos soportados: .xlsx, .xls,
                                        .csv</p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-warning/10 border border-warning/20 text-warning p-3 rounded-md mb-6 text-xs flex gap-3 items-center">
                            <i data-lucide="shield-alert" class="size-4 shrink-0"></i>
                            <p>Las cabeceras esperadas del archivo SIAGIE son: <strong>DNI_ESTUDIANTE, NOMBRES,
                                    APELLIDO_PATERNO, APELLIDO_MATERNO, SEXO, GRADO, SECCION</strong></p>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-default-200">
                            <a href="{{ route('students.index') }}"
                                class="btn bg-default-200 text-default-800 hover:bg-default-300">Cancelar</a>
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
                            <div
                                class="size-6 rounded bg-success/10 flex items-center justify-center text-success shrink-0 mt-0.5">
                                <i data-lucide="check" class="size-4"></i>
                            </div>
                            <div>
                                <strong class="text-default-800 block mb-0.5">Importación por I.E.</strong>
                                Cada carga se asocia a una sola institución educativa, reduciendo la carga y aislando los
                                datos.
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div
                                class="size-6 rounded bg-success/10 flex items-center justify-center text-success shrink-0 mt-0.5">
                                <i data-lucide="check" class="size-4"></i>
                            </div>
                            <div>
                                <strong class="text-default-800 block mb-0.5">Unicidad por DNI</strong>
                                El sistema usa el DNI para evitar duplicados. Si el alumno existe, solo actualiza su
                                matrícula.
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div
                                class="size-6 rounded bg-success/10 flex items-center justify-center text-success shrink-0 mt-0.5">
                                <i data-lucide="check" class="size-4"></i>
                            </div>
                            <div>
                                <strong class="text-default-800 block mb-0.5">Autogeneración QR</strong>
                                Cada registro nuevo recibirá un identificador UUID único encriptable en código de barras
                                bidimensional.
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div
                                class="size-6 rounded bg-warning/10 flex items-center justify-center text-warning shrink-0 mt-0.5">
                                <i data-lucide="alert-triangle" class="size-4"></i>
                            </div>
                            <div>
                                <strong class="text-default-800 block mb-0.5">Aislamiento de Datos</strong>
                                Los estudiantes cargados no serán visibles para otras Instituciones Educativas.
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div
                                class="size-6 rounded bg-info/10 flex items-center justify-center text-info shrink-0 mt-0.5">
                                <i data-lucide="info" class="size-4"></i>
                            </div>
                            <div>
                                <strong class="text-default-800 block mb-0.5">Selección de Nivel</strong>
                                El archivo SIAGIE se importa por nivel (Primaria o Secundaria) para mantener la
                                organización.
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('fileInput').addEventListener('change', function(e) {
                const fileName = e.target.files[0]?.name;
                if (fileName) {
                    document.getElementById('fileName').textContent = fileName;
                }
            });
        </script>
    @endpush
@endsection
