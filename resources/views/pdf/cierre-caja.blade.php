<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cierre de Caja - {{ date('d/m/Y') }}</title>
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

        .header .date {
            font-size: 14px;
            font-weight: bold;
            color: #3B82F6;
            margin-top: 10px;
        }

        /* Contenedor de Resumen */
        .summary-container {
            width: 100%;
            display: table;
            margin-bottom: 20px;
        }

        .summary-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }

        .box {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            background-color: #f8fafc;
        }

        .box-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .row-total {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #cbd5e1;
            font-size: 15px;
            font-weight: bold;
        }

        .text-green {
            color: #10B981;
        }

        .text-red {
            color: #EF4444;
        }

        /* Balance Final */
        .balance-box {
            text-align: center;
            border: 2px solid #3B82F6;
            border-radius: 12px;
            padding: 25px;
            margin-top: 30px;
            background-color: #eff6ff;
        }

        .balance-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #64748b;
            margin-bottom: 10px;
        }

        .balance-amount {
            font-size: 36px;
            font-weight: bold;
            margin: 0;
        }

        .balance-message {
            font-size: 12px;
            color: #64748b;
            margin-top: 10px;
        }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            margin-top: 50px;
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
        <p>Cierre de Caja</p>
        <div class="date">{{ \Carbon\Carbon::today()->translatedFormat('l, d \d\e F \d\e Y') }}</div>
    </div>

    <!-- Resumen de Operaciones -->
    <div class="summary-container">
        <!-- Ingresos -->
        <div class="summary-box">
            <div class="box">
                <div class="box-title">Ingresos</div>
                <div class="row">
                    <span>Servicios Dados:</span>
                    <span class="text-green font-bold">${{ number_format($totalServicios, 2) }}</span>
                </div>
                <div class="row">
                    <span>Ventas de Productos:</span>
                    <span class="text-green font-bold">${{ number_format($totalVentas, 2) }}</span>
                </div>
                <div class="row-total">
                    <span>Total Ingresos:</span>
                    <span class="text-green">${{ number_format($ingresosTotales, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Egresos -->
        <div class="summary-box">
            <div class="box">
                <div class="box-title">Egresos</div>
                <div class="row">
                    <span>Gastos de la Barbería:</span>
                    <span class="text-red font-bold">${{ number_format($totalGastos, 2) }}</span>
                </div>
                <div class="row">
                    <span>Pagos a Barberos:</span>
                    <span class="text-red font-bold">${{ number_format($totalPagos, 2) }}</span>
                </div>
                <div class="row-total">
                    <span>Total Egresos:</span>
                    <span class="text-red">${{ number_format($egresosTotales, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Final -->
    <div class="balance-box">
        <div class="balance-title">Balance del Día</div>

        @if($balanceFinal < 0)
            <p class="balance-amount text-red">-${{ number_format(abs($balanceFinal), 2) }}</p>
            <p class="balance-message">Hubo una pérdida hoy. Los egresos superaron los ingresos.</p>
            @elseif($balanceFinal > 0)
            <p class="balance-amount text-green">${{ number_format($balanceFinal, 2) }}</p>
            <p class="balance-message">¡Buen día! Hubo una ganancia neta.</p>
            @else
            <p class="balance-amount" style="color: #64748b;">$0.00</p>
            <p class="balance-message">El balance del día quedó en cero.</p>
            @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        Este documento fue generado automáticamente por Korteo.<br>
        Sistema de Gestión de Barberías
    </div>

</body>

</html>