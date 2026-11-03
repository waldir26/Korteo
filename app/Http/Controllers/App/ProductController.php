<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoriaId = $request->get('categoria');

        $query = Product::with('category')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%")
                    ->orWhere('marca', 'like', "%{$search}%");
            });
        }

        if ($categoriaId) {
            $query->where('categoria_id', $categoriaId);
        }

        $products = $query->paginate(10);

        $categories = Category::orderBy('nombre')->get();

        return view('app.products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:products,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'categoria_id' => ['required', 'exists:categories,id'],
            'marca' => ['nullable', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        $categoriaNombre = Category::find($validated['categoria_id'])->nombre ?? 'General';

        $data = [
            'codigo' => $validated['codigo'],
            'nombre' => $validated['nombre'],
            'categoria' => $categoriaNombre,
            'categoria_id' => $validated['categoria_id'],
            'marca' => $validated['marca'],
            'precio' => $validated['precio'],
            'stock' => $validated['stock'],
            'stock_minimo' => $validated['stock_minimo'],
            'activo' => true,
        ];

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('products', 'public');
            $data['foto'] = $path;
        }

        Product::create($data);

        return redirect()->back()->with('success', 'Producto registrado exitosamente.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:products,codigo,' . $product->id],
            'nombre' => ['required', 'string', 'max:255'],
            'categoria_id' => ['required', 'exists:categories,id'],
            'marca' => ['nullable', 'string', 'max:255'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        $categoriaNombre = Category::find($validated['categoria_id'])->nombre ?? 'General';

        $product->codigo = $validated['codigo'];
        $product->nombre = $validated['nombre'];
        $product->categoria = $categoriaNombre;
        $product->categoria_id = $validated['categoria_id'];
        $product->marca = $validated['marca'];
        $product->precio = $validated['precio'];

        // El stock no se actualiza aquí por seguridad.

        $product->stock_minimo = $validated['stock_minimo'];

        if ($request->hasFile('foto')) {
            if ($product->foto && Storage::disk('public')->exists($product->foto)) {
                Storage::disk('public')->delete($product->foto);
            }
            $path = $request->file('foto')->store('products', 'public');
            $product->foto = $path;
        }

        $product->save();

        return redirect()->back()->with('success', 'Producto actualizado correctamente. (El stock no se modificó por seguridad).');
    }

    // NUEVO MÉTODO: Ajustar Stock (Corrección manual con razón)
    public function ajustarStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'nuevo_stock' => ['required', 'integer', 'min:0'],
            'razon' => ['required', 'string', 'max:255'],
        ]);

        // (Opcional) Si tuvieras una tabla de auditoría, aquí podrías guardar el registro:
        // Audit::create([
        //     'user_id' => auth()->id(),
        //     'accion' => 'Ajuste de stock',
        //     'detalles' => 'Producto: ' . $product->nombre . ' | Stock anterior: ' . $product->stock . ' | Nuevo stock: ' . $validated['nuevo_stock'] . ' | Razón: ' . $validated['razon']
        // ]);

        // Actualizamos el stock al nuevo valor
        $product->stock = $validated['nuevo_stock'];
        $product->save();

        return redirect()->back()->with('success', 'Stock ajustado exitosamente a ' . $validated['nuevo_stock'] . ' unidades. Razón: ' . $validated['razon']);
    }

    public function destroy(Product $product)
    {
        if ($product->foto && Storage::disk('public')->exists($product->foto)) {
            Storage::disk('public')->delete($product->foto);
        }

        $product->forceDelete();

        return redirect()->back()->with('success', 'Producto eliminado correctamente.');
    }
}
