<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Directorio de Personal - SATA QR</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #1e293b;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
            color: #4F46E5;
            margin-bottom: 2px;
        }

        .header p {
            font-size: 9px;
            color: #64748b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead th {
            background-color: #4F46E5;
            color: #fff;
            padding: 6px 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody td {
            padding: 5px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9px;
        }

        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 600;
        }

        .badge-active {
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #dc2626;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }

        .meta {
            text-align: right;
            font-size: 8px;
            color: #94a3b8;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>SATA QR — Directorio de Personal</h1>
        <p>UGEL Huacaybamba • Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="meta">Total: {{ $users->count() }} usuarios</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>DNI</th>
                <th>Institución</th>
                <th>Cargo</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Último Acceso</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->dni ?? '—' }}</td>
                    <td>{{ $user->tenant?->nombre ?? 'UGEL Huacaybamba' }}</td>
                    <td>{{ $user->cargo ?? '—' }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <span class="badge {{ $user->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>{{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Nunca' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        SATA QR — Sistema de Asistencia y Tutoría Automatizado • {{ now()->format('Y') }}
    </div>
</body>

</html>
