@extends('layouts.app')

@section('title', 'Servicios')

@section('content')
<div x-data="{ 
    openModal: {{ $errors->any() && !request()->isMethod('PUT') ? 'true' : 'false' }}, 
    editModal: false,
    editAction: '',
    editNombre: '',
    editPrecio: '',
    editDuracion: '',
    deleteModal: false,
    deleteAction: '',
    
    // Variables para Buscador y Ordenamiento
    search: '',
    sortBy: 'nombre', // nombre, precio, duracion
    sortAsc: true,

    // Datos de los servicios pasados desde Laravel
    services: {{ $services->items() ? json_encode($services->items()) : '[]' }},

    get filteredServices() {
        let filtered = this.services;
        
        // Filtrar por búsqueda
        if (this.search !== '') {
            let query = this.search.toLowerCase();
            filtered = filtered.filter(s => s.nombre.toLowerCase().includes(query));
        }

        // Ordenar
        filtered.sort((a, b) => {
            let valA = a[this.sortBy];
            let valB = b[this.sortBy];
            
            // Manejar nulos en duración
            if (this.sortBy === 'duracion') {
                valA = valA ? parseInt(valA) : 0;
                valB = valB ? parseInt(valB) : 0;
            }
            if (this.sortBy === 'precio') {
                valA = parseFloat(valA);
                valB = parseFloat(valB);
            }

            if (valA < valB) return this.sortAsc ? -1 : 1;
            if (valA > valB) return this.sortAsc ? 1 : -1;
            return 0;
        });

        return filtered;
    },

    setSort(column) {
        if (this.sortBy === column) {
            this.sortAsc = !this.sortAsc;
        } else {
            this.sortBy = column;
            this.sortAsc = true;
        }
    }
}">

    <!-- Header Responsivo -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Servicios</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Gestiona el menú de servicios de tu barbería.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <!-- BUSCADOR -->
            <div class="relative w-full sm:w-64">
                <i data-lucide="search" class="w-5 h-5 absolute left-3 top-2.5 text-slate-400"></i>
                <input type="text" x-model="search" placeholder="Buscar servicio..." class="w-full pl-10 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent text-sm">
            </div>

            <!-- Botón Nuevo (PC) -->
            <button x-on:click="openModal = true" class="hidden md:flex bg-barber-accent hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-xl transition-all items-center gap-2 shadow-sm shrink-0">
                <i data-lucide="plus" class="w-5 h-5"></i>
                Nuevo Servicio
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- VISTA ESCRITORIO (Tabla) -->
    <!-- ========================================== -->
    <div class="hidden md:block bg-white dark:bg-barber-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-sm border-b border-slate-200 dark:border-slate-700/50">
                        <th class="px-6 py-4 font-medium cursor-pointer hover:text-barber-accent transition-colors" @click="setSort('nombre')">
                            <div class="flex items-center gap-1">
                                Servicio
                                <i data-lucide="arrow-up-down" class="w-3 h-3" x-show="sortBy === 'nombre'"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 font-medium cursor-pointer hover:text-barber-accent transition-colors" @click="setSort('duracion')">
                            <div class="flex items-center gap-1">
                                Duración
                                <i data-lucide="arrow-up-down" class="w-3 h-3" x-show="sortBy === 'duracion'"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 font-medium cursor-pointer hover:text-barber-accent transition-colors" @click="setSort('precio')">
                            <div class="flex items-center gap-1">
                                Precio
                                <i data-lucide="arrow-up-down" class="w-3 h-3" x-show="sortBy === 'precio'"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 font-medium">Estado</th>
                        <th class="px-6 py-4 font-medium text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                    <template x-for="service in filteredServices" :key="service.id">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900 dark:text-white" x-text="service.nombre"></div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                <span x-text="service.duracion ? service.duracion + ' min' : '—'"></span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                                $<span x-text="parseFloat(service.precio).toFixed(2)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                                    Activo
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 items-center">
                                    <button x-on:click="
                                        editModal = true; 
                                        editAction = '/app/servicios/' + service.id + '/update';
                                        editNombre = service.nombre;
                                        editPrecio = service.precio;
                                        editDuracion = service.duracion;
                                    " class="p-2 text-slate-400 hover:text-barber-accent transition-colors" title="Editar">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button type="button" x-on:click="
                                        deleteModal = true;
                                        deleteAction = '/app/servicios/' + service.id + '/delete';
                                    " class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Eliminar">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <template x-if="filteredServices.length === 0">
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <i data-lucide="scissors" class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600"></i>
                                <p x-text="search !== '' ? 'No se encontraron servicios con ese criterio.' : 'No hay servicios registrados todavía.'"></p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- VISTA MÓVIL (Tarjetas estilo App) -->
    <!-- ========================================== -->
    <div class="md:hidden space-y-3 pb-24">
        <template x-for="service in filteredServices" :key="service.id">
            <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-4 shadow-sm border border-slate-200 dark:border-slate-700/50 flex flex-col gap-3 relative overflow-hidden">
                <!-- Info Principal -->
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg" x-text="service.nombre"></h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                            <span x-text="service.duracion ? service.duracion + ' minutos' : 'Duración no definida'"></span>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-extrabold text-barber-accent">$<span x-text="parseFloat(service.precio).toFixed(2)"></span></p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">Activo</span>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="flex items-center gap-2 mt-2 pt-3 border-t border-slate-100 dark:border-slate-700/50">
                    <button x-on:click="
                            editModal = true; 
                            editAction = '/app/servicios/' + service.id + '/update';
                            editNombre = service.nombre;
                            editPrecio = service.precio;
                            editDuracion = service.duracion;
                        " class="flex-1 flex justify-center items-center gap-2 py-2 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        <i data-lucide="edit" class="w-4 h-4"></i> Editar
                    </button>
                    <button type="button" x-on:click="
                            deleteModal = true;
                            deleteAction = '/app/servicios/' + service.id + '/delete';
                        " class="flex-1 flex justify-center items-center gap-2 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-xl text-sm font-medium hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar
                    </button>
                </div>
            </div>
        </template>

        <template x-if="filteredServices.length === 0">
            <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-10 text-center border border-slate-200 dark:border-slate-700/50 flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="scissors" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
                </div>
                <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Sin servicios</h4>
                <p class="text-slate-500 dark:text-slate-400 text-sm" x-text="search !== '' ? 'No se encontraron resultados.' : 'No has agregado ningún servicio todavía.'"></p>
            </div>
        </template>
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

                <form action="{{ route('app.servicios.store') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white" id="modal-title">Nuevo Servicio</h3>
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

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nombre del Servicio *</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Corte de pelo, Corte + Barba" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Precio ($) *</label>
                                <input type="number" step="0.01" name="precio" value="{{ old('precio') }}" required min="0" placeholder="10.00" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Duración (minutos)</label>
                                <input type="number" name="duracion" value="{{ old('duracion') }}" min="1" placeholder="Opcional" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
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

                <form :action="editAction" method="POST" autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Editar Servicio</h3>
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
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nombre del Servicio *</label>
                            <input type="text" name="nombre" x-model="editNombre" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Precio ($) *</label>
                                <input type="number" step="0.01" name="precio" x-model="editPrecio" required min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Duración (minutos)</label>
                                <input type="number" name="duracion" x-model="editDuracion" min="1" placeholder="Opcional" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
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
                        <h3 class="text-lg font-semibold text-center text-slate-900 dark:text-white mb-2">Eliminar Servicio</h3>
                        <p class="text-sm text-center text-slate-500 dark:text-slate-400">
                            ¿Estás seguro de que deseas eliminar este servicio? Esta acción no se puede deshacer.
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