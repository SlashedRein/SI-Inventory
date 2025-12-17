@php
    use Carbon\Carbon;
    // Default store info
    $toko_nama = 'DEWI COOKIES';
    $toko_sub = 'Aneka Kue Kering';
    $toko_alamat = 'Perum Telaga Murni Blok C8 No.18, Cikarang Barat';
    $toko_hp = '0852-8756-0800';
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size:10px; margin:0; padding:0; background:#f0f0f0; color:#000 }
        .struk-container { width:58mm; min-height:100mm; background:#fff; margin:20px auto; padding:10px 5px; box-shadow:0 4px 10px rgba(0,0,0,0.1) }
        .text-center{ text-align:center } .text-right{ text-align:right } .bold{ font-weight:700 }
        .logo { font-size:16px; font-weight:800; text-transform:uppercase; margin-bottom:2px }
        .sub-logo { font-size:9px; margin-bottom:5px; color:#555 }
        .address { font-size:8px; color:#333; line-height:1.2; margin-bottom:5px }
        .dashed-line{ border-top:1px dashed #000; margin:8px 0; width:100% }
        .meta-info{ display:flex; justify-content:space-between; font-size:9px; margin-bottom:2px }
        table{ width:100%; border-collapse:collapse; margin-top:5px }
        td{ vertical-align:top; padding:2px 0 }
        .item-name{ font-size:10px; font-weight:600; display:block }
        .item-detail{ font-size:9px; color:#333 }
        .item-price{ font-size:10px; font-weight:600 }
        .total-section{ margin-top:5px }
        .row-total{ display:flex; justify-content:space-between; margin-bottom:2px; font-size:10px }
        .grand-total{ font-size:12px; font-weight:800; margin-top:5px }
        .footer{ margin-top:15px; text-align:center; font-size:9px; color:#333 }
        @media print {
            @page { size: 58mm auto; margin: 0; padding: 0; }
            * { margin: 0; padding: 0; }
            html { margin: 0; padding: 0; }
            body { margin: 0 !important; padding: 0 !important; text-align: center; background: #fff; }
            .struk-container { width: 58mm; margin: 10mm auto; box-shadow: none; padding: 10px 5px; page-break-inside: avoid; text-align: left; }
            .no-print { display: none !important; }
        }
        .btn-print{ position:fixed; bottom:20px; right:20px; background:#25D366; color:white; border:none; padding:12px 24px; border-radius:50px; font-weight:700; cursor:pointer }
    </style>
</head>

<body>

    <div class="struk-container">
        <div class="header text-center">
            <div class="logo">{{ $toko_nama }}</div>
            <div class="sub-logo">{{ $toko_sub }}</div>
            <div class="address">{{ $toko_alamat }}<br>WA: {{ $toko_hp }}</div>
        </div>

        <div class="dashed-line"></div>

        <div class="meta-info">
            <span>No: #{{ str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT) }}</span>
            <span>{{ Carbon::parse($penjualan->tgl_penjualan)->format('d/m/y H:i') }}</span>
        </div>
        <div class="meta-info">
            <span>Cust: {{ Str::limit(optional($penjualan->customer)->nama ?? 'Umum', 15) }}</span>
            <span>Kasir: {{ $penjualan->user->name ?? 'Admin' }}</span>
        </div>

        <div class="dashed-line"></div>

        <table>
            @php $total_qty = 0; @endphp
            @foreach($penjualan->detail as $row)
                @php $total_qty += $row->jumlah; @endphp
                <tr>
                    <td colspan="2"><span class="item-name">{{ $row->produk->nama_produk ?? '[Produk Dihapus]' }}</span></td>
                </tr>
                <tr>
                    <td class="item-detail">{{ $row->jumlah }} x {{ number_format($row->harga_satuan ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right item-price">{{ number_format($row->sub_total ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>

        <div class="dashed-line"></div>

        <div class="total-section">
            <div class="row-total"><span>Total Item</span><span>{{ $total_qty }}</span></div>
            <div class="row-total grand-total"><span>TOTAL</span><span>Rp {{ number_format($penjualan->total ?? 0, 0, ',', '.') }}</span></div>
        </div>

        <div class="dashed-line"></div>

        <div class="footer">
            <p class="bold">TERIMA KASIH</p>
            <p>Selamat Menikmati Cookies Kami</p>
            <p>IG: @dewicookies</p>
        </div>

        <br>
        <div class="text-center" style="font-size:8px;">.</div>
    </div>

    <button class="no-print btn-print" onclick="window.print()">🖨️ Cetak</button>

</body>

</html>