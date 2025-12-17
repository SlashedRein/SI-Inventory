<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ str_pad($penjualan->id_penjualan, 6, '0', STR_PAD_LEFT) }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{ font-family: Arial, sans-serif; color:#333; margin:20px; }
        .invoice-container{ max-width:800px; margin:0 auto; }
        .invoice-header{ display:flex; justify-content:space-between; }
        .company-name{ font-size:28px; color:#D4AF37; font-weight:700 }
        .company-sub{ color:#D4AF37; font-weight:700 }
        table{ width:100%; border-collapse:collapse; margin-top:12px }
        th, td{ border:1px solid #ddd; padding:8px }
        th{ background:#f5f5f5; }
        .text-right{ text-align:right }
        .text-center{text-align:center}
        @media print { .no-print{ display:none } }
    </style>
</head>
<body>
<div class="invoice-container">
    <div class="invoice-header">
        <div>
            <div class="company-name">dewi <span style="color:#444;">Cookies</span></div>
            <div class="company-sub">Aneka Kue Kering</div>
            <div style="font-size:13px; color:#666; margin-top:6px;">
                Perum Telaga Murni Blok C8 No.18<br>
                Jln. Mangga II Desa Telaga Murni, Cikarang Barat<br>
                HP: 0852 87560 800 | Email: dewicookies73@gmail.com
            </div>
        </div>
        <div style="min-width:220px; text-align:left">
            <div>{{ 'Cikarang, ' . \Carbon\Carbon::parse($penjualan->tgl_penjualan)->format('d F Y') }}</div>
            <div style="margin-top:10px; font-weight:700">Kepada Yth,</div>
            <div style="min-height:18px; border-bottom:1px dotted #ccc; padding-bottom:4px;">
                {{ optional($penjualan->customer)->nama ?? '.......................................' }}
            </div>
            <div style="min-height:18px; border-bottom:1px dotted #ccc; padding-bottom:4px; margin-top:6px;">
                {{ optional($penjualan->customer)->alamat ? \Illuminate\Support\Str::limit(optional($penjualan->customer)->alamat, 40) : '.......................................' }}
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">NO.</th>
                <th width="55%">Nama Menu</th>
                <th width="15%" class="text-right">Harga Satuan</th>
                <th width="25%" class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($penjualan->detail as $row)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>
                    @if($row->produk && $row->produk->nama_produk)
                        {{ $row->produk->nama_produk }}
                    @else
                        <em>[Produk Dihapus]</em>
                    @endif
                    <span style="float:right; font-size:12px; color:#666;">(x{{ $row->jumlah }})</span>
                </td>
                <td class="text-right">Rp {{ number_format($row->harga_satuan ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($row->sub_total ?? 0, 0, ',', '.') }}</td>
            </tr>
            @endforeach

            @for($i=0;$i<3;$i++)
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endfor

            <tr>
                <td colspan="3" class="text-right" style="font-weight:700">TOTAL Rp.</td>
                <td class="text-right" style="font-weight:700">{{ number_format($penjualan->total ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="display:flex; justify-content:space-between; margin-top:30px">
        <div style="width:30%; text-align:center">Penerima<div style="margin-top:60px; border-top:1px dotted #999"></div></div>
        <div style="width:30%; text-align:center">Hormat kami<div style="margin-top:60px; border-top:1px dotted #999"></div></div>
    </div>

    <div style="margin-top:20px; font-style:italic; color:#D4AF37; font-weight:700">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan, kecuali ada perjanjian. Terima Kasih</div>

    <div class="no-print" style="margin-top:12px; text-align:right">
        <button onclick="window.print()" style="padding:8px 14px; background:#D4AF37; border:none; color:white; border-radius:4px; cursor:pointer">🖨️ Cetak</button>
    </div>
</div>
</body>
</html>
