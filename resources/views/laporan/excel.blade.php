<?php
// Header agar file didownload sebagai Excel
header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_DewiCookies.xls");
?>

<center>
    <h3>Laporan Penjualan</h3>
    <p>Periode: {{ $startDate }} s/d {{ $endDate }}</p>
</center>

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Pelanggan</th>
            <th>Tipe</th>
            <th>Total Bayar</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transaksi as $key => $item)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $item->tgl_penjualan }}</td>
            <td>{{ optional($item->customer)->nama }}</td>
            <td>
                {{ preg_match('/(PT|CV|UD)/i', optional($item->customer)->nama) ? 'PERUSAHAAN' : 'PERORANGAN' }}
            </td>
            <td>{{ $item->total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>