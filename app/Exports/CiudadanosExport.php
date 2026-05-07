<?php

namespace App\Exports;

use App\Models\Ciudadano;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CiudadanosExport implements FromCollection, WithColumnWidths, WithHeadings, WithStyles
{
    public function collection()
    {
        return Ciudadano::select(
            'dni',
            'nombres',
            'ape_paterno',
            'ape_materno',
            'fecha_nacimiento',
            'genero',
            'telefono',
            'email',
            'direccion_referencia'
        )->get()->map(function ($ciudadano) {
            return [
                'dni' => $ciudadano->dni,
                'nombres' => $ciudadano->nombres,
                'ape_paterno' => $ciudadano->ape_paterno,
                'ape_materno' => $ciudadano->ape_materno,
                'fecha_nacimiento' => $ciudadano->fecha_nacimiento ? $ciudadano->fecha_nacimiento->format('d/m/Y') : '',
                'genero' => $ciudadano->genero ?? '',
                'telefono' => $ciudadano->telefono ?? '',
                'email' => $ciudadano->email ?? '',
                'direccion_referencia' => $ciudadano->direccion_referencia ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'DNI',
            'Nombres',
            'Apellido Paterno',
            'Apellido Materno',
            'Fecha de Nacimiento',
            'Genero',
            'Telefono',
            'Email',
            'Direccion/Referencia',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 18,
            'F' => 12,
            'G' => 15,
            'H' => 25,
            'I' => 35,
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
            'F' => ['alignment' => ['horizontal' => 'center']],
        ];
    }
}
