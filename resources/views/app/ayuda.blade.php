@extends('layouts.app')

@section('title', 'Ayuda y Soporte')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8 text-center md:text-left">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Centro de Ayuda</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Encuentra respuestas rápidas o contacta a nuestro equipo técnico.</p>
    </div>

    <!-- PREGUNTAS FRECUENTES (ACORDEÓN CON ALPINE) -->
    <div class="space-y-4 mb-10">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <i data-lucide="help-circle" class="w-5 h-5 text-barber-accent"></i>
            Preguntas Frecuentes
        </h3>

        <!-- Pregunta 1 -->
        <div x-data="{ open: false }" class="bg-white dark:bg-barber-card-dark rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden shadow-sm">
            <button @click="open = !open" class="w-full px-5 py-4 flex justify-between items-center text-left hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="font-medium text-slate-900 dark:text-white">¿Cómo agendo una cita para un cliente nuevo?</span>
                <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" x-collapse class="px-5 pb-4 text-sm text-slate-600 dark:text-slate-400">
                Primero debes ir a la sección de <strong>Clientes</strong>, registrar a la persona con su nombre y teléfono, y guardar. Luego ve a <strong>Agendar Cita</strong> y podrás buscarlo en la lista.
            </div>
        </div>

        <!-- Pregunta 2 -->
        <div x-data="{ open: false }" class="bg-white dark:bg-barber-card-dark rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden shadow-sm">
            <button @click="open = !open" class="w-full px-5 py-4 flex justify-between items-center text-left hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="font-medium text-slate-900 dark:text-white">¿Cómo actualizo el stock de mis productos?</span>
                <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" x-collapse class="px-5 pb-4 text-sm text-slate-600 dark:text-slate-400">
                Ve a <strong>Gastos</strong>, haz clic en "Registrar Gasto", selecciona "Compra de Mercancía" y elige el producto. Al guardar el costo, el sistema sumará automáticamente la cantidad al inventario.
            </div>
        </div>

        <!-- Pregunta 3 -->
        <div x-data="{ open: false }" class="bg-white dark:bg-barber-card-dark rounded-2xl border border-slate-200 dark:border-slate-700/50 overflow-hidden shadow-sm">
            <button @click="open = !open" class="w-full px-5 py-4 flex justify-between items-center text-left hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                <span class="font-medium text-slate-900 dark:text-white">¿Cómo cierro la caja al final del día?</span>
                <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" x-collapse class="px-5 pb-4 text-sm text-slate-600 dark:text-slate-400">
                Ve a la sección de <strong>Cierre de Caja</strong>. Ahí verás el resumen exacto de ingresos, egresos y el balance neto del día. Puedes imprimirlo o tomar captura para tus registros.
            </div>
        </div>
    </div>

    <!-- TARJETA DE CONTACTO DIRECTO -->
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-3xl p-8 border border-blue-200 dark:border-blue-800 text-center flex flex-col items-center">
        <div class="w-16 h-16 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center shadow-sm mb-4">
            <i data-lucide="message-circle" class="w-8 h-8 text-barber-accent"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">¿No encontraste lo que buscabas?</h3>
        <p class="text-slate-600 dark:text-slate-400 text-sm mb-6 max-w-md mx-auto">
            Si tienes un problema técnico, una duda sobre tu facturación o un error en el sistema, nuestro equipo está listo para ayudarte.
        </p>

        <a href="https://wa.me/50375227231?text=Hola,%20necesito%20soporte%20t%C3%A9cnico%20con%20Korteo.%20Mi%20barber%C3%ADa%20es:%20{{ urlencode(auth()->user()->barbershop->nombre ?? '') }}%20y%20mi%20usuario%20es:%20{{ urlencode(auth()->user()->name) }}"
            target="_blank"
            class="inline-flex items-center justify-center gap-2 bg-barber-accent hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-md hover:shadow-lg active:scale-95">
            <i data-lucide="life-buoy" class="w-5 h-5"></i>
            Contactar a Soporte Técnico
        </a>
    </div>
</div>
@endsection