@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<style>
@media print {

    body {
        font-size: 12px;
        color: #000;
        background: #fff;
    }

    .no-print {
        display: none !important;
    }

    .card, .shadow-sm {
        box-shadow: none !important;
        border: none !important;
    }

    .print-title {
        text-align: center;
        margin-bottom: 20px;
    }

    .print-title h2 {
        font-size: 18px;
        margin-bottom: 4px;
        font-weight: bold;
    }

    .print-title p {
        font-size: 12px;
        margin: 0;
    }

    table {
        width: 100%;
        border-collapse: collapse !important;
    }

    table th,
    table td {
        border: 1px solid #000 !important;
        padding: 6px !important;
        background: #fff !important;
        color: #000 !important;
    }

    thead {
        background: #f2f2f2 !important;
    }

    .badge {
        background: none !important;
        color: #000 !important;
        padding: 0 !important;
        border: none !important;
        font-weight: normal !important;
    }

    tfoot td {
        font-weight: bold;
        font-size: 14px;
    }

    @page {
        size: A4;
        margin: 15mm;
    }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <h2 class="fw-bold text-primary">📊 Laporan Penjualan</h2>
        <p class="text-muted mb-0">Monitor data penjualan harian, mingguan, dan bulanan.</p>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-outline-secondary me-2">🖨️ Print</button>
        <a href="{{ route('laporan.export', request()->all()) }}" class="btn btn-success">📥 Export Excel</a>
    </div>
</div>

{{-- HEADER KHUSUS CETAK --}}
<div class="print-title d-none d-print-block">
    <h2>LAPORAN PENJUALAN</h2>
    <p>
        Periode {{ date('d M Y', strtotime($startDate)) }}
        s/d {{ date('d M Y', strtotime($endDate)) }}
    </p>
</div>

<div class="card shadow-sm mb-4 no-print">
    <div class="card-body">
        <form action="{{ route('laporan.index') }}" method="GET">
            <div class="row g-3 align-items-center mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase text-muted">Quick Filter Waktu</label>
                    <div class="btn-group w-100">
                        <a href="{{ route('laporan.index', ['tgl_awal' => date('Y-m-d'), 'tgl_akhir' => date('Y-m-d'), 'tipe' => $tipe]) }}" class="btn btn-outline-primary">Hari Ini</a>
                        <a href="{{ route('laporan.index', ['tgl_awal' => \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d'), 'tgl_akhir' => \Carbon\Carbon::now()->endOfWeek()->format('Y-m-d'), 'tipe' => $tipe]) }}" class="btn btn-outline-primary">Minggu Ini</a>
                        <a href="{{ route('laporan.index', ['tgl_awal' => date('Y-m-01'), 'tgl_akhir' => date('Y-m-t'), 'tipe' => $tipe]) }}" class="btn btn-outline-primary">Bulan Ini</a>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tipe Pelanggan</label>
                    <div class="nav nav-pills nav-fill bg-light p-1 rounded border">
                        <a class="nav-link {{ $tipe == 'semua' ? 'active' : '' }}" href="{{ route('laporan.index', ['tgl_awal' => $startDate, 'tgl_akhir' => $endDate, 'tipe' => 'semua']) }}">Semua</a>
                        <a class="nav-link {{ $tipe == 'biasa' ? 'active' : '' }}" href="{{ route('laporan.index', ['tgl_awal' => $startDate, 'tgl_akhir' => $endDate, 'tipe' => 'biasa']) }}">Pelanggan Biasa</a>
                        <a class="nav-link {{ $tipe == 'tetap' ? 'active' : '' }}" href="{{ route('laporan.index', ['tgl_awal' => $startDate, 'tgl_akhir' => $endDate, 'tipe' => 'tetap']) }}">Tetap (PT/CV)</a>
                    </div>
                    <input type="hidden" name="tipe" value="{{ $tipe }}">
                </div>
            </div>

            <hr>

            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Tanggal Mulai</label>
                    <input type="date" name="tgl_awal" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Tanggal Akhir</label>
                    <input type="date" name="tgl_akhir" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-secondary w-100">Terapkan Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th class="text-center" width="5%">No</th>
                    <th width="20%">Tanggal</th>
                    <th width="35%">Nama Pelanggan</th>
                    <th width="15%" class="text-center">Tipe</th>
                    <th width="25%" class="text-end">Total Bayar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $key => $item)
                @php
                    $nama = optional($item->customer)->nama ?? 'Tanpa Nama';
                    $isPerusahaan = preg_match('/(PT|CV|UD)/i', $nama);
                @endphp
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tgl_penjualan)->isoFormat('D MMMM Y') }}</td>
                    <td>{{ $nama }}</td>
                    <td class="text-center">
                        {{ $isPerusahaan ? 'Perusahaan' : 'Personal' }}
                    </td>
                    <td class="text-end">
                        Rp {{ number_format($item->total, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4">
                        Tidak ada data
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end">GRAND TOTAL</td>
                    <td class="text-end">
                        Rp {{ number_format($transaksi->sum('total'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection
