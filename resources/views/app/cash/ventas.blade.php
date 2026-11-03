@extends('layouts.app')

@section('title', 'Ventas de Productos')

@section('content')
<div x-data="{ 
    ventaModal: {{ $errors->any() ? 'true' : 'false' }}, 
    deleteModal: false,
    deleteAction: ''
}">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Ventas de Productos</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Registro de ventas de ceras, shampoo y otros productos.</p>
        </div>

        <!-- Botón solo visible en PC -->
        <button x-on:click="ventaModal = true" class="hidden md:flex bg-barber-accent hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-xl transition-all items-center justify-center gap-2 shadow-sm shrink-0">
            <i data-lucide="plus-circle" class="w-5 h-5"></i> Registrar Venta
        </button>
    </div>

    @if ($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 text-sm flex items-start gap-2">
        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
        <div>
            <p class="font-bold mb-1">No se pudo procesar la venta:</p>
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
        <form action="{{ route('app.cash.ventas') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Rango de Fechas</label>
                <div class="relative">
                    <i data-lucide="calendar" class="w-5 h-5 absolute left-3 top-3 text-slate-400"></i>
                    <input type="text" id="rango-fechas-ventas" name="rango" value="{{ $rango ?? '' }}" placeholder="Selecciona un rango de fechas..." class="w-full pl-10 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2.5 focus:ring-2 focus:ring-barber-accent text-base">
                </div>
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-medium py-2.5 px-6 rounded-xl transition-all flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filtrar
                </button>
                @if(request('rango'))
                <a href="{{ route('app.cash.ventas') }}" class="flex-1 md:flex-none text-sm font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white py-2.5 px-4 text-center bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center">Limpiar</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tarjeta de Total -->
    <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-5 border border-slate-200 dark:border-slate-700/50 shadow-sm mb-6">
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total en ventas en el período</p>
        <p class="text-3xl font-bold text-slate-900 dark:text-white">${{ number_format($totalVentas, 2) }}</p>
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
                        <th class="px-6 py-4 font-medium">Producto / Descripción</th>
                        <th class="px-6 py-4 font-medium">Monto</th>
                        <th class="px-6 py-4 font-medium text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                    @forelse ($ventas as $venta)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $venta->created_at->format('d/m/Y') }}</div>
                            <div>{{ $venta->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $venta->descripcion }}</div>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                            +${{ number_format($venta->monto, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" x-on:click="
                                deleteModal = true;
                                deleteAction = '{{ route('app.cash.delete', $venta->id) }}';
                            " class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Eliminar">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i data-lucide="shopping-bag" class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600"></i>
                            <p>No hay ventas en este período.</p>
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
        @forelse ($ventas as $venta)
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-4 shadow-sm border border-slate-200 dark:border-slate-700/50 flex flex-col gap-3 relative overflow-hidden">

            <!-- Header: Fecha y Monto -->
            <div class="flex justify-between items-center border-b border-slate-100 dark:border-slate-700/50 pb-3">
                <div class="flex flex-col gap-0.5">
                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $venta->created_at->format('d M, Y') }}</span>
                    <span class="text-xs text-slate-500 flex items-center gap-1">
                        <i data-lucide="clock" class="w-3 h-3"></i>
                        {{ $venta->created_at->format('h:i A') }}
                    </span>
                </div>
                <div class="text-xl font-extrabold text-green-600 dark:text-green-400">
                    +${{ number_format($venta->monto, 2) }}
                </div>
            </div>

            <!-- Descripción -->
            <div class="py-1">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-0.5">Producto / Descripción:</p>
                <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $venta->descripcion }}</p>
            </div>

            <!-- Acciones -->
            <div class="flex justify-end mt-1 pt-3 border-t border-slate-100 dark:border-slate-700/50">
                <button type="button" x-on:click="
                        deleteModal = true;
                        deleteAction = '{{ route('app.cash.delete', $venta->id) }}';
                    " class="flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-xl text-sm font-bold hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar
                </button>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-10 text-center border border-slate-200 dark:border-slate-700/50 flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="shopping-bag" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
            </div>
            <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Sin ventas</h4>
            <p class="text-slate-500 dark:text-slate-400 text-sm">No hay ventas en este período.</p>
        </div>
        @endforelse
    </div>

    <!-- BOTÓN FLOTANTE (FAB) SOLO PARA MÓVIL -->
    <button x-on:click="ventaModal = true" class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-barber-accent hover:bg-blue-600 text-white rounded-full shadow-lg flex items-center justify-center z-40 transition-transform active:scale-95">
        <i data-lucide="plus" class="w-6 h-6"></i>
    </button>

    <!-- MODAL DE VENTA (CARRITO) -->
    <div x-show="ventaModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="ventaModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="ventaModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200 dark:border-slate-700/50">

                <form action="{{ route('app.cash.store-ingreso') }}" method="POST" autocomplete="off"
                    x-data="{
                        search: '',
                        cart: [],
                        products: {{ $products->toJson() }},
                        get filteredProducts() {
                            if (this.search === '') return [];
                            let query = this.search.toLowerCase();
                            return this.products.filter(p => 
                                p.nombre.toLowerCase().includes(query) || 
                                p.codigo.toLowerCase().includes(query) || 
                                (p.marca && p.marca.toLowerCase().includes(query))
                            ).slice(0, 5);
                        },
                        get cartTotal() {
                            return this.cart.reduce((sum, item) => sum + (item.precio * item.cantidad), 0).toFixed(2);
                        },
                        addToCart(product) {
                            let existing = this.cart.find(item => item.id === product.id);
                            if (existing) {
                                if (existing.cantidad < existing.stock) existing.cantidad++;
                                else alert('No hay más stock de este producto.');
                            } else {
                                this.cart.push({ ...product, cantidad: 1 });
                            }
                            this.search = '';
                        },
                        removeFromCart(index) {
                            this.cart.splice(index, 1);
                        }
                    }">
                    @csrf
                    <input type="hidden" name="items" :value="JSON.stringify(cart)">

                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white" id="modal-title">Registrar Venta</h3>
                        <button type="button" x-on:click="ventaModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        <div class="relative">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Agregar Producto</label>
                            <input type="text" x-model="search" placeholder="Buscar por código, nombre o marca..." class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base">

                            <div x-show="search.length > 0" class="absolute z-10 w-full mt-2 bg-white dark:bg-barber-card-dark border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                                <template x-for="product in filteredProducts" :key="product.id">
                                    <div x-on:click="addToCart(product)" class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer border-b border-slate-100 dark:border-slate-700/50 last:border-0 flex justify-between items-center transition-colors">
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white text-base" x-text="product.nombre"></p>
                                            <p class="text-xs text-slate-500 mt-0.5">
                                                <span x-text="product.codigo"></span> • <span x-text="product.marca"></span>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-base text-slate-900 dark:text-white">$<span x-text="product.precio"></span></p>
                                            <p class="text-xs text-slate-500">Stock: <span x-text="product.stock"></span></p>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="filteredProducts.length === 0" class="p-4 text-sm text-slate-500 text-center">
                                    No se encontraron productos.
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 border-t border-slate-200 dark:border-slate-700/50 pt-4">
                            <h4 class="text-sm font-semibold text-slate-900 dark:text-white mb-3">Productos a cobrar</h4>

                            <div x-show="cart.length === 0" class="text-center py-6 text-sm text-slate-500">
                                No has agregado productos.
                            </div>

                            <div class="space-y-2">
                                <template x-for="(item, index) in cart" :key="index">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-200 dark:border-slate-700/50 gap-3">
                                        <div class="flex-1">
                                            <p class="font-bold text-slate-900 dark:text-white text-sm" x-text="item.nombre"></p>
                                            <p class="text-xs text-slate-500">$<span x-text="item.precio"></span> c/u</p>
                                        </div>
                                        <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-slate-500 font-medium">Cant:</span>
                                                <input type="number" x-model="item.cantidad" :max="item.stock" min="1" class="w-16 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-2 py-1.5 text-center text-sm font-bold">
                                            </div>
                                            <p class="font-bold text-slate-900 dark:text-white text-base w-20 text-right">$<span x-text="(item.precio * item.cantidad).toFixed(2)"></span></p>
                                            <button type="button" x-on:click="removeFromCart(index)" class="p-2 text-slate-400 hover:text-red-500 bg-white dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-4 rounded-b-2xl">
                        <div class="text-lg w-full sm:w-auto flex justify-between sm:block items-center">
                            <span class="text-slate-600 dark:text-slate-400 text-sm">Total a cobrar:</span>
                            <span class="text-2xl font-bold text-slate-900 dark:text-white sm:ml-2">$<span x-text="cartTotal"></span></span>
                        </div>
                        <div class="flex gap-3 w-full sm:w-auto">
                            <button type="button" x-on:click="ventaModal = false" class="flex-1 sm:flex-none px-4 py-3 sm:py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-200 sm:bg-transparent dark:bg-slate-700 sm:dark:bg-transparent rounded-xl sm:rounded-none transition-colors">Cancelar</button>
                            <button type="submit" :disabled="cart.length === 0" class="flex-1 sm:flex-none bg-barber-accent hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold sm:font-medium py-3 sm:py-2 px-6 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i> Cobrar
                            </button>
                        </div>
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
                        <h3 class="text-lg font-semibold text-center text-slate-900 dark:text-white mb-2">Eliminar Venta</h3>
                        <p class="text-sm text-center text-slate-500 dark:text-slate-400">
                            ¿Estás seguro de que deseas eliminar este registro? El balance se actualizará.
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