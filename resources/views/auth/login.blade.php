<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar sesión — Korteo</title>
    <!-- Etiquetas para PWA (Instalar como App) -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563EB">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Korteo">
    <link rel="icon" type="image/png" href="{{ asset('logos/favicon.png') }}">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- FontAwesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-white dark:bg-barber-dark flex flex-col" x-data="{ recoverModal: false }">

    <!-- ENCABEZADO CON FORMA DE OLA (SVG) -->
    <div class="relative w-full h-[38vh] min-h-[280px] bg-gradient-to-br from-blue-600 to-indigo-700 flex flex-col items-center justify-center overflow-hidden shrink-0">

        <!-- Adornos de fondo -->
        <div class="absolute top-[-20%] left-[-10%] w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-40 h-40 bg-blue-400/20 rounded-full blur-xl pointer-events-none"></div>

        <!-- Logo -->
        <div class="relative z-10 flex flex-col items-center mt-[-40px]">
            <img src="{{ asset('logos/logo-claro.png') }}" alt="Korteo" class="h-20 w-auto mb-1 drop-shadow-md">
            <p class="text-blue-100 text-xs font-medium tracking-wide">Gestión de barberías</p>
        </div>

        <!-- EL TRUCO DE LA OLA: SVG posicionado al fondo de este contenedor -->
        <div class="absolute bottom-[-1px] left-0 w-full overflow-hidden leading-none z-20">
            <svg class="relative block w-[calc(100%+1.3px)] h-[60px] sm:h-[80px]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,60 C300,120 600,0 1200,60 L1200,120 L0,120 Z"
                    class="fill-white dark:fill-barber-dark"></path>
            </svg>
        </div>
    </div>

    <!-- ÁREA DEL FORMULARIO -->
    <div class="flex-1 bg-white dark:bg-barber-dark w-full max-w-md mx-auto px-6 pt-2 pb-6 relative z-30">

        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white mb-8 text-center">¡Bienvenido de nuevo!</h2>

        <div>
            @if ($errors->any())
            @if (str_contains($errors->first(), 'suspendida') || str_contains($errors->first(), 'cancelada'))
            <div class="mb-6 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 px-4 py-3 text-sm">
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation mt-1 shrink-0"></i>
                    <div>
                        <p class="font-bold mb-1">Tu barbería está suspendida</p>
                        <p class="mb-3 text-xs leading-relaxed">Para reactivar tu acceso, comunícate con soporte:</p>
                        <a href="https://wa.me/50375227231?text=Hola,%20mi%20barbería%20está%20suspendida%20y%20necesito%20reactivar%20mi%20acceso%20a%20Korteo." target="_blank" class="inline-flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1DA851] text-white font-medium py-2 px-4 rounded-xl transition-colors w-full">
                            Hablar con Soporte
                        </a>
                    </div>
                </div>
            </div>
            @else
            <div class="mb-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 px-4 py-3 text-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $errors->first() }}
            </div>
            @endif
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <!-- INPUT DE USUARIO -->
                <div class="relative">
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus autocomplete="username"
                        placeholder="Usuario"
                        class="w-full rounded-2xl border-0 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 px-5 py-4 focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-barber-card-dark transition-all text-base font-medium placeholder-slate-400">
                </div>

                <!-- INPUT DE CONTRASEÑA -->
                <div x-data="{ showPassword: false }" class="relative">
                    <input id="password" name="password" x-bind:type="showPassword ? 'text' : 'password'" required autocomplete="current-password"
                        placeholder="Contraseña"
                        class="w-full rounded-2xl border-0 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 pl-5 pr-12 py-4 focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-barber-card-dark transition-all text-base font-medium placeholder-slate-400">

                    <button type="button" x-on:click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-500 transition-colors">
                        <i class="fa-regular fa-eye" x-show="!showPassword"></i>
                        <i class="fa-regular fa-eye-slash" x-show="showPassword" style="display: none;"></i>
                    </button>
                </div>

                <!-- RECORDAR Y OLVIDAR CONTRASEÑA -->
                <div class="flex items-center justify-between pt-1 pb-2">
                    <label class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 cursor-pointer font-medium">
                        <input type="checkbox" name="remember" value="1" class="w-4 h-4 rounded-full border-slate-300 text-blue-600 focus:ring-blue-500">
                        Recordarme
                    </label>

                    <button type="button" @click="recoverModal = true" class="text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">
                        ¿Olvidaste tu clave?
                    </button>
                </div>

                <!-- BOTÓN DE INICIAR SESIÓN -->
                <button type="submit"
                    class="w-full bg-transparent border-2 border-blue-600 text-blue-600 hover:bg-blue-50 font-bold text-lg rounded-2xl py-3.5 mt-2 transition-all active:scale-[0.98]">
                    Iniciar sesión
                </button>
            </form>

            <p class="text-center text-xs font-medium text-slate-400 mt-8">© {{ date('Y') }} Korteo</p>
        </div>
    </div>

    <!-- MODAL DE RECUPERAR CONTRASEÑA -->
    <div x-show="recoverModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="recoverModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="recoverModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="recoverModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-barber-card-dark text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700/50">

                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-key text-blue-600"></i>
                        Recuperar Contraseña
                    </h3>
                    <button type="button" @click="recoverModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 bg-white dark:bg-slate-700 rounded-full w-8 h-8 flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="px-6 py-6">
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 text-center">
                        Para restablecer tu contraseña, haz clic en el botón de abajo. Se abrirá WhatsApp con un mensaje automático.
                    </p>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 text-center">
                        Incluye tu <strong>nombre de usuario</strong> y el <strong>nombre de tu barbería</strong>.
                    </p>

                    <a href="https://wa.me/50375227231?text=Hola,%20solicito%20ayuda%20para%20recuperar%20mi%20contrase%C3%B1a%20de%20Korteo.%20Mi%20usuario%20es:%20[ESCRIBE%20TU%20USUARIO]%20y%20mi%20barber%C3%ADa%20se%20llama:%20[ESCRIBE%20TU%20BARBER%C3%8DA]"
                        target="_blank"
                        class="w-full flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#1DA851] text-white font-bold rounded-xl py-3.5 px-4 transition-colors duration-200 shadow-md">
                        <i class="fa-brands fa-whatsapp text-2xl"></i>
                        Solicitar por WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para activar la PWA -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js').then(function(registration) {
                    console.log('ServiceWorker registrado con éxito:', registration.scope);
                }, function(err) {
                    console.log('Fallo al registrar el ServiceWorker:', err);
                });
            });
        }
    </script>
</body>

</html>