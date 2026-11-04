<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CashCloseExport;

class CashController extends Controller
{
    public function indexVentas(Request $request)
    {
        $fechaInicio = Carbon::today()->toDateString();
        $fechaFin = Carbon::today()->toDateString();
        $rango = $request->get('rango');

        if ($rango) {
            $fechas = explode(' a ', $rango);
            if (count($fechas) == 2) {
                $fechaInicio = $fechas[0];
                $fechaFin = $fechas[1];
            } else {
                $fechaInicio = $rango;
                $fechaFin = $rango;
            }
        }

        $ventas = Transaction::where('tipo', 'ingreso_producto')
            ->whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $fechaFin)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalVentas = $ventas->sum('monto');

        $products = Product::where('activo', true)
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->get();

        return view('app.cash.ventas', compact('ventas', 'totalVentas', 'products', 'rango'));
    }

    public function indexGastos(Request $request)
    {
        $fechaInicio = Carbon::today()->toDateString();
        $fechaFin = Carbon::today()->toDateString();
        $rango = $request->get('rango');
        $tipo = $request->get('tipo');

        if ($rango) {
            $fechas = explode(' a ', $rango);
            if (count($fechas) == 2) {
                $fechaInicio = $fechas[0];
                $fechaFin = $fechas[1];
            } else {
                $fechaInicio = $rango;
                $fechaFin = $rango;
            }
        }

        $query = Transaction::where('tipo', 'gasto')
            ->whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $fechaFin);

        if ($tipo === 'compra') {
            $query->where('descripcion', 'like', 'Compra:%');
        } elseif ($tipo === 'normal') {
            $query->where('descripcion', 'not like', 'Compra:%');
        }

        $gastos = $query->orderBy('created_at', 'desc')->get();
        $totalGastos = $gastos->sum('monto');

        $products = Product::where('activo', true)->orderBy('nombre')->get();

        return view('app.cash.gastos', compact('gastos', 'totalGastos', 'products', 'rango', 'tipo', 'fechaInicio', 'fechaFin'));
    }

    public function indexPagos(Request $request)
    {
        $fechaInicio = Carbon::today()->toDateString();
        $fechaFin = Carbon::today()->toDateString();
        $rango = $request->get('rango');
        $barberId = $request->get('barber_id');

        if ($rango) {
            $fechas = explode(' a ', $rango);
            if (count($fechas) == 2) {
                $fechaInicio = $fechas[0];
                $fechaFin = $fechas[1];
            } else {
                $fechaInicio = $rango;
                $fechaFin = $rango;
            }
        }

        $query = Transaction::where('tipo', 'pago_barbero')
            ->whereDate('created_at', '>=', $fechaInicio)
            ->whereDate('created_at', '<=', $fechaFin);

        if ($barberId) {
            $barber = Barber::find($barberId);
            if ($barber) {
                $query->where('descripcion', 'like', '%' . $barber->nombre . '%');
            }
        }

        $pagos = $query->orderBy('created_at', 'desc')->get();
        $totalPagos = $pagos->sum('monto');
        $barbers = Barber::where('activo', true)->orderBy('nombre')->get();

        return view('app.cash.pagos', compact('pagos', 'totalPagos', 'barbers', 'rango', 'barberId', 'fechaInicio', 'fechaFin'));
    }

    public function indexCierre()
    {
        $hoy = Carbon::today();

        $totalServicios = Transaction::whereDate('created_at', $hoy)->where('tipo', 'ingreso_servicio')->sum('monto');
        $totalVentas = Transaction::whereDate('created_at', $hoy)->where('tipo', 'ingreso_producto')->sum('monto');
        $totalGastos = Transaction::whereDate('created_at', $hoy)->where('tipo', 'gasto')->sum('monto');
        $totalPagos = Transaction::whereDate('created_at', $hoy)->where('tipo', 'pago_barbero')->sum('monto');

        $ingresosTotales = $totalServicios + $totalVentas;
        $egresosTotales = $totalGastos + $totalPagos;
        $balanceFinal = $ingresosTotales - $egresosTotales;

        return view('app.cash.cierre', compact(
            'totalServicios',
            'totalVentas',
            'totalGastos',
            'totalPagos',
            'ingresosTotales',
            'egresosTotales',
            'balanceFinal'
        ));
    }

    // ==========================================
    // EXPORTACIÓN DE CIERRE DE CAJA (PDF / EXCEL)
    // ==========================================

    public function exportCierrePdf()
    {
        $hoy = Carbon::today();

        $totalServicios = Transaction::whereDate('created_at', $hoy)->where('tipo', 'ingreso_servicio')->sum('monto');
        $totalVentas = Transaction::whereDate('created_at', $hoy)->where('tipo', 'ingreso_producto')->sum('monto');
        $totalGastos = Transaction::whereDate('created_at', $hoy)->where('tipo', 'gasto')->sum('monto');
        $totalPagos = Transaction::whereDate('created_at', $hoy)->where('tipo', 'pago_barbero')->sum('monto');
        $ingresosTotales = $totalServicios + $totalVentas;
        $egresosTotales = $totalGastos + $totalPagos;
        $balanceFinal = $ingresosTotales - $egresosTotales;

        $data = compact('totalServicios', 'totalVentas', 'totalGastos', 'totalPagos', 'ingresosTotales', 'egresosTotales', 'balanceFinal');
        $pdf = Pdf::loadView('pdf.cierre-caja', $data);
        return $pdf->download('Cierre_Caja_' . date('d-m-Y') . '.pdf');
    }

    public function exportCierreExcel()
    {
        return Excel::download(new CashCloseExport(), 'Cierre_Caja_' . date('d-m-Y') . '.xlsx');
    }

    // ==========================================
    // ACCIONES DE CAJA (Guardar / Eliminar)
    // ==========================================

    public function storeIngreso(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
        ]);

        $totalVenta = 0;
        $descripcionVenta = [];

        try {
            DB::beginTransaction();

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->stock < $item['cantidad']) {
                    throw new \Exception("No hay suficiente stock de: " . $product->nombre);
                }
                $totalVenta += ($product->precio * $item['cantidad']);
                $descripcionVenta[] = $product->nombre . ' (x' . $item['cantidad'] . ')';
                $product->decrement('stock', $item['cantidad']);
            }

            Transaction::create([
                'tipo' => 'ingreso_producto',
                'descripcion' => implode(', ', $descripcionVenta),
                'monto' => $totalVenta,
            ]);

            DB::commit();
            return redirect()->route('app.cash.ventas')->with('success', 'Venta registrada y stock actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function storeGasto(Request $request)
    {
        $validated = $request->validate([
            'tipo' => ['required', 'in:gasto,pago_barbero,compra_producto'],
            'descripcion' => ['required', 'string', 'max:255'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'concepto' => ['nullable', 'string', 'max:255'],
            'product_id' => ['nullable', 'exists:products,id'],
            'cantidad' => ['nullable', 'integer', 'min:1'],
        ]);

        try {
            DB::beginTransaction();

            $descripcionFinal = $validated['descripcion'];
            $tipoFinal = $validated['tipo'];

            if ($validated['tipo'] === 'pago_barbero' && !empty($validated['concepto'])) {
                $descripcionFinal = $validated['descripcion'] . ' (' . $validated['concepto'] . ')';
            }

            if ($validated['tipo'] === 'compra_producto') {
                if (empty($validated['product_id']) || empty($validated['cantidad'])) {
                    throw new \Exception("Debes seleccionar un producto y una cantidad.");
                }

                $product = Product::findOrFail($validated['product_id']);

                if ($product->created_at->toDateString() === now()->toDateString()) {
                    $product->stock = $validated['cantidad'];
                    $product->save();
                } else {
                    $product->increment('stock', $validated['cantidad']);
                }

                $descripcionFinal = 'Compra: ' . $product->nombre . ' (x' . $validated['cantidad'] . ')';
                $tipoFinal = 'gasto';
            }

            Transaction::create([
                'tipo' => $tipoFinal,
                'descripcion' => $descripcionFinal,
                'monto' => $validated['monto'],
            ]);

            DB::commit();

            if ($validated['tipo'] === 'gasto' || $validated['tipo'] === 'compra_producto') {
                return redirect()->route('app.cash.gastos')->with('success', 'Movimiento registrado exitosamente.');
            } else {
                return redirect()->route('app.cash.pagos')->with('success', 'Pago a barbero registrado exitosamente.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Transaction $transaction)
    {
        $tipo = $transaction->tipo;
        $descripcion = $transaction->descripcion;

        try {
            DB::beginTransaction();

            if (str_starts_with($descripcion, 'Compra: ')) {
                if (preg_match('/^Compra:\s*(.*?)\s*\(x(\d+)\)$/', $descripcion, $matches)) {
                    $productNombre = $matches[1];
                    $cantidadComprada = (int)$matches[2];

                    $product = Product::where('nombre', $productNombre)->first();

                    if ($product && $product->created_at->toDateString() !== now()->toDateString()) {
                        $product->decrement('stock', $cantidadComprada);
                    }
                }
            }

            $transaction->delete();
            DB::commit();

            if ($tipo === 'ingreso_producto') {
                return redirect()->route('app.cash.ventas')->with('success', 'Movimiento eliminado.');
            } elseif ($tipo === 'gasto') {
                return redirect()->route('app.cash.gastos')->with('success', 'Movimiento eliminado y stock actualizado.');
            } else {
                return redirect()->route('app.cash.pagos')->with('success', 'Movimiento eliminado.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'No se pudo eliminar el movimiento: ' . $e->getMessage()]);
        }
    }
}
