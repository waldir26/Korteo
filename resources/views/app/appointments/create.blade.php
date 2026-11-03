@extends('layouts.app')

@section('title', 'Agendar Cita')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Agendar Nueva Cita</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Selecciona el cliente, el servicio y el barbero.</p>
    </div>

    <!-- AVISO PARA CLIENTES NUEVOS (CON BOTÓN DE CERRAR) -->
    <div x-data="{ showInfo: true }" class="mb-6">
        <div x-show="showInfo" x-transition class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4 flex items-start gap-3 transition-colors duration-300 relative">
            <div class="p-1.5 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-lg shrink-0 mt-0.5">
                <i data-lucide="info" class="w-5 h-5"></i>
            </div>
            <div class="pr-6">
                <p class="text-sm text-blue-800 dark:text-blue-200 font-medium">
                    ¿El cliente es nuevo?
                </p>
                <p class="text-sm text-blue-600 dark:text-blue-300 mt-1">
                    Si no aparece en la lista, primero debes registrarlo en la sección de
                    <a href="{{ route('app.clientes.index') }}" class="font-bold underline hover:text-blue-800 dark:hover:text-white transition-colors">Clientes</a>
                    para poder agendarle una cita. Recuerda que también debes tener creados a los
                    <a href="{{ route('app.barberos.index') }}" class="font-bold underline hover:text-blue-800 dark:hover:text-white transition-colors">Barberos</a>
                    y los
                    <a href="{{ route('app.servicios.index') }}" class="font-bold underline hover:text-blue-800 dark:hover:text-white transition-colors">Servicios</a>.
                </p>
            </div>
            <button x-on:click="showInfo = false" class="absolute top-4 right-4 text-blue-400 hover:text-blue-600 dark:hover:text-blue-200">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div x-show="!showInfo" style="display: none;">
            <button x-on:click="showInfo = true" class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center gap-1">
                <i data-lucide="help-circle" class="w-4 h-4"></i>
                ¿Necesitas ayuda para agendar?
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-barber-card-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-5 sm:p-8">
        <form action="{{ route('app.appointments.store') }}" method="POST" autocomplete="off"
            x-data="{
                search: '',
                selectedClient: null,
                clients: {{ $clients->toJson() }},
                get filteredClients() {
                    if (this.search === '') return [];
                    let query = this.search.toLowerCase();
                    return this.clients.filter(c => 
                        c.nombre.toLowerCase().includes(query) || 
                        (c.telefono && c.telefono.toLowerCase().includes(query))
                    ).slice(0, 5);
                },
                
                // Variables para Dropdown de Barbero
                openBarber: false,
                selectedBarberId: '{{ auth()->user()->hasRole('barbero') ? auth()->user()->barber->id : old('barber_id') }}',
                selectedBarberName: '{{ auth()->user()->hasRole('barbero') ? auth()->user()->name : (old('barber_id') ? $barbers->firstWhere('id', old('barber_id'))->nombre : '') }}',
                barbers: {{ $barbers->toJson() }},

                // Variables para Dropdown de Servicio
                openService: false,
                selectedServiceId: '{{ old('service_id') }}',
                selectedServiceName: '{{ old('service_id') ? $services->firstWhere('id', old('service_id'))->nombre : '' }}',
                services: {{ $services->toJson() }}
            }">
            @csrf

            <input type="hidden" id="check-route" value="{{ route('app.appointments.check') }}">

            @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-2 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                <!-- Buscador de Cliente -->
                <div class="md:col-span-2 relative">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Cliente *</label>
                    <input type="hidden" name="client_id" :value="selectedClient ? selectedClient.id : ''">

                    <template x-if="selectedClient">
                        <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border-2 border-barber-accent">
                            <div>
                                <p class="font-bold text-slate-900 dark:text-white text-base" x-text="selectedClient.nombre"></p>
                                <p class="text-sm text-slate-500 mt-0.5" x-text="selectedClient.telefono || 'Sin teléfono'"></p>
                            </div>
                            <button type="button" x-on:click="selectedClient = null; search = ''" class="p-2 bg-white dark:bg-slate-700 rounded-full shadow-sm text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/20 transition-colors">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </template>

                    <template x-if="!selectedClient">
                        <div class="relative">
                            <input type="text" x-model="search" placeholder="Escribe el nombre o teléfono..." class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3.5 focus:ring-2 focus:ring-barber-accent text-base">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="search" class="w-5 h-5"></i>
                            </div>

                            <div x-show="search.length > 0" class="absolute z-20 w-full mt-2 bg-white dark:bg-barber-card-dark border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-60 overflow-y-auto overflow-hidden">
                                <template x-for="client in filteredClients" :key="client.id">
                                    <div x-on:click="selectedClient = client; search = ''" class="p-4 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer border-b border-slate-100 dark:border-slate-700/50 last:border-0 transition-colors">
                                        <p class="font-bold text-slate-900 dark:text-white text-base" x-text="client.nombre"></p>
                                        <p class="text-sm text-slate-500 mt-0.5" x-text="client.telefono || 'Sin teléfono'"></p>
                                    </div>
                                </template>
                                <div x-show="filteredClients.length === 0" class="p-4 text-sm text-slate-500 text-center">
                                    No se encontraron clientes.
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- SELECTOR DE BARBERO -->
                @role('admin')
                <!-- Si es dueño, ve el dropdown normal -->
                <div class="relative" @click.away="openBarber = false">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Barbero *</label>
                    <input type="hidden" name="barber_id" :value="selectedBarberId" required>

                    <button type="button" @click="openBarber = !openBarber" class="w-full flex justify-between items-center rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3.5 focus:ring-2 focus:ring-barber-accent text-base transition-colors">
                        <span x-text="selectedBarberId ? selectedBarberName : '— Seleccione un barbero —'" :class="!selectedBarberId ? 'text-slate-500' : ''"></span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform" :class="openBarber ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="openBarber" x-transition.opacity x-cloak class="absolute z-30 w-full mt-2 bg-white dark:bg-barber-card-dark border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                        <template x-for="barber in barbers" :key="barber.id">
                            <button type="button" @click="selectedBarberId = barber.id; selectedBarberName = barber.nombre; openBarber = false" class="w-full text-left px-4 py-3 text-base text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                                <span x-text="barber.nombre"></span>
                            </button>
                        </template>
                    </div>
                </div>
                @else
                <!-- Si es barbero, solo ve su nombre bloqueado -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Barbero Asignado</label>
                    <input type="hidden" name="barber_id" value="{{ auth()->user()->barber->id ?? '' }}">
                    <div class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-4 py-3.5 font-bold flex items-center gap-2 cursor-not-allowed">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                        {{ auth()->user()->name }}
                    </div>
                </div>
                @endrole

                <!-- Dropdown Personalizado de Servicio -->
                <div class="relative" @click.away="openService = false">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Servicio *</label>

                    <!-- Input oculto para enviar al backend -->
                    <input type="hidden" name="service_id" :value="selectedServiceId" required>

                    <button type="button" @click="openService = !openService" class="w-full flex justify-between items-center rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3.5 focus:ring-2 focus:ring-barber-accent text-base transition-colors">
                        <span x-text="selectedServiceId ? selectedServiceName : '— Seleccione un servicio —'" :class="!selectedServiceId ? 'text-slate-500' : ''"></span>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform" :class="openService ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="openService" x-transition.opacity x-cloak class="absolute z-30 w-full mt-2 bg-white dark:bg-barber-card-dark border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                        <template x-for="service in services" :key="service.id">
                            <button type="button" @click="selectedServiceId = service.id; selectedServiceName = service.nombre + ' ($' + service.precio + ')'; openService = false" class="w-full text-left px-4 py-3 text-base text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors border-b border-slate-100 dark:border-slate-700/50 last:border-0 flex justify-between items-center">
                                <span x-text="service.nombre"></span>
                                <span class="text-sm font-bold text-green-600 dark:text-green-400" x-text="'$' + service.precio"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Fecha (Flatpickr) -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Fecha *</label>
                    <input type="text" name="fecha" id="fecha-cita" value="{{ old('fecha', date('Y-m-d')) }}" required placeholder="Seleccione fecha" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3.5 focus:ring-2 focus:ring-barber-accent text-base">
                </div>

                <!-- Hora (Flatpickr) -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Hora *</label>
                    <input type="text" name="hora" id="hora-cita" value="{{ old('hora') }}" required placeholder="--:--" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3.5 focus:ring-2 focus:ring-barber-accent text-base">
                    <div id="availability-alert" class="mt-2 text-sm font-medium hidden"></div>
                </div>

                <!-- Notas -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Notas (Opcional)</label>
                    <input type="text" name="notas" value="{{ old('notas') }}" placeholder="Ej. Llegará 10 min tarde..." class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3.5 focus:ring-2 focus:ring-barber-accent text-base">
                </div>
            </div>

            <!-- BOTONES ADAPTADOS A MÓVIL -->
            <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3 pt-5 border-t border-slate-200 dark:border-slate-700/50">
                <a href="{{ route('app.appointments.index') }}" class="w-full sm:w-auto px-6 py-3.5 sm:py-2.5 text-sm font-bold sm:font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-100 sm:bg-transparent dark:bg-slate-800 sm:dark:bg-transparent rounded-xl sm:rounded-none text-center transition-colors">
                    Cancelar
                </a>
                <button type="submit" id="btn-submit" class="w-full sm:w-auto bg-barber-accent hover:bg-blue-600 text-white font-bold sm:font-medium py-3.5 sm:py-2.5 px-6 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i data-lucide="calendar-check" class="w-5 h-5 sm:w-4 sm:h-4"></i>
                    Agendar Cita
                </button>
            </div>
        </form>
    </div>
</div>
@endsection