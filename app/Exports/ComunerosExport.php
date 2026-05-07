<?php

namespace App\Exports;

use App\Models\Comunero;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ComunerosExport implements FromCollection, WithColumnWidths, WithHeadings, WithStyles
{
    public function collection()
    {
        return Comunero::with('ciudadano')
            ->get()
            ->map(function ($comunero) {
                return [
                    'id' => $comunero->id,
                    'dni' => $comunero->ciudadano->dni ?? '',
                    'nombres' => $comunero->ciudadano->nombres ?? '',
                    'ape_paterno' => $comunero->ciudadano->ape_paterno ?? '',
                    'ape_materno' => $comunero->ciudadano->ape_materno ?? '',
                    'estado_comunero' => $comunero->estado_comunero,
                    'fecha_empadronamiento' => $comunero->fecha_empadronamiento ? $comunero->fecha_empadronamiento->format('d/m/Y') : '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'DNI',
            'Nombres',
            'Apellido Paterno',
            'Apellido Materno',
            'Estado',
            'Fecha de Empadronamiento',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 15,
            'C' => 25,
            'D' => 20,
            'E' => 20,
            'F' => 14,
            'G' => 24,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1F2937']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
            'A' => ['alignment' => ['horizontal' => 'center']],
            'B' => ['alignment' => ['horizontal' => 'center']],
            'F' => ['alignment' => ['horizontal' => 'center']],
            'G' => ['alignment' => ['horizontal' => 'center']],
        ];
    }
}
