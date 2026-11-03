<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Barbershop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class BarbershopController extends Controller
{
    public function index()
    {
        // Buscamos las barberías y cargamos solo al usuario que tiene el rol 'admin'
        $barbershops = Barbershop::with(['users' => function ($query) {
            $query->role('admin');
        }])->latest()->paginate(10);

        return view('superadmin.barbershops.index', compact('barbershops'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:255'],
            'telefono' => ['required', 'numeric', 'digits:8'],
            'direccion' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!str_contains($value, '<iframe') && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $fail('La ubicación debe ser un código iframe de Google Maps o un enlace válido.');
                }
            }],
            'estado' => ['required', 'in:activa,en_prueba'],

            // Nuevos campos del administrador
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'admin_password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()],
        ]);

        try {
            DB::beginTransaction();

            $telefonoCompleto = '+503 ' . $validated['telefono'];

            $barbershop = Barbershop::create([
                'nombre' => $validated['nombre'],
                'slug' => Str::slug($validated['nombre']) . '-' . Str::random(4),
                'telefono' => $telefonoCompleto,
                'direccion' => $validated['direccion'],
                'estado' => $validated['estado'],
            ]);

            $admin = User::create([
                'barbershop_id' => $barbershop->id,
                'name' => $validated['admin_name'],
                'username' => $validated['admin_username'],
                'password' => Hash::make($validated['admin_password']),
                'activo' => true,
                'debe_cambiar_password' => true,
            ]);

            $admin->assignRole('admin');

            DB::commit();
            return redirect()->route('superadmin.barbershops.index')
                ->with('success', 'Barbería y administrador creados exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Ocurrió un error: ' . $e->getMessage()]);
        }
    }

    public function edit(Barbershop $barbershop)
    {
        return view('superadmin.barbershops.edit', compact('barbershop'));
    }

    public function update(Request $request, Barbershop $barbershop)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:255'],
            'telefono' => ['required', 'numeric', 'digits:8'],
            'direccion' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!str_contains($value, '<iframe') && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $fail('La ubicación debe ser un código iframe de Google Maps o un enlace válido.');
                }
            }],
            'estado' => ['required', 'in:activa,suspendida,en_prueba,cancelada'],
            'motivo_estado' => ['nullable', 'string', 'max:255'],
            'new_password' => ['nullable', 'string', 'min:8'], // Validación para la nueva clave
        ]);

        $barbershop->update([
            'nombre' => $validated['nombre'],
            'telefono' => '+503 ' . $validated['telefono'],
            'direccion' => $validated['direccion'],
            'estado' => $validated['estado'],
            'motivo_estado' => $validated['motivo_estado'] ?? $barbershop->motivo_estado,
        ]);

        if ($request->estado === 'suspendida' && !$barbershop->suspendida_en) {
            $barbershop->update(['suspendida_en' => now()]);
        } elseif ($request->estado === 'cancelada' && !$barbershop->cancelada_en) {
            $barbershop->update(['cancelada_en' => now()]);
        }

        // LÓGICA PARA RESTABLECER LA CONTRASEÑA
        if ($request->filled('new_password')) {
            // Buscamos al administrador de esta barbería específica
            $admin = $barbershop->users()->role('admin')->first();

            if ($admin) {
                $admin->update([
                    'password' => Hash::make($request->new_password),
                    'debe_cambiar_password' => true, // Obligamos a que la cambie al entrar
                ]);
            }
        }

        return redirect()->route('superadmin.barbershops.index')
            ->with('success', 'Barbería actualizada correctamente. ' . ($request->filled('new_password') ? 'Se restableció la contraseña del administrador.' : ''));
    }

    public function destroy(Barbershop $barbershop)
    {
        $barbershop->users()->update(['activo' => false]);
        $barbershop->delete();

        return redirect()->route('superadmin.barbershops.index')
            ->with('success', 'Barbería eliminada y usuarios desactivados.');
    }
}
