<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return view('app.clients.index', compact('clients'));
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

            // 1. Desvinculamos las citas (para que no haya errores de historial)
            Appointment::where('client_id', $client->id)->update(['client_id' => null]);

            // 2. Eliminamos el cliente de la base de datos de verdad
            $client->forceDelete();

            DB::commit();
            return redirect()->back()->with('success', 'Cliente eliminado permanentemente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }
}
