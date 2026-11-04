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

class SingleAppointmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function collection(): Enumerable
    {
        // Solo devolvemos la cita real. La librería la pondrá automáticamente después de los encabezados.
        return collect([$this->appointment]);
    }

    public function headings(): array
    {
        // Estos son los encabezados (irán en la Fila 2)
        return ['ID', 'Fecha', 'Hora', 'Cliente', 'Barbero', 'Servicio', 'Precio', 'Estado', 'Notas'];
    }

    public function map($appointment): array
    {
        // Estos son los datos de la cita (irán en la Fila 3)
        return [
            $appointment->id,
            $appointment->fecha_hora->format('d/m/Y'),
            $appointment->fecha_hora->format('H:i A'),
            $appointment->client?->nombre ?? 'N/A',
            $appointment->barber?->nombre ?? 'N/A',
            $appointment->service?->nombre ?? 'N/A',
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

    // Aquí pintamos la fila de encabezados (que ahora es la Fila 2)
    public function styles(Worksheet $sheet): array
    {
        return [
            2 => [ // Fila 2
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF3B82F6']], // Azul
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

                // 1. Mover los encabezados (que por defecto van en la fila 1) a la fila 2
                $sheet->insertNewRowBefore(1, 1);

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

                // 3. Texto "Comprobante de Cita Generado" en la Fila 4
                $sheet->mergeCells('A4:I4');
                $sheet->setCellValue('A4', 'Comprobante de Cita Generado: ' . date('d/m/Y H:i A'));
                $sheet->getStyle('A4')->getFont()->setSize(10)->getColor()->setARGB('FF64748B');
                $sheet->getStyle('A4')->getAlignment()->setHorizontal('right');

                // 4. Formato de Moneda para la columna Precio (Fila 3)
                $sheet->getStyle('G3')->getNumberFormat()->setFormatCode('$#,##0.00');

                // 5. Bordes para la tabla (Encabezados en 2, Datos en 3)
                $sheet->getStyle('A2:I3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('A2:I3')->getBorders()->getAllBorders()->getColor()->setARGB('FFE2E8F0');
            },
        ];
    }
}
