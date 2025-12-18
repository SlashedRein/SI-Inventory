<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Pembelian #{{ $pembelian->id_beli }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #f0f0f0;
        }

        .invoice-box {
            background: #fff;
            max-width: 210mm;
            margin: 0 auto;
            padding: 10mm;
            min-height: 297mm;
            box-shadow: 0 0 10px rgba(0,0,0,.15);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .header-left h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header-left p {
            margin: 2px 0;
        }

        .header-right-box {
            border: 1px solid #000;
            width: 300px;
        }

        .box-row {
            display: flex;
            border-bottom: 1px solid #000;
        }

        .box-row:last-child {
            border-bottom: none;
        }

        .box-col {
            flex: 1;
            padding: 5px;
            text-align: center;
            border-right: 1px solid #000;
            font-weight: bold;
            font-size: 11px;
        }

        .box-col:last-child {
            border-right: none;
        }

        .box-val {
            font-weight: normal;
        }

        .to-section {
            margin-bottom: 20px;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 10px;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid #000;
            padding: 8px;
        }

        table.main-table th {
            background: #e0e0e0;
            font-size: 11px;
            text-align: center;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .footer-total {
            text-align: right;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            text-align: center;
        }

        .sig-box { width: 30%; }
        .sig-space { height: 80px; }
        .sig-line {
            border-top: 1px dotted #000;
            margin-top: 5px;
            width: 80%;
            display: inline-block;
        }

        .disclaimer {
            text-align: center;
            font-size: 10px;
            margin-top: 20px;
            font-style: italic;
        }

        .bank-info {
            margin-top: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .invoice-box { box-shadow: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>

<body>

<div class="invoice-box">

    {{-- HEADER --}}
    <div class="header-container">
        <div class="header-left">
            <h2>{{ strtoupper($pembelian->supplier->nama_supplier ?? '-') }}</h2>
            <p>Supplier Bahan</p>
            <p>{{ $pembelian->supplier->alamat ?? '-' }}</p>
            <p>Wa. {{ $pembelian->supplier->no_telp ?? '-' }}</p>
        </div>

        <div class="header-right-box">
            <div class="box-row">
                <div class="box-col">DATE</div>
                <div class="box-col">DUE</div>
                <div class="box-col">TOP</div>
            </div>
            <div class="box-row">
                <div class="box-col box-val">{{ \Carbon\Carbon::parse($pembelian->tgl)->format('d M Y') }}</div>
                <div class="box-col box-val">{{ \Carbon\Carbon::parse($pembelian->tgl)->format('d M Y') }}</div>
                <div class="box-col box-val">0 Days</div>
            </div>
        </div>
    </div>

    {{-- TO --}}
    <div class="to-section">
        <strong>DEWI COOKIES (Cikarang)</strong><br>
        Perum Telaga Murni Jl. Mangga 2 Blok C8 No.18<br>
        0812 9631 5967
    </div>

    {{-- TABLE --}}
    <table class="main-table">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="45%" class="text-left">NAME</th>
                <th width="10%">QTY</th>
                <th width="20%" class="text-right">PRICE (RP)</th>
                <th width="20%" class="text-right">TOTAL (RP)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $grandQty = 0; @endphp
            @foreach($pembelian->detail as $d)
                @php $grandQty += $d->jumlah; @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $d->bahan->nama_bahan ?? 'Bahan Dihapus' }}</td>
                    <td class="text-center">{{ $d->jumlah }}</td>
                    <td class="text-right">{{ number_format($d->harga_satuan,0,',','.') }}</td>
                    <td class="text-right">{{ number_format($d->sub_total,0,',','.') }}</td>
                </tr>
            @endforeach

            @for($i=0;$i<3;$i++)
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
            @endfor
        </tbody>
    </table>

    <div class="footer-total">
        Total Qty : {{ $grandQty }} |
        Amount : Rp {{ number_format($pembelian->total_beli,0,',','.') }}
    </div>

    <div class="signature-section">
        <div class="sig-box">
            Penerima<br><div class="sig-space"></div><div class="sig-line"></div>
        </div>
        <div class="sig-box">
            Pengemudi<br><div class="sig-space"></div><div class="sig-line"></div>
        </div>
        <div class="sig-box">
            Mengetahui<br><div class="sig-space"></div><div class="sig-line"></div>
        </div>
    </div>

    <div class="disclaimer">
        Periksa kembali barang anda.<br>
        Barang yang sudah dibeli tidak dapat dikembalikan.
    </div>

    <div class="bank-info">
        NO REKENING {{ strtoupper($pembelian->supplier->nama_supplier ?? '-') }}<br>
        ATAS NAMA : {{ $pembelian->supplier->nama_supplier ?? '-' }}<br>
        NO REKENING : {{ $pembelian->supplier->no_rek ?? '-' }}
    </div>

    <div style="font-size:10px;margin-top:10px;font-style:italic">
        Print by system at {{ now()->format('d M Y H:i') }}
    </div>

</div>

<div class="no-print" style="position:fixed;top:20px;right:20px">
    <button onclick="window.print()">🖨️ Cetak</button>
    <button onclick="window.close()">Tutup</button>
</div>

</body>
</html>
