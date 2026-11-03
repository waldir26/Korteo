<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class BarberController extends Controller
{
    public function index()
    {
        $barbers = Barber::with('user')->latest()->paginate(10);
        return view('app.barbers.index', compact('barbers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        try {
            DB::beginTransaction();

            // 1. Creamos el Usuario (para el Login)
            $user = User::create([
                'barbershop_id' => auth()->user()->barbershop_id,
                'name' => $validated['nombre'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'activo' => true,
            ]);

            $user->assignRole('barbero');

            // 2. Creamos el Barbero
            $data = [
                'user_id' => $user->id,
                'nombre' => $validated['nombre'],
                'telefono' => $validated['telefono'],
                'activo' => true,
            ];

            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('barbers', 'public');
                $data['foto'] = $path;
            }

            Barber::create($data);

            DB::commit();
            return redirect()->back()->with('success', 'Barbero creado exitosamente con su cuenta de acceso.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al crear el barbero: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, Barber $barber)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'new_password' => ['nullable', 'string', 'min:6'],
        ]);

        try {
            DB::beginTransaction();

            $barber->nombre = $validated['nombre'];
            $barber->telefono = $validated['telefono'];

            if ($request->hasFile('foto')) {
                if ($barber->foto && Storage::disk('public')->exists($barber->foto)) {
                    Storage::disk('public')->delete($barber->foto);
                }
                $path = $request->file('foto')->store('barbers', 'public');
                $barber->foto = $path;
            }

            $barber->save();

            if ($barber->user) {
                $userData = ['name' => $validated['nombre']];

                if ($request->filled('new_password')) {
                    $userData['password'] = Hash::make($validated['new_password']);
                }

                $barber->user->update($userData);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Barbero y credenciales actualizados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al actualizar el barbero: ' . $e->getMessage()]);
        }
    }

    public function destroy(Barber $barber)
    {
        try {
            DB::beginTransaction();

            // OPCIONAL: Desvincular las citas de este barbero para no romper el historial
            // En lugar de borrarlas, las pasamos a "Sin asignar" o las cancelamos.
            \App\Models\Appointment::where('barber_id', $barber->id)->update(['barber_id' => null]);

            // 1. Eliminamos el usuario para quitarle el acceso
            if ($barber->user) {
                $barber->user->forceDelete(); // Forzamos el borrado del usuario
            }

            // 2. Eliminamos la foto
            if ($barber->foto && Storage::disk('public')->exists($barber->foto)) {
                Storage::disk('public')->delete($barber->foto);
            }

            // 3. Eliminamos al barbero permanentemente de la BD
            $barber->forceDelete();

            DB::commit();
            return redirect()->back()->with('success', 'Barbero eliminado permanentemente y acceso al sistema revocado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar el barbero: ' . $e->getMessage());
        }
    }
}
