<?php

namespace App\Exports;

use App\Models\AreaOffice;
use App\Models\CollectionPoint;
use App\Models\InventoryItem;
use App\Models\MilkPurchase;
use App\Models\Price;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use phpDocumentor\Reflection\Types\Null_;
use PhpOffice\PhpSpreadsheet\Style\Font;
use MongoDB\BSON\UTCDateTime;

class ExportFreshMilkPurchaseSummary implements FromCollection, WithHeadings, WithCustomStartCell, ShouldAutoSize, WithEvents, WithStyles
{
    public $totalGross;
    public $totalTS;
    public $details;
    public $areaOffice;
    public $cpName = "All";
    public $dateString;
    public function __construct($details)
    {
        $this->details = $details;
    }
    public function startCell(): string
    {

        return 'A8';
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {
                /** @var Sheet $sheet */
                $sheet = $event->sheet;

                $sheet->mergeCells('A1:D1');
                $sheet->mergeCells('A2:D2');
                $sheet->mergeCells('A3:D3');
                $sheet->mergeCells('A4:D4');
                $sheet->mergeCells('A5:D5');
                $sheet->setCellValue('A1', "FFL - MCAS");
                $sheet->setCellValue('A2', "Fresh Milk Purchase Summary");
                $sheet->setCellValue('A3', "Period From:" . $this->dateString);
                $sheet->setCellValue('A4', "Area Office:" . $this->areaOffice->name);
                $cp = (isset($this->details['collection_point_id'])) ? $this->cpName : 'ALL';
                $sheet->setCellValue('A5', "Collection Point:" . $cp);
                $event->sheet->getStyle('A1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                    'font' => [
                        'size' => 15,

                    ]
                ]);
                $event->sheet->getStyle('A2')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                    'font' => [
                        'size' => 15,

                    ]
                ]);
                $event->sheet->getStyle('A3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ]);
                $event->sheet->getStyle('A4')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ]);
                $event->sheet->getStyle('A5')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ]);

                $highestRow = $event->sheet->getHighestRow();

                $sheet->mergeCells('A' . $highestRow . ':' . 'H' . $highestRow);
                $sheet->setCellValue('A' . $highestRow, "TOTAL");
                $sheet->setCellValue('I' . $highestRow, " ");
                $sheet->setCellValue('J' . $highestRow, $this->totalGross);
                $sheet->setCellValue('K' . $highestRow, $this->totalTS);
                $event->sheet->getStyle('A' . $highestRow)->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ]);
                $event->sheet->getStyle('J' . $highestRow)->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ]);
                $event->sheet->getStyle('K' . $highestRow)->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ]);
            },
        ];
    }

    public function headings(): array
    {

        return [
            'Sr.#',
            'Area Office',
            'Date',
            'Time',
            'MPR #',
            'Milk Supplier Code',
            'Business Name',
            'Milk Supplier`s Name',
            'Source',
            'MCC Code',
            'Milk Collection Center Name',
            'No of Chillers Installed',
            'Chillers Capacity',
            'MCC latitude',
            'MCC Longitude',
            'Gross Volume',
            'Fat %',
            '(calculated)LR',
            'SNF %',
            'Volume @ 13 TS',
            'Base Price Per Ltr @ 13 TS',
            'Base Price Amount'
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            'A' => ['font' => ['bold' => true]], // Make the "Area Office" column (column A) bold
        ];
    }
    public function collection()
    {
        $milkPurchaseRecords = '';
        $this->areaOffice = AreaOffice::find($this->details['area_office_id']);
        $fromDate = Carbon::createFromFormat('Y-m-d', $this->details['from_date'])->startOfDay();
        $toDate = Carbon::createFromFormat('Y-m-d', $this->details['to_date'])->endOfDay();


        if (isset($this->details['collection_point_id'])) {
            $colps = CollectionPoint::find($this->details['collection_point_id']);
            $this->cpName = $colps->name;

            if ($colps->is_mcc == "1") {
                $milkPurchaseRecords = MilkPurchase::where('mcc_id', $this->details['collection_point_id'])->whereBetween('created_at', [$fromDate, $toDate])->get();
            } else {
                $milkPurchaseRecords = MilkPurchase::where('cp_id', $this->details['collection_point_id'])->whereBetween('created_at', [$fromDate, $toDate])->get();
            }
        } else {
            $collectionPointIds = CollectionPoint::where('area_office_id', $this->details['area_office_id'])->pluck('_id')->toArray();
            $milkPurchaseRecords =  MilkPurchase::where('area_office_id', $this->details['area_office_id'])
                ->orWhereIn('cp_id', $collectionPointIds)
                ->orWhereIn('mcc_id', $collectionPointIds)
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->get();
        }


        // $this->totalGross = $milkPurchaseRecords->sum('gross_volume');
        // $this->totalTS = $milkPurchaseRecords->sum('ts_volume');

        $this->dateString = 'From ' . $this->details['from_date'] . ' To ' . $this->details['to_date'] . '';


        // dd($milkPurchaseRecords[0]);

        return $milkPurchaseRecords->map(function ($row, $index) {
            $basePrice = $this->getBasePrice($row);
            $sr = $index + 1;
            $areaOffice = $this->areaOffice->name;
            $timeStr = $row->time;

            $dateTime = Carbon::createFromFormat('d M, y h:i A', $timeStr);

            $time = $dateTime->format('H:i:s');

            $mpr = $row->serial_number;
            $supplier_code = $row->supplier->code;
            $business_name = $row->supplier->business_name;
            $supplier_name = $row->supplier->name;
            $source = $row->supplier->supplier_type->name;
            $mcc_code = ($row->mcc ? $row->mcc->code : ($row->cp ? $row->cp->code : 'N/A'));
            $mcc_name = ($row->mcc ? $row->mcc->name : ($row->cp ? $row->cp->name : 'N/A'));
            $no_of_chillers = ($row->mcc ? count($row->mcc->chillers ?? []) : count($row->cp->chillers ?? []));

            $chillerIds = $row->mcc ? collect($row->mcc->chillers)->pluck('id')->toArray() : collect($row->cp->chillers)->pluck('id')->toArray();
            $existingChillers = InventoryItem::whereIn('_id', $chillerIds)->get();
            $chillers_capacity = $existingChillers->pluck('capacity')->map(function ($capacity) {
                return is_numeric($capacity) ? intval($capacity) : intval($capacity);
            })->sum();

            $latitude = ($row->mcc ? $row->mcc->latitude : ($row->cp ? $row->cp->latitude : 'N/A'));
            $longitude = ($row->mcc ? $row->mcc->longitude : ($row->cp ? $row->cp->longitude : 'N/A'));
            $gross_volume = $row->gross_volume;
            $tests = collect($row->tests);
            $fat = $tests->where('qa_test_name', 'Fat')->pluck('value')->first();
            $lr = $tests->where('qa_test_name', 'LR')->pluck('value')->first();
            $snf = $tests->where('qa_test_name', 'SNF')->pluck('value')->first();

            $ts_volume = $row->ts_volume;

            return [
                'Sr.#' => $sr ?? 'N/A',
                'Area Office' => $areaOffice ?? 'N/A',
                'date' => Carbon::createFromFormat('Y-m-d', $row->booked_at)->format('d M, y'),
                'Time' => $time ?? 'N/A',
                'MPR #' => $mpr ?? 'N/A',
                'Milk Supplier Code' => $supplier_code ?? 'N/A',
                'Business Name' => $business_name ?? 'N/A',
                'Milk Supplier`s Name' => $supplier_name ?? 'N/A',
                'Source' => $source ?? 'N/A',
                'MCC Code' => $mcc_code ?? 'N/A',
                'Milk Collection Center Name' => $mcc_name ?? 'N/A',
                'No of Chillers Installed' => $no_of_chillers ?? 0,
                'Chillers Capacity' => $chillers_capacity ?? 'N/A',
                'MCC latitude' => $latitude ?? 'N/A',
                'MCC Longitude' => $longitude ?? 'N/A',
                'Gross Volume' => $gross_volume ?? 'N/A',
                'Fat %' => $fat ?? 'N/A',
                '(calculated)LR' => $lr ?? 'N/A',
                'SNF %' => $snf ?? 'N/A',
                'Volume @ 13 TS' => $ts_volume ?? 'N/A',
                'Base Price Per Ltr @ 13 TS' => $basePrice ?? 'N/A',
                'Base Price Amount' => $basePrice * $ts_volume,
            ];
        });
    }

    private function getBasePrice($purchase)
    {
        $basePricing = Price::where('approved_at', 'exists', true)->get();

        $basePrice = $basePricing
            ->where('supplier', $purchase->supplier_id)
            ->where('collection_point', null)
            ->where('source_type', null)
            ->sortByDesc('wef')
            ->first();

        if (!$basePrice) {
            $cp = $purchase->mcc ? $purchase->mcc->id : $purchase->cp_id;
            $basePrice = $basePricing
                ->where('supplier', $purchase->supplier_id)
                ->where('collection_point', $cp)
                ->where('source_type', null)
                ->sortByDesc('wef')
                ->first();

            if ($basePrice == null) {
                $basePrice = $basePricing->where('collection_point', $cp)
                    ->where('source_type', $purchase->supplier_type_id)
                    ->where('supplier', null)
                    ->sortByDesc('wef')
                    ->first();
            }

            if ($basePrice == null) {
                $basePrice = $basePricing->where('collection_point', $cp)
                    ->where('source_type', null)
                    ->where('supplier', null)
                    ->sortByDesc('wef')
                    ->first();
            }

            if ($basePrice) {
                return $basePrice->price;
            }
        }

        if (!$basePrice) {
            $basePrice = $basePricing
                ->where('supplier', null)
                ->where('collection_point', null)
                ->where('source_type', $purchase->supplier_type_id)
                ->sortByDesc('wef')
                ->first();

            if ($basePrice) {
                return $basePrice->price;
            }
        }

        return 0;
    }
}
