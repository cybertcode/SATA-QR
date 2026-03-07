<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carnet QR - {{ $student->nombre_completo }}</title>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background-color: #f3f4f6; 
            margin: 0; 
        }
        .carnet { 
            background: white; 
            border-radius: 12px; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); 
            width: 320px; 
            overflow: hidden; 
            text-align: center; 
            border: 1px solid #e5e7eb; 
            position: relative;
        }
        .header { 
            background-color: {{ $student->tenant->config['primary_color'] ?? '#1e3a8a' }}; 
            color: white; 
            padding: 20px 15px; 
            font-weight: bold; 
            font-size: 1.1rem;
        }
        .header img {
            height: 40px;
            margin-bottom: 10px;
        }
        .body { 
            padding: 25px 20px; 
        }
        .qr-container { 
            margin: 0 auto 15px; 
            padding: 15px; 
            background: white; 
            border: 2px dashed #e5e7eb; 
            display: inline-block; 
            border-radius: 8px;
        }
        .name { 
            font-size: 1.2rem; 
            font-weight: 800; 
            color: #1f2937; 
            line-height: 1.2;
        }
        .dni { 
            color: #6b7280; 
            font-size: 0.95rem; 
            margin-top: 5px;
            margin-bottom: 15px; 
            font-weight: 500;
        }
        .footer { 
            background-color: #f9fafb; 
            padding: 12px; 
            font-size: 0.75rem; 
            color: #6b7280; 
            border-top: 1px solid #e5e7eb; 
            font-weight: 600;
            letter-spacing: 1px;
        }
        .badge {
            background: #eff6ff; 
            color: #1e40af; 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 0.85rem; 
            font-weight: bold;
            display: inline-block;
            border: 1px solid #bfdbfe;
        }
        @media print {
            body { background: white; }
            .carnet { box-shadow: none; border: 1px solid #000; }
        }
    </style>
</head>
<body>
    <div class="carnet">
        <div class="header">
            @if(isset($student->tenant->config['logo_path']))
                <img src="{{ asset($student->tenant->config['logo_path']) }}" alt="Logo">
                <br>
            @endif
            {{ $student->tenant->nombre }}
        </div>
        <div class="body">
            <div class="qr-container">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(160)->margin(0)->generate($student->qr_uuid) !!}
            </div>
            <div class="name">{{ $student->nombres }}<br>{{ $student->apellido_paterno }} {{ $student->apellido_materno }}</div>
            <div class="dni">DNI: {{ $student->dni }}</div>
            
            @php $matricula = $student->matriculas->first(); @endphp
            @if($matricula)
                <div class="badge">
                    {{ $matricula->seccion->grado }}° "{{ $matricula->seccion->letra }}" - {{ $matricula->seccion->nivel }}
                </div>
            @endif
        </div>
        <div class="footer">
            SATA-QR | UGEL HUACAYBAMBA
        </div>
    </div>
    <script>
        // Imprimir automáticamente al abrir (ideal para impresión masiva)
        window.onload = function() { 
            setTimeout(() => { window.print(); }, 500); 
        }
    </script>
</body>
</html>