<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carnet QR - {{ $student->nombre_completo }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f2f5;
            padding: 20px;
        }

        .carnet {
            background: white;
            border-radius: 14px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(0, 0, 0, 0.05);
            width: 340px;
            overflow: hidden;
            text-align: center;
            position: relative;
        }

        .carnet::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, {{ $student->tenant->config['primary_color'] ?? '#1e3a8a' }}, {{ $student->tenant->config['primary_color'] ?? '#1e3a8a' }}88);
        }

        .header {
            background: linear-gradient(135deg, {{ $student->tenant->config['primary_color'] ?? '#1e3a8a' }}, {{ $student->tenant->config['primary_color'] ?? '#1e3a8a' }}dd);
            color: white;
            padding: 18px 16px 14px;
            position: relative;
            overflow: hidden;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: -10%;
            width: 120%;
            height: 40px;
            background: white;
            border-radius: 50%;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .header img {
            height: 36px;
            margin-bottom: 6px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        .header .ie-name {
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .header .ie-subtitle {
            font-size: 0.6rem;
            font-weight: 500;
            opacity: 0.85;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .body {
            padding: 20px 20px 16px;
        }

        .qr-wrapper {
            display: inline-block;
            padding: 12px;
            background: #fafbfc;
            border: 2px solid #e8ecf0;
            border-radius: 12px;
            margin-bottom: 14px;
            position: relative;
        }

        .qr-wrapper svg {
            display: block;
        }

        .student-name {
            font-size: 1.15rem;
            font-weight: 900;
            color: #111827;
            line-height: 1.25;
            margin-bottom: 2px;
        }

        .student-lastname {
            font-size: 0.95rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
        }

        .dni-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f3f4f6;
            color: #4b5563;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            border: 1px solid #e5e7eb;
        }

        .dni-badge strong {
            color: #111827;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
        }

        .grade-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .grade-primaria {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            border: 1px solid #fbbf24;
        }

        .grade-secundaria {
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
            color: #3730a3;
            border: 1px solid #818cf8;
        }

        .footer {
            background: #f8fafc;
            padding: 10px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e8ecf0;
        }

        .footer-brand {
            font-size: 0.65rem;
            font-weight: 700;
            color: #9ca3af;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .footer-year {
            font-size: 0.65rem;
            font-weight: 800;
            color: {{ $student->tenant->config['primary_color'] ?? '#1e3a8a' }};
            letter-spacing: 1px;
        }

        .no-print {
            margin-top: 20px;
            text-align: center;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 28px;
            background: {{ $student->tenant->config['primary_color'] ?? '#1e3a8a' }};
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-print:hover {
            opacity: 0.9;
        }

        @media print {
            body {
                background: white;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .carnet {
                box-shadow: none;
                border: 1px solid #ccc;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div>
        <div class="carnet">
            <div class="header">
                <div class="header-content">
                    @if (isset($student->tenant->config['logo_path']))
                        <img src="{{ asset($student->tenant->config['logo_path']) }}" alt="Logo"><br>
                    @endif
                    <div class="ie-name">{{ $student->tenant->nombre }}</div>
                    <div class="ie-subtitle">Carnet de Identificación Estudiantil</div>
                </div>
            </div>
            <div class="body">
                <div class="qr-wrapper">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->margin(0)->generate($student->qr_uuid) !!}
                </div>
                <div class="student-name">{{ $student->nombres }}</div>
                <div class="student-lastname">{{ $student->apellido_paterno }} {{ $student->apellido_materno }}</div>
                <div class="dni-badge">DNI: <strong>{{ $student->dni }}</strong></div>

                @php $matricula = $student->matriculaActual; @endphp
                @if ($matricula)
                    @php $nivel = $matricula->seccion->nivelEducativo->nivel; @endphp
                    <br>
                    <div class="grade-badge {{ $nivel === 'Primaria' ? 'grade-primaria' : 'grade-secundaria' }}">
                        {{ $matricula->seccion->grado }}° "{{ $matricula->seccion->letra }}" — {{ $nivel }}
                    </div>
                @endif
            </div>
            <div class="footer">
                <span class="footer-brand">SATA-QR • UGEL Huacaybamba</span>
                <span class="footer-year">{{ now()->year }}</span>
            </div>
        </div>

        <div class="no-print">
            <button class="btn-print" onclick="window.print()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Imprimir Carnet
            </button>
        </div>
    </div>
</body>

</html>
