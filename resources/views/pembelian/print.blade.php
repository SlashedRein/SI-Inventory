<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO-{{ str_pad($pembelian->id_beli, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            background-color: white;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 { font-size: 28px; color: #0d6efd; margin-bottom: 5px; }
        .header p { color: #666; font-size: 14px; }
        .header .po-number {
            font-size: 18px;
            font-weight: bold;
            color: #0d6efd;
            margin-top: 10px;
        }
        
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 30px;
        }
        .info-block h3 { font-size: 12px; text-transform: uppercase; color: #999; margin-bottom: 8px; font-weight: 600; }
        .info-block p { font-size: 16px; color: #333; margin-bottom: 5px; }
        .info-block .label { font-size: 13px; color: #666; }
        
        .items-section {
            margin-bottom: 30px;
        }
        .items-section h3 { font-size: 12px; text-transform: uppercase; color: #999; margin-bottom: 15px; font-weight: 600; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table thead { background-color: #f8f9fa; }
        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            color: #555;
            text-transform: uppercase;
            border-bottom: 2px solid #dee2e6;
        }
        table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
            color: #333;
        }
        table tbody tr:last-child td { border-bottom: 2px solid #dee2e6; }
        
        th.text-center, td.text-center { text-align: center; }
        th.text-right, td.text-right { text-align: right; }
        
        .quantity { font-weight: 600; color: #0d6efd; }
        .price { font-weight: 600; color: #333; }
        .total-row td { font-weight: bold; background-color: #f8f9fa; }
        .total-amount { font-size: 18px; color: #0d6efd; }
        
        .summary {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }
        .summary-box {
            width: 300px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .summary-row.total {
            font-size: 18px;
            font-weight: bold;
            color: #0d6efd;
            border-top: 2px solid #dee2e6;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .footer {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 50px;
            padding-top: 40px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            text-align: center;
        }
        .signature-block { }
        .signature-block .line { border-top: 1px solid #333; margin-top: 50px; margin-bottom: 10px; }
        .signature-block .name { font-weight: 600; }
        
        .notes {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 12px;
            color: #856404;
        }
        .notes strong { color: #333; }
        
        @media print {
            body { background-color: white; padding: 0; }
            .container { box-shadow: none; border-radius: 0; }
            .btn-print { display: none; }
        }
        
        .btn-print {
            display: block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
        }
        .btn-print:hover { background-color: #0b5ed7; }
        
        .deleted-item { color: #999; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <button class="btn-print" onclick="window.print();">🖨️ Cetak / Print</button>
        
        <div class="header">
            <h1>PURCHASE ORDER</h1>
            <p>Pesanan Pembelian Bahan Baku</p>
            <div class="po-number">
                PO-{{ str_pad($pembelian->id_beli, 5, '0', STR_PAD_LEFT) }}
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-block">
                <h3>📅 Tanggal PO</h3>
                <p>{{ date('d Maret Y', strtotime($pembelian->tgl)) }}</p>
                <p class="label">{{ date('H:i', strtotime($pembelian->created_at ?? now())) }}</p>
            </div>
            <div class="info-block">
                <h3>👤 Petugas Pemesan</h3>
                <p>{{ $pembelian->user->name ?? 'Admin' }}</p>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-block">
                <h3>🏪 Supplier</h3>
                <p>{{ optional($pembelian->supplier)->nama_supplier ?? 'N/A' }}</p>
                <p class="label">{{ optional($pembelian->supplier)->alamat ?? '-' }}</p>
            </div>
        </div>
        
        <div class="items-section">
            <h3>📦 Daftar Bahan Baku</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nama Bahan Baku</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Satuan</th>
                        <th class="text-right">Harga Satuan</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if($pembelian->detail && count($pembelian->detail) > 0)
                        @foreach($pembelian->detail as $item)
                        <tr>
                            <td>
                                @if($item->bahan && $item->bahan->nama_bahan)
                                    {{ $item->bahan->nama_bahan }}
                                @else
                                    <span class="deleted-item">[Bahan Dihapus]</span>
                                @endif
                            </td>
                            <td class="text-center quantity">{{ $item->jumlah }}</td>
                            <td class="text-center">
                                @if($item->bahan && $item->bahan->satuan)
                                    {{ $item->bahan->satuan }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right price">Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right price">Rp {{ number_format($item->sub_total ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999;">Tidak ada item</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <div class="summary">
            <div class="summary-box">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>Rp {{ number_format($pembelian->total_beli ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>PPN (0%):</span>
                    <span>Rp 0</span>
                </div>
                <div class="summary-row total">
                    <span>Total Pembelian:</span>
                    <span>Rp {{ number_format($pembelian->total_beli ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <div class="signature-block">
                <strong>Dibuat oleh</strong>
                <div class="line"></div>
                <div class="name">{{ $pembelian->user->name ?? 'Admin' }}</div>
                <div style="font-size: 11px; color: #999;">{{ date('d M Y', strtotime($pembelian->created_at ?? now())) }}</div>
            </div>
            <div class="signature-block">
                <strong>Disetujui oleh</strong>
                <div class="line"></div>
                <div class="name">_______________</div>
                <div style="font-size: 11px; color: #999;">Tanda Tangan</div>
            </div>
        </div>
    </div>
</body>
</html>
