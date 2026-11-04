<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
use App\Models\Product;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Forzar a Vite a usar la carpeta build en producción
        if (app()->environment('production')) {
            Vite::useBuildDirectory('build');
        }

        // 2. Compartimos la variable $productosBajoStock con TODAS las vistas del sistema
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
