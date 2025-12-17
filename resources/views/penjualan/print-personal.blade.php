<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk {{ str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT) }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{ font-family: monospace; margin:10px; }
        .struk{ width:80mm; }
        .text-right{text-align:right}
        .text-center{text-align:center}
        @media print{ .no-print{ display:none } }
    </style>
</head>
<body>
<div class="struk">
    <div class="text-center" style="font-weight:700;">Gudang Kue</div>
    <div class="text-center" style="font-size:12px;">Struk Penjualan</div>
    <div style="margin-top:6px">No: {{ str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT) }}</div>
    <div style="margin-top:4px">Tanggal: {{ \Carbon\Carbon::parse($penjualan->tgl_penjualan)->format('d-m-Y H:i') }}</div>

    <table style="width:100%; margin-top:8px;">
        @foreach($penjualan->detail as $row)
        <tr>
            <td>{{ $row->produk->nama_produk ?? '[Produk Dihapus]' }}</td>
            <td class="text-center">{{ $row->jumlah }}</td>
            <td class="text-right">{{ number_format($row->sub_total ?? 0,0,',','.') }}</td>
        </tr>
        @endforeach
    </table>

    <div style="margin-top:6px; font-weight:700" class="text-right">Total: Rp {{ number_format($penjualan->total ?? 0,0,',','.') }}</div>

    <div class="no-print" style="margin-top:10px; text-align:center">
        <button onclick="window.print()" style="padding:6px 12px;">Cetak</button>
    </div>
</div>
</body>
</html>
