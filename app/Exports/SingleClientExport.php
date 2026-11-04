<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Enumerable;

class SingleClientExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function collection(): Enumerable
    {
        return collect([$this->client]);
    }

    public function headings(): array
    {
        return ['ID', 'Nombre', 'Teléfono', 'Email', 'Notas'];
    }

    public function map($client): array
    {
        return [
            $client->id,
            $client->nombre,
            $client->telefono ?? 'N/A',
            $client->email ?? 'N/A',
            $client->notas ?? 'Sin notas'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 30,
            'C' => 20,
            'D' => 30,
            'E' => 50,
        ];
    }

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

                $sheet->insertNewRowBefore(1, 3);

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

                $sheet->setCellValue('A2', 'Ficha del Cliente');
                $sheet->mergeCells('A2:E2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('FF64748B');

                $sheet->setCellValue('D2', 'Generado: ' . date('d/m/Y h:i A'));
                $sheet->getStyle('D2')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('D2')->getFont()->setSize(9)->getColor()->setARGB('FF94A3B8');

                $sheet->getStyle('A4:E5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('A4:E5')->getBorders()->getAllBorders()->getColor()->setARGB('FFE2E8F0');
            },
        ];
    }
}
