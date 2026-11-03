<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::latest()->paginate(10);
        return view('app.services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'duracion' => ['nullable', 'integer', 'min:1'], // Ahora es opcional
        ]);

        Service::create([
            'nombre' => $validated['nombre'],
            'precio' => $validated['precio'],
            'duracion' => $validated['duracion'] ?? null,
            'activo' => true,
        ]);

        return redirect()->back()->with('success', 'Servicio creado exitosamente.');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'duracion' => ['nullable', 'integer', 'min:1'], // Ahora es opcional
        ]);

        $service->update($validated);

        return redirect()->back()->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Service $service)
    {

        $service->forceDelete();

        return redirect()->back()->with('success', 'Servicio eliminado permanentemente.');
    }
}
