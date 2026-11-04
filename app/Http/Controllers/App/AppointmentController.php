<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Barber;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AppointmentsExport;
use App\Exports\SingleAppointmentExport;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $fecha = $request->get('fecha', Carbon::today()->toDateString());
        $search = $request->get('search');

        $query = Appointment::with(['client', 'barber', 'service'])
            ->whereHas('client')
            ->whereHas('service')
            ->whereDate('fecha_hora', $fecha);

        if ($user->hasRole('barbero')) {
            $query->where('barber_id', $user->barber->id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('client', function ($qClient) use ($search) {
                    $qClient->where('nombre', 'like', "%{$search}%");
                })->orWhereHas('barber', function ($qBarber) use ($search) {
                    $qBarber->where('nombre', 'like', "%{$search}%");
                });
            });
        }

        $appointments = $query->orderBy('fecha_hora', 'asc')->get();

        $barbers = Barber::where('activo', true)->orderBy('nombre')->get();
        $services = Service::where('activo', true)->orderBy('nombre')->get();

        return view('app.appointments.index', compact('appointments', 'fecha', 'search', 'barbers', 'services'));
    }

    public function create()
    {
        $user = auth()->user();

        $clients = Client::orderBy('nombre')->get();
        $barbers = Barber::where('activo', true)->orderBy('nombre')->get();
        $services = Service::where('activo', true)->orderBy('nombre')->get();

        $defaultBarberId = $user->hasRole('barbero') ? $user->barber->id : null;

        return view('app.appointments.create', compact('clients', 'barbers', 'services', 'defaultBarberId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'barber_id' => ['required', 'exists:barbers,id'],
            'service_id' => ['required', 'exists:services,id'],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'notas' => ['nullable', 'string'],
        ]);

        $fechaHora = Carbon::parse($validated['fecha'] . ' ' . $validated['hora']);

        $choque = Appointment::where('barber_id', $validated['barber_id'])
            ->where('fecha_hora', $fechaHora)
            ->where('estado', '!=', 'cancelada')
            ->exists();

        if ($choque) {
            return back()->withInput()->withErrors(['hora' => 'El barbero ya tiene una cita agendada exactamente a esta hora. Por favor, elige otra hora.']);
        }

        Appointment::create([
            'client_id' => $validated['client_id'],
            'barber_id' => $validated['barber_id'],
            'service_id' => $validated['service_id'],
            'fecha_hora' => $fechaHora,
            'estado' => 'pendiente',
            'notas' => $validated['notas'],
        ]);

        return redirect()->route('app.appointments.index', ['fecha' => $validated['fecha']])
            ->with('success', 'Cita agendada exitosamente.');
    }

    public function update(Request $request, Appointment $appointment)
    {
        if (auth()->user()->hasRole('barbero') && $appointment->barber_id !== auth()->user()->barber->id) {
            abort(403, 'No tienes permiso para editar esta cita.');
        }

        $validated = $request->validate([
            'barber_id' => ['required', 'exists:barbers,id'],
            'service_id' => ['required', 'exists:services,id'],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'notas' => ['nullable', 'string'],
        ]);

        $fechaHora = Carbon::parse($validated['fecha'] . ' ' . $validated['hora']);

        $choque = Appointment::where('barber_id', $validated['barber_id'])
            ->where('fecha_hora', $fechaHora)
            ->where('estado', '!=', 'cancelada')
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($choque) {
            return back()->withInput()->withErrors(['hora' => 'El barbero ya tiene otra cita agendada a esta misma hora.']);
        }

        $appointment->update([
            'barber_id' => $validated['barber_id'],
            'service_id' => $validated['service_id'],
            'fecha_hora' => $fechaHora,
            'notas' => $validated['notas'],
        ]);

        return redirect()->back()->with('success', 'Cita actualizada correctamente.');
    }

    public function pay(Appointment $appointment)
    {
        Transaction::create([
            'tipo' => 'ingreso_servicio',
            'descripcion' => 'Cobro: ' . $appointment->service->nombre . ' (' . $appointment->client->nombre . ')',
            'monto' => $appointment->service->precio,
            'appointment_id' => $appointment->id,
        ]);

        $appointment->update(['estado' => 'completada']);

        return redirect()->back()->with('success', 'Pago registrado exitosamente y cita completada.');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'estado' => ['required', 'in:pendiente,completada,cancelada']
        ]);

        $appointment->update(['estado' => $validated['estado']]);

        return redirect()->back()->with('success', 'Estado de la cita actualizado.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->forceDelete();

        return redirect()->back()->with('success', 'Cita eliminada permanentemente.');
    }

    public function calendar()
    {
        return view('app.appointments.calendar');
    }

    public function calendarData()
    {
        $user = auth()->user();

        $query = Appointment::with(['client', 'barber', 'service'])
            ->where('estado', '!=', 'cancelada');

        if ($user->hasRole('barbero')) {
            $query->where('barber_id', $user->barber->id);
        }

        $appointments = $query->get();

        $events = $appointments->map(function ($cita) {
            $color = match ($cita->estado) {
                'pendiente' => '#3B82F6',
                'completada' => '#10B981',
                default => '#64748B',
            };

            return [
                'id' => $cita->id,
                'title' => $cita->client->nombre,
                'date' => $cita->fecha_hora->format('Y-m-d'),
                'start' => $cita->fecha_hora->toIso8601String(),
                'backgroundColor' => $color,
                'extendedProps' => [
                    'cliente' => $cita->client->nombre,
                    'servicio' => $cita->service->nombre,
                    'precio' => number_format($cita->service->precio, 2),
                    'barbero' => $cita->barber->nombre,
                    'estado' => ucfirst($cita->estado),
                    'notas' => $cita->notas ?? 'Sin notas',
                    'hora' => $cita->fecha_hora->format('h:i A'),
                    'fecha' => $cita->fecha_hora->format('d/m/Y'),
                ]
            ];
        });

        return response()->json($events);
    }

    public function checkAvailability(Request $request)
    {
        $fecha = $request->get('fecha');
        $hora = $request->get('hora');
        $barber_id = $request->get('barber_id');

        if (!$fecha || !$hora || !$barber_id) {
            return response()->json(['disponible' => true]);
        }

        $fechaHora = Carbon::parse($fecha . ' ' . $hora);

        $ocupado = Appointment::where('barber_id', $barber_id)
            ->where('fecha_hora', $fechaHora)
            ->where('estado', '!=', 'cancelada')
            ->first();

        if ($ocupado) {
            return response()->json([
                'disponible' => false,
                'cliente' => $ocupado->client->nombre
            ]);
        }

        return response()->json(['disponible' => true]);
    }

    // ==========================================
    // MÉTODOS DE EXPORTACIÓN
    // ==========================================

    public function exportSinglePdf(Appointment $appointment)
    {
        $pdf = Pdf::loadView('pdf.cita', compact('appointment'));
        return $pdf->download('Cita_' . $appointment->id . '.pdf');
    }

    public function exportSingleExcel(Appointment $appointment)
    {
        // Usamos la clase de Excel real (SingleAppointmentExport)
        return Excel::download(new SingleAppointmentExport($appointment), 'Cita_' . $appointment->id . '.xlsx');
    }

    public function exportGeneralPdf(Request $request)
    {
        $query = Appointment::with(['client', 'barber', 'service']);

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_hora', $request->fecha);
        }
        if ($request->filled('search')) {
            $query->whereHas('client', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%');
            });
        }

        if (auth()->user()->hasRole('barbero')) {
            $query->where('barber_id', auth()->user()->barber->id);
        }

        $appointments = $query->orderBy('fecha_hora', 'asc')->get();

        $pdf = Pdf::loadView('pdf.reporte-citas', compact('appointments'));
        return $pdf->download('Reporte_Citas_' . date('d-m-Y') . '.pdf');
    }

    public function exportGeneralExcel(Request $request)
    {
        $fecha = $request->get('fecha');
        $search = $request->get('search');
        $barberId = $request->get('barber_id');

        if (auth()->user()->hasRole('barbero')) {
            $barberId = auth()->user()->barber->id;
        }

        return Excel::download(new AppointmentsExport($fecha, $search, $barberId), 'Reporte_Citas_' . date('d-m-Y') . '.xlsx');
    }
}
