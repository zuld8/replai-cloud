<?php

namespace App\Exports;

use App\Models\Blash\BlashWhatsapp;
use App\Observers\Blash\BlashDetailObserver;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BlashGroupResultExport implements FromQuery, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{

    use Exportable;

    public $request;
    protected $blashDetailObserver;
    protected $blashWhatsapp;

    public function __construct(Request $request, BlashDetailObserver $blashDetailObserver, BlashWhatsapp $blashWhatsapp)
    {
        $this->request              = $request;
        $this->blashDetailObserver  = $blashDetailObserver;
        $this->blashWhatsapp        = $blashWhatsapp;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1   =>  [
                'font' => [
                    'bold'          => true,
                    'color'         => ['argb' => 'FFFFFF'],
                    'size'          => 14
                ],
                'fill' => [
                    'fillType'      => 'solid',
                    'startColor'    => ['rgb' => '0084ff'],
                ],
                'alignment' => [
                    'horizontal'    => Alignment::HORIZONTAL_CENTER,
                    'vertical'      => Alignment::VERTICAL_CENTER,
                    'wrapText'      => true,
                ],
            ],
        ];
    }

    public function query()
    {
        return $this->blashDetailObserver->getData($this->request, $this->blashWhatsapp);
    }

    public function headings(): array
    {
        return [
            'GROUP NAME',
            'GROUP ID',
            'TEMPLATE',
            'STATUS',
            'LOG',
            'DATE'
        ];
    }

    /**
     * @param Detail $detail
     */
    public function map($detail): array
    {

        return [
            $detail->group->name ?? '',
            $detail->phone,
            $detail->parent->template->name ?? '',
            $detail->status == 'yes' ? 'OFF' : 'ON',
            $detail->reports,
            $detail->created_at,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 15,
            'C' => 20,
            'D' => 15,
            'E' => 20,
            'F' => 20,
        ];
    }
}
