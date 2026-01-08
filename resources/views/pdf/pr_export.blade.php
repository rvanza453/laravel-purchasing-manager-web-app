<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Purchase Request - {{ $pr->pr_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #000;
            padding: 15px;
        }
        
        .header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        
        .header-left {
            display: table-cell;
            width: 15%;
            vertical-align: middle;
        }
        
        .header-left img {
            max-width: 80px;
            height: auto;
        }
        
        .header-center {
            display: table-cell;
            width: 70%;
            text-align: center;
            vertical-align: middle;
        }
        
        .header-center h1 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .header-right {
            display: table-cell;
            width: 15%;
            text-align: right;
            vertical-align: top;
            font-size: 10pt;
        }
        
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .info-left {
            display: table-cell;
            width: 50%;
            padding-right: 10px;
            vertical-align: top;
        }
        
        .info-right {
            display: table-cell;
            width: 50%;
            padding-left: 10px;
            vertical-align: top;
            border-left: 1px solid #000;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 2px 5px;
            font-size: 8pt;
        }
        
        .info-table .label {
            width: 40%;
            font-weight: normal;
        }
        
        .info-table .value {
            font-weight: bold;
        }
        
        .budget-info {
            font-size: 7pt;
            line-height: 1.4;
        }
        
        .budget-info .budget-label {
            display: inline-block;
            width: 120px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 8pt;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: left;
        }
        
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 7pt;
        }
        
        .items-table .text-right {
            text-align: right;
        }
        
        .items-table .text-center {
            text-align: center;
        }
        
        .items-table .no-col {
            width: 3%;
        }
        
        .items-table .code-col {
            width: 7%;
        }
        
        .items-table .name-col {
            width: 15%;
        }
        
        .items-table .spec-col {
            width: 15%;
        }
        
        .items-table .qty-col {
            width: 3%;
        }
        
        .items-table .price-col {
            width: 10%;
        }
        
        .items-table .note-col {
            width: 10%;
        }
        
        .sub-header {
            font-size: 6pt;
            padding: 1px 2px !important;
        }
        
        .footer-table {
            margin-top: 5px;
            font-size: 7pt;
        }
        
        .signatures {
            margin-top: 15px;
            page-break-inside: avoid;
        }
        
        .signature-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        
        .signature-box {
            display: table-cell;
            text-align: center;
            padding: 5px;
            vertical-align: top;
            border: 1px solid #000;
            font-size: 7pt;
        }
        
        .signature-role {
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .signature-img-container {
            height: 40px;
            margin: 5px 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .signature-img {
            max-height: 35px;
            max-width: 80px;
        }
        
        .signature-name {
            font-weight: bold;
            border-top: 1px solid #000;
            display: inline-block;
            min-width: 80px;
            padding-top: 2px;
            margin-top: 3px;
            font-size: 7pt;
        }
        
        .signature-date {
            font-size: 6pt;
            color: #666;
            margin-top: 2px;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/saraswantiLogo.png') }}" alt="Saraswanti Logo">
        </div>
        <div class="header-center">
            <h1>PURCHASING REQUEST</h1>
        </div>
        <div class="header-right">
            <strong>No. PP:</strong>
        </div>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-left">
            <table class="info-table">
                <tr>
                    <td class="label">Departemen</td>
                    <td class="value">: {{ $pr->department->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Pengajuan</td>
                    <td class="value">: {{ $pr->request_date->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis/Pekerjaan/Unit/Stadium/Kategori</td>
                    <td class="value">: {{ $pr->subDepartment->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">No. PP</td>
                    <td class="value">: {{ $pr->pr_number }}</td>
                </tr>
            </table>
        </div>
        <div class="info-right">
            <div class="budget-info">
                <div><span class="budget-label">Total Anggaran</span>: <strong>Rp {{ number_format($budgetInfo['total'], 0, ',', '.') }}</strong></div>
                <div><span class="budget-label">Actual Pengeluaran</span>: <strong>Rp {{ number_format($budgetInfo['actual'], 0, ',', '.') }}</strong></div>
                <div><span class="budget-label">Permintaan Saat Ini</span>: <strong>Rp {{ number_format($budgetInfo['current'], 0, ',', '.') }}</strong></div>
                <div><span class="budget-label">Saldo Anggaran</span>: <strong>Rp {{ number_format($budgetInfo['saldo'], 0, ',', '.') }}</strong></div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th rowspan="2" class="no-col">No</th>
                <th rowspan="2" class="code-col">Kode<br>Barang</th>
                <th rowspan="2" class="name-col">Nama Barang</th>
                <th rowspan="2" class="spec-col">Detail Spesifikasi</th>
                <th colspan="6">Kuantitas</th>
                <th colspan="3">Kuantitas Disetujui</th>
                <th rowspan="2" class="price-col">Total Harga (Rp)</th>
                <th rowspan="2" class="note-col">Keterangan</th>
            </tr>
            <tr>
                <th class="sub-header qty-col">Sat.</th>
                <th class="sub-header qty-col">Pengajuan</th>
                <th class="sub-header qty-col">Stock</th>
                <th class="sub-header qty-col">Mgr</th>
                <th class="sub-header qty-col">FPD</th>
                <th class="sub-header qty-col">DCOO</th>
                <th class="sub-header qty-col">Anggaran</th>
                <th class="sub-header qty-col">Pengajuan</th>
                <th class="sub-header qty-col">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pr->items as $index => $item)
            @php
                $finalQty = $item->getFinalQuantity();
                
                // Get approval quantities by level/role
                $mgrQty = '';
                $fpdQty = '';
                $dcooQty = '';
                
                foreach($pr->approvals as $approval) {
                    if($approval->adjusted_quantities && isset($approval->adjusted_quantities[$item->id])) {
                        $adjQty = $approval->adjusted_quantities[$item->id];
                        // Map by role name or level
                        if(stripos($approval->role_name, 'manager') !== false) {
                            $mgrQty = $adjQty;
                        } elseif(stripos($approval->role_name, 'fpd') !== false || stripos($approval->role_name, 'epd') !== false) {
                            $fpdQty = $adjQty;
                        } elseif(stripos($approval->role_name, 'coo') !== false || stripos($approval->role_name, 'dcoo') !== false) {
                            $dcooQty = $adjQty;
                        }
                    }
                }
                
                // Anggaran: Estimasi harga bila ada di product (dari product price_estimation)
                $anggaranPrice = $item->product && $item->product->price_estimation ? $item->product->price_estimation : 0;
                
                // Pengajuan: Estimasi harga dari pembuat PR (price_estimation yang diinput)
                $pengajuanPrice = $item->price_estimation;
                
                // Total: (anggaran atau pengajuan) * kuantitas disetujui
                // Gunakan anggaran jika ada, kalau tidak ada gunakan pengajuan
                $basePrice = $anggaranPrice > 0 ? $anggaranPrice : $pengajuanPrice;
                $totalPrice = $basePrice * $finalQty;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $item->product->code ?? '-' }}</td>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->specification ?? '-' }}</td>
                <td class="text-center">{{ $item->unit ?? '-' }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-center">-</td>
                <td class="text-center">{{ $mgrQty }}</td>
                <td class="text-center">{{ $fpdQty }}</td>
                <td class="text-center">{{ $dcooQty }}</td>
                <td class="text-right">{{ $anggaranPrice > 0 ? number_format($anggaranPrice, 0, ',', '.') : '-' }}</td>
                <td class="text-right">{{ number_format($pengajuanPrice, 0, ',', '.') }}</td>
                <td class="text-center">{{ $finalQty }}</td>
                <td class="text-right">{{ number_format($totalPrice, 0, ',', '.') }}</td>
                <td>-</td>
            </tr>
            @endforeach
            
            <tr class="total-row">
                <td colspan="13" class="text-right">Total Anggaran: Rp {{ number_format($budgetInfo['total'], 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="13" class="text-right" style="font-size: 7pt;">
                    Actual: Rp {{ number_format($budgetInfo['actual'], 0, ',', '.') }} | 
                    Permintaan: Rp {{ number_format($budgetInfo['current'], 0, ',', '.') }} | 
                    Saldo: Rp {{ number_format($budgetInfo['saldo'], 0, ',', '.') }}
                </td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature-row">
            <div class="signature-box" style="width: 50%;">
                <div class="signature-role">Diperiksa Oleh</div>
                <div class="signature-img-container"></div>
                <div class="signature-name">_________________</div>
            </div>
            <div class="signature-box" style="width: 50%;">
                <div class="signature-role">Disetujui Oleh,</div>
                <div class="signature-img-container"></div>
                <div class="signature-name">_________________</div>
            </div>
        </div>
        
        <div class="signature-row">
            @php
                // Define fixed positions for specific roles
                $rolePositions = [
                    'KTU' => null,
                    'Estate Manager' => null,
                    'Finance' => null,
                    'Cost Control' => null,
                    'Head EPD' => null,
                    'Deputy COO' => null
                ];
                
                // Fill in approvers
                foreach($approvals as $approval) {
                    $roleName = $approval->role_name;
                    if(stripos($roleName, 'ktu') !== false) {
                        $rolePositions['KTU'] = $approval;
                    } elseif(stripos($roleName, 'estate') !== false || stripos($roleName, 'manager') !== false) {
                        $rolePositions['Estate Manager'] = $approval;
                    } elseif(stripos($roleName, 'finance') !== false) {
                        $rolePositions['Finance'] = $approval;
                    } elseif(stripos($roleName, 'cost') !== false) {
                        $rolePositions['Cost Control'] = $approval;
                    } elseif(stripos($roleName, 'epd') !== false) {
                        $rolePositions['Head EPD'] = $approval;
                    } elseif(stripos($roleName, 'coo') !== false) {
                        $rolePositions['Deputy COO'] = $approval;
                    }
                }
            @endphp
            
            @foreach($rolePositions as $role => $approval)
            <div class="signature-box" style="width: 16.66%;">
                <div class="signature-role">{{ $role }}</div>
                <div class="signature-img-container">
                    @if($approval && $approval->approver->signature_path)
                        @php
                            $signaturePath = storage_path('app/public/' . $approval->approver->signature_path);
                            if(file_exists($signaturePath)) {
                                $imageData = base64_encode(file_get_contents($signaturePath));
                                $imageMime = mime_content_type($signaturePath);
                                $base64Image = 'data:' . $imageMime . ';base64,' . $imageData;
                            } else {
                                $base64Image = null;
                            }
                        @endphp
                        @if(isset($base64Image) && $base64Image)
                            <img src="{{ $base64Image }}" alt="Signature" class="signature-img">
                        @endif
                    @endif
                </div>
                <div class="signature-name">
                    @if($approval)
                        {{ $approval->approver->name }}
                    @else
                        _________
                    @endif
                </div>
                @if($approval)
                <div class="signature-date">{{ $approval->approved_at->format('d/m/Y') }}</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</body>
</html>
