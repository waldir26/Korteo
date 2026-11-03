<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Compartimos la variable $productosBajoStock con TODAS las vistas del sistema
        View::composer('*', function ($view) {
            if (Auth::check() && Auth::user()->barbershop_id) {
                $count = Product::where('activo', true)
                    ->whereColumn('stock', '<=', 'stock_minimo')
                    ->count();
                $view->with('productosBajoStock', $count);
            } else {
                $view->with('productosBajoStock', 0);
            }
        });
    }
}
