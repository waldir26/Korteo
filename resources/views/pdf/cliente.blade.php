<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ficha del Cliente - {{ $client->nombre }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header img {
            max-height: 80px;
            width: auto;
            margin-bottom: 10px;
        }

        .header h1 {
            color: #000;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .divider {
            border-top: 2px solid #e2e8f0;
            margin: 20px 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .info-table td {
            padding: 8px 0;
            vertical-align: top;
            font-size: 14px;
        }

        .info-label {
            font-weight: bold;
            color: #64748b;
            width: 120px;
        }

        .info-value {
            color: #0f172a;
            font-weight: 600;
        }

        .stats-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            text-align: center;
        }

        .stats-box h3 {
            margin-top: 0;
            font-size: 14px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #3B82F6;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>

<body>

    <div class="header">
        @if(auth()->user()->barbershop && auth()->user()->barbershop->logo)
        <img src="{{ public_path('storage/' . auth()->user()->barbershop->logo) }}" alt="Logo">
        @else
        <h1>{{ auth()->user()->barbershop->nombre ?? 'Korteo' }}</h1>
        @endif
        <p>Ficha de Cliente</p>
    </div>

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td class="info-label">Nombre:</td>
            <td class="info-value">{{ $client->nombre }}</td>
        </tr>
        <tr>
            <td class="info-label">Teléfono:</td>
            <td class="info-value">{{ $client->telefono ?? 'No registrado' }}</td>
        </tr>
        <tr>
            <td class="info-label">Email:</td>
            <td class="info-value">{{ $client->email ?? 'No registrado' }}</td>
        </tr>
        <tr>
            <td class="info-label">Notas:</td>
            <td class="info-value">{{ $client->notas ?? 'Sin notas adicionales.' }}</td>
        </tr>
        <tr>
            <td class="info-label">Cliente desde:</td>
            <td class="info-value">{{ $client->created_at->format('d/m/Y') }}</td>
        </tr>
    </table>

    <!-- Resumen de actividad -->
    @php
    $totalCitas = $client->appointments()->count();
    $totalGastado = $client->appointments()->where('estado', 'completada')->join('services', 'appointments.service_id', '=', 'services.id')->sum('services.precio');
    @endphp

    <div class="stats-box">
        <h3>Resumen de Actividad</h3>
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <div class="stat-number">{{ $totalCitas }}</div>
                    <div style="font-size: 12px; color: #64748b;">Citas Agendadas</div>
                </td>
                <td style="width: 50%;">
                    <div class="stat-number">${{ number_format($totalGastado, 2) }}</div>
                    <div style="font-size: 12px; color: #64748b;">Total Consumido</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Este documento fue generado automáticamente por Korteo.<br>
        Sistema de Gestión de Barberías
    </div>

</body>

</html>