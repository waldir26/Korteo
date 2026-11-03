document.addEventListener('DOMContentLoaded', function () {
    // ==========================================
    // FLATPICKR PARA CREAR CITA
    // ==========================================
    const fechaInput = document.getElementById('fecha-cita');
    const horaInput = document.getElementById('hora-cita');

    if (fechaInput) {
        flatpickr(fechaInput, {
            locale: "es",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F, Y",
            minDate: "today",
            disableMobile: true,
            position: "above left"
        });
    }

    if (horaInput) {
        flatpickr(horaInput, {
            locale: "es",
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            altInput: true,
            altFormat: "h:i K",
            time_24hr: false,
            minuteIncrement: 15,
            disableMobile: true,
            position: "above left"
        });
    }

    // ==========================================
    // LÓGICA DE DISPONIBILIDAD EN TIEMPO REAL
    // ==========================================
    const barberSelect = document.getElementById('barber_id');
    const alertDiv = document.getElementById('availability-alert');
    const submitBtn = document.getElementById('btn-submit');
    const checkRoute = document.getElementById('check-route')?.value;

    function verificarDisponibilidad() {
        if (!barberSelect || !alertDiv || !submitBtn || !checkRoute) return;

        const barberId = barberSelect.value;
        const fecha = fechaInput ? fechaInput.value : '';
        const hora = horaInput ? horaInput.value : '';

        if (!barberId || !fecha || !hora) {
            alertDiv.classList.add('hidden');
            submitBtn.disabled = false;
            return;
        }

        fetch(checkRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                barber_id: barberId,
                fecha: fecha,
                hora: hora
            })
        })
            .then(response => response.json())
            .then(data => {
                alertDiv.classList.remove('hidden');
                if (data.disponible) {
                    alertDiv.className = 'mt-2 text-sm font-medium text-green-600 dark:text-green-400 flex items-center gap-1';
                    alertDiv.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4"></i> Barbero disponible';
                    submitBtn.disabled = false;
                } else {
                    alertDiv.className = 'mt-2 text-sm font-medium text-red-600 dark:text-red-400 flex items-center gap-1';
                    alertDiv.innerHTML = '<i data-lucide="x-circle" class="w-4 h-4"></i> Ocupado (Cita con ' + data.cliente + ')';
                    submitBtn.disabled = true;
                }
                if (window.lucide) window.lucide.createIcons();
            })
            .catch(error => console.error('Error:', error));
    }

    if (barberSelect) {
        barberSelect.addEventListener('change', verificarDisponibilidad);
    }

    if (fechaInput && fechaInput._flatpickr) {
        fechaInput._flatpickr.config.onChange.push(verificarDisponibilidad);
    }
    if (horaInput && horaInput._flatpickr) {
        horaInput._flatpickr.config.onChange.push(verificarDisponibilidad);
    }

    // ==========================================
    // FLATPICKR PARA FILTRO Y EDICIÓN DE CITAS
    // ==========================================
    const fechaFiltro = document.getElementById('fecha-filtro');
    if (fechaFiltro) {
        flatpickr(fechaFiltro, {
            locale: "es",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F, Y",
            disableMobile: true
        });
    }

    const fechaEditar = document.getElementById('fecha-editar');
    if (fechaEditar) {
        flatpickr(fechaEditar, {
            locale: "es",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j F, Y",
            disableMobile: true,
            onChange: function (selectedDates, dateStr) {
                const app = document.getElementById('appointments-page');
                if (app && app._x_dataStack) app._x_dataStack[0].editFecha = dateStr;
            }
        });
    }

    const horaEditar = document.getElementById('hora-editar');
    if (horaEditar) {
        flatpickr(horaEditar, {
            locale: "es",
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            altInput: true,
            altFormat: "h:i K",
            time_24hr: false,
            minuteIncrement: 15,
            disableMobile: true,
            onChange: function (selectedDates, dateStr) {
                const app = document.getElementById('appointments-page');
                if (app && app._x_dataStack) app._x_dataStack[0].editHora = dateStr;
            }
        });
    }

    // ==========================================
    // FLATPICKR PARA RANGO DE FECHAS EN GASTOS
    // ==========================================
    const rangoFechas = document.getElementById('rango-fechas');
    if (rangoFechas) {
        flatpickr(rangoFechas, {
            locale: "es",
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j M, Y",
            onClose: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const inicio = instance.formatDate(selectedDates[0], "Y-m-d");
                    const fin = instance.formatDate(selectedDates[1], "Y-m-d");
                    rangoFechas.value = inicio + ' a ' + fin;
                }
            }
        });
    }

    // ==========================================
    // FLATPICKR PARA RANGO DE FECHAS EN PAGOS
    // ==========================================
    const rangoFechasPagos = document.getElementById('rango-fechas-pagos');
    if (rangoFechasPagos) {
        flatpickr(rangoFechasPagos, {
            locale: "es",
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j M, Y",
            onClose: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const inicio = instance.formatDate(selectedDates[0], "Y-m-d");
                    const fin = instance.formatDate(selectedDates[1], "Y-m-d");
                    rangoFechasPagos.value = inicio + ' a ' + fin;
                }
            }
        });
    }

    // ==========================================
    // FLATPICKR PARA RANGO DE FECHAS EN VENTAS
    // ==========================================
    const rangoFechasVentas = document.getElementById('rango-fechas-ventas');
    if (rangoFechasVentas) {
        flatpickr(rangoFechasVentas, {
            locale: "es",
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j M, Y",
            onClose: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const inicio = instance.formatDate(selectedDates[0], "Y-m-d");
                    const fin = instance.formatDate(selectedDates[1], "Y-m-d");
                    rangoFechasVentas.value = inicio + ' a ' + fin;
                }
            }
        });
    }
});