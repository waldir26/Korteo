@extends('layouts.app')

@section('title', 'Cierre de Caja')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ exportModal: false }">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Cierre de Caja</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Resumen general de todas las operaciones del día.</p>
        </div>

        <!-- BOTÓN DE EXPORTAR -->
        <button type="button" x-on:click="exportModal = true" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-6 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm shrink-0">
            <i data-lucide="download" class="w-5 h-5"></i> Exportar Reporte
        </button>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

        <!-- Ingresos -->
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-6">
            <div class="flex items-center gap-3 mb-4 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                <div class="p-2 bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400 rounded-lg">
                    <i data-lucide="arrow-down-circle" class="w-6 h-6"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Ingresos</h3>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Servicios Dados:</span>
                    <span class="font-bold text-slate-900 dark:text-white">${{ number_format($totalServicios, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Ventas de Productos:</span>
                    <span class="font-bold text-slate-900 dark:text-white">${{ number_format($totalVentas, 2) }}</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-slate-100 dark:border-slate-700/50">
                    <span class="font-semibold text-slate-800 dark:text-slate-200">Total Ingresos:</span>
                    <span class="text-xl font-bold text-green-600 dark:text-green-400">${{ number_format($ingresosTotales, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Egresos -->
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-6">
            <div class="flex items-center gap-3 mb-4 border-b border-slate-100 dark:border-slate-700/50 pb-4">
                <div class="p-2 bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 rounded-lg">
                    <i data-lucide="arrow-up-circle" class="w-6 h-6"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Egresos</h3>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Gastos de la Barbería:</span>
                    <span class="font-bold text-slate-900 dark:text-white">${{ number_format($totalGastos, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Pagos a Barberos:</span>
                    <span class="font-bold text-slate-900 dark:text-white">${{ number_format($totalPagos, 2) }}</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-slate-100 dark:border-slate-700/50">
                    <span class="font-semibold text-slate-800 dark:text-slate-200">Total Egresos:</span>
                    <span class="text-xl font-bold text-red-600 dark:text-red-400">${{ number_format($egresosTotales, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Final -->
    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700/50 p-8 text-center shadow-sm">
        <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Balance del Día</p>

        <p class="text-5xl font-extrabold {{ $balanceFinal < 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }} mb-3">
            {{ $balanceFinal < 0 ? '-$' . number_format(abs($balanceFinal), 2) : '$' . number_format($balanceFinal, 2) }}
        </p>

        <p class="text-sm text-slate-500 dark:text-slate-400">
            @if($balanceFinal < 0)
                Hubo una pérdida hoy. Los egresos superaron los ingresos por ${{ number_format(abs($balanceFinal), 2) }}.
                @elseif($balanceFinal> 0)
                ¡Buen día! Hubo una ganancia neta de ${{ number_format($balanceFinal, 2) }}.
                @else
                El balance del día quedó en cero. No hubo ganancias ni pérdidas.
                @endif
        </p>
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
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Exportar Cierre de Caja</h3>
                    <button type="button" x-on:click="exportModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="px-6 py-6 space-y-3">
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 text-center">Selecciona el formato en el que deseas descargar el cierre del día de hoy.</p>

                    <a href="{{ route('app.cash.cierre.pdf') }}" class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-200 dark:border-red-800">
                        <i data-lucide="file-text" class="w-6 h-6"></i>
                        <div>
                            <p class="font-bold">Exportar a PDF</p>
                            <p class="text-xs">Ideal para imprimir y firmar.</p>
                        </div>
                    </a>

                    <a href="{{ route('app.cash.cierre.excel') }}" class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-xl hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors border border-green-200 dark:border-green-800">
                        <i data-lucide="file-spreadsheet" class="w-6 h-6"></i>
                        <div>
                            <p class="font-bold">Exportar a Excel</p>
                            <p class="text-xs">Ideal para enviar al contador.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection