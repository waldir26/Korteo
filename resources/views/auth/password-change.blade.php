<!DOCTYPE html>
<html lang="es" class="{{ request()->cookie('theme') === 'dark' ? 'dark' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cambiar contraseña — Korteo</title>
    <link rel="icon" type="image/png" href="{{ asset('logos/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-barber-light dark:bg-barber-dark flex items-center justify-center p-4 transition-colors duration-300">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <!-- Logo Modo Claro -->
            <img src="{{ asset('logos/logo-claro.png') }}" alt="Korteo" class="h-16 w-auto block dark:hidden mx-auto">
            <!-- Logo Modo Oscuro -->
            <img src="{{ asset('logos/logo-oscuro.png') }}" alt="Korteo" class="h-16 w-auto hidden dark:block mx-auto">
            <p class="text-slate-500 dark:text-slate-400 mt-4">Debes cambiar tu contraseña para continuar</p>
        </div>

        <div class="bg-white dark:bg-barber-card-dark rounded-2xl shadow-xl p-8 border border-slate-200 dark:border-slate-700/50">
            @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 text-sm">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('password.change.post') }}" class="space-y-5" autocomplete="off">
                @csrf

                <!-- Contraseña actual -->
                <div x-data="{ showCurrent: false }">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Contraseña actual</label>
                    <div class="relative">
                        <input name="current_password" :type="showCurrent ? 'text' : 'password'" required autocomplete="new-password"
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-barber-accent">
                        <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 px-3 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                            <svg x-show="!showCurrent" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showCurrent" style="display: none;" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Nueva contraseña -->
                <div x-data="{ showNew: false }">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nueva contraseña</label>
                    <div class="relative">
                        <input name="password" :type="showNew ? 'text' : 'password'" required autocomplete="new-password"
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-barber-accent">
                        <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 px-3 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                            <svg x-show="!showNew" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showNew" style="display: none;" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Mínimo 8 caracteres, una mayúscula y un número.</p>
                </div>

                <!-- Confirmar contraseña -->
                <div x-data="{ showConfirm: false }">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Confirmar nueva contraseña</label>
                    <div class="relative">
                        <input name="password_confirmation" :type="showConfirm ? 'text' : 'password'" required autocomplete="new-password"
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-barber-accent">
                        <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 px-3 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                            <svg x-show="!showConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showConfirm" style="display: none;" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-barber-accent hover:bg-blue-600 text-white font-semibold rounded-lg py-2.5 transition-colors duration-200 mt-2">
                    Cambiar contraseña
                </button>
            </form>
        </div>
    </div>
</body>

</html>