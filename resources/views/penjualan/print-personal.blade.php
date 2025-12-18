<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #{{ $penjualan->id_penjualan }}</title>

    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 10px;
            margin: 0;
            background: #f0f0f0;
        }

        .struk-container {
            width: 58mm;
            background: #fff;
            margin: 20px auto;
            padding: 10px 5px;
            box-shadow: 0 4px 10px rgba(0,0,0,.15);
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }

        .logo { font-size: 16px; font-weight: 800; }
        .sub-logo { font-size: 9px; color: #555; }
        .address { font-size: 8px; color: #333; }

        .dashed-line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
        }

        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }

        .item-name { font-weight: 600; font-size: 10px; }
        .item-detail { font-size: 9px; }
        .item-price { font-weight: 600; }

        .row-total {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }

        .grand-total {
            font-size: 12px;
            font-weight: 800;
        }

        .footer { text-align: center; font-size: 9px; margin-top: 10px; }

        @media print {
            body { background: #fff; }
            .struk-container { box-shadow: none; margin: 0; }
            .no-print { display: none; }
        }

        .btn-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #25D366;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 14px;
        }
    </style>
</head>

<body>

@php
    $toko_nama   = 'DEWI COOKIES';
    $toko_sub    = 'Aneka Kue Kering';
    $toko_alamat = 'Perum Telaga Murni Blok C8 No.18, Cikarang Barat';
    $toko_hp     = '0852-8756-0800';
    $totalQty   = $penjualan->detail->sum('jumlah');
@endphp

<div class="struk-container">

    <div class="text-center">
        <div class="logo">{{ $toko_nama }}</div>
        <div class="sub-logo">{{ $toko_sub }}</div>
        <div class="address">
            {{ $toko_alamat }}<br>
            WA: {{ $toko_hp }}
        </div>
    </div>

    <div class="dashed-line"></div>

    <div class="meta-info">
        <span>No: #{{ str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT) }}</span>
        <span>{{ \Carbon\Carbon::parse($penjualan->tgl_penjualan)->format('d/m/y H:i') }}</span>
    </div>
    <div class="meta-info">
        <span>Cust: {{ Str::limit($penjualan->customer->nama ?? 'Umum', 15) }}</span>
        <span>Kasir: {{ $penjualan->user->name ?? 'Admin' }}</span>
    </div>

    <div class="dashed-line"></div>

    <table>
        @foreach($penjualan->detail as $d)
            <tr>
                <td colspan="2">
                    <span class="item-name">{{ $d->produk->nama_produk ?? 'Produk Dihapus' }}</span>
                </td>
            </tr>
            <tr>
                <td class="item-detail">
                    {{ $d->jumlah }} x {{ number_format($d->harga_satuan,0,',','.') }}
                </td>
                <td class="text-right item-price">
                    {{ number_format($d->sub_total,0,',','.') }}
                </td>
            </tr>
        @endforeach
    </table>

    <div class="dashed-line"></div>

    <div class="row-total">
        <span>Total Item</span>
        <span>{{ $totalQty }}</span>
    </div>
    <div class="row-total grand-total">
        <span>TOTAL</span>
        <span>Rp {{ number_format($penjualan->total,0,',','.') }}</span>
    </div>

    <div class="dashed-line"></div>

    <div class="footer">
        <p class="bold">TERIMA KASIH</p>
        <p>Selamat Menikmati Cookies Kami</p>
        <p>IG: @dewicookies</p>
    </div>
</div>

<button class="btn-print no-print" onclick="window.print()">🖨️ Cetak</button>

</body>
</html>
