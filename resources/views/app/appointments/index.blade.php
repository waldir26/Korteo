@extends('layouts.app')

@section('title', 'Citas')

@section('content')
<div x-data="{ 
    exportModal: false,
    exportSingleAction: '',
    editModal: false,
    editAction: '',
    editBarber: '',
    editService: '',
    editFecha: '',
    editHora: '',
    editNotas: '',
    
    payModal: false,
    payAction: '',
    payClient: '',
    payService: '',
    payAmount: 0,

    openBarberEdit: false,
    openServiceEdit: false,
    editBarberName: '',
    editServiceName: ''
}" id="appointments-page">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Citas</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Historial y control de citas.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <a href="{{ route('app.appointments.create') }}" class="w-full sm:w-auto bg-barber-accent hover:bg-blue-600 text-white font-medium py-2.5 px-4 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm shrink-0">
                <i data-lucide="plus" class="w-5 h-5"></i>
                Agendar Cita
            </a>
        </div>
    </div>

    <!-- FILTROS DE BÚSQUEDA -->
    <div class="bg-white dark:bg-barber-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-4 mb-6">
        <form action="{{ route('app.appointments.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Buscar por cliente o barbero</label>
                <div class="relative">
                    <i data-lucide="search" class="w-5 h-5 absolute left-3 top-3 text-slate-400"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ej. Juan Pérez o Carlos..." class="w-full pl-10 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2.5 focus:ring-2 focus:ring-barber-accent text-base">
                </div>
            </div>
            <div class="w-full md:w-auto">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Fecha</label>
                <input type="text" name="fecha" id="fecha-filtro" value="{{ $fecha }}" placeholder="Seleccione fecha" class="w-full md:w-auto rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2.5 focus:ring-2 focus:ring-barber-accent text-base">
            </div>

            <div class="flex flex-wrap gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-medium py-2.5 px-6 rounded-xl transition-all flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filtrar
                </button>

                <!-- BOTÓN DE EXPORTAR GENERAL -->
                <button type="button" x-on:click="exportSingleAction = ''; exportModal = true" class="flex-1 md:flex-none bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-6 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm">
                    <i data-lucide="download" class="w-4 h-4"></i> Exportar
                </button>

                @if($search || $fecha != date('Y-m-d'))
                <a href="{{ route('app.appointments.index') }}" class="flex-1 md:flex-none text-sm font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white py-2.5 px-4 text-center bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center">Limpiar</a>
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
                        <th class="px-6 py-4 font-medium">Hora</th>
                        <th class="px-6 py-4 font-medium">Cliente</th>
                        <th class="px-6 py-4 font-medium">Servicio</th>
                        <th class="px-6 py-4 font-medium">Barbero</th>
                        <th class="px-6 py-4 font-medium">Estado</th>
                        <th class="px-6 py-4 font-medium text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                    @forelse ($appointments as $appointment)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($appointment->fecha_hora)->format('h:i A') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $appointment->client?->nombre ?? 'Cliente eliminado' }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $appointment->client?->telefono ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-slate-900 dark:text-white">{{ $appointment->service?->nombre ?? 'Servicio eliminado' }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">${{ number_format($appointment->service?->precio ?? 0, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                            {{ $appointment->barber?->nombre ?? 'Barbero eliminado' }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                            $color = match($appointment->estado) {
                            'pendiente' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                            'completada' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                            'cancelada' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                            };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium capitalize {{ $color }}">
                                {{ $appointment->estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2 items-center">
                                <!-- BOTÓN EXPORTAR FILA (Abre el modal) -->
                                <button type="button" x-on:click="
                                    exportSingleAction = '{{ $appointment->id }}';
                                    exportModal = true;
                                " class="p-2 text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 rounded-lg transition-colors" title="Exportar esta cita">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                </button>

                                @if($appointment->estado === 'pendiente')
                                <button type="button" x-on:click="
                                    payModal = true;
                                    payAction = '{{ route('app.appointments.pay', $appointment) }}';
                                    payClient = '{{ addslashes($appointment->client?->nombre ?? 'Cliente eliminado') }}';
                                    payService = '{{ addslashes($appointment->service?->nombre ?? 'Servicio eliminado') }}';
                                    payAmount = '{{ $appointment->service?->precio ?? 0 }}';
                                " class="px-3 py-1.5 bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-500/20 dark:text-green-400 dark:hover:bg-green-500/30 rounded-lg text-xs font-bold transition-colors flex items-center gap-1 shadow-sm" title="Cobrar y Completar">
                                    <i data-lucide="dollar-sign" class="w-3 h-3"></i> Cobrar
                                </button>
                                @endif

                                @if($appointment->estado === 'pendiente')
                                <form action="{{ route('app.appointments.update-status', $appointment) }}" method="POST" onsubmit="return confirm('¿Estás seguro de cancelar esta cita?');">
                                    @csrf
                                    <input type="hidden" name="estado" value="cancelada">
                                    <button type="submit" class="p-2 text-slate-400 hover:text-amber-500 transition-colors" title="Cancelar Cita">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endif

                                <button x-on:click="
                                    editModal = true; 
                                    editAction = '{{ route('app.appointments.update', $appointment) }}';
                                    editBarber = '{{ $appointment->barber_id }}';
                                    editBarberName = '{{ addslashes($appointment->barber?->nombre) }}';
                                    editService = '{{ $appointment->service_id }}';
                                    editServiceName = '{{ addslashes($appointment->service?->nombre) }}';
                                    editFecha = '{{ \Carbon\Carbon::parse($appointment->fecha_hora)->format('Y-m-d') }}';
                                    editHora = '{{ \Carbon\Carbon::parse($appointment->fecha_hora)->format('H:i') }}';
                                    editNotas = '{{ addslashes($appointment->notas) }}';
                                    
                                    setTimeout(() => {
                                        if(document.getElementById('fecha-editar')._flatpickr) {
                                            document.getElementById('fecha-editar')._flatpickr.setDate(editFecha);
                                        }
                                        if(document.getElementById('hora-editar')._flatpickr) {
                                            document.getElementById('hora-editar')._flatpickr.setDate(editHora);
                                        }
                                    }, 100);
                                " class="p-2 text-slate-400 hover:text-barber-accent transition-colors" title="Editar Cita">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>

                                <form id="delete-form-{{ $appointment->id }}" action="{{ route('app.appointments.delete', $appointment->id) }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                                <button type="button" onclick="if(confirm('¿Eliminar cita?')) document.getElementById('delete-form-{{ $appointment->id }}').submit();" class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Eliminar">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i data-lucide="calendar" class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600"></i>
                            <p>No hay citas para mostrar con estos filtros.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- VISTA MÓVIL (Tarjetas) -->
    <!-- ========================================== -->
    <div class="md:hidden space-y-3 pb-24">
        @forelse ($appointments as $appointment)
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-4 shadow-sm border border-slate-200 dark:border-slate-700/50 flex flex-col gap-3 relative overflow-hidden">

            <!-- Header: Hora y Estado -->
            <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-700/50 pb-3">
                <div class="flex items-center gap-2 text-barber-accent font-bold text-lg">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                    {{ \Carbon\Carbon::parse($appointment->fecha_hora)->format('h:i A') }}
                </div>
                @php
                $color = match($appointment->estado) {
                'pendiente' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                'completada' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                'cancelada' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                };
                @endphp
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $color }}">
                    {{ $appointment->estado }}
                </span>
            </div>

            <!-- Info: Cliente y Servicio -->
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white text-lg">{{ $appointment->client?->nombre ?? 'Cliente eliminado' }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5">
                    <i data-lucide="scissors" class="w-4 h-4"></i>
                    {{ $appointment->service?->nombre ?? 'Servicio eliminado' }}
                    <span class="font-bold text-green-600 dark:text-green-400">(${{ number_format($appointment->service?->precio ?? 0, 2) }})</span>
                </p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    Barbero: {{ $appointment->barber?->nombre ?? 'Barbero eliminado' }}
                </p>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center gap-2 mt-2 pt-3 border-t border-slate-100 dark:border-slate-700/50 flex-wrap">

                <!-- Botón Exportar Móvil (Abre el modal) -->
                <button type="button" x-on:click="
                    exportSingleAction = '{{ $appointment->id }}';
                    exportModal = true;
                " class="flex items-center justify-center gap-2 px-3 py-2.5 bg-green-50 dark:bg-green-500/10 text-green-600 rounded-xl text-sm font-bold border border-green-100 dark:border-green-500/20 hover:bg-green-100 transition-colors" title="Exportar">
                    <i data-lucide="download" class="w-4 h-4"></i> Exportar
                </button>

                @if($appointment->estado === 'pendiente')
                <button type="button" x-on:click="
                            payModal = true;
                            payAction = '{{ route('app.appointments.pay', $appointment) }}';
                            payClient = '{{ addslashes($appointment->client?->nombre ?? 'Cliente eliminado') }}';
                            payService = '{{ addslashes($appointment->service?->nombre ?? 'Servicio eliminado') }}';
                            payAmount = '{{ $appointment->service?->precio ?? 0 }}';
                        " class="flex-1 flex justify-center items-center gap-2 py-2.5 bg-green-50 text-green-700 rounded-xl text-sm font-bold border border-green-200 hover:bg-green-100 transition-colors">
                    <i data-lucide="dollar-sign" class="w-4 h-4"></i> Cobrar
                </button>

                <form action="{{ route('app.appointments.update-status', $appointment) }}" method="POST" onsubmit="return confirm('¿Estás seguro de cancelar esta cita?');" class="flex-1">
                    @csrf
                    <input type="hidden" name="estado" value="cancelada">
                    <button type="submit" class="w-full flex justify-center items-center gap-2 py-2.5 bg-amber-50 text-amber-700 rounded-xl text-sm font-bold border border-amber-200 hover:bg-amber-100 transition-colors">
                        <i data-lucide="x-circle" class="w-4 h-4"></i> Cancelar
                    </button>
                </form>
                @endif

                <button x-on:click="
                        editModal = true; 
                        editAction = '{{ route('app.appointments.update', $appointment) }}';
                        editBarber = '{{ $appointment->barber_id }}';
                        editBarberName = '{{ addslashes($appointment->barber?->nombre) }}';
                        editService = '{{ $appointment->service_id }}';
                        editServiceName = '{{ addslashes($appointment->service?->nombre) }}';
                        editFecha = '{{ \Carbon\Carbon::parse($appointment->fecha_hora)->format('Y-m-d') }}';
                        editHora = '{{ \Carbon\Carbon::parse($appointment->fecha_hora)->format('H:i') }}';
                        editNotas = '{{ addslashes($appointment->notas) }}';
                        
                        setTimeout(() => {
                            if(document.getElementById('fecha-editar')._flatpickr) {
                                document.getElementById('fecha-editar')._flatpickr.setDate(editFecha);
                            }
                            if(document.getElementById('hora-editar')._flatpickr) {
                                document.getElementById('hora-editar')._flatpickr.setDate(editHora);
                            }
                        }, 100);
                    " class="p-2.5 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 transition-colors">
                    <i data-lucide="edit" class="w-5 h-5"></i>
                </button>

                <form id="delete-form-mob-{{ $appointment->id }}" action="{{ route('app.appointments.delete', $appointment->id) }}" method="POST" class="hidden">
                    @csrf
                </form>
                <button type="button" onclick="if(confirm('¿Eliminar cita?')) document.getElementById('delete-form-mob-{{ $appointment->id }}').submit();" class="p-2.5 bg-red-50 dark:bg-red-500/10 text-red-600 rounded-xl border border-red-100 dark:border-red-500/20 hover:bg-red-100 transition-colors">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-10 text-center border border-slate-200 dark:border-slate-700/50 flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="calendar" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
            </div>
            <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Sin citas</h4>
            <p class="text-slate-500 dark:text-slate-400 text-sm">No hay citas para mostrar con estos filtros.</p>
        </div>
        @endforelse
    </div>

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
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white" x-text="exportSingleAction !== '' ? 'Exportar Cita' : 'Exportar Reporte General'"></h3>
                    <button type="button" x-on:click="exportModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="px-6 py-6 space-y-3">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 text-center">
                        <span x-show="exportSingleAction === ''">Selecciona el formato para descargar el reporte con los filtros actuales.</span>
                        <span x-show="exportSingleAction !== ''">Selecciona el formato para descargar los detalles de esta cita.</span>
                    </p>

                    <!-- OPCIÓN PDF -->
                    <template x-if="exportSingleAction === ''">
                        <a href="{{ route('app.appointments.pdf-general', request()->all()) }}" class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-200 dark:border-red-800">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                            <div>
                                <p class="font-bold">Exportar a PDF</p>
                                <p class="text-xs">Ideal para imprimir o enviar por correo.</p>
                            </div>
                        </a>
                    </template>
                    <template x-if="exportSingleAction !== ''">
                        <a :href="'/app/citas/' + exportSingleAction + '/pdf'" class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-200 dark:border-red-800">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                            <div>
                                <p class="font-bold">Exportar a PDF</p>
                                <p class="text-xs">Recibo de la cita individual.</p>
                            </div>
                        </a>
                    </template>

                    <!-- OPCIÓN EXCEL -->
                    <template x-if="exportSingleAction === ''">
                        <a href="{{ route('app.appointments.export-general', request()->all()) }}" class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-xl hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors border border-green-200 dark:border-green-800">
                            <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
                            <div>
                                <p class="font-bold">Exportar a Excel</p>
                                <p class="text-xs">Ideal para análisis o contabilidad.</p>
                            </div>
                        </a>
                    </template>
                    <template x-if="exportSingleAction !== ''">
                        <a :href="'/app/citas/' + exportSingleAction + '/excel'" class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-xl hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors border border-green-200 dark:border-green-800">
                            <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
                            <div>
                                <p class="font-bold">Exportar a Excel</p>
                                <p class="text-xs">Datos de la cita individual.</p>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE COBRO -->
    <div x-show="payModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="payModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" x-on:click="payModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="payModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700/50">

                <form :action="payAction" method="POST">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto bg-green-100 dark:bg-green-500/20 rounded-full mb-4">
                            <i data-lucide="dollar-sign" class="w-8 h-8 text-green-600 dark:text-green-400"></i>
                        </div>

                        <h3 class="text-xl font-bold text-center text-slate-900 dark:text-white mb-2">Confirmar Cobro</h3>
                        <p class="text-sm text-center text-slate-500 dark:text-slate-400 mb-6">
                            ¿Estás seguro de registrar el pago por este servicio?
                        </p>

                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 space-y-3 mb-6 border border-slate-100 dark:border-slate-700/50">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500 dark:text-slate-400">Cliente:</span>
                                <span class="text-sm font-medium text-slate-900 dark:text-white" x-text="payClient"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500 dark:text-slate-400">Servicio:</span>
                                <span class="text-sm font-medium text-slate-900 dark:text-white" x-text="payService"></span>
                            </div>
                            <div class="pt-3 border-t border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                                <span class="text-base font-medium text-slate-600 dark:text-slate-300">Total a cobrar:</span>
                                <span class="text-2xl font-bold text-green-600 dark:text-green-400">$<span x-text="payAmount"></span></span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex justify-center gap-3 rounded-b-2xl">
                        <button type="button" x-on:click="payModal = false" class="px-5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 rounded-xl transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-xl transition-all shadow-sm flex items-center gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Confirmar Pago
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL DE EDICIÓN -->
    <div x-show="editModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="editModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" x-on:click="editModal = false"></div>

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
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Editar Cita</h3>
                        <button type="button" x-on:click="editModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Dropdown Barbero -->
                            <div class="relative" @click.away="openBarberEdit = false">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Barbero *</label>
                                <input type="hidden" name="barber_id" :value="editBarber" required>

                                <button type="button" @click="openBarberEdit = !openBarberEdit" class="w-full flex justify-between items-center rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base transition-colors">
                                    <span x-text="editBarberName || 'Seleccione barbero'" :class="!editBarberName ? 'text-slate-500' : ''"></span>
                                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform" :class="openBarberEdit ? 'rotate-180' : ''"></i>
                                </button>

                                <div x-show="openBarberEdit" x-transition.opacity x-cloak class="absolute z-30 w-full mt-2 bg-white dark:bg-barber-card-dark border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                                    @foreach($barbers as $barber)
                                    <button type="button" @click="editBarber = '{{ $barber->id }}'; editBarberName = '{{ addslashes($barber->nombre) }}'; openBarberEdit = false" class="w-full text-left px-4 py-3 text-base text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                                        {{ $barber->nombre }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Dropdown Servicio -->
                            <div class="relative" @click.away="openServiceEdit = false">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Servicio *</label>
                                <input type="hidden" name="service_id" :value="editService" required>

                                <button type="button" @click="openServiceEdit = !openServiceEdit" class="w-full flex justify-between items-center rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base transition-colors">
                                    <span x-text="editServiceName || 'Seleccione servicio'" :class="!editServiceName ? 'text-slate-500' : ''"></span>
                                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform" :class="openServiceEdit ? 'rotate-180' : ''"></i>
                                </button>

                                <div x-show="openServiceEdit" x-transition.opacity x-cloak class="absolute z-30 w-full mt-2 bg-white dark:bg-barber-card-dark border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                                    @foreach($services as $service)
                                    <button type="button" @click="editService = '{{ $service->id }}'; editServiceName = '{{ addslashes($service->nombre) }}'; openServiceEdit = false" class="w-full text-left px-4 py-3 text-base text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors border-b border-slate-100 dark:border-slate-700/50 last:border-0 flex justify-between items-center">
                                        <span>{{ $service->nombre }}</span>
                                        <span class="text-sm font-bold text-green-600 dark:text-green-400">${{ $service->precio }}</span>
                                    </button>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Fecha *</label>
                                <input type="text" name="fecha" id="fecha-editar" x-model="editFecha" required placeholder="Seleccione fecha" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Hora *</label>
                                <input type="text" name="hora" id="hora-editar" x-model="editHora" required placeholder="--:--" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Notas</label>
                                <input type="text" name="notas" x-model="editNotas" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base">
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3 rounded-b-2xl">
                        <button type="button" x-on:click="editModal = false" class="w-full sm:w-auto px-4 py-3 sm:py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-200 sm:bg-transparent dark:bg-slate-700 sm:dark:bg-transparent rounded-xl sm:rounded-none transition-colors">Cancelar</button>
                        <button type="submit" class="w-full sm:w-auto bg-barber-accent hover:bg-blue-600 text-white font-medium py-3 sm:py-2 px-4 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection