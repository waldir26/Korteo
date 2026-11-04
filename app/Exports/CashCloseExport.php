<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Enumerable;
use Carbon\Carbon;

class CashCloseExport implements FromCollection, WithStyles, WithColumnWidths, WithEvents
{
    public function collection(): Enumerable
    {
        // No necesitamos devolver una colección real, usaremos eventos para dibujar todo.
        return collect([]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 20,
            'C' => 5, // Columna separadora
            'D' => 30,
            'E' => 20,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $barbershop = auth()->user()->barbershop;
                $hoy = Carbon::today();

                // 1. Obtener datos
                $totalServicios = Transaction::whereDate('created_at', $hoy)->where('tipo', 'ingreso_servicio')->sum('monto');
                $totalVentas = Transaction::whereDate('created_at', $hoy)->where('tipo', 'ingreso_producto')->sum('monto');
                $totalGastos = Transaction::whereDate('created_at', $hoy)->where('tipo', 'gasto')->sum('monto');
                $totalPagos = Transaction::whereDate('created_at', $hoy)->where('tipo', 'pago_barbero')->sum('monto');

                $ingresosTotales = $totalServicios + $totalVentas;
                $egresosTotales = $totalGastos + $totalPagos;
                $balanceFinal = $ingresosTotales - $egresosTotales;

                // 2. Logo o Nombre
                if ($barbershop && $barbershop->logo) {
                    $logoPath = public_path('storage/' . $barbershop->logo);
                    if (file_exists($logoPath)) {
                        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawing->setName('Logo');
                        $drawing->setPath($logoPath);
                        $drawing->setHeight(60);
                        $drawing->setCoordinates('A1');
                        $drawing->setWorksheet($sheet);
                    }
                } else {
                    $nombre = $barbershop->nombre ?? 'Korteo';
                    $sheet->setCellValue('A1', $nombre);
                    $sheet->mergeCells('A1:E1');
                    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FF000000');
                }

                // 3. Título y Fecha
                $sheet->setCellValue('A3', 'Cierre de Caja');
                $sheet->mergeCells('A3:B3');
                $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FF3B82F6');

                $sheet->setCellValue('D3', 'Fecha: ' . date('d/m/Y'));
                $sheet->mergeCells('D3:E3');
                $sheet->getStyle('D3')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('D3')->getFont()->setSize(11)->getColor()->setARGB('FF64748B');

                // 4. Sección de Ingresos (Fila 5)
                $sheet->setCellValue('A5', 'INGRESOS');
                $sheet->mergeCells('A5:B5');
                $sheet->getStyle('A5')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle('A5:B5')->getFill()->setFillType('solid')->getStartColor()->setARGB('FF10B981'); // Verde

                $sheet->setCellValue('A6', 'Servicios Dados:');
                $sheet->setCellValue('B6', '$' . number_format($totalServicios, 2));

                $sheet->setCellValue('A7', 'Ventas de Productos:');
                $sheet->setCellValue('B7', '$' . number_format($totalVentas, 2));

                $sheet->setCellValue('A8', 'Total Ingresos:');
                $sheet->setCellValue('B8', '$' . number_format($ingresosTotales, 2));
                $sheet->getStyle('A8:B8')->getFont()->setBold(true);
                $sheet->getStyle('A8:B8')->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // 5. Sección de Egresos (Fila 10)
                $sheet->setCellValue('A10', 'EGRESOS');
                $sheet->mergeCells('A10:B10');
                $sheet->getStyle('A10')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle('A10:B10')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFEF4444'); // Rojo

                $sheet->setCellValue('A11', 'Gastos de la Barbería:');
                $sheet->setCellValue('B11', '$' . number_format($totalGastos, 2));

                $sheet->setCellValue('A12', 'Pagos a Barberos:');
                $sheet->setCellValue('B12', '$' . number_format($totalPagos, 2));

                $sheet->setCellValue('A13', 'Total Egresos:');
                $sheet->setCellValue('B13', '$' . number_format($egresosTotales, 2));
                $sheet->getStyle('A13:B13')->getFont()->setBold(true);
                $sheet->getStyle('A13:B13')->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // 6. Balance Final (Fila 15)
                $sheet->setCellValue('A15', 'BALANCE DEL DÍA');
                $sheet->mergeCells('A15:B15');
                $sheet->getStyle('A15')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle('A15:B15')->getFill()->setFillType('solid')->getStartColor()->setARGB('FF3B82F6'); // Azul

                $sheet->setCellValue('A16', 'Total Neto:');
                $sheet->setCellValue('B16', '$' . number_format($balanceFinal, 2));
                $sheet->getStyle('A16:B16')->getFont()->setBold(true)->setSize(14);

                // Pintar el número final de verde o rojo (Corregido)
                $colorBalance = $balanceFinal < 0 ? 'FFFF0000' : 'FF00B050'; // Rojo o Verde
                $sheet->getStyle('B16')->getFont()->getColor()->setARGB($colorBalance);

                // 7. Bordes generales a las tablas (A5:B16)
                $sheet->getStyle('A5:B16')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('A5:B16')->getBorders()->getAllBorders()->getColor()->setARGB('FFE2E8F0');
            },
        ];
    }
}
