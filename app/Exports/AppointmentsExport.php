<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Enumerable;

class AppointmentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $fecha;
    protected $search;
    protected $barberId;

    public function __construct($fecha = null, $search = null, $barberId = null)
    {
        $this->fecha = $fecha;
        $this->search = $search;
        $this->barberId = $barberId;
    }

    public function collection(): Enumerable
    {
        $query = Appointment::with(['client', 'barber', 'service']);

        if ($this->fecha) {
            $query->whereDate('fecha_hora', $this->fecha);
        }
        if ($this->barberId) {
            $query->where('barber_id', $this->barberId);
        }
        if ($this->search) {
            $query->whereHas('client', function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%');
            })->orWhereHas('barber', function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('fecha_hora', 'asc')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Fecha', 'Hora', 'Cliente', 'Barbero', 'Servicio', 'Precio', 'Estado', 'Notas'];
    }

    public function map($appointment): array
    {
        return [
            $appointment->id,
            $appointment->fecha_hora->format('d/m/Y'),
            $appointment->fecha_hora->format('h:i A'),
            $appointment->client?->nombre ?? 'Cliente eliminado',
            $appointment->barber?->nombre ?? 'Barbero eliminado',
            $appointment->service?->nombre ?? 'Servicio eliminado',
            $appointment->service?->precio ?? '0.00',
            ucfirst($appointment->estado),
            $appointment->notas ?? 'Sin notas'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 15,
            'C' => 12,
            'D' => 25,
            'E' => 25,
            'F' => 25,
            'G' => 12,
            'H' => 15,
            'I' => 30,
        ];
    }

    // Estilos para los encabezados (Fila 4)
    public function styles(Worksheet $sheet): array
    {
        return [
            4 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF3B82F6']],
                'alignment' => ['horizontal' => 'center']
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $barbershop = auth()->user()->barbershop;

                // 1. Mover los encabezados 3 filas hacia abajo (para dejar espacio al logo y título)
                $sheet->insertNewRowBefore(1, 3);

                // 2. Logo o Nombre en la Fila 1
                if ($barbershop && $barbershop->logo) {
                    $logoPath = public_path('storage/' . $barbershop->logo);
                    if (file_exists($logoPath)) {
                        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawing->setName('Logo');
                        $drawing->setPath($logoPath);
                        $drawing->setHeight(50);
                        $drawing->setCoordinates('A1');
                        $drawing->setWorksheet($sheet);
                    }
                } else {
                    $nombre = $barbershop->nombre ?? 'Korteo';
                    $sheet->setCellValue('A1', $nombre);
                    $sheet->mergeCells('A1:E1');
                    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FF000000');
                }

                // 3. Título del Reporte en la Fila 2
                $sheet->setCellValue('A2', 'Reporte General de Citas');
                $sheet->mergeCells('A2:E2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('FF64748B');

                // 4. Fecha de generación en la Fila 2 (A la derecha)
                $sheet->setCellValue('G2', 'Generado: ' . date('d/m/Y h:i A'));
                $sheet->getStyle('G2')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('G2')->getFont()->setSize(9)->getColor()->setARGB('FF94A3B8');

                // 5. Formato de Moneda para toda la columna de Precios (desde la fila 5 en adelante)
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('G5:G' . $highestRow)->getNumberFormat()->setFormatCode('$#,##0.00');

                // 6. Bordes para la tabla
                $sheet->getStyle('A4:I' . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('A4:I' . $highestRow)->getBorders()->getAllBorders()->getColor()->setARGB('FFE2E8F0');
            },
        ];
    }
}
