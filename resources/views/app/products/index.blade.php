@extends('layouts.app')

@section('title', 'Inventario')

@section('content')
<div x-data="{ 
    openModal: {{ $errors->any() && !request()->isMethod('POST') && !request()->routeIs('app.categorias.store') ? 'true' : 'false' }}, 
    editModal: false,
    editAction: '',
    editCodigo: '',
    editNombre: '',
    editCategoriaId: '',
    editMarca: '',
    editPrecio: '',
    editStock: '',
    editStockMinimo: '',
    
    deleteModal: false,
    deleteAction: '',

    catModal: {{ $errors->any() && request()->routeIs('app.categorias.store') ? 'true' : 'false' }},
    deleteCatModal: false,
    deleteCatAction: ''
}">

    <!-- Header Responsivo -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Inventario de Productos</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Gestiona los productos que vendes en tu barbería.</p>
        </div>
        <!-- Botones solo visibles en PC -->
        <div class="hidden md:flex gap-2 w-full sm:w-auto">
            <button x-on:click="catModal = true" class="w-full sm:w-auto bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-medium py-2 px-4 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm shrink-0 hover:bg-slate-50 dark:hover:bg-slate-700">
                <i data-lucide="tag" class="w-5 h-5"></i>
                Categorías
            </button>
            <button x-on:click="openModal = true" class="w-full sm:w-auto bg-barber-accent hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm shrink-0">
                <i data-lucide="plus" class="w-5 h-5"></i>
                Nuevo Producto
            </button>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="bg-white dark:bg-barber-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-4 mb-6">
        <!-- Usamos x-data local para el dropdown de categorías -->
        <form action="{{ route('app.productos.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end" x-data="{ openCat: false, selectedCat: '{{ request('categoria') }}' }">

            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Buscar por nombre, código o marca</label>
                <div class="relative">
                    <i data-lucide="search" class="w-5 h-5 absolute left-3 top-2.5 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ej. Cera, 001, Suavecito..." class="w-full pl-10 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                </div>
            </div>

            <!-- DROPDOWN PERSONALIZADO -->
            <div class="w-full md:w-auto relative">
                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Categoría</label>

                <!-- Input oculto para que el formulario envíe el valor -->
                <input type="hidden" name="categoria" :value="selectedCat">

                <button type="button" @click="openCat = !openCat" @click.away="openCat = false" class="w-full md:w-56 flex justify-between items-center rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent transition-colors">
                    <span x-text="selectedCat === '' ? 'Todas las categorías' : document.getElementById('cat-name-' + selectedCat)?.innerText || 'Todas las categorías'"></span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform" :class="openCat ? 'rotate-180' : ''"></i>
                </button>

                <!-- Lista flotante -->
                <div x-show="openCat" x-transition.opacity x-cloak class="absolute z-50 w-full md:w-56 mt-1 bg-white dark:bg-barber-card-dark rounded-xl shadow-lg border border-slate-200 dark:border-slate-700/50 py-1 max-h-60 overflow-y-auto">
                    <button type="button" @click="selectedCat = ''; openCat = false" class="w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        Todas las categorías
                    </button>
                    @foreach($categories as $cat)
                    <button type="button" @click="selectedCat = '{{ $cat->id }}'; openCat = false" class="w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <span id="cat-name-{{ $cat->id }}">{{ $cat->nombre }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-medium py-2 px-6 rounded-lg transition-all flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i> Filtrar
                </button>
                @if(request('search') || request('categoria'))
                <a href="{{ route('app.productos.index') }}" class="flex-1 md:flex-none text-sm font-medium text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white py-2 px-4 text-center bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center">Limpiar</a>
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
                        <th class="px-6 py-4 font-medium">Producto</th>
                        <th class="px-6 py-4 font-medium">Categoría</th>
                        <th class="px-6 py-4 font-medium">Código</th>
                        <th class="px-6 py-4 font-medium">Marca</th>
                        <th class="px-6 py-4 font-medium">Precio</th>
                        <th class="px-6 py-4 font-medium">Stock</th>
                        <th class="px-6 py-4 font-medium text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700/50">
                    @forelse ($products as $product)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                @if($product->foto)
                                <img src="{{ asset('storage/' . $product->foto) }}" alt="{{ $product->nombre }}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 dark:border-slate-700">
                                @else
                                <div class="w-10 h-10 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 font-bold border border-slate-300 dark:border-slate-600">
                                    <i data-lucide="package" class="w-5 h-5"></i>
                                </div>
                                @endif
                                <div class="font-medium text-slate-900 dark:text-white">{{ $product->nombre }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                {{ $product->category->nombre ?? 'Sin categoría' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300 text-sm font-mono">
                            {{ $product->codigo }}
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                            {{ $product->marca ?? '—' }}
                        </td>
                        <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                            ${{ number_format($product->precio, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($product->stock <= $product->stock_minimo)
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400 flex items-center gap-1 w-max">
                                    <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                    {{ $product->stock }} (Bajo)
                                </span>
                                @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                                    {{ $product->stock }}
                                </span>
                                @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button x-on:click="
                                    editModal = true; 
                                    editAction = '{{ route('app.productos.update', $product->id) }}';
                                    editCodigo = '{{ addslashes($product->codigo) }}';
                                    editNombre = '{{ addslashes($product->nombre) }}';
                                    editCategoriaId = '{{ $product->categoria_id }}';
                                    editMarca = '{{ addslashes($product->marca) }}';
                                    editPrecio = '{{ $product->precio }}';
                                    editStock = '{{ $product->stock }}';
                                    editStockMinimo = '{{ $product->stock_minimo }}';
                                " class="p-2 text-slate-400 hover:text-barber-accent transition-colors" title="Editar">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button type="button" x-on:click="
                                    deleteModal = true;
                                    deleteAction = '{{ route('app.productos.delete', $product->id) }}';
                                " class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Eliminar">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                            <i data-lucide="package" class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600"></i>
                            <p>No hay productos {{ request('search') ? 'que coincidan con la búsqueda' : 'en el inventario todavía' }}.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-800/30">
            {{ $products->links() }}
        </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- VISTA MÓVIL (Tarjetas estilo App) -->
    <!-- ========================================== -->
    <div class="md:hidden space-y-3 pb-24">
        <!-- Botón de Categorías para móvil -->
        <button x-on:click="catModal = true" class="w-full bg-white dark:bg-barber-card-dark border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 font-medium py-2.5 px-4 rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm mb-4 hover:bg-slate-50 dark:hover:bg-slate-700">
            <i data-lucide="tag" class="w-5 h-5"></i>
            Gestionar Categorías
        </button>

        @forelse ($products as $product)
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-4 shadow-sm border border-slate-200 dark:border-slate-700/50 flex flex-col gap-3 relative overflow-hidden">

            <!-- Info Principal -->
            <div class="flex items-start gap-4">
                @if($product->foto)
                <img src="{{ asset('storage/' . $product->foto) }}" alt="{{ $product->nombre }}" class="w-14 h-14 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                @else
                <div class="w-14 h-14 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 shrink-0 border border-slate-200 dark:border-slate-700">
                    <i data-lucide="package" class="w-6 h-6"></i>
                </div>
                @endif

                <div class="flex-1 min-w-0">
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg truncate">{{ $product->nombre }}</h3>
                    <div class="flex flex-wrap gap-2 mt-1">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            {{ $product->category->nombre ?? 'Sin categoría' }}
                        </span>
                        <span class="text-xs text-slate-500 font-mono mt-0.5">{{ $product->codigo }}</span>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 truncate">
                        {{ $product->marca ?? 'Sin marca' }}
                    </p>
                </div>
            </div>

            <!-- Info Financiera y Stock -->
            <div class="flex items-center justify-between mt-1 pt-3 border-t border-slate-100 dark:border-slate-700/50">
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Precio</p>
                    <p class="text-lg font-extrabold text-barber-accent">${{ number_format($product->precio, 2) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Stock</p>
                    @if($product->stock <= $product->stock_minimo)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400 mt-1">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                            {{ $product->stock }} (Bajo)
                        </span>
                        @else
                        <span class="inline-block px-2.5 py-1 rounded-md text-xs font-bold bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400 mt-1">
                            {{ $product->stock }} disp.
                        </span>
                        @endif
                </div>
            </div>

            <!-- Acciones -->
            <div class="flex items-center gap-2 mt-2">
                <button x-on:click="
                        editModal = true; 
                        editAction = '{{ route('app.productos.update', $product->id) }}';
                        editCodigo = '{{ addslashes($product->codigo) }}';
                        editNombre = '{{ addslashes($product->nombre) }}';
                        editCategoriaId = '{{ $product->categoria_id }}';
                        editMarca = '{{ addslashes($product->marca) }}';
                        editPrecio = '{{ $product->precio }}';
                        editStock = '{{ $product->stock }}';
                        editStockMinimo = '{{ $product->stock_minimo }}';
                    " class="flex-1 flex justify-center items-center gap-2 py-2 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    <i data-lucide="edit" class="w-4 h-4"></i> Editar
                </button>
                <button type="button" x-on:click="
                        deleteModal = true;
                        deleteAction = '{{ route('app.productos.delete', $product->id) }}';
                    " class="flex-1 flex justify-center items-center gap-2 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-xl text-sm font-medium hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar
                </button>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-barber-card-dark rounded-2xl p-10 text-center border border-slate-200 dark:border-slate-700/50 flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                <i data-lucide="package" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
            </div>
            <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Sin productos</h4>
            <p class="text-slate-500 dark:text-slate-400 text-sm">No se encontraron productos en el inventario.</p>
        </div>
        @endforelse

        @if($products->hasPages())
        <div class="mt-4">
            {{ $products->links() }}
        </div>
        @endif
    </div>

    <!-- BOTÓN FLOTANTE (FAB) SOLO PARA MÓVIL -->
    <button x-on:click="openModal = true" class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-barber-accent hover:bg-blue-600 text-white rounded-full shadow-lg flex items-center justify-center z-40 transition-transform active:scale-95">
        <i data-lucide="plus" class="w-6 h-6"></i>
    </button>

    <!-- MODAL DE CREACIÓN DE PRODUCTO -->
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

                <form action="{{ route('app.productos.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white" id="modal-title">Nuevo Producto</h3>
                        <button type="button" x-on:click="openModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        @if ($errors->any() && !request()->routeIs('app.categorias.store'))
                        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-2 text-sm text-left">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Código / SKU *</label>
                                <input type="text" name="codigo" value="{{ old('codigo') }}" required placeholder="Ej. 1001" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nombre del Producto *</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Cera Mate" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Categoría *</label>
                                <select name="categoria_id" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                                    <option value="">Seleccionar categoría...</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Marca</label>
                                <input type="text" name="marca" value="{{ old('marca') }}" placeholder="Ej. Suavecito" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Foto (Opcional)</label>
                                <input type="file" name="foto" accept="image/*" class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-barber-accent/10 file:text-barber-accent hover:file:bg-barber-accent/20 transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Precio de Venta ($) *</label>
                                <input type="number" step="0.01" name="precio" value="{{ old('precio') }}" required min="0" placeholder="0.00" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Stock *</label>
                                    <input type="number" name="stock" value="{{ old('stock', 0) }}" required min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Stock Mín. *</label>
                                    <input type="number" name="stock_minimo" value="{{ old('stock_minimo', 5) }}" required min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
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

    <!-- MODAL DE EDICIÓN DE PRODUCTO -->
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
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Editar Producto</h3>
                        <button type="button" x-on:click="editModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        @if ($errors->any() && !request()->routeIs('app.categorias.store'))
                        <div class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-2 text-sm text-left">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Código / SKU *</label>
                                <input type="text" name="codigo" x-model="editCodigo" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nombre del Producto *</label>
                                <input type="text" name="nombre" x-model="editNombre" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Categoría *</label>
                                <select name="categoria_id" x-model="editCategoriaId" required class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                                    <option value="">Seleccionar categoría...</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Marca</label>
                                <input type="text" name="marca" x-model="editMarca" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nueva Foto (Opcional)</label>
                                <input type="file" name="foto" accept="image/*" class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-barber-accent/10 file:text-barber-accent hover:file:bg-barber-accent/20 transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Precio de Venta ($) *</label>
                                <input type="number" step="0.01" name="precio" x-model="editPrecio" required min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Stock Actual</label>
                                    <input type="text" :value="editStock" disabled class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-3 py-2 cursor-not-allowed font-bold" title="El stock no se puede editar aquí. Se actualiza al registrar compras en Gastos.">
                                    <p class="text-[10px] text-slate-500 mt-1">El stock solo se modifica registrando compras en Gastos.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Stock Mín. *</label>
                                    <input type="number" name="stock_minimo" x-model="editStockMinimo" required min="0" class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                                </div>
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

    <!-- MODAL DE ELIMINAR PRODUCTO -->
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
                        <h3 class="text-lg font-semibold text-center text-slate-900 dark:text-white mb-2">Eliminar Producto</h3>
                        <p class="text-sm text-center text-slate-500 dark:text-slate-400">
                            ¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.
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

    <!-- MODAL DE GESTIONAR CATEGORÍAS -->
    <div x-show="catModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="catModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="catModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700/50">

                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Gestionar Categorías</h3>
                    <button type="button" x-on:click="catModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="px-6 py-4 space-y-6">
                    <form action="{{ route('app.categorias.store') }}" method="POST" class="flex gap-2" autocomplete="off">
                        @csrf
                        <input type="text" name="nombre" required placeholder="Ej. Ceras, Shampoo, Lociones..." class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 focus:ring-2 focus:ring-barber-accent">
                        <button type="submit" class="bg-barber-accent hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-all shadow-sm shrink-0">
                            Agregar
                        </button>
                    </form>

                    @if ($errors->any() && request()->routeIs('app.categorias.store'))
                    <div class="rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-2 text-sm text-left">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <hr class="border-slate-200 dark:border-slate-700/50">

                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        @forelse($categories as $cat)
                        <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-800/50 p-3 rounded-lg border border-slate-200 dark:border-slate-700/50">
                            <span class="font-medium text-slate-900 dark:text-white">{{ $cat->nombre }}</span>
                            <button type="button" x-on:click="
                                deleteCatModal = true;
                                deleteCatAction = '{{ route('app.categorias.destroy', $cat->id) }}';
                            " class="text-slate-400 hover:text-red-500 transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                        @empty
                        <p class="text-center text-sm text-slate-500 py-4">No hay categorías creadas.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" x-on:click="catModal = false" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE ELIMINAR CATEGORÍA -->
    <div x-show="deleteCatModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="deleteCatModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="deleteCatModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-barber-card-dark text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700/50">

                <form :action="deleteCatAction" method="POST">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-500/20 rounded-full mb-4">
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-center text-slate-900 dark:text-white mb-2">Eliminar Categoría</h3>
                        <p class="text-sm text-center text-slate-500 dark:text-slate-400">
                            ¿Estás seguro de que deseas eliminar esta categoría? Si tiene productos asignados, no se podrá eliminar.
                        </p>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex justify-center gap-3 rounded-b-2xl">
                        <button type="button" x-on:click="deleteCatModal = false" class="px-5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 rounded-xl transition-colors">
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