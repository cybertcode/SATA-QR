@extends('layouts.vertical', ['title' => 'Punto de Control QR'])

@section('content')
    {{-- USAMOS EL COMPONENTE DE TÍTULO OFICIAL DE LA PLANTILLA --}}
    @include('layouts.partials/page-title', ['subtitle' => 'Operaciones', 'title' => 'Asistencia con Código QR'] )

    <div class="grid grid-cols-12 gap-5">
        
        {{-- COLUMNA DEL ESCÁNER (ESTILO DASHBOARD OFICIAL) --}}
        <div class="col-span-12 lg:col-span-8">
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <h6 class="card-title">Cámara de Control en Vivo</h6>
                    <span id="engine-status" class="badge bg-slate-100 text-slate-500 font-bold uppercase">EN ESPERA</span>
                </div>
                <div class="card-body p-0 bg-slate-50 relative min-h-[450px] flex items-center justify-center overflow-hidden">
                    
                    {{-- ESTADO INICIAL (BOTÓN TIPO PLANTILLA) --}}
                    <div id="scanner-placeholder" class="text-center p-10">
                        <div class="mx-auto size-20 bg-primary/10 rounded-full flex items-center justify-center mb-6">
                            <i data-lucide="camera" class="size-10 text-primary"></i>
                        </div>
                        <h5 class="text-16 font-semibold mb-2">Sensor de Visión Desconectado</h5>
                        <p class="text-slate-500 mb-6">Haga clic en el botón para activar la cámara y comenzar el registro.</p>
                        <button onclick="bootScanner()" class="btn bg-primary text-white font-medium px-10 py-2.5 shadow-md">
                            Activar Escáner
                        </button>
                    </div>

                    {{-- EL LECTOR (SE INTEGRARÁ CON LOS ESTILOS DE LA PLANTILLA) --}}
                    <div id="reader" class="w-full h-full hidden"></div>
                </div>
                <div class="card-footer border-t border-slate-200 flex justify-between items-center bg-slate-50/50">
                    <p class="text-[11px] text-slate-400 font-mono italic">SATA-QR Engine v7.1 Profesional (Localizado)</p>
                    <button onclick="location.reload()" class="btn btn-sm bg-slate-200 text-slate-600 hover:bg-slate-300">
                        Reiniciar Módulo
                    </button>
                </div>
            </div>
        </div>

        {{-- COLUMNA DE DATOS (ESTILO BENTO GRID DE LA PLANTILLA) --}}
        <div class="col-span-12 lg:col-span-4 flex flex-col gap-5">
            
            {{-- WIDGETS DE ESTADÍSTICAS (COMO EN EL DASHBOARD ECOMMERCE) --}}
            <div class="grid grid-cols-2 gap-5">
                <div class="card p-4">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded bg-success/10 flex items-center justify-center text-success">
                            <i data-lucide="user-check" class="size-5"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-500 uppercase font-bold">Presentes</p>
                            <h4 id="stat-p" class="text-xl font-bold">0</h4>
                        </div>
                    </div>
                </div>
                <div class="card p-4">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded bg-warning/10 flex items-center justify-center text-warning">
                            <i data-lucide="clock" class="size-5"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-500 uppercase font-bold">Tardanzas</p>
                            <h4 id="stat-t" class="text-xl font-bold">0</h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LISTA DE ACTIVIDAD (ESTILO TABLA O LISTA DE LA PLANTILLA) --}}
            <div class="card overflow-hidden flex-grow">
                <div class="card-header border-b border-slate-200 py-3 flex justify-between items-center bg-slate-50">
                    <h6 class="card-title text-13">Ingresos Recientes</h6>
                    <span id="scan-count" class="badge bg-primary/10 text-primary font-bold">0 HOY</span>
                </div>
                <div class="card-body p-0 overflow-y-auto max-h-[400px]" id="history-feed">
                    <div id="empty-msg" class="p-10 text-center text-slate-400 opacity-50">
                        <i data-lucide="inbox" class="size-12 mx-auto mb-2"></i>
                        <p class="text-xs uppercase font-bold">Esperando registros</p>
                    </div>
                </div>
            </div>

            {{-- ALERTA DE RIESGO (ESTILO ALERT DE LA PLANTILLA) --}}
            <div id="risk-box" class="hidden">
                <div class="bg-danger/10 border border-danger/20 text-danger p-4 rounded-md flex items-start gap-3 shadow-sm">
                    <i data-lucide="alert-octagon" class="size-5 shrink-0"></i>
                    <div>
                        <h6 class="text-xs font-bold uppercase mb-1">¡Alerta de Deserción!</h6>
                        <p id="risk-msg" class="text-[11px] leading-tight opacity-90"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('js/vendor/html5-qrcode.min.js') }}"></script>
<script>
    let qrScanner;
    let counts = { P: 0, T: 0 };

    function bootScanner() {
        document.getElementById('scanner-placeholder').classList.add('hidden');
        document.getElementById('reader').classList.remove('hidden');
        
        const status = document.getElementById('engine-status');
        status.textContent = "INICIALIZANDO...";
        status.className = "badge bg-warning/10 text-warning font-bold uppercase";

        qrScanner = new Html5QrcodeScanner("reader", { 
            fps: 15, 
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0,
            rememberLastUsedCamera: true
        });

        qrScanner.render(onSuccess, (err) => {});
        
        // --- TRADUCCIÓN Y ESTILO DINÁMICO (SATA-PRO LOCALE) ---
        const observer = new MutationObserver(() => {
            // 1. Traducir Botón de Permisos
            const btnPerm = document.getElementById('html5-qrcode-button-camera-permission');
            if (btnPerm && btnPerm.innerText !== "Conceder Permiso de Cámara") {
                btnPerm.innerText = "Conceder Permiso de Cámara";
                btnPerm.className = "btn bg-primary text-white font-medium px-6 py-2 mt-4 shadow-sm";
            }
            
            // 2. Traducir Botón de Detener
            const btnStop = document.getElementById('html5-qrcode-button-camera-stop');
            if (btnStop && btnStop.innerText !== "Detener Cámara") {
                btnStop.innerText = "Detener Cámara";
                btnStop.className = "btn bg-danger text-white font-medium px-6 py-2 mt-4 mb-4 shadow-sm";
                status.textContent = "CONECTADO";
                status.className = "badge bg-success/10 text-success font-bold uppercase";
            }

            // 3. Traducir Botón de Iniciar
            const btnStart = document.getElementById('html5-qrcode-button-camera-start');
            if (btnStart && btnStart.innerText !== "Iniciar Cámara") {
                btnStart.innerText = "Iniciar Cámara";
                btnStart.className = "btn bg-success text-white font-medium px-6 py-2 mt-4 shadow-sm";
            }

            // 4. Traducir Etiquetas de Selección
            const select = document.getElementById('html5-qrcode-select-camera');
            if (select) {
                select.className = "form-input mt-4 mb-2 bg-slate-50 border-slate-200 text-sm font-medium";
                
                // Buscamos cualquier elemento que contenga "Select Camera" y lo traducimos
                const allElements = document.querySelectorAll('#reader *');
                allElements.forEach(el => {
                    if (el.children.length === 0 && el.innerText.includes('Select Camera')) {
                        el.innerText = el.innerText.replace('Select Camera', 'Seleccionar Cámara');
                        el.className = "text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1";
                    }
                });
            }

            // 5. Ocultar enlaces de la librería
            document.querySelectorAll('#reader a').forEach(a => a.style.display = 'none');
        });
        observer.observe(document.getElementById('reader'), { childList: true, subtree: true });
    }

    function onSuccess(decodedText) {
        if (window.isLock) return;
        window.isLock = true;

        fetch("{{ route('scan.process') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ qr_uuid: decodedText })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                addFeedEntry(data);
                updateStats(data.estado);
            } else {
                alert("Error: " + data.message);
            }
            setTimeout(() => { window.isLock = false; }, 3000);
        })
        .catch(e => {
            console.error(e);
            window.isLock = false;
        });
    }

    function updateStats(estado) {
        if (estado === 'PRESENTE') counts.P++; else counts.T++;
        document.getElementById('stat-p').textContent = counts.P;
        document.getElementById('stat-t').textContent = counts.T;
        document.getElementById('scan-count').textContent = `${counts.P + counts.T} HOY`;
    }

    function addFeedEntry(data) {
        const feed = document.getElementById('history-feed');
        const empty = document.getElementById('empty-msg');
        if (empty) empty.remove();

        const isTarde = data.estado === 'TARDE';
        const badgeColor = isTarde ? 'bg-warning/10 text-warning' : 'bg-success/10 text-success';

        const html = `
            <div class="p-4 border-b border-slate-100 flex items-center justify-between hover:bg-slate-50/50 transition-all">
                <div class="flex items-center gap-3">
                    <div class="size-9 rounded bg-slate-100 flex items-center justify-center text-slate-500">
                        <i data-lucide="${isTarde ? 'clock' : 'user-check'}" class="size-4"></i>
                    </div>
                    <div>
                        <h6 class="text-sm font-bold text-slate-800 uppercase leading-none">${data.student}</h6>
                        <p class="text-[10px] text-slate-400 font-mono mt-1">${data.hora} | ${data.dni}</p>
                    </div>
                </div>
                <span class="badge ${badgeColor} text-[10px] font-bold italic">${data.estado}</span>
            </div>
        `;
        feed.insertAdjacentHTML('afterbegin', html);
        lucide.createIcons();

        if (data.vulnerabilidad !== 'Ninguna') {
            const risk = document.getElementById('risk-box');
            document.getElementById('risk-msg').textContent = `${data.student} tiene riesgo por: ${data.vulnerabilidad}`;
            risk.classList.remove('hidden');
            setTimeout(() => risk.classList.add('hidden'), 8000);
        }
    }
</script>

<style>
    #reader { border: none !important; }
    #reader video { border-radius: 0 !important; object-fit: cover !important; width: 100% !important; min-height: 400px; }
    #reader__dashboard_section_swaplink { display: none !important; }
    #reader__status_span { font-size: 11px !important; color: #64748b !important; }
</style>
@endsection
