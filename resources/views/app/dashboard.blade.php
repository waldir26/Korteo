@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@vite(['resources/js/dashboard.js'])

<div class="bg-white dark:bg-barber-card-dark rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-5 md:p-8 transition-colors duration-300">

    <!-- SALUDO -->
    <div class="mb-6 md:mb-8 text-center md:text-left">
        <h2 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">
            @php
            $hora = now()->hour;
            if ($hora < 12) { $saludo='Buenos días' ; }
                elseif ($hora < 18) { $saludo='Buenas tardes' ; }
                else { $saludo='Buenas noches' ; }
                @endphp
                {{ $saludo }}, {{ auth()->user()->name }}
                </h2>
                <p class="text-sm md:text-base text-slate-500 dark:text-slate-400 mt-1">
                    Bienvenido al panel de <strong>{{ auth()->user()->barbershop->nombre }}</strong>.
                </p>
    </div>

    <!-- AVISO DE LOGO -->
    @if(!auth()->user()->barbershop->logo)
    <div class="mb-6 md:mb-8 rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4 md:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition-colors duration-300">
        <div class="flex items-start gap-3 md:gap-4">
            <div class="p-2.5 md:p-3 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-xl shrink-0">
                <i data-lucide="image" class="w-5 h-5 md:w-6 md:h-6"></i>
            </div>
            <div>
                <h3 class="text-sm md:text-base font-semibold text-blue-900 dark:text-blue-100">¡Personaliza tu barbería!</h3>
                <p class="text-xs md:text-sm text-blue-700 dark:text-blue-300 mt-0.5">Aún no has subido el logo de tu negocio.</p>
            </div>
        </div>
        <a href="{{ route('app.settings.edit') }}" class="w-full sm:w-auto shrink-0 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 text-sm">
            <i data-lucide="upload" class="w-4 h-4"></i> Subir Logo
        </a>
    </div>
    @endif

    <!-- TARJETAS DE RESUMEN (Estilo App) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6 mb-8">
        <!-- Citas -->
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/50 flex items-center md:flex-col md:items-start gap-4 md:gap-0 transition-transform hover:scale-[1.02]">
            <div class="p-3 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-xl shrink-0 md:mb-3">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 md:mb-1">Citas de Hoy</h3>
                <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $citasHoy }}</p>
            </div>
        </div>

        <!-- Ingresos -->
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/50 flex items-center md:flex-col md:items-start gap-4 md:gap-0 transition-transform hover:scale-[1.02]">
            <div class="p-3 bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400 rounded-xl shrink-0 md:mb-3">
                <i data-lucide="dollar-sign" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 md:mb-1">Ingresos del Día</h3>
                <p class="text-3xl font-bold text-slate-900 dark:text-white">${{ number_format($ingresosHoy, 2) }}</p>
            </div>
        </div>

        <!-- Barberos -->
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/50 flex items-center md:flex-col md:items-start gap-4 md:gap-0 transition-transform hover:scale-[1.02]">
            <div class="p-3 bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 rounded-xl shrink-0 md:mb-3">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 md:mb-1">Barberos Activos</h3>
                <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $barberosActivos }}</p>
            </div>
        </div>
    </div>

    <!-- PRÓXIMAS CITAS Y ACCESOS RÁPIDOS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        <!-- Próximas Citas -->
        <div class="lg:col-span-2 bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/50">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="clock" class="w-5 h-5 text-barber-accent"></i>
                    Próximas Citas de Hoy
                </h3>
                <a href="{{ route('app.appointments.index') }}" class="text-sm font-medium text-barber-accent hover:underline">Ver todas</a>
            </div>

            <div class="space-y-3">
                @forelse($proximasCitas ?? [] as $cita)
                <div class="bg-white dark:bg-barber-card-dark rounded-xl p-3 border border-slate-200 dark:border-slate-700/50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 font-bold px-3 py-1.5 rounded-lg text-sm text-center min-w-[70px]">
                            {{ \Carbon\Carbon::parse($cita->fecha_hora)->format('h:i A') }}
                        </div>
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white text-sm">{{ $cita->client?->nombre ?? 'Cliente eliminado' }}</p>
                            <p class="text-xs text-slate-500">{{ $cita->service?->nombre ?? 'Servicio' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Barbero</p>
                        <p class="font-medium text-slate-700 dark:text-slate-300 text-sm">{{ $cita->barber?->nombre }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-6">
                    <p class="text-sm text-slate-500 dark:text-slate-400">No hay más citas pendientes para hoy.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 border border-slate-100 dark:border-slate-700/50 flex flex-col justify-center gap-3">
            <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white mb-1">Accesos Rápidos</h3>

            <a href="{{ route('app.appointments.create') }}" class="flex items-center gap-3 p-3 bg-white dark:bg-barber-card-dark rounded-xl border border-slate-200 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
                <div class="p-2 bg-blue-100 dark:bg-blue-500/20 text-blue-600 rounded-lg">
                    <i data-lucide="calendar-plus" class="w-5 h-5"></i>
                </div>
                <span class="font-medium text-slate-800 dark:text-slate-200">Agendar Cita</span>
            </a>

            <a href="{{ route('app.cash.ventas') }}" class="flex items-center gap-3 p-3 bg-white dark:bg-barber-card-dark rounded-xl border border-slate-200 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
                <div class="p-2 bg-green-100 dark:bg-green-500/20 text-green-600 rounded-lg">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                </div>
                <span class="font-medium text-slate-800 dark:text-slate-200">Registrar Venta</span>
            </a>

            <a href="{{ route('app.clientes.index') }}" class="flex items-center gap-3 p-3 bg-white dark:bg-barber-card-dark rounded-xl border border-slate-200 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm">
                <div class="p-2 bg-amber-100 dark:bg-amber-500/20 text-amber-600 rounded-lg">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                </div>
                <span class="font-medium text-slate-800 dark:text-slate-200">Nuevo Cliente</span>
            </a>
        </div>
    </div>
    <!-- GRÁFICOS Y TOP (Apilados en celular) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Gráfico -->
        <div class="lg:col-span-2 bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 md:p-6 border border-slate-100 dark:border-slate-700/50">
            <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white mb-4">Ingresos de los últimos 7 días</h3>
            <div class="w-full h-56 md:h-64">
                <canvas id="ingresosChart"
                    data-fechas="{{ json_encode($fechasSemana) }}"
                    data-ingresos="{{ json_encode($ingresosSemana) }}">
                </canvas>
            </div>
        </div>

        <!-- Top Barberos -->
        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-5 md:p-6 border border-slate-100 dark:border-slate-700/50">
            <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white mb-4">Barberos Destacados</h3>
            <div class="space-y-3 md:space-y-4">
                @forelse($topBarberos as $index => $top)
                <div class="flex items-center justify-between bg-white dark:bg-slate-900/50 md:bg-transparent md:dark:bg-transparent p-3 md:p-0 rounded-xl md:rounded-none border border-slate-100 dark:border-slate-700/50 md:border-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-sm font-bold text-slate-500 dark:text-slate-400">
                            {{ $index + 1 }}
                        </div>
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $top->barber->nombre }}</span>
                    </div>
                    <span class="text-xs md:text-sm font-bold text-barber-accent bg-blue-50 dark:bg-blue-900/30 md:bg-transparent px-2 py-1 md:p-0 rounded-md">{{ $top->total }} citas</span>
                </div>
                @empty
                <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-4">No hay datos de esta semana.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection