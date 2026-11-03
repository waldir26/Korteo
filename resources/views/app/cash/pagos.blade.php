@extends('layouts.app')

@section('title', 'Pagos a Barberos')

@section('content')
<div x-data="{ 
    pagoModal: {{ $errors->any() ? 'true' : 'false' }}, 
    deleteModal: false,
    deleteAction: '',
    
    // Variables para el Dropdown de Barbero (Filtro)
    openBarberFiltro: false,
    selectedBarberId: '{{ $barberId ?? '' }}',
    selectedBarberName: '{{ $barberId ? $barbers->firstWhere('id', $barberId)?->nombre : 'Todos los barberos' }}',
    
    // Variables para el Dropdown de Barbero (Modal)
    openBarberModal: false,
    selectedBarberPago: ''
}">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Pagos a Barberos</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Registro de salidas de dinero por comisiones o salarios.</p>
        </div>

        <!-- Botón solo visible en PC -->
        <button x-on:click="pagoModal = true" class="hidden md:flex bg-barber-accent hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-xl transition-all items-center justify-center gap-2 shadow-sm shrink-0">
            <i data-lucide="user-check" class="w-5 h-5"></i> Registrar Pago
        </button>
    </div>

    @if ($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 text-sm flex items-start gap-2">
        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
        <div>
            <p class="font-bold mb-1">No se pudo procesar el pago:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- FILTROS DE BÚSQUEDA -->
    <div class="bg-white dark:bg-barber-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-4 mb-6">
        <form action="{{ route('app.cash.pagos') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">

            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Rango de Fechas</label>
                <div class="relative">
                    <i data-lucide="calendar" class="w-5 h-5 absolute left-3 top-3 text-slate-400"></i>
                    <input type="text" id="rango-fechas-pagos" name="rango" value="{{ $rango ?? '' }}" placeholder="Selecciona un rango de fechas..." class="w-full pl-10 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2.5 focus:ring-2 focus:ring-barber-accent text-base">
                </div>
            </div>

            <!-- Dropdown Barbero (Filtro) -->
            <div class="w-full md:w-auto relative" @click.away="openBarberFiltro = false">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Barbero</label>
                <input type="hidden" name="barber_id" :value="selectedBarberId">

                <button type="button" @click="openBarberFiltro = !openBarberFiltro" class="w-full md:w-56 flex justify-between items-center rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-2.5 focus:ring-2 focus:ring-barber-accent text-base transition-colors">
                    <span x-text="selectedBarberName"></span>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform" :class="openBarberFiltro ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="openBarberFiltro" x-transition.opacity x-cloak class="absolute z-50 w-full md:w-56 mt-1 bg-white dark:bg-barber-card-dark rounded-xl shadow-lg border border-slate-200 dark:border-slate-700/50 py-1 max-h-60 overflow-y-auto">
                    <button type="button" @click="selectedBarberId = ''; selectedBarberName = 'Todos los barberos'; openBarberFiltro = false" class="w-full text-left px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Todos los barberos
                    </button>
                    @foreach($barbers as $barber)
                    <button type="button" @click="selectedBarberId = '{{ $barber->id }}'; selectedBarberName = '{{ addslashes($barber->nombre) }}'; openBarberFiltro = false" class="w-full text-left px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        {{ $barber->nombre }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-medium py-2.5 px-6 rounded-xl transition-all flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filtrar
                </button>
                @if(request('rango') || request('barber_id'))
                <a href="{{ route('app.cash.pagos') }}" class="flex-1 md:flex-none text-sm font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white py-2.5 px-4 text-center bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center">Limpiar</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tarjeta de Total -->
    <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-5 border border-slate-200 dark:border-slate-700/50 shadow-sm mb-6">
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total pagado en el período</p>
        <p class="text-3xl font-bold text-slate-900 dark:text-white">${{ number_format($totalPagos, 2) }}</p>
    </div>

    <!-- ========================================== -->
    <!-- VISTA ESCRITORIO (Tabla) -->
    <!-- ========================================== -->
    <div class="hidden md:block bg-white dark:bg-barber-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-sm border-b border-slate-200 dark:border-slate-700/50">
                        <th class="px-6 py-4 font-medium">Fecha / Hora</th>
                        <th class="px-6 py-4 font-medium">Barbero / Concepto</th>
                        <th class="px-6 py-4 font-medium">Monto</th>
                        <th class="px-6 py-4 font-medium text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                    @forelse ($pagos as $pago)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $pago->created_at->format('d/m/Y') }}</div>
                            <div>{{ $pago->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $pago->descripcion }}</div>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                            ${{ number_format($pago->monto, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form id="delete-form-{{ $pago->id }}" action="{{ route('app.cash.delete', $pago->id) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            <button type="button" onclick="if(confirm('¿Eliminar este pago?')) document.getElementById('delete-form-{{ $pago->id }}').submit();" class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Eliminar">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i data-lucide="user-check" class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600"></i>
                            <p>No hay pagos registrados en este período.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- VISTA MÓVIL (Tarjetas estilo App) -->
    <!-- ========================================== -->
    <div class="md:hidden space-y-3 pb-24">
        @forelse ($pagos as $pago)
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-4 shadow-sm border border-slate-200 dark:border-slate-700/50 flex flex-col gap-3 relative overflow-hidden">

            <!-- Header: Fecha y Monto -->
            <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-700/50 pb-3">
                <div class="flex flex-col gap-0.5">
                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $pago->created_at->format('d M, Y') }}</span>
                    <span class="text-xs text-slate-500 flex items-center gap-1">
                        <i data-lucide="clock" class="w-3 h-3"></i>
                        {{ $pago->created_at->format('h:i A') }}
                    </span>
                </div>
                <div class="text-xl font-extrabold text-slate-900 dark:text-white">
                    ${{ number_format($pago->monto, 2) }}
                </div>
            </div>

            <!-- Descripción -->
            <div class="py-1">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-0.5">Concepto:</p>
                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $pago->descripcion }}</p>
            </div>

            <!-- Acciones -->
            <div class="flex justify-end mt-1 pt-3 border-t border-slate-100 dark:border-slate-700/50">
                <form id="delete-form-mob-{{ $pago->id }}" action="{{ route('app.cash.delete', $pago->id) }}" method="POST" class="hidden">
                    @csrf
                </form>
                <button type="button" onclick="if(confirm('¿Eliminar este pago?')) document.getElementById('delete-form-mob-{{ $pago->id }}').submit();" class="flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-xl text-sm font-bold hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar
                </button>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-10 text-center border border-slate-200 dark:border-slate-700/50 flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="user-check" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
            </div>
            <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Sin pagos</h4>
            <p class="text-slate-500 dark:text-slate-400 text-sm">No hay pagos registrados en este período.</p>
        </div>
        @endforelse
    </div>

    <!-- BOTÓN FLOTANTE (FAB) SOLO PARA MÓVIL -->
    <button x-on:click="pagoModal = true" class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-barber-accent hover:bg-blue-600 text-white rounded-full shadow-lg flex items-center justify-center z-40 transition-transform active:scale-95">
        <i data-lucide="plus" class="w-6 h-6"></i>
    </button>

    <!-- MODAL DE PAGO -->
    <div x-show="pagoModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="pagoModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="pagoModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700/50">

                <form action="{{ route('app.cash.store-gasto') }}" method="POST" autocomplete="off">
                    @csrf
                    <input type="hidden" name="tipo" value="pago_barbero">
                    <input type="hidden" name="descripcion" :value="selectedBarberPago ? 'Pago a ' + selectedBarberPago : ''">

                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white" id="modal-title">Pago a Barbero</h3>
                        <button type="button" x-on:click="pagoModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        <!-- Dropdown Barbero (Modal) -->
                        <div class="relative" @click.away="openBarberModal = false">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Seleccionar Barbero *</label>

                            <button type="button" @click="openBarberModal = !openBarberModal" class="w-full flex justify-between items-center rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base transition-colors">
                                <span x-text="selectedBarberPago || '— Seleccione el barbero —'" :class="!selectedBarberPago ? 'text-slate-500' : ''"></span>
                                <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform" :class="openBarberModal ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="openBarberModal" x-transition.opacity x-cloak class="absolute z-30 w-full mt-2 bg-white dark:bg-barber-card-dark border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                                @foreach($barbers as $barber)
                                <button type="button" @click="selectedBarberPago = '{{ addslashes($barber->nombre) }}'; openBarberModal = false" class="w-full text-left px-4 py-3 text-base text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                                    {{ $barber->nombre }}
                                </button>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Concepto / Nota (Opcional)</label>
                            <input type="text" name="concepto" value="{{ old('concepto') }}" placeholder="Ej. Pago semanal, Comisión 5 cortes..." class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Monto ($) *</label>
                            <input type="number" step="0.01" name="monto" value="{{ old('monto') }}" required min="0.01" placeholder="0.00" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base">
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3 rounded-b-2xl">
                        <button type="button" x-on:click="pagoModal = false" class="w-full sm:w-auto px-4 py-3 sm:py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-200 sm:bg-transparent dark:bg-slate-700 sm:dark:bg-transparent rounded-xl sm:rounded-none transition-colors">Cancelar</button>
                        <button type="submit" :disabled="!selectedBarberPago" class="w-full sm:w-auto bg-barber-accent hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium py-3 sm:py-2 px-4 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Guardar Pago
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection