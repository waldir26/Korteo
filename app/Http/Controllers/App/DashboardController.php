<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Barber;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();

        // 1. Tarjetas superiores
        $citasHoy = Appointment::whereDate('fecha_hora', $hoy)->where('estado', '!=', 'cancelada')->count();
        $ingresosHoy = Appointment::whereDate('fecha_hora', $hoy)->where('estado', 'completada')->join('services', 'appointments.service_id', '=', 'services.id')->sum('services.precio');
        $barberosActivos = Barber::where('activo', true)->count();

        // 2. Gráfico: Ingresos de los últimos 7 días
        $ingresosSemana = [];
        $fechasSemana = [];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $fechasSemana[] = $fecha->translatedFormat('D d'); // Ej: Lun 20

            $ingresosDia = Appointment::whereDate('fecha_hora', $fecha)
                ->where('estado', 'completada')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->sum('services.precio');

            $ingresosSemana[] = $ingresosDia;
        }

        // 3. Top Barberos (Citas completadas esta semana)
        $topBarberos = Appointment::where('estado', 'completada')
            ->whereBetween('fecha_hora', [Carbon::today()->subDays(6), Carbon::today()->endOfDay()])
            ->select('barber_id', DB::raw('count(*) as total'))
            ->groupBy('barber_id')
            ->orderByDesc('total')
            ->with('barber')
            ->take(3)
            ->get();

        // 4. Próximas Citas de Hoy (Para el nuevo widget del Dashboard)
        $proximasCitas = Appointment::whereDate('fecha_hora', $hoy)
            ->where('fecha_hora', '>=', now())
            ->where('estado', 'pendiente')
            ->orderBy('fecha_hora')
            ->with(['client', 'service', 'barber'])
            ->take(3)
            ->get();

        return view('app.dashboard', compact(
            'citasHoy',
            'ingresosHoy',
            'barberosActivos',
            'fechasSemana',
            'ingresosSemana',
            'topBarberos',
            'proximasCitas' // <-- Agregado aquí
        ));
    }
}
