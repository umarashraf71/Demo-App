<!DOCTYPE html>
<html>
<head>
    <style>
        body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
}

.table-container {
  padding: 20px;
}

table {
  border-collapse: collapse;
  width: 100%;
  border: 1px solid #ddd;
}

th, td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: center;
}

th {
  background-color: #f2f2f2;
  font-weight: bold;
}

th:first-child,
td:first-child {
  text-align: left;
}

/* To make the first row fixed (like Excel's header) */
tr:first-child {
  position: sticky;
  top: 0;
  background-color: #f2f2f2;
}

/* To style alternating rows with a different background color */
tr:nth-child(even) {
  background-color: #f9f9f9;
}

/* To style the first column with a different background color */


        </style>
  <title>Excel-like Table</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="table-container">
    <table>
     
      <tr >
        <td colspan="14" style="text-align: center; font-size:20px">
                FFL-MCAS
        </td>
      </tr>

      <tr >
        <td colspan="14" style="text-align: center; font-size:20px">
            Area Office Collection Summary (Quantitative)
        </td>
      </tr>

      <tr >
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      
      <tr>
        <td colspan="7"><span style="font-weight: bold">Area Office:</span> {{$areaOffice->name}}</td>
        <td colspan="7" style="text-align: end" ><span style="font-weight: bold">Date From:</span> {{date('d-M-Y',strtotime($dateFrom))}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span style="font-weight: bold">Date To:</span> {{date('d-M-Y',strtotime($dateTo))}}</td>
      </tr>
      <tr>
        <td colspan="7"><span style="font-weight: bold">Collection Point:</span> {{$cpName}}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>

      <tr >
        <td></td>
        <td></td>
        <td colspan="3" style="text-align: center; font-weight: bold">Purchase</td>
        <td></td>
        <td colspan="2" style="text-align: center; font-weight: bold">Supplier</td>
        <td></td>
        <td colspan="2" style="text-align: center; font-weight: bold">Volume</td>
        <td></td>
        <td></td>
        <td></td>
      </tr>

      <tr>
        <th>Serial No</th>
        <th>Area Office</th>
        <th>Type</th>
        <th>Date</th>
        <th>Time</th>
        <th>Collection Point</th>
        <th>Supplier Code</th>
        <th>Supplier Name</th>
        <th>Source Type</th>
        <th>Gross Volume</th>
        <th>Ts Volume</th>
        <th>FAT</th>
        <th>LR</th>
        <th>SNF</th>
      </tr>

      @foreach($milkPurchaseRecords as $key => $row)
    
      @php 
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


        $tests = collect($row->tests);
        $fat = $tests->where('qa_test_name', 'Fat')->pluck('value')->first();
        $lr = $tests->where('qa_test_name', 'LR')->pluck('value')->first();
        $snf = $tests->where('qa_test_name', 'SNF')->pluck('value')->first();

      @endphp
      <tr>
        <td>{{($row->serial_number) ? 'MPR-' . $row->serial_number : ''}}</td>
        <td>{{$areaOffice}}</td>
        <td>{{$newType}}</td>
        <td>{{\Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $row->getAttributes()['time'])->format('d M, y')}}</td>
        <td>{{\Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $row->getAttributes()['time'])->format('H:i A')}}</td>
        <td>{{ ($row->mcc) ? $row->mcc->name : ($row->cp ? $row->cp->name : 'N/A')}}</td>
        <td>{{($row->supplier) ?  $row->supplier->code : 'N/A'}}</td>
        <td>{{($row->supplier) ? $row->supplier->name : 'N/A'}}</td>
        <td>{{$row->supplier->supplier_type->name ?? 'N/A'}}</td>
        <td>{{ $row->gross_volume}}</td>
        <td>{{$row->ts_volume}}</td>
        <td>{{ $fat ?? 0}}</td>
        <td>{{$lr ?? 0}}</td>
        <td>{{$snf ?? 0}}</td>
      </tr>
@endforeach
      <tr >
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>

      <tr>
        <td colspan="8" style="text-align: end"><span style="font-weight: bold">Total</span></td>
        <td></td>
        <td style="font-weight: bold">{{$totalGross}}</td>
        <td style="font-weight: bold">{{$totalTS}}</td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      <!-- Add more rows as needed -->
    </table>
  </div>
</body>
</html>
