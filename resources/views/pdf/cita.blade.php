<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recibo de Cita #{{ $appointment->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        /* Encabezado */
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
            letter-spacing: 1px;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Línea divisoria */
        .divider {
            border-top: 2px solid #e2e8f0;
            margin: 20px 0;
        }

        /* Info Grid */
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

        /* Caja de detalles */
        .details-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .details-box h3 {
            margin-top: 0;
            font-size: 14px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        /* Total */
        .total-box {
            text-align: right;
            border-top: 2px solid #3B82F6;
            padding-top: 15px;
            margin-top: 15px;
        }

        .total-label {
            font-size: 14px;
            color: #64748b;
        }

        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #0f172a;
        }

        /* Footer */
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

    <!-- Encabezado con Logo Dinámico -->
    <div class="header">
        @if(auth()->user()->barbershop && auth()->user()->barbershop->logo)
        <!-- Si hay logo, lo mostramos -->
        <img src="{{ public_path('storage/' . auth()->user()->barbershop->logo) }}" alt="Logo">
        @else
        <!-- Si no hay logo, mostramos el nombre en negro -->
        <h1>{{ auth()->user()->barbershop->nombre ?? 'Korteo' }}</h1>
        @endif
        <p>Comprobante Oficial de Cita</p>
    </div>

    <div class="divider"></div>

    <!-- Datos del Cliente y Cita -->
    <table class="info-table">
        <tr>
            <td class="info-label">Folio:</td>
            <td class="info-value">#{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}</td>
            <td class="info-label">Fecha y Hora:</td>
            <td class="info-value">{{ \Carbon\Carbon::parse($appointment->fecha_hora)->format('d/m/Y h:i A') }}</td>
        </tr>
        <tr>
            <td class="info-label">Cliente:</td>
            <td class="info-value">{{ $appointment->client?->nombre ?? 'Cliente eliminado' }}</td>
            <td class="info-label">Barbero:</td>
            <td class="info-value">{{ $appointment->barber?->nombre ?? 'Barbero eliminado' }}</td>
        </tr>
        <tr>
            <td class="info-label">Teléfono:</td>
            <td class="info-value">{{ $appointment->client?->telefono ?? '—' }}</td>
            <td class="info-label">Estado:</td>
            <td class="info-value" style="text-transform: capitalize;">{{ $appointment->estado }}</td>
        </tr>
    </table>

    <!-- Detalles del Servicio -->
    <div class="details-box">
        <h3>Detalles del Servicio</h3>

        <table class="info-table" style="margin-bottom: 0;">
            <tr>
                <td class="info-label" style="width: 100px;">Servicio:</td>
                <td class="info-value">{{ $appointment->service?->nombre ?? 'Servicio eliminado' }}</td>
            </tr>
            <tr>
                <td class="info-label" style="width: 100px;">Duración:</td>
                <td class="info-value">{{ $appointment->service?->duracion ? $appointment->service->duracion . ' minutos' : 'No definida' }}</td>
            </tr>
            <tr>
                <td class="info-label" style="width: 100px;">Notas:</td>
                <td class="info-value">{{ $appointment->notas ?? 'Sin notas adicionales.' }}</td>
            </tr>
        </table>

        <div class="total-box">
            <span class="total-label">Total a Pagar: </span>
            <span class="total-amount">${{ number_format($appointment->service?->precio ?? 0, 2) }}</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Este comprobante fue generado automáticamente por Korteo.<br>
        Sistema de Gestión de Barberías
    </div>

</body>

</html>