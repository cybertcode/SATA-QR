<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carnets QR — {{ $tenant->nombre }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            color: #1f2937;
        }

        /* ─── Toolbar (solo pantalla) ─── */
        .toolbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .toolbar-left h1 {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
        }

        .toolbar-left .badge {
            background: #eff6ff;
            color: #1e40af;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid #bfdbfe;
        }

        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toolbar select {
            font-family: 'Inter', sans-serif;
            padding: 6px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.8rem;
            background: white;
            color: #374151;
            cursor: pointer;
        }

        .toolbar select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 18px;
            border: none;
            border-radius: 6px;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background: #1e3a8a;
            color: white;
        }

        .btn-primary:hover {
            background: #1e40af;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        /* ─── Grid de Carnets A4 ─── */
        .page-container {
            padding: 20px;
            max-width: 220mm;
            margin: 0 auto;
        }

        .a4-page {
            width: 210mm;
            min-height: 297mm;
            background: white;
            margin: 0 auto 20px;
            padding: 8mm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            gap: 4mm;
            align-content: start;
        }

        /* ─── Carnet Compacto ─── */
        .carnet {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
            display: flex;
            flex-direction: column;
            height: 90mm;
            position: relative;
            background: white;
        }

        .carnet-header {
            padding: 5px 6px 4px;
            color: white;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }

        .carnet-header::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: -10%;
            width: 120%;
            height: 16px;
            background: white;
            border-radius: 50%;
        }

        .carnet-header .ie-name {
            font-size: 0.48rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            line-height: 1.2;
            position: relative;
            z-index: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .carnet-body {
            flex: 1;
            padding: 4px 6px 3px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
        }

        .carnet-body .qr-box {
            padding: 4px;
            background: #fafbfc;
            border: 1.5px solid #e8ecf0;
            border-radius: 6px;
            display: inline-block;
            line-height: 0;
        }

        .carnet-body .qr-box svg {
            display: block;
        }

        .carnet-body .stu-name {
            font-size: 0.55rem;
            font-weight: 800;
            color: #111827;
            line-height: 1.2;
            margin-top: 2px;
        }

        .carnet-body .stu-lastname {
            font-size: 0.5rem;
            font-weight: 600;
            color: #374151;
            line-height: 1.15;
        }

        .carnet-body .stu-dni {
            font-size: 0.48rem;
            font-weight: 600;
            color: #6b7280;
            margin-top: 1px;
        }

        .carnet-body .stu-dni strong {
            color: #111827;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
        }

        .carnet-body .stu-grade {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.45rem;
            font-weight: 700;
            margin-top: 2px;
        }

        .carnet-body .grade-primaria {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fbbf24;
        }

        .carnet-body .grade-secundaria {
            background: #e0e7ff;
            color: #3730a3;
            border: 1px solid #818cf8;
        }

        .carnet-footer {
            background: #f8fafc;
            padding: 3px 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e8ecf0;
            flex-shrink: 0;
        }

        .carnet-footer span {
            font-size: 0.38rem;
            font-weight: 700;
            color: #9ca3af;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .carnet-footer .year {
            font-weight: 800;
            color: #3b82f6;
        }

        /* ─── Estado vacío ─── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin-bottom: 16px;
            color: #9ca3af;
        }

        /* ─── Print ─── */
        @media print {
            body {
                background: white;
            }

            .toolbar {
                display: none !important;
            }

            .page-container {
                padding: 0;
                max-width: none;
            }

            .a4-page {
                width: 210mm;
                height: 297mm;
                padding: 8mm;
                margin: 0;
                box-shadow: none;
                border-radius: 0;
                page-break-after: always;
                page-break-inside: avoid;
            }

            .a4-page:last-child {
                page-break-after: avoid;
            }
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }
    </style>
</head>

<body>
    {{-- ═══ TOOLBAR ═══ --}}
    <div class="toolbar">
        <div class="toolbar-left">
            <a href="{{ route('institutions.index') }}" class="btn btn-secondary" title="Volver">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7" />
                </svg>
            </a>
            <h1>{{ $tenant->nombre }}</h1>
            <span class="badge">{{ $students->count() }} carnets</span>
        </div>
        <div class="toolbar-right">
            <select id="filterNivel" onchange="applyFilters()">
                <option value="">Todos los niveles</option>
                <option value="Primaria" {{ request('nivel') == 'Primaria' ? 'selected' : '' }}>Primaria</option>
                <option value="Secundaria" {{ request('nivel') == 'Secundaria' ? 'selected' : '' }}>Secundaria</option>
            </select>
            <select id="filterGrado" onchange="applyFilters()">
                <option value="">Todos los grados</option>
                @for ($g = 1; $g <= 6; $g++)
                    <option value="{{ $g }}" {{ request('grado') == $g ? 'selected' : '' }}>
                        {{ $g }}° Grado</option>
                @endfor
            </select>
            <button class="btn btn-primary" onclick="window.print()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Imprimir Todo
            </button>
        </div>
    </div>

    {{-- ═══ PÁGINAS A4 ═══ --}}
    <div class="page-container">
        @if ($students->isEmpty())
            <div class="a4-page" style="display: flex; align-items: center; justify-content: center">
                <div class="empty-state" style="grid-column: span 3">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" style="display:inline-block">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <path d="M3 9h18M9 21V9" />
                    </svg>
                    <h3 style="color:#6b7280;font-size:1rem;font-weight:600;margin-bottom:4px">Sin estudiantes activos
                    </h3>
                    <p style="color:#9ca3af;font-size:0.85rem">No hay estudiantes con matrícula activa para esta I.E.
                        con los filtros seleccionados.</p>
                </div>
            </div>
        @else
            @php $primaryColor = $tenant->config['primary_color'] ?? '#1e3a8a'; @endphp
            @foreach ($students->chunk(9) as $pageStudents)
                <div class="a4-page">
                    @foreach ($pageStudents as $student)
                        @php $matricula = $student->matriculaActual; @endphp
                        <div class="carnet">
                            <div class="carnet-header"
                                style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $primaryColor }}dd)">
                                <div class="ie-name">{{ $tenant->nombre }}</div>
                            </div>
                            <div class="carnet-body">
                                <div class="qr-box">
                                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(75)->margin(0)->generate($student->qr_uuid) !!}
                                </div>
                                <div class="stu-name">{{ $student->nombres }}</div>
                                <div class="stu-lastname">{{ $student->apellido_paterno }}
                                    {{ $student->apellido_materno }}</div>
                                <div class="stu-dni">DNI: <strong>{{ $student->dni }}</strong></div>
                                @if ($matricula)
                                    @php $nivel = $matricula->seccion->nivelEducativo->nivel; @endphp
                                    <div
                                        class="stu-grade {{ $nivel === 'Primaria' ? 'grade-primaria' : 'grade-secundaria' }}">
                                        {{ $matricula->seccion->grado }}° "{{ $matricula->seccion->letra }}" —
                                        {{ $nivel }}
                                    </div>
                                @endif
                            </div>
                            <div class="carnet-footer">
                                <span>SATA-QR • UGEL Huacaybamba</span>
                                <span class="year">{{ now()->year }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>

    <script>
        function applyFilters() {
            const nivel = document.getElementById('filterNivel').value;
            const grado = document.getElementById('filterGrado').value;
            const params = new URLSearchParams();
            if (nivel) params.set('nivel', nivel);
            if (grado) params.set('grado', grado);
            const qs = params.toString();
            window.location.href = window.location.pathname + (qs ? '?' + qs : '');
        }
    </script>
</body>

</html>
