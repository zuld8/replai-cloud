<?php

namespace App\Exports;

use App\Models\Store\Scrapping;
use App\Observers\Store\StoreObserver;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScrappingResultExport implements FromQuery, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    use Exportable;

    public $request;
    protected $storeObserver;
    protected $scrapping;

    public function __construct(Request $request, StoreObserver $storeObserver, Scrapping $scrapping)
    {
        $this->request          = $request;
        $this->storeObserver    = $storeObserver;
        $this->scrapping        = $scrapping;
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
        return $this->storeObserver->getData($this->request)->where("scrapping_id", $this->scrapping->id);
    }

    public function headings(): array
    {
        return [
            'NAME',
            'CATEGORY',
            'PHONE',
            'EMAIL',
            'DISTRICT',
            'ADDRESS',
            'PEMILIK',
            'PROVINSI',
            'KOTA/KAB',
            'KECAMATAN'
        ];
    }

    /**
     * @param Store $store
     */
    public function map($store): array
    {

        $cityType = $store->district->city->type ?? '';
        $cityName = $store->district->city->name ?? '';

        return [
            $store->name,
            $store->category->name ?? '',
            $store->phone,
            $store->email,
            $store->district_id,
            $store->address,
            $store->pemilik,
            $store->district->city->province->name ?? '',
            $cityType . ' ' . $cityName,
            $store->district->name ?? ''
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 25,
            'C' => 15,
            'D' => 25,
            'E' => 10,
            'F' => 30,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 20,
        ];
    }
}
