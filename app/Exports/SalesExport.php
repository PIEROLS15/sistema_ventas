<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function collection()
    {
        return collect($this->sales);
    }

    public function headings(): array
    {
        return [
            'Código de Venta',
            'Nombre del Cliente',
            'Tipo de Identificación',
            'Número de Identificación',
            'Correo del Cliente',
            'Total de Productos',
            'Monto Total',
            'Fecha de Venta'
        ];
    }
}