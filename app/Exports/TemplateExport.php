<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateExport implements WithHeadings, ShouldAutoSize, WithStyles
{
    private $headers;
    public function __construct($headers)
    {
        $this->headers = $headers;
    }

    public function headings(): array
    {
		return [
			$this->headers
		];
    }
	public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'name' => 'Parkinsans',
                    'size' => 12,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
    }
}
