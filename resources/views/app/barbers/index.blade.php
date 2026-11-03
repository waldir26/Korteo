@extends('layouts.app')

@section('title', 'Barberos')

@section('content')
<div x-data="{ 
    openModal: {{ $errors->any() && !request()->isMethod('POST') ? 'true' : 'false' }}, 
    editModal: false,
    editAction: '',
    editNombre: '',
    editTelefono: '',
    deleteModal: false,
    deleteAction: ''
}">

    <!-- Header Responsivo -->
    <div class="flex justify-between items-start md:items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Barberos</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Gestiona el personal que trabaja en tu barbería.</p>
        </div>
        <!-- Botón solo visible en PC -->
        <button x-on:click="openModal = true" class="hidden md:flex bg-barber-accent hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-xl transition-all items-center gap-2 shadow-sm">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Nuevo Barbero
        </button>
    </div>

    <!-- ========================================== -->
    <!-- VISTA ESCRITORIO (Tabla) -->
    <!-- ========================================== -->
    <div class="hidden md:block bg-white dark:bg-barber-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-sm border-b border-slate-200 dark:border-slate-700/50">
                        <th class="px-6 py-4 font-medium">Barbero</th>
                        <th class="px-6 py-4 font-medium">Teléfono</th>
                        <th class="px-6 py-4 font-medium">Estado</th>
                        <th class="px-6 py-4 font-medium text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                    @forelse ($barbers as $barber)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                @if($barber->foto)
                                <img src="{{ asset('storage/' . $barber->foto) }}" alt="{{ $barber->nombre }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                                @else
                                <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 font-bold border border-slate-300 dark:border-slate-600">
                                    {{ substr($barber->nombre, 0, 1) }}
                                </div>
                                @endif
                                <div>
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $barber->nombre }}</div>
                                    @if($barber->user)
                                    <div class="text-xs text-slate-500">@ {{ $barber->user->username }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                            {{ $barber->telefono ?? '—' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($barber->activo)
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">Activo</span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button x-on:click="
                                    editModal = true; 
                                    editAction = '{{ route('app.barberos.update', $barber->id) }}';
                                    editNombre = '{{ addslashes($barber->nombre) }}';
                                    editTelefono = '{{ $barber->telefono }}';
                                " class="p-2 text-slate-400 hover:text-barber-accent transition-colors" title="Editar">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button type="button" x-on:click="
                                    deleteModal = true;
                                    deleteAction = '{{ route('app.barberos.delete', $barber->id) }}';
                                " class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Eliminar">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i data-lucide="user-square" class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600"></i>
                            <p>No hay barberos registrados todavía.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($barbers->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-800/30">
            {{ $barbers->links() }}
        </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- VISTA MÓVIL (Tarjetas estilo App) -->
    <!-- ========================================== -->
    <div class="md:hidden space-y-3 pb-24">
        @forelse ($barbers as $barber)
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-4 shadow-sm border border-slate-200 dark:border-slate-700/50 flex flex-col gap-3 relative overflow-hidden">

            <!-- Info Principal -->
            <div class="flex items-center gap-4">
                @if($barber->foto)
                <img src="{{ asset('storage/' . $barber->foto) }}" alt="{{ $barber->nombre }}" class="w-14 h-14 rounded-full object-cover border-2 border-slate-100 dark:border-slate-700 shadow-sm">
                @else
                <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 font-bold text-xl border border-slate-200 dark:border-slate-700">
                    {{ substr($barber->nombre, 0, 1) }}
                </div>
                @endif

                <div class="flex-1">
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg">{{ $barber->nombre }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1.5">
                        <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                        {{ $barber->telefono ?? 'Sin teléfono' }}
                    </p>
                    @if($barber->user)
                    <p class="text-xs text-slate-500 mt-1">Usuario: <span class="font-mono">{{ $barber->user->username }}</span></p>
                    @endif
                </div>

                <div>
                    @if($barber->activo)
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">Activo</span>
                    @else
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">Inactivo</span>
                    @endif
                </div>
            </div>

            <!-- Acciones -->
            <div class="flex items-center gap-2 mt-2 pt-3 border-t border-slate-100 dark:border-slate-700/50">
                <button x-on:click="
                        editModal = true; 
                        editAction = '{{ route('app.barberos.update', $barber->id) }}';
                        editNombre = '{{ addslashes($barber->nombre) }}';
                        editTelefono = '{{ $barber->telefono }}';
                    " class="flex-1 flex justify-center items-center gap-2 py-2 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    <i data-lucide="edit" class="w-4 h-4"></i> Editar
                </button>
                <button type="button" x-on:click="
                        deleteModal = true;
                        deleteAction = '{{ route('app.barberos.delete', $barber->id) }}';
                    " class="flex-1 flex justify-center items-center gap-2 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-xl text-sm font-medium hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar
                </button>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-10 text-center border border-slate-200 dark:border-slate-700/50 flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="user-square" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
            </div>
            <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Sin barberos</h4>
            <p class="text-slate-500 dark:text-slate-400 text-sm">No has agregado a ningún barbero todavía.</p>
        </div>
        @endforelse

        @if($barbers->hasPages())
        <div class="mt-4">
            {{ $barbers->links() }}
        </div>
        @endif
    </div>

    <!-- BOTÓN FLOTANTE (FAB) SOLO PARA MÓVIL -->
    <button x-on:click="openModal = true" class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-barber-accent hover:bg-blue-600 text-white rounded-full shadow-lg flex items-center justify-center z-40 transition-transform active:scale-95">
        <i data-lucide="plus" class="w-6 h-6"></i>
    </button>

    <!-- MODAL DE CREACIÓN -->
    <div x-show="openModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="openModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-200 dark:border-slate-700/50">

                <form action="{{ route('app.barberos.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white" id="modal-title">Nuevo Barbero</h3>
                        <button type="button" x-on:click="openModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        @if ($errors->any() && !request()->isMethod('POST'))
                        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-2 text-sm text-left">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nombre completo *</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Carlos Pérez" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                        </div>

                        <!-- NUEVOS CAMPOS DE ACCESO CON TRUCO ANTI-AUTOCOMPLETADO -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Usuario de Acceso *</label>
                                <input type="text" name="username" value="{{ old('username') }}" required autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly');" placeholder="Ej. carlos.cortes" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Contraseña *</label>
                                <input type="password" name="password" required minlength="6" autocomplete="new-password" placeholder="Mínimo 6 caracteres" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Teléfono</label>
                                <input type="text" name="telefono" value="{{ old('telefono') }}" placeholder="Opcional" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Foto de perfil</label>
                                <input type="file" name="foto" accept="image/*" class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-barber-accent/10 file:text-barber-accent hover:file:bg-barber-accent/20 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" x-on:click="openModal = false" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">Cancelar</button>
                        <button type="submit" class="bg-barber-accent hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-xl transition-all shadow-sm flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL DE EDICIÓN -->
    <div x-show="editModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="editModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="editModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-200 dark:border-slate-700/50">

                <form :action="editAction" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Editar Barbero</h3>
                        <button type="button" x-on:click="editModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        @if ($errors->any() && request()->isMethod('PUT'))
                        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-2 text-sm text-left">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nombre completo *</label>
                            <input type="text" name="nombre" x-model="editNombre" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Teléfono</label>
                                <input type="text" name="telefono" x-model="editTelefono" placeholder="Opcional" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nueva Foto (Opcional)</label>
                                <input type="file" name="foto" accept="image/*" class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-barber-accent/10 file:text-barber-accent hover:file:bg-barber-accent/20 transition-all">
                            </div>
                        </div>

                        <!-- NUEVA SECCIÓN: RESTABLECER CONTRASEÑA -->
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700/50 mt-4">
                            <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                                <i data-lucide="key" class="w-4 h-4 text-red-500"></i>
                                Restablecer Contraseña de Acceso
                            </h4>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nueva Contraseña (Opcional)</label>
                                <input type="text" name="new_password" autocomplete="new-password" placeholder="Dejar en blanco para no cambiar la actual" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-red-500">
                                <p class="text-xs text-slate-500 mt-1">Si escribes aquí, la contraseña del barbero se actualizará inmediatamente.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                        <button type="button" x-on:click="editModal = false" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">Cancelar</button>
                        <button type="submit" class="bg-barber-accent hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-xl transition-all shadow-sm flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL DE ELIMINAR -->
    <div x-show="deleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="deleteModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="deleteModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700/50">

                <form :action="deleteAction" method="POST">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-500/20 rounded-full mb-4">
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-center text-slate-900 dark:text-white mb-2">Eliminar Barbero</h3>
                        <p class="text-sm text-center text-slate-500 dark:text-slate-400">
                            ¿Estás seguro de que deseas eliminar a este barbero? Esta acción no se puede deshacer.
                        </p>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex justify-center gap-3 rounded-b-2xl">
                        <button type="button" x-on:click="deleteModal = false" class="px-5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 rounded-xl transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-5 rounded-xl transition-all shadow-sm">
                            Sí, eliminar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection