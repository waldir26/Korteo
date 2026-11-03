@extends('layouts.superadmin')

@section('title', 'Barberías')

@section('content')
<!-- Envolvemos todo en x-data para controlar los modales, las variables y el buscador -->
<div x-data="{ 
    openModal: {{ $errors->any() && !request()->isMethod('PUT') ? 'true' : 'false' }}, 
    openMap: null,
    editModal: false,
    editAction: '',
    editNombre: '',
    editTelefono: '',
    editDireccion: '',
    editEstado: '',
    deleteModal: false,
    deleteAction: '',
    
    // Variables para el buscador
    search: '',
    barbershops: {{ $barbershops->items() ? json_encode($barbershops->items()) : '[]' }},
    get filteredBarbershops() {
        if (this.search === '') return this.barbershops;
        let query = this.search.toLowerCase();
        return this.barbershops.filter(b => 
            b.nombre.toLowerCase().includes(query) || 
            (b.users && b.users.length > 0 && b.users[0].name.toLowerCase().includes(query)) ||
            (b.users && b.users.length > 0 && b.users[0].username.toLowerCase().includes(query))
        );
    }
}">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Barberías</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Gestiona las barberías registradas en el sistema.</p>
        </div>

        <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-3">
            <!-- BUSCADOR EN TIEMPO REAL -->
            <div class="relative w-full sm:w-64">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-3 text-slate-400"></i>
                <input type="text" x-model="search" placeholder="Buscar barbería o admin..." class="w-full pl-9 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent text-sm">
            </div>

            <button x-on:click="openModal = true" class="bg-barber-accent hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm shrink-0">
                <i data-lucide="plus" class="w-5 h-5"></i>
                Nueva Barbería
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-barber-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-sm border-b border-slate-200 dark:border-slate-700/50">
                        <th class="px-6 py-4 font-medium">Barbería</th>
                        <th class="px-6 py-4 font-medium">Ubicación</th>
                        <th class="px-6 py-4 font-medium">Estado</th>
                        <th class="px-6 py-4 font-medium">Administrador</th>
                        <th class="px-6 py-4 font-medium">Registro</th>
                        <th class="px-6 py-4 font-medium text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                    <!-- Iteramos sobre la lista filtrada de Alpine -->
                    <template x-for="barbershop in filteredBarbershops" :key="barbershop.id">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900 dark:text-white" x-text="barbershop.nombre"></div>
                                <div class="text-xs text-slate-500 dark:text-slate-400" x-text="barbershop.slug"></div>
                            </td>
                            <td class="px-6 py-4">
                                <template x-if="barbershop.direccion">
                                    <button type="button"
                                        :data-mapa="btoa(barbershop.direccion)"
                                        x-on:click="openMap = atob($event.currentTarget.dataset.mapa)"
                                        class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 hover:text-barber-accent transition-colors"
                                        title="Ver mapa">
                                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                                        Ver mapa
                                    </button>
                                </template>
                                <template x-if="!barbershop.direccion">
                                    <span class="text-sm text-slate-400">No asignada</span>
                                </template>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium capitalize"
                                    :class="{
                                        'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400': barbershop.estado === 'activa',
                                        'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400': barbershop.estado === 'en_prueba',
                                        'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400': barbershop.estado === 'suspendida',
                                        'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400': barbershop.estado === 'cancelada'
                                    }" x-text="barbershop.estado.replace('_', ' ')">
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <template x-if="barbershop.users && barbershop.users.length > 0">
                                    <div>
                                        <div class="text-sm font-medium text-slate-900 dark:text-white" x-text="barbershop.users[0].name"></div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400" x-text="'@' + barbershop.users[0].username"></div>
                                    </div>
                                </template>
                                <template x-if="!barbershop.users || barbershop.users.length === 0">
                                    <span class="text-sm text-slate-400">Sin admin</span>
                                </template>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300 text-sm">
                                <span x-text="new Date(barbershop.created_at).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' })"></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <!-- Botón Editar -->
                                    <button x-on:click="
                                        editModal = true; 
                                        editAction = '/superadmin/barbershops/' + barbershop.id;
                                        editNombre = barbershop.nombre;
                                        editTelefono = barbershop.telefono ? barbershop.telefono.replace('+503 ', '') : '';
                                        editDireccion = barbershop.direccion || '';
                                        editEstado = barbershop.estado;
                                    " class="p-2 text-slate-400 hover:text-barber-accent transition-colors" title="Editar">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Botón Eliminar -->
                                    <button type="button" x-on:click="
                                        deleteModal = true;
                                        deleteAction = '/superadmin/barbershops/' + barbershop.id;
                                    " class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Eliminar">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Mostrar mensaje si no hay resultados -->
                    <template x-if="filteredBarbershops.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <i data-lucide="store" class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600"></i>
                                <p x-text="search !== '' ? 'No se encontraron barberías con ese criterio.' : 'No hay barberías registradas todavía.'"></p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
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
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200 dark:border-slate-700/50">

                <form action="{{ route('superadmin.barbershops.store') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white" id="modal-title">Nueva Barbería</h3>
                        <button type="button" x-on:click="openModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        @if ($errors->any() && !request()->isMethod('PUT'))
                        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-2 text-sm text-left">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nombre de la Barbería *</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}" required minlength="3" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Teléfono *</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 sm:text-sm font-medium">
                                        +503
                                    </span>
                                    <input type="text" name="telefono" value="{{ old('telefono') }}" required maxlength="8" placeholder="74795685" class="w-full rounded-r-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Estado Inicial *</label>
                                <select name="estado" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                                    <option value="en_prueba" {{ old('estado') == 'en_prueba' ? 'selected' : '' }}>En Prueba</option>
                                    <option value="activa" {{ old('estado') == 'activa' ? 'selected' : '' }}>Activa</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Ubicación (Google Maps) *</label>
                                <input type="text" name="direccion" value="{{ old('direccion') }}" required placeholder="Pega el iframe o enlace de Google Maps" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700/50 mt-4">
                            <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                                <i data-lucide="key" class="w-4 h-4 text-barber-accent"></i>
                                Credenciales de Acceso (Administrador de la Barbería)
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nombre completo del Administrador *</label>
                                    <input type="text" name="admin_name" value="{{ old('admin_name') }}" required placeholder="Ej. Carlos Lizama" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nombre de usuario *</label>
                                    <input type="text" name="admin_username" value="{{ old('admin_username') }}" required autocomplete="off" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                                </div>
                                <div x-data="{ showPassword: false }">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Contraseña *</label>
                                    <div class="relative">
                                        <input :type="showPassword ? 'text' : 'password'" name="admin_password" required minlength="8" autocomplete="new-password" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 pr-10 focus:ring-2 focus:ring-barber-accent">
                                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                                            <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="showPassword" style="display: none;" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
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

    <!-- MODAL DE MAPA -->
    <div x-show="openMap !== null" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="openMap !== null" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="openMap !== null" x-transition class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-slate-200 dark:border-slate-700/50">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Ubicación de la Barbería</h3>
                    <button type="button" x-on:click="openMap = null" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="p-6">
                    <div class="w-full h-96 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700/50 [&>iframe]:w-full [&>iframe]:h-full" x-html="openMap"></div>
                </div>
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
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200 dark:border-slate-700/50">

                <form :action="editAction" method="POST" autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Editar Barbería</h3>
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nombre de la Barbería *</label>
                                <input type="text" name="nombre" x-model="editNombre" required minlength="3" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Teléfono *</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 sm:text-sm font-medium">
                                        +503
                                    </span>
                                    <input type="text" name="telefono" x-model="editTelefono" required maxlength="8" class="w-full rounded-r-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Estado *</label>
                                <select name="estado" x-model="editEstado" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                                    <option value="en_prueba">En Prueba</option>
                                    <option value="activa">Activa</option>
                                    <option value="suspendida">Suspendida</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Ubicación (Google Maps) *</label>
                                <input type="text" name="direccion" x-model="editDireccion" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                        </div>

                        <!-- NUEVA SECCIÓN DE RESTABLECER CONTRASEÑA -->
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700/50 mt-4">
                            <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                                <i data-lucide="key" class="w-4 h-4 text-red-500"></i>
                                Restablecer Contraseña del Dueño
                            </h4>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nueva Contraseña (Opcional)</label>
                                <input type="text" name="new_password" placeholder="Dejar en blanco para no cambiar la actual" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-red-500">
                                <p class="text-xs text-slate-500 mt-1">Si escribes aquí, la contraseña del administrador se actualizará inmediatamente.</p>
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
                    @method('DELETE')

                    <div class="p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-500/20 rounded-full mb-4">
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-center text-slate-900 dark:text-white mb-2">Eliminar Barbería</h3>
                        <p class="text-sm text-center text-slate-500 dark:text-slate-400">
                            ¿Estás seguro de que deseas eliminar esta barbería? Los usuarios asociados serán desactivados y no podrán iniciar sesión.
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