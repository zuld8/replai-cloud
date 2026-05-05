<?php

namespace App\Exports;

use App\Observers\Blash\LogObserver;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WhatsappLogExport implements FromQuery, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    use Exportable;

    public $request;
    protected $logObserver;

    public function __construct(Request $request, LogObserver $logObserver)
    {
        $this->request          = $request;
        $this->logObserver    = $logObserver;
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
        return $this->logObserver->getData($this->request, $this->request->type ?? 'whatsapp');
    }

    public function headings(): array
    {
        return [
            'STATUS',
            'DEVICE',
            'USER',
            'PHONE',
            'SENT TIME',
            'TEXT',
            'ERROR'
        ];
    }

    /**
     * @param Logs $log
     */
    public function map($log): array
    {

        $status     = '';

        if ($log->status == 'pending') {
            $status = 'PENDING';
        }
        if ($log->status == 'success') {
            $status = 'TERKIRIM';
        }
        if ($log->status == 'error') {
            $status = 'GAGAL';
        }


        return [
            $status,
            $log->device->name ?? '',
            $log->store->name ?? '',
            $log->phone,
            $log->sending,
            $log->text,
            $log->error
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 25,
            'D' => 20,
            'E' => 20,
            'F' => 30,
            'G' => 30
        ];
    }
}
