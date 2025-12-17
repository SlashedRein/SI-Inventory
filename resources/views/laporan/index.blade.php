@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<style>
    @media print { .no-print { display: none !important; } }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-primary">📊 Laporan Penjualan</h2>
        <p class="text-muted mb-0">Monitor data penjualan harian, mingguan, dan bulanan.</p>
    </div>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-outline-secondary me-2">🖨️ Print</button>
        <a href="{{ route('laporan.export', request()->all()) }}" class="btn btn-success">📥 Export Excel</a>
    </div>
</div>

<div class="card shadow-sm mb-4 no-print">
    <div class="card-body">
        <form action="{{ route('laporan.index') }}" method="GET" id="filterForm">
            <div class="row g-3 align-items-center mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase text-muted">Quick Filter Waktu</label><br>
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('laporan.index', ['tgl_awal' => date('Y-m-d'), 'tgl_akhir' => date('Y-m-d'), 'tipe' => $tipe]) }}" class="btn btn-outline-primary {{ $startDate == date('Y-m-d') && $endDate == date('Y-m-d') ? 'active' : '' }}">Hari Ini</a>
                        <a href="{{ route('laporan.index', ['tgl_awal' => \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d'), 'tgl_akhir' => \Carbon\Carbon::now()->endOfWeek()->format('Y-m-d'), 'tipe' => $tipe]) }}" class="btn btn-outline-primary">Minggu Ini</a>
                        <a href="{{ route('laporan.index', ['tgl_awal' => date('Y-m-01'), 'tgl_akhir' => date('Y-m-t'), 'tipe' => $tipe]) }}" class="btn btn-outline-primary {{ $startDate == date('Y-m-01') ? 'active' : '' }}">Bulan Ini</a>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase text-muted">Tipe Pelanggan</label>
                    <div class="nav nav-pills nav-fill bg-light p-1 rounded border">
                        <a class="nav-link {{ $tipe == 'semua' ? 'active' : '' }}" href="{{ route('laporan.index', ['tgl_awal' => $startDate, 'tgl_akhir' => $endDate, 'tipe' => 'semua']) }}">Semua</a>
                        <a class="nav-link {{ $tipe == 'biasa' ? 'active' : '' }}" href="{{ route('laporan.index', ['tgl_awal' => $startDate, 'tgl_akhir' => $endDate, 'tipe' => 'biasa']) }}">👤 Pelanggan Biasa</a>
                        <a class="nav-link {{ $tipe == 'tetap' ? 'active' : '' }}" href="{{ route('laporan.index', ['tgl_awal' => $startDate, 'tgl_akhir' => $endDate, 'tipe' => 'tetap']) }}">🏢 Tetap (PT/CV)</a>
                    </div>
                    <input type="hidden" name="tipe" value="{{ $tipe }}">
                </div>
            </div>

            <hr class="text-muted">

            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted small">Custom Tanggal Mulai</label>
                    <input type="date" name="tgl_awal" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small">Custom Tanggal Akhir</label>
                    <input type="date" name="tgl_akhir" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-secondary w-100">🔍 Terapkan Filter Manual</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">
            Hasil:
            @if($tipe == 'tetap') <span class="badge bg-primary">Pelanggan Tetap (PT/CV)</span>
            @elseif($tipe == 'biasa') <span class="badge bg-success">Pelanggan Biasa</span>
            @else <span class="badge bg-secondary">Semua Data</span>
            @endif
            <small class="text-muted ms-2 fw-normal" style="font-size: 0.9rem">({{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }})</small>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-dark">
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
                        <td class="fw-bold">{{ $nama }}</td>
                        <td class="text-center">
                            @if($isPerusahaan)
                                <span class="badge bg-primary">🏢 Perusahaan</span>
                            @else
                                <span class="badge bg-success">👤 Personal</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <h4 class="fw-bold">Tidak ada data 😔</h4>
                            <p>Coba ubah filter tanggal atau tipe pelanggan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <td colspan="4" class="text-end fw-bold fs-5">GRAND TOTAL</td>
                        <td class="text-end fw-bold fs-5 text-primary">Rp {{ number_format($transaksi->sum('total'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection