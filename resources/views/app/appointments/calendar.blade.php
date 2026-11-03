@extends('layouts.app')

@section('title', 'Calendario de Citas')

@section('content')
<div class="max-w-7xl mx-auto" x-data="calendarApp()" x-init="init()">

    <!-- Header de la Página -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Calendario</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Visualiza y gestiona las citas de la barbería.</p>
    </div>

    <!-- SELECTOR DE VISTAS -->
    <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1 hide-scrollbar">
        <button @click="currentView = 'month'" :class="currentView === 'month' ? 'bg-barber-accent text-white shadow-md' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700'" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap">Mes</button>
        <button @click="currentView = 'week'" :class="currentView === 'week' ? 'bg-barber-accent text-white shadow-md' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700'" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap">Semana</button>
        <button @click="currentView = 'day'" :class="currentView === 'day' ? 'bg-barber-accent text-white shadow-md' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700'" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap">Día</button>
        <button @click="currentView = 'year'" :class="currentView === 'year' ? 'bg-barber-accent text-white shadow-md' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700'" class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap">Año</button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- TARJETA DEL CALENDARIO (Izquierda) -->
        <div class="lg:col-span-1 flex flex-col gap-6">

            <!-- VISTA MES -->
            <div x-show="currentView === 'month'" class="bg-white dark:bg-barber-card-dark rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 overflow-hidden flex flex-col">
                <div class="flex justify-between items-center p-5 border-b border-slate-100 dark:border-slate-700/50">
                    <button @click="prevMonth()" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors"><i class="fa-solid fa-chevron-left"></i></button>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white capitalize" x-text="monthNames[currentMonth] + ' ' + currentYear"></h3>
                    <button @click="nextMonth()" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
                <div class="p-5 flex-1">
                    <div class="grid grid-cols-7 mb-2 text-center">
                        <template x-for="day in ['L','M','M','J','V','S','D']">
                            <div class="text-xs font-bold text-slate-400 dark:text-slate-500" x-text="day"></div>
                        </template>
                    </div>
                    <div class="grid grid-cols-7 gap-y-2 text-center">
                        <template x-for="blank in blanks">
                            <div class="h-10"></div>
                        </template>
                        <template x-for="day in daysInMonth" :key="currentYear + '-' + currentMonth + '-' + day">
                            <button @click="selectDate(day)" class="h-10 w-10 mx-auto rounded-full flex items-center justify-center text-sm transition-all relative group"
                                :class="{
                                    'bg-barber-accent text-white font-bold shadow-md': isSelected(day) && currentView === 'month',
                                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium': !(isSelected(day) && currentView === 'month') && !isToday(day) && !isHoliday(day),
                                    'text-blue-600 dark:text-blue-400 font-bold border border-blue-200 dark:border-blue-800': isToday(day) && !(isSelected(day) && currentView === 'month'),
                                    'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 font-bold ring-1 ring-amber-300 dark:ring-amber-500/30': isHoliday(day) && !(isSelected(day) && currentView === 'month')
                                }">
                                <span x-text="day"></span>
                                <span x-show="hasEvents(day) && !(isSelected(day) && currentView === 'month')" class="absolute bottom-1 w-1 h-1 rounded-full bg-barber-accent"></span>
                                <template x-if="isHoliday(day) && !(isSelected(day) && currentView === 'month')">
                                    <i :class="getHoliday(day).icon" class="absolute top-0.5 right-0.5 text-[9px] text-amber-600 dark:text-amber-400 pointer-events-none"></i>
                                </template>
                            </button>
                        </template>
                    </div>
                </div>
                <div x-show="holidayInfo" x-transition x-cloak class="mx-5 mb-5 p-3 rounded-2xl border flex items-start gap-3 bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700/50">
                    <div class="p-2 rounded-xl shrink-0 bg-barber-accent text-white flex items-center justify-center shadow-sm">
                        <i :class="holidayInfo?.icon || 'fa-solid fa-calendar'" class="text-sm"></i>
                    </div>
                    <div class="min-w-0 flex items-center">
                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate" x-text="holidayInfo?.name"></p>
                    </div>
                </div>
            </div>

            <!-- VISTA SEMANA (Selector de Días) -->
            <div x-show="currentView === 'week'" style="display: none;" class="bg-white dark:bg-barber-card-dark rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 overflow-hidden flex flex-col p-5">
                <div class="flex justify-between items-center mb-4">
                    <button @click="prevWeek()" class="p-2 text-slate-500 hover:bg-slate-100 rounded-lg"><i class="fa-solid fa-chevron-left"></i></button>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white" x-text="'Semana del ' + formatWeekRange()"></h3>
                    <button @click="nextWeek()" class="p-2 text-slate-500 hover:bg-slate-100 rounded-lg"><i class="fa-solid fa-chevron-right"></i></button>
                </div>

                <div class="grid grid-cols-7 gap-1">
                    <template x-for="d in weekDays" :key="d.date">
                        <button @click="selectedDate = d.date; updateSelectedDate();" class="flex flex-col items-center justify-center p-2 rounded-xl transition-all"
                            :class="selectedDate === d.date ? 'bg-barber-accent text-white shadow-md' : 'hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400'">
                            <span class="text-[10px] font-medium uppercase mb-1" x-text="d.dayName"></span>
                            <span class="text-sm font-bold" x-text="d.dayNumber"></span>
                            <span x-show="hasEventsOnDate(d.date)" class="w-1 h-1 rounded-full bg-current mt-1"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- VISTA DÍA (Selector de Fecha) -->
            <div x-show="currentView === 'day'" style="display: none;" class="bg-white dark:bg-barber-card-dark rounded-3xl p-5 shadow-sm border border-slate-200 dark:border-slate-700/50">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Seleccionar día:</label>
                <input type="date" x-model="selectedDate" @change="updateSelectedDate()" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-barber-dark text-slate-800 dark:text-slate-100 px-4 py-3 focus:ring-2 focus:ring-barber-accent text-base">
            </div>

            <!-- VISTA AÑO (Lista de meses) -->
            <div x-show="currentView === 'year'" style="display: none;" class="bg-white dark:bg-barber-card-dark rounded-3xl p-5 shadow-sm border border-slate-200 dark:border-slate-700/50">
                <div class="flex justify-between items-center mb-4">
                    <button @click="currentYear--" class="p-2 text-slate-500 hover:bg-slate-100 rounded-lg"><i class="fa-solid fa-chevron-left"></i></button>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="currentYear"></h3>
                    <button @click="currentYear++" class="p-2 text-slate-500 hover:bg-slate-100 rounded-lg"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <template x-for="(month, index) in monthNames" :key="index">
                        <button @click="currentMonth = index; currentView = 'month'" class="p-3 text-sm font-medium rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300" x-text="month.substring(0, 3)"></button>
                    </template>
                </div>
            </div>

        </div>

        <!-- LISTA DE CITAS (DERECHA) -->
        <div class="lg:col-span-2">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-lg">
                    <i class="fa-solid fa-calendar-days text-lg"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white capitalize" x-text="getViewTitle()"></h3>
            </div>

            <!-- VISTA DÍA: AGENDA POR HORAS -->
            <div x-show="currentView === 'day'" style="display: none;" class="bg-white dark:bg-barber-card-dark rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700/50 overflow-hidden">
                <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    <template x-for="hour in dayHours" :key="hour.value">
                        <div class="flex min-h-[80px] group hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <!-- Columna de Hora -->
                            <div class="w-20 py-3 px-2 text-right shrink-0 border-r border-slate-100 dark:border-slate-700/50">
                                <span class="text-xs font-bold text-slate-400 dark:text-slate-500" x-text="hour.label"></span>
                            </div>
                            <!-- Columna de Contenido -->
                            <div class="flex-1 p-2 relative">
                                <!-- Aquí iteramos las citas de esa hora exacta -->
                                <template x-for="event in getEventsForHour(hour.value)" :key="event.id">
                                    <div @click="openEvent(event)" class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-barber-accent p-3 rounded-r-xl cursor-pointer hover:shadow-md transition-shadow mb-2">
                                        <p class="text-sm font-bold text-slate-900 dark:text-white" x-text="event.extendedProps.cliente"></p>
                                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5"><i class="fa-solid fa-scissors mr-1"></i> <span x-text="event.extendedProps.servicio"></span></p>
                                        <p class="text-xs text-slate-600 dark:text-slate-400"><i class="fa-solid fa-user mr-1"></i> <span x-text="event.extendedProps.barbero"></span></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- VISTA MES, SEMANA, AÑO: LISTA DE TARJETAS -->
            <div x-show="currentView !== 'day'" class="space-y-3">
                <template x-if="filteredEvents.length === 0">
                    <div class="bg-white dark:bg-barber-card-dark rounded-3xl p-10 text-center border border-slate-200 dark:border-slate-700/50 flex flex-col items-center justify-center h-64">
                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                            <i class="fa-solid fa-calendar-xmark text-3xl text-slate-300 dark:text-slate-600"></i>
                        </div>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Día libre</h4>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">No hay citas agendadas para este período.</p>
                    </div>
                </template>

                <template x-for="event in filteredEvents" :key="event.id">
                    <div @click="openEvent(event)" class="bg-white dark:bg-barber-card-dark rounded-2xl p-4 shadow-sm border border-slate-200 dark:border-slate-700/50 cursor-pointer hover:shadow-md transition-all flex items-center gap-4 relative overflow-hidden group">
                        <div class="absolute left-0 top-0 bottom-0 w-1.5" :style="'background-color: ' + event.backgroundColor"></div>
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-xl px-3 py-2 text-center shrink-0 min-w-[80px]">
                            <p class="text-sm font-bold text-slate-900 dark:text-white" x-text="event.extendedProps.hora"></p>
                            <!-- Si es vista semana o año, mostramos la fecha pequeña -->
                            <p x-show="currentView === 'week' || currentView === 'year'" class="text-[10px] text-slate-500 mt-1" x-text="formatShortDate(event.date)"></p>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-slate-900 dark:text-white truncate" x-text="event.extendedProps.cliente"></h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-medium text-slate-500 dark:text-slate-400" x-text="event.extendedProps.servicio"></span>
                                <span class="text-xs text-slate-400">•</span>
                                <span class="text-xs text-slate-400 truncate" x-text="event.extendedProps.barbero"></span>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-300 shrink-0"></i>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- MODAL DE DETALLES DE CITA -->
    <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" @click="showModal = false"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showModal" x-transition class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-barber-card-dark text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700/50">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/50 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-calendar-check text-barber-accent"></i> Detalles de la Cita
                    </h3>
                    <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-6 space-y-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-50 dark:bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                            <i class="fa-solid fa-user text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Cliente</p>
                            <p class="text-lg font-bold text-slate-900 dark:text-white" x-text="cita.cliente"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Servicio</p>
                            <p class="font-semibold text-slate-900 dark:text-white text-sm" x-text="cita.servicio"></p>
                            <p class="text-xs font-bold text-green-600 dark:text-green-400 mt-1" x-text="'$' + cita.precio"></p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Barbero</p>
                            <p class="font-semibold text-slate-900 dark:text-white text-sm" x-text="cita.barbero"></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <i class="fa-solid fa-calendar text-slate-400"></i>
                            <div>
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Fecha</p>
                                <p class="font-semibold text-slate-900 dark:text-white text-sm" x-text="cita.fecha"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50">
                            <i class="fa-regular fa-clock text-slate-400"></i>
                            <div>
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Hora</p>
                                <p class="font-semibold text-slate-900 dark:text-white text-sm" x-text="cita.hora"></p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 flex items-center gap-1"><i class="fa-solid fa-file-lines"></i> Notas</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700/50" x-text="cita.notas || 'Sin notas adicionales.'"></p>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex justify-end">
                    <button type="button" @click="showModal = false" class="px-6 py-2.5 text-sm font-bold text-white bg-barber-accent hover:bg-blue-600 rounded-xl transition-colors shadow-sm">
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function calendarApp() {
        return {
            events: [],
            currentView: 'month',
            currentMonth: new Date().getMonth(),
            currentYear: new Date().getFullYear(),
            selectedDay: new Date().getDate(),
            selectedDate: new Date().toISOString().split('T')[0],
            showModal: false,
            cita: {},
            holidayInfo: null,
            holidayList: [],
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],

            init() {
                this.fetchEvents();
                this.updateHolidays();
                this.holidayInfo = this.getHoliday(this.selectedDay);
                this.$watch('currentYear', () => this.updateHolidays());
            },

            fetchEvents() {
                fetch('{{ route("app.appointments.calendar.data") }}')
                    .then(response => response.json())
                    .then(data => {
                        this.events = data;
                    });
            },

            getEasterSunday(year) {
                const a = year % 19;
                const b = Math.floor(year / 100);
                const c = year % 100;
                const d = Math.floor(b / 4);
                const e = b % 4;
                const f = Math.floor((b + 8) / 25);
                const g = Math.floor((b - f + 1) / 3);
                const h = (19 * a + b - d - g + 15) % 30;
                const i = Math.floor(c / 4);
                const k = c % 4;
                const l = (32 + 2 * e + 2 * i - h - k) % 7;
                const m = Math.floor((a + 11 * h + 22 * l) / 451);
                const month = Math.floor((h + l - 7 * m + 114) / 31);
                const day = ((h + l - 7 * m + 114) % 31) + 1;
                return new Date(year, month - 1, day);
            },

            updateHolidays() {
                const year = this.currentYear;
                const easter = this.getEasterSunday(year);
                const addDays = (date, days) => {
                    const d = new Date(date);
                    d.setDate(d.getDate() + days);
                    return d;
                };
                const juevesSanto = addDays(easter, -3);
                const viernesSanto = addDays(easter, -2);
                const sabadoSanto = addDays(easter, -1);

                this.holidayList = [{
                        month: 1,
                        day: 1,
                        name: 'Año Nuevo',
                        icon: 'fa-solid fa-champagne-glasses'
                    },
                    {
                        month: 1,
                        day: 16,
                        name: 'Firma de los Acuerdos de Paz',
                        icon: 'fa-solid fa-file-signature'
                    },
                    {
                        month: 2,
                        day: 14,
                        name: 'Día de San Valentín / Amor y Amistad',
                        icon: 'fa-solid fa-heart'
                    },
                    {
                        month: 3,
                        day: 8,
                        name: 'Día de la Mujer',
                        icon: 'fa-solid fa-venus'
                    },
                    {
                        month: juevesSanto.getMonth() + 1,
                        day: juevesSanto.getDate(),
                        name: 'Jueves Santo',
                        icon: 'fa-solid fa-church'
                    },
                    {
                        month: viernesSanto.getMonth() + 1,
                        day: viernesSanto.getDate(),
                        name: 'Viernes Santo',
                        icon: 'fa-solid fa-cross'
                    },
                    {
                        month: sabadoSanto.getMonth() + 1,
                        day: sabadoSanto.getDate(),
                        name: 'Sábado Santo',
                        icon: 'fa-solid fa-church'
                    },
                    {
                        month: 5,
                        day: 1,
                        name: 'Día del Trabajo',
                        icon: 'fa-solid fa-helmet-safety'
                    },
                    {
                        month: 5,
                        day: 3,
                        name: 'Día de la Cruz',
                        icon: 'fa-solid fa-cross'
                    },
                    {
                        month: 5,
                        day: 7,
                        name: 'Día del Soldado',
                        icon: 'fa-solid fa-shield'
                    },
                    {
                        month: 5,
                        day: 10,
                        name: 'Día de la Madre',
                        icon: 'fa-solid fa-heart'
                    },
                    {
                        month: 6,
                        day: 17,
                        name: 'Día del Padre',
                        icon: 'fa-solid fa-user-tie'
                    },
                    {
                        month: 6,
                        day: 22,
                        name: 'Día del Maestro',
                        icon: 'fa-solid fa-chalkboard-user'
                    },
                    {
                        month: 7,
                        day: 20,
                        name: 'Día del Ingeniero',
                        icon: 'fa-solid fa-gears'
                    },
                    {
                        month: 7,
                        day: 26,
                        name: 'Fiestas Julias (Santa Ana)',
                        icon: 'fa-solid fa-music'
                    },
                    {
                        month: 7,
                        day: 31,
                        name: 'Día del Periodista',
                        icon: 'fa-solid fa-newspaper'
                    },
                    {
                        month: 8,
                        day: 3,
                        name: 'Fiestas Agostinas',
                        icon: 'fa-solid fa-star'
                    },
                    {
                        month: 8,
                        day: 5,
                        name: 'Fiestas Agostinas',
                        icon: 'fa-solid fa-star'
                    },
                    {
                        month: 8,
                        day: 6,
                        name: 'Divino Salvador del Mundo',
                        icon: 'fa-solid fa-sun'
                    },
                    {
                        month: 9,
                        day: 15,
                        name: 'Día de la Independencia',
                        icon: 'fa-solid fa-flag'
                    },
                    {
                        month: 10,
                        day: 1,
                        name: 'Día del Niño',
                        icon: 'fa-solid fa-baby'
                    },
                    {
                        month: 10,
                        day: 12,
                        name: 'Día de la Raza / Hispanidad',
                        icon: 'fa-solid fa-globe'
                    },
                    {
                        month: 11,
                        day: 1,
                        name: 'Día de Todos los Santos',
                        icon: 'fa-solid fa-dove'
                    },
                    {
                        month: 11,
                        day: 2,
                        name: 'Día de los Difuntos',
                        icon: 'fa-solid fa-leaf'
                    },
                    {
                        month: 11,
                        day: 21,
                        name: 'Día de la Reina de la Paz (San Miguel)',
                        icon: 'fa-solid fa-crown'
                    },
                    {
                        month: 12,
                        day: 24,
                        name: 'Nochebuena',
                        icon: 'fa-solid fa-gift'
                    },
                    {
                        month: 12,
                        day: 25,
                        name: 'Navidad',
                        icon: 'fa-solid fa-tree'
                    },
                    {
                        month: 12,
                        day: 31,
                        name: 'Fin de Año / Nochevieja',
                        icon: 'fa-solid fa-champagne-glasses'
                    },
                ];
            },

            getHoliday(day) {
                return this.holidayList.find(h => h.month === (this.currentMonth + 1) && h.day === day) || null;
            },

            isHoliday(day) {
                return this.getHoliday(day) !== null;
            },

            get daysInMonth() {
                return new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
            },

            get blanks() {
                let firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
                return firstDay === 0 ? 6 : firstDay - 1;
            },

            prevMonth() {
                if (this.currentMonth === 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
                this.selectedDay = 1;
                this.updateSelectedDate();
            },

            nextMonth() {
                if (this.currentMonth === 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                } else {
                    this.currentMonth++;
                }
                this.selectedDay = 1;
                this.updateSelectedDate();
            },

            selectDate(day) {
                this.selectedDay = day;
                this.updateSelectedDate();
                this.holidayInfo = this.getHoliday(day);
            },

            updateSelectedDate() {
                let month = String(this.currentMonth + 1).padStart(2, '0');
                let day = String(this.selectedDay).padStart(2, '0');
                this.selectedDate = `${this.currentYear}-${month}-${day}`;
                this.holidayInfo = this.getHoliday(this.selectedDay);
            },

            isSelected(day) {
                return this.selectedDay === day;
            },

            isToday(day) {
                const today = new Date();
                return day === today.getDate() && this.currentMonth === today.getMonth() && this.currentYear === today.getFullYear();
            },

            hasEvents(day) {
                let month = String(this.currentMonth + 1).padStart(2, '0');
                let d = String(day).padStart(2, '0');
                let dateString = `${this.currentYear}-${month}-${d}`;
                return this.events.some(e => e.date === dateString);
            },

            hasEventsOnDate(dateString) {
                return this.events.some(e => e.date === dateString);
            },

            getViewTitle() {
                if (this.currentView === 'month') {
                    const date = new Date(this.selectedDate + 'T00:00:00');
                    return date.toLocaleDateString('es-ES', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long'
                    });
                } else if (this.currentView === 'week') {
                    return 'Citas de la Semana';
                } else if (this.currentView === 'day') {
                    const date = new Date(this.selectedDate + 'T00:00:00');
                    return date.toLocaleDateString('es-ES', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long'
                    });
                } else {
                    return 'Resumen del Año ' + this.currentYear;
                }
            },

            // Lógica para la vista Semana
            get weekDays() {
                let startOfWeek = new Date(this.selectedDate + 'T00:00:00');
                let dayOfWeek = startOfWeek.getDay() || 7; // Lunes = 1, Domingo = 7
                startOfWeek.setDate(startOfWeek.getDate() - dayOfWeek + 1); // Ir al Lunes

                let days = [];
                for (let i = 0; i < 7; i++) {
                    let d = new Date(startOfWeek);
                    d.setDate(startOfWeek.getDate() + i);
                    days.push({
                        date: d.toISOString().split('T')[0],
                        dayName: d.toLocaleDateString('es-ES', {
                            weekday: 'short'
                        }).substring(0, 3),
                        dayNumber: d.getDate()
                    });
                }
                return days;
            },

            formatWeekRange() {
                let start = new Date(this.weekDays[0].date + 'T00:00:00');
                let end = new Date(this.weekDays[6].date + 'T00:00:00');
                return `${start.getDate()} ${this.monthNames[start.getMonth()].substring(0,3)} - ${end.getDate()} ${this.monthNames[end.getMonth()].substring(0,3)}`;
            },

            prevWeek() {
                let d = new Date(this.selectedDate + 'T00:00:00');
                d.setDate(d.getDate() - 7);
                this.selectedDate = d.toISOString().split('T')[0];
                this.updateSelectedDate();
            },

            nextWeek() {
                let d = new Date(this.selectedDate + 'T00:00:00');
                d.setDate(d.getDate() + 7);
                this.selectedDate = d.toISOString().split('T')[0];
                this.updateSelectedDate();
            },

            // Lógica para la vista Día
            get dayHours() {
                let hours = [];
                for (let i = 7; i <= 20; i++) { // De 7 AM a 8 PM
                    let hourStr = i.toString().padStart(2, '0') + ':00';
                    let ampm = i >= 12 ? 'PM' : 'AM';
                    let displayHour = i > 12 ? i - 12 : (i === 0 ? 12 : i);
                    hours.push({
                        value: hourStr,
                        label: `${displayHour}:00 ${ampm}`
                    });
                }
                return hours;
            },

            getEventsForHour(hourValue) {
                // Filtramos las citas del día seleccionado y cuya hora coincida
                return this.events.filter(e =>
                    e.date === this.selectedDate &&
                    e.extendedProps.hora.startsWith(hourValue.split(':')[0] + ':')
                );
            },

            formatShortDate(dateString) {
                const d = new Date(dateString + 'T00:00:00');
                return d.toLocaleDateString('es-ES', {
                    day: 'numeric',
                    month: 'short'
                });
            },

            get filteredEvents() {
                if (this.currentView === 'month' || this.currentView === 'day') {
                    return this.events.filter(e => e.date === this.selectedDate);
                } else if (this.currentView === 'week') {
                    return this.events.filter(e => e.date === this.selectedDate).sort((a, b) => new Date(a.date) - new Date(b.date));
                } else if (this.currentView === 'year') {
                    return this.events.filter(e => e.date.startsWith(this.currentYear.toString())).sort((a, b) => new Date(a.date) - new Date(b.date));
                }
                return [];
            },

            openEvent(event) {
                this.cita = event.extendedProps;
                this.showModal = true;
            }
        }
    }
</script>

<style>
    [x-cloak] {
        display: none !important;
    }

    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection