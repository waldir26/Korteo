<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte General de Clientes</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 11px;
            margin: 0;
            padding: 20px;
        }

        /* Encabezado */
        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header img {
            max-height: 70px;
            width: auto;
            margin-bottom: 10px;
        }

        .header h1 {
            color: #000;
            margin: 0;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #3B82F6;
            color: #ffffff;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            border-bottom: 1px solid #e2e8f0;
            padding: 10px;
            color: #334155;
        }

        /* Filas alternas (Cebra) */
        tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>

<body>

    <!-- Encabezado con Logo Dinámico -->
    <div class="header">
        @if(auth()->user()->barbershop && auth()->user()->barbershop->logo)
        <img src="{{ public_path('storage/' . auth()->user()->barbershop->logo) }}" alt="Logo">
        @else
        <h1>{{ auth()->user()->barbershop->nombre ?? 'Korteo' }}</h1>
        @endif
        <p>Reporte General de Clientes</p>
        <p style="font-size: 9px; color: #999; margin-top: 8px;">Generado el: {{ date('d/m/Y h:i A') }}</p>
    </div>

    <!-- Tabla de Clientes -->
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Notas</th>
                <th>Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
            <tr>
                <td style="font-weight: bold;">{{ $client->nombre }}</td>
                <td>{{ $client->telefono ?? 'N/A' }}</td>
                <td>{{ $client->email ?? 'N/A' }}</td>
                <td>{{ Str::limit($client->notas, 40) ?? 'Sin notas' }}</td>
                <td>{{ $client->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        Este reporte fue generado automáticamente por Korteo.<br>
        Sistema de Gestión de Barberías
    </div>

</body>

</html>