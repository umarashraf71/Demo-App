<?php

namespace App\Exports;

use App\Models\AreaOffice;
use App\Models\CollectionPoint;
use App\Models\MilkPurchase;
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


class ExportAoCollectionSummary implements FromCollection, WithHeadings, WithCustomStartCell, ShouldAutoSize, WithEvents, WithStyles
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

                $sheet->mergeCells('A1:N1');
                $sheet->mergeCells('A2:N2');
                $sheet->mergeCells('A4:F4');
                $sheet->mergeCells('G4:N4');
                $sheet->mergeCells('A5:F5');
                $sheet->mergeCells('C7:E7');
                $sheet->mergeCells('G7:H7');
                $sheet->mergeCells('J7:K7');
                $sheet->setCellValue('A1', "FFL - MCAS");
                $sheet->setCellValue('A2', "Area Office Collection Summary (Quantitative)");
                $sheet->setCellValue('A4', "Area Office:" . $this->areaOffice->name);
                $sheet->setCellValue('G4', "Date:" . $this->dateString);
                $cp = (isset($this->details['collection_point_id'])) ? $this->cpName : 'ALL';
                $sheet->setCellValue('A5', "Collection Point:" . $cp);
                $sheet->setCellValue('C7', "Purchase");
                $sheet->setCellValue('G7', "Supplier");
                $sheet->setCellValue('J7', "Volume");
                $event->sheet->getStyle('A1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'size' => 15,

                    ]
                ]);
                $event->sheet->getStyle('A2')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'size' => 15,

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
                $event->sheet->getStyle('G4')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ]);
                $event->sheet->getStyle('A8:N8')->applyFromArray([
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
                $event->sheet->getStyle('C7')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ]);
                $event->sheet->getStyle('G7')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ]);
                $event->sheet->getStyle('J7')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ]);
                $highestRow = $event->sheet->getHighestRow();
                $highestRow = $highestRow + 2;

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
            'Serial No',
            'Area Office',
            'Type',
            'Date',
            'Purchase booked Date',
            'Time',
            'Collection Point',
            'Supplier Code',
            'Supplier Name',
            'Source Type',
            'Gross Volume',
            'Ts Volume',
            'FAT',
            'LR',
            'SNF'

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
        $fromDate = Carbon::createFromFormat('Y-m-d', $this->details['from_date']);
        $toDate = Carbon::createFromFormat('Y-m-d', $this->details['to_date']);


        if (isset($this->details['collection_point_id'])) {
            $colps = CollectionPoint::find($this->details['collection_point_id']);
            $this->cpName = $colps->name;

            if ($colps->is_mcc == "1") {
                $milkPurchaseRecords = MilkPurchase::where('mcc_id', $this->details['collection_point_id'])->whereBetween('booked_at', [ $this->details['from_date'], $this->details['to_date'] ])->get();
            } else {
                $milkPurchaseRecords = MilkPurchase::where('cp_id', $this->details['collection_point_id'])->whereBetween('booked_at', [ $this->details['from_date'], $this->details['to_date'] ])->get();
            }
        } else {
            $collectionPointIds = CollectionPoint::where('area_office_id', $this->details['area_office_id'])->pluck('_id')->toArray();
            $milkPurchaseRecords =  MilkPurchase::where('area_office_id', $this->details['area_office_id'])
                ->orWhereIn('cp_id', $collectionPointIds)
                ->orWhereIn('mcc_id', $collectionPointIds)
                ->whereBetween('booked_at', [ $this->details['from_date'], $this->details['to_date'] ])
                ->get();
        }

        

        $this->totalGross = $milkPurchaseRecords->sum('gross_volume');
        $this->totalTS = $milkPurchaseRecords->sum('ts_volume');

        $this->dateString = 'From ' . $this->details['from_date'] . ' To ' . $this->details['to_date'] . '';



        return $milkPurchaseRecords->map(function ($row) {
            //Get AreaOffice Name
            $areaOffice = 'N/A';
            if ($row->ao <> null)
                $areaOffice = $row->ao->name;
            elseif ($row->mcc <> null)
                $areaOffice = $row->mcc->area_office->name;
            elseif ($row->cp <> null)
                $areaOffice = $row->cp->area_office->name;
            else  "N/A";

            $newType = '';
            if ($row->type == 'purchase_at_mcc')
                $newType = "MCC Purchase";
            elseif ($row->type == 'mmt_purchase')
                $newType = "MMT Purchase";
            elseif ($row->type == 'purchase_at_ao')
                $newType = "Area Office Purchase";
            elseif ($row->type == 'purchase_at_plant')
                $newType = "Plant Purchase";

            //Get FAT, LR, SNF Volumes From Tests
            $tests = collect($row->tests);
            $fat = $tests->where('qa_test_name', 'Fat')->pluck('value')->first();
            $lr = $tests->where('qa_test_name', 'LR')->pluck('value')->first();
            $snf = $tests->where('qa_test_name', 'SNF')->pluck('value')->first();

            return [
                'serial_number' => ($row->serial_number) ? 'MPR-' . $row->serial_number : '',
                'area_office' => $areaOffice,
                'type' => $newType,
                'Date' => Carbon::createFromFormat('Y-m-d H:i:s', $row->getAttributes()['time'])->format('d M, y'),
                'booked_at' => Carbon::createFromFormat('Y-m-d', $row->booked_at)->format('d M, y'),
                'time' => Carbon::createFromFormat('Y-m-d H:i:s', $row->getAttributes()['time'])->format('H:i A'),
                'collection_point' => ($row->mcc) ? $row->mcc->name : ($row->cp ? $row->cp->name : 'N/A'),
                'supplier_code' => ($row->supplier) ?  $row->supplier->code : 'N/A',
                'supplier_name' => ($row->supplier) ? $row->supplier->name : 'N/A',
                'source_type' =>  $row->supplier->supplier_type->name ?? 'N/A',
                'gross_volume' => $row->gross_volume,
                'ts_volume' => $row->ts_volume,
                'fat' => $fat ?? 0,
                'lr' => $lr ?? 0,
                'snf' => $snf ?? 0
            ];
        });
    }
}
