@extends('layouts.app')

@section('title', 'Clientes')

@section('content')
<div x-data="{ 
    openModal: {{ $errors->any() && !request()->isMethod('POST') ? 'true' : 'false' }}, 
    editModal: false,
    editAction: '',
    editNombre: '',
    editTelefono: '',
    editEmail: '',
    editNotas: '',
    deleteModal: false,
    deleteAction: '',
    
    // Variables para Exportación
    exportModal: false,
    exportSingleAction: ''
}">

    <!-- Header Responsivo -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Clientes</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Gestiona el directorio de clientes de tu barbería.</p>
        </div>
        <!-- Botón solo visible en PC -->
        <button x-on:click="openModal = true" class="hidden md:flex bg-barber-accent hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-xl transition-all items-center justify-center gap-2 shadow-sm shrink-0">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Nuevo Cliente
        </button>
    </div>

    <!-- FILTRO DE BÚSQUEDA -->
    <div class="bg-white dark:bg-barber-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-4 mb-6">
        <form action="{{ route('app.clientes.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Buscar por nombre o teléfono</label>
                <div class="relative">
                    <i data-lucide="search" class="w-5 h-5 absolute left-3 top-3 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ej. Juan Pérez..." class="w-full pl-10 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2.5 focus:ring-2 focus:ring-barber-accent text-base">
                </div>
            </div>

            <div class="flex flex-wrap gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-medium py-2.5 px-6 rounded-xl transition-all flex items-center justify-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i> Buscar
                </button>

                <!-- BOTÓN EXPORTAR GENERAL -->
                <button type="button" x-on:click="exportSingleAction = ''; exportModal = true" class="flex-1 md:flex-none bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-6 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm">
                    <i data-lucide="download" class="w-4 h-4"></i> Exportar
                </button>

                @if(request('search'))
                <a href="{{ route('app.clientes.index') }}" class="flex-1 md:flex-none text-sm font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white py-2.5 px-4 text-center bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center">Limpiar</a>
                @endif
            </div>
        </form>
    </div>

    <!-- ========================================== -->
    <!-- VISTA ESCRITORIO (Tabla) -->
    <!-- ========================================== -->
    <div class="hidden md:block bg-white dark:bg-barber-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-sm border-b border-slate-200 dark:border-slate-700/50">
                        <th class="px-6 py-4 font-medium">Cliente</th>
                        <th class="px-6 py-4 font-medium">Contacto</th>
                        <th class="px-6 py-4 font-medium">Notas</th>
                        <th class="px-6 py-4 font-medium text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                    @forelse ($clients as $client)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 font-bold border border-slate-300 dark:border-slate-600">
                                    {{ substr($client->nombre, 0, 1) }}
                                </div>
                                <div class="font-medium text-slate-900 dark:text-white">{{ $client->nombre }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($client->telefono)
                            <div class="text-sm text-slate-900 dark:text-white">{{ $client->telefono }}</div>
                            @endif
                            @if($client->email)
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $client->email }}</div>
                            @endif
                            @if(!$client->telefono && !$client->email)
                            <span class="text-sm text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 max-w-xs truncate">
                            {{ $client->notas ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2 items-center">
                                <!-- BOTÓN EXPORTAR FILA -->
                                <button type="button" x-on:click="
                                    exportSingleAction = '{{ $client->id }}';
                                    exportModal = true;
                                " class="p-2 text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 rounded-lg transition-colors" title="Exportar este cliente">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                </button>

                                <button x-on:click="
                                    editModal = true; 
                                    editAction = '{{ route('app.clientes.update', $client->id) }}';
                                    editNombre = '{{ addslashes($client->nombre) }}';
                                    editTelefono = '{{ $client->telefono }}';
                                    editEmail = '{{ $client->email }}';
                                    editNotas = '{{ addslashes($client->notas) }}';
                                " class="p-2 text-slate-400 hover:text-barber-accent transition-colors" title="Editar">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button type="button" x-on:click="
                                    deleteModal = true;
                                    deleteAction = '{{ route('app.clientes.delete', $client->id) }}';
                                " class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Eliminar">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600"></i>
                            <p>No hay clientes registrados {{ request('search') ? 'con ese criterio de búsqueda' : 'todavía' }}.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-800/30">
            {{ $clients->links() }}
        </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- VISTA MÓVIL (Tarjetas estilo App) -->
    <!-- ========================================== -->
    <div class="md:hidden space-y-3 pb-24">
        @forelse ($clients as $client)
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-4 shadow-sm border border-slate-200 dark:border-slate-700/50 flex flex-col gap-3 relative overflow-hidden">

            <!-- Info Principal -->
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 font-bold text-xl border border-slate-200 dark:border-slate-700 shrink-0">
                    {{ substr($client->nombre, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg truncate">{{ $client->nombre }}</h3>
                    <div class="flex flex-col gap-0.5 mt-1">
                        @if($client->telefono)
                        <p class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                            <i data-lucide="phone" class="w-3.5 h-3.5"></i> {{ $client->telefono }}
                        </p>
                        @endif
                        @if($client->email)
                        <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-1.5 truncate">
                            <i data-lucide="mail" class="w-3.5 h-3.5"></i> {{ $client->email }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notas -->
            @if($client->notas)
            <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Notas:</p>
                <p class="text-sm text-slate-700 dark:text-slate-300 line-clamp-2">{{ $client->notas }}</p>
            </div>
            @endif

            <!-- Acciones -->
            <div class="flex items-center gap-2 mt-1 pt-3 border-t border-slate-100 dark:border-slate-700/50 flex-wrap">

                <!-- Botón Exportar Móvil -->
                <button type="button" x-on:click="
                    exportSingleAction = '{{ $client->id }}';
                    exportModal = true;
                " class="flex items-center justify-center gap-2 px-3 py-2 bg-green-50 dark:bg-green-500/10 text-green-600 rounded-xl text-sm font-bold border border-green-100 dark:border-green-500/20 hover:bg-green-100 transition-colors" title="Exportar">
                    <i data-lucide="download" class="w-4 h-4"></i> Exportar
                </button>

                <button x-on:click="
                        editModal = true; 
                        editAction = '{{ route('app.clientes.update', $client->id) }}';
                        editNombre = '{{ addslashes($client->nombre) }}';
                        editTelefono = '{{ $client->telefono }}';
                        editEmail = '{{ $client->email }}';
                        editNotas = '{{ addslashes($client->notas) }}';
                    " class="flex-1 flex justify-center items-center gap-2 py-2 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    <i data-lucide="edit" class="w-4 h-4"></i> Editar
                </button>
                <button type="button" x-on:click="
                        deleteModal = true;
                        deleteAction = '{{ route('app.clientes.delete', $client->id) }}';
                    " class="flex-1 flex justify-center items-center gap-2 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-xl text-sm font-medium hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar
                </button>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-10 text-center border border-slate-200 dark:border-slate-700/50 flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="users" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
            </div>
            <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Sin clientes</h4>
            <p class="text-slate-500 dark:text-slate-400 text-sm">No se encontraron clientes.</p>
        </div>
        @endforelse

        @if($clients->hasPages())
        <div class="mt-4">
            {{ $clients->links() }}
        </div>
        @endif
    </div>

    <!-- BOTÓN FLOTANTE (FAB) SOLO PARA MÓVIL -->
    <button x-on:click="openModal = true" class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-barber-accent hover:bg-blue-600 text-white rounded-full shadow-lg flex items-center justify-center z-40 transition-transform active:scale-95">
        <i data-lucide="plus" class="w-6 h-6"></i>
    </button>

    <!-- MODAL DE EXPORTAR -->
    <div x-show="exportModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="exportModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" x-on:click="exportModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="exportModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700/50">

                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white" x-text="exportSingleAction !== '' ? 'Exportar Cliente' : 'Exportar Reporte General'"></h3>
                    <button type="button" x-on:click="exportModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="px-6 py-6 space-y-3">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 text-center">
                        <span x-show="exportSingleAction === ''">Selecciona el formato para descargar el reporte con los filtros actuales.</span>
                        <span x-show="exportSingleAction !== ''">Selecciona el formato para descargar la ficha de este cliente.</span>
                    </p>

                    <!-- OPCIÓN PDF -->
                    <template x-if="exportSingleAction === ''">
                        <a href="{{ route('app.clientes.pdf-general', request()->all()) }}" class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-200 dark:border-red-800">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                            <div>
                                <p class="font-bold">Exportar a PDF</p>
                                <p class="text-xs">Ideal para imprimir o enviar por correo.</p>
                            </div>
                        </a>
                    </template>
                    <template x-if="exportSingleAction !== ''">
                        <a :href="'/app/clientes/' + exportSingleAction + '/pdf'" class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-200 dark:border-red-800">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                            <div>
                                <p class="font-bold">Exportar a PDF</p>
                                <p class="text-xs">Ficha del cliente individual.</p>
                            </div>
                        </a>
                    </template>

                    <!-- OPCIÓN EXCEL -->
                    <template x-if="exportSingleAction === ''">
                        <a href="{{ route('app.clientes.export-general', request()->all()) }}" class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-xl hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors border border-green-200 dark:border-green-800">
                            <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
                            <div>
                                <p class="font-bold">Exportar a Excel</p>
                                <p class="text-xs">Ideal para análisis o contabilidad.</p>
                            </div>
                        </a>
                    </template>
                    <template x-if="exportSingleAction !== ''">
                        <a :href="'/app/clientes/' + exportSingleAction + '/excel'" class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-xl hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors border border-green-200 dark:border-green-800">
                            <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
                            <div>
                                <p class="font-bold">Exportar a Excel</p>
                                <p class="text-xs">Datos del cliente individual.</p>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
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
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-200 dark:border-slate-700/50">

                <form action="{{ route('app.clientes.store') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white" id="modal-title">Nuevo Cliente</h3>
                        <button type="button" x-on:click="openModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        @if ($errors->any())
                        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-2 text-sm text-left">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nombre completo *</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Juan Pérez" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Teléfono</label>
                                <input type="text" name="telefono" value="{{ old('telefono') }}" placeholder="Opcional" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Correo Electrónico</label>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="Opcional" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Notas o Preferencias</label>
                            <textarea name="notas" rows="3" placeholder="Ej. Prefiere máquina 1 a los lados..." class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base"></textarea>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3 rounded-b-2xl">
                        <button type="button" x-on:click="openModal = false" class="w-full sm:w-auto px-4 py-3 sm:py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-200 sm:bg-transparent dark:bg-slate-700 sm:dark:bg-transparent rounded-xl sm:rounded-none transition-colors">Cancelar</button>
                        <button type="submit" class="w-full sm:w-auto bg-barber-accent hover:bg-blue-600 text-white font-medium py-3 sm:py-2 px-4 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
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
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Editar Cliente</h3>
                        <button type="button" x-on:click="editModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        @if ($errors->any())
                        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-2 text-sm text-left">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nombre completo *</label>
                            <input type="text" name="nombre" x-model="editNombre" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Teléfono</label>
                                <input type="text" name="telefono" x-model="editTelefono" placeholder="Opcional" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Correo Electrónico</label>
                                <input type="email" name="email" x-model="editEmail" placeholder="Opcional" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Notas o Preferencias</label>
                            <textarea name="notas" x-model="editNotas" rows="3" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base"></textarea>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3 rounded-b-2xl">
                        <button type="button" x-on:click="editModal = false" class="w-full sm:w-auto px-4 py-3 sm:py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-200 sm:bg-transparent dark:bg-slate-700 sm:dark:bg-transparent rounded-xl sm:rounded-none transition-colors">Cancelar</button>
                        <button type="submit" class="w-full sm:w-auto bg-barber-accent hover:bg-blue-600 text-white font-medium py-3 sm:py-2 px-4 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
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
                        <h3 class="text-lg font-semibold text-center text-slate-900 dark:text-white mb-2">Eliminar Cliente</h3>
                        <p class="text-sm text-center text-slate-500 dark:text-slate-400">
                            ¿Estás seguro de que deseas eliminar a este cliente? Se perderá su historial de notas.
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