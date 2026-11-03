@extends('layouts.app')

@section('title', 'Gastos')

@section('content')
<div x-data="{ 
    gastoModal: {{ $errors->any() ? 'true' : 'false' }}, 
    deleteModal: false,
    deleteAction: '',
    tipoGasto: 'gasto',
    search: '',
    selectedProduct: null,
    products: {{ $products->toJson() ?? '[]' }},
    cantidadInput: '',

    get filteredProducts() {
        if (this.search === '') return [];
        let query = this.search.toLowerCase();
        return this.products.filter(p => 
            p.nombre.toLowerCase().includes(query) || 
            p.codigo.toLowerCase().includes(query)
        ).slice(0, 5);
    },
    selectProduct(product) {
        this.selectedProduct = product;
        this.search = '';
        this.cantidadInput = product.stock; 
    },
    clearProduct() {
        this.selectedProduct = null;
        this.cantidadInput = '';
    }
}">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Gastos</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Registro de salidas de dinero por reparaciones, insumos, etc.</p>
        </div>

        <!-- Botón solo visible en PC -->
        <button x-on:click="gastoModal = true; tipoGasto = 'gasto'; selectedProduct = null;" class="hidden md:flex bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-xl transition-all items-center justify-center gap-2 shadow-sm shrink-0">
            <i data-lucide="minus-circle" class="w-5 h-5"></i> Registrar Gasto
        </button>
    </div>

    <!-- FILTROS DE BÚSQUEDA CON DROPDOWN PERSONALIZADO -->
    <div class="bg-white dark:bg-barber-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-4 mb-6">
        <form action="{{ route('app.cash.gastos') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end" x-data="{ openTipo: false, selectedTipo: '{{ request('tipo', '') }}', tipoName: '{{ request('tipo') == 'normal' ? 'Gastos Normales' : (request('tipo') == 'compra' ? 'Compras de Mercancía' : 'Todos los gastos') }}' }">

            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Rango de Fechas</label>
                <div class="relative">
                    <i data-lucide="calendar" class="w-5 h-5 absolute left-3 top-3 text-slate-400"></i>
                    <input type="text" id="rango-fechas" name="rango" value="{{ $rango }}" placeholder="Selecciona un rango de fechas..." class="w-full pl-10 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2.5 focus:ring-2 focus:ring-barber-accent text-base">
                </div>
            </div>

            <!-- DROPDOWN DE TIPO DE GASTO -->
            <div class="w-full md:w-auto relative" @click.away="openTipo = false">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Tipo de Gasto</label>
                <input type="hidden" name="tipo" :value="selectedTipo">
                <button type="button" @click="openTipo = !openTipo" class="w-full md:w-56 flex justify-between items-center rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-2.5 focus:ring-2 focus:ring-barber-accent text-base transition-colors">
                    <span x-text="tipoName"></span>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform" :class="openTipo ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="openTipo" x-transition.opacity x-cloak class="absolute z-50 w-full md:w-56 mt-1 bg-white dark:bg-barber-card-dark rounded-xl shadow-lg border border-slate-200 dark:border-slate-700/50 py-1 max-h-60 overflow-y-auto">
                    <button type="button" @click="selectedTipo = ''; tipoName = 'Todos los gastos'; openTipo = false" class="w-full text-left px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Todos los gastos</button>
                    <button type="button" @click="selectedTipo = 'normal'; tipoName = 'Gastos Normales'; openTipo = false" class="w-full text-left px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Gastos Normales</button>
                    <button type="button" @click="selectedTipo = 'compra'; tipoName = 'Compras de Mercancía'; openTipo = false" class="w-full text-left px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Compras de Mercancía</button>
                </div>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-medium py-2.5 px-6 rounded-xl transition-all flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filtrar
                </button>
                @if(request('rango') || request('tipo'))
                <a href="{{ route('app.cash.gastos') }}" class="flex-1 md:flex-none text-sm font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white py-2.5 px-4 text-center bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center">Limpiar</a>
                @endif
            </div>
        </form>
    </div>

    @if ($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 text-sm flex items-start gap-2">
        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
        <div>
            <p class="font-bold mb-1">No se pudo guardar:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Tarjeta de Total -->
    <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-5 border border-slate-200 dark:border-slate-700/50 shadow-sm mb-6">
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total de gastos en el período</p>
        <p class="text-3xl font-bold text-red-600 dark:text-red-400">${{ number_format($totalGastos, 2) }}</p>
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
                        <th class="px-6 py-4 font-medium">Descripción</th>
                        <th class="px-6 py-4 font-medium">Monto</th>
                        <th class="px-6 py-4 font-medium text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                    @forelse ($gastos as $gasto)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $gasto->created_at->format('d/m/Y') }}</div>
                            <div>{{ $gasto->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $gasto->descripcion }}</div>
                        </td>
                        <td class="px-6 py-4 font-bold text-red-600 dark:text-red-400">
                            -${{ number_format($gasto->monto, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if(!str_starts_with($gasto->descripcion, 'Compra: '))
                            <button type="button" x-on:click="
                                    deleteModal = true;
                                    deleteAction = '{{ route('app.cash.delete', $gasto->id) }}';
                                " class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Eliminar Gasto">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i data-lucide="receipt" class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600"></i>
                            <p>No hay gastos en este período.</p>
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
        @forelse ($gastos as $gasto)
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-4 shadow-sm border border-slate-200 dark:border-slate-700/50 flex flex-col gap-3 relative overflow-hidden">

            <!-- Header: Fecha y Monto -->
            <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-700/50 pb-3">
                <div class="flex flex-col gap-0.5">
                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $gasto->created_at->format('d M, Y') }}</span>
                    <span class="text-xs text-slate-500 flex items-center gap-1">
                        <i data-lucide="clock" class="w-3 h-3"></i>
                        {{ $gasto->created_at->format('h:i A') }}
                    </span>
                </div>
                <div class="text-xl font-extrabold text-red-600 dark:text-red-400">
                    -${{ number_format($gasto->monto, 2) }}
                </div>
            </div>

            <!-- Descripción -->
            <div class="py-1">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-0.5">Descripción:</p>
                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $gasto->descripcion }}</p>
            </div>

            <!-- Acciones -->
            @if(!str_starts_with($gasto->descripcion, 'Compra: '))
            <div class="flex justify-end mt-1 pt-3 border-t border-slate-100 dark:border-slate-700/50">
                <button type="button" x-on:click="
                        deleteModal = true;
                        deleteAction = '{{ route('app.cash.delete', $gasto->id) }}';
                    " class="flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-xl text-sm font-bold hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar
                </button>
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-10 text-center border border-slate-200 dark:border-slate-700/50 flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="receipt" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
            </div>
            <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Sin gastos</h4>
            <p class="text-slate-500 dark:text-slate-400 text-sm">No hay gastos en este período.</p>
        </div>
        @endforelse
    </div>

    <!-- BOTÓN FLOTANTE (FAB) SOLO PARA MÓVIL -->
    <button x-on:click="gastoModal = true; tipoGasto = 'gasto'; selectedProduct = null;" class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-lg flex items-center justify-center z-40 transition-transform active:scale-95">
        <i data-lucide="minus" class="w-6 h-6"></i>
    </button>

    <!-- MODAL DE GASTO (CREACIÓN) -->
    <div x-show="gastoModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="gastoModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="gastoModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700/50">

                <form action="{{ route('app.cash.store-gasto') }}" method="POST" autocomplete="off">
                    @csrf
                    <input type="hidden" name="tipo" :value="tipoGasto">
                    <input type="hidden" name="product_id" :value="selectedProduct ? selectedProduct.id : ''">

                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white" id="modal-title">Registrar Salida de Dinero</h3>
                        <button type="button" x-on:click="gastoModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Tipo de Movimiento *</label>
                            <select x-model="tipoGasto" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-red-500 text-base appearance-none">
                                <option value="gasto">Gasto Normal (Luz, Agua, Alquiler...)</option>
                                <option value="compra_producto">Compra de Mercancía (Surtir Inventario)</option>
                            </select>
                        </div>

                        <div x-show="tipoGasto === 'compra_producto'" style="display: none;" class="space-y-4">
                            <div class="relative">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Buscar Producto *</label>
                                <template x-if="!selectedProduct">
                                    <div>
                                        <input type="text" x-model="search" placeholder="Escribe el nombre o código..." class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-red-500 text-base">
                                        <div x-show="search.length > 0" class="absolute z-10 w-full mt-2 bg-white dark:bg-barber-card-dark border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                                            <template x-for="product in filteredProducts" :key="product.id">
                                                <div x-on:click="selectProduct(product)" class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                                                    <p class="font-bold text-slate-900 dark:text-white text-base" x-text="product.nombre"></p>
                                                    <p class="text-xs text-slate-500 mt-0.5">Stock actual: <span x-text="product.stock"></span></p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="selectedProduct">
                                    <div class="flex items-center justify-between bg-red-50 dark:bg-red-900/20 p-4 rounded-xl border-2 border-red-200 dark:border-red-800">
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white text-base" x-text="selectedProduct.nombre"></p>
                                        </div>
                                        <button type="button" x-on:click="clearProduct()" class="p-2 bg-white dark:bg-slate-700 rounded-full text-slate-400 hover:text-red-500 transition-colors">
                                            <i data-lucide="x" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Cantidad Comprada *</label>
                                <input type="number" name="cantidad" min="1" x-model="cantidadInput" :required="tipoGasto === 'compra_producto'" placeholder="Ej. 20"
                                    class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-red-500 text-base">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Descripción *</label>
                            <input type="text" name="descripcion"
                                x-bind:value="tipoGasto === 'compra_producto' ? (selectedProduct ? 'Compra de ' + selectedProduct.nombre : '') : ''"
                                :required="tipoGasto === 'gasto' || (tipoGasto === 'compra_producto' && selectedProduct)"
                                :readonly="tipoGasto === 'compra_producto'"
                                placeholder="Describe el gasto o elige un producto..."
                                class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-red-500 text-base">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Costo Total ($) *</label>
                            <input type="number" step="0.01" name="monto" required min="0.01" placeholder="0.00" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-red-500 text-base">
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end gap-3 rounded-b-2xl">
                        <button type="button" x-on:click="gastoModal = false" class="w-full sm:w-auto px-4 py-3 sm:py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-200 sm:bg-transparent dark:bg-slate-700 sm:dark:bg-transparent rounded-xl sm:rounded-none transition-colors">Cancelar</button>
                        <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-medium py-3 sm:py-2 px-4 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Guardar
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
            <div x-show="deleteModal" x-transition class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700/50">
                <form :action="deleteAction" method="POST">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-500/20 rounded-full mb-4">
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-center text-slate-900 dark:text-white mb-2">Eliminar Gasto</h3>
                        <p class="text-sm text-center text-slate-500 dark:text-slate-400">¿Estás seguro de eliminar este registro? El balance se actualizará.</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex justify-center gap-3 rounded-b-2xl">
                        <button type="button" x-on:click="deleteModal = false" class="px-5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 rounded-xl transition-colors">Cancelar</button>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-5 rounded-xl transition-all shadow-sm">Sí, eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection