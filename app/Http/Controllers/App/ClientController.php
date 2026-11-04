<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;
use App\Exports\SingleClientExport;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Client::latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        $clients = $query->paginate(10);

        return view('app.clients.index', compact('clients', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'notas' => ['nullable', 'string'],
        ]);

        Client::create($validated);

        return redirect()->back()->with('success', 'Cliente registrado exitosamente.');
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'notas' => ['nullable', 'string'],
        ]);

        $client->update($validated);

        return redirect()->back()->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client)
    {
        try {
            DB::beginTransaction();
            Appointment::where('client_id', $client->id)->update(['client_id' => null]);
            $client->forceDelete();
            DB::commit();
            return redirect()->back()->with('success', 'Cliente eliminado permanentemente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }

    // ==========================================
    // MÉTODOS DE EXPORTACIÓN
    // ==========================================

    // PDF Individual (Ficha del Cliente)
    public function exportSinglePdf(Client $client)
    {
        $pdf = Pdf::loadView('pdf.cliente', compact('client'));
        return $pdf->download('Cliente_' . $client->id . '.pdf');
    }

    // Excel Individual
    public function exportSingleExcel(Client $client)
    {
        return Excel::download(new SingleClientExport($client), 'Cliente_' . $client->id . '.xlsx');
    }

    // PDF General (Listado)
    public function exportGeneralPdf(Request $request)
    {
        $query = Client::latest();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%");
            });
        }
        $clients = $query->get();
        $pdf = Pdf::loadView('pdf.reporte-clientes', compact('clients'));
        return $pdf->download('Reporte_Clientes_' . date('d-m-Y') . '.pdf');
    }

    // Excel General
    public function exportGeneralExcel(Request $request)
    {
        $search = $request->get('search');
        return Excel::download(new ClientsExport($search), 'Reporte_Clientes_' . date('d-m-Y') . '.xlsx');
    }
}
