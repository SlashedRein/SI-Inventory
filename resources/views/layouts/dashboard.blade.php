@extends('layouts.app')

@section('title', 'Dashboard')
@section('title_page', 'Overview Bisnis')

@section('content')
<div class="row g-4 mb-4">
    @if(Auth::user()->role == 'owner')
    <div class="col-md-6 col-xl-3">
        <div class="card-custom p-4 h-100 position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 p-3 opacity-10">
                <i class="bi bi-wallet2 display-1 text-success"></i>
            </div>
            <p class="text-muted fw-bold small text-uppercase mb-1">Pendapatan Bulan Ini</p>
            <h3 class="fw-bold text-dark mb-0">Rp {{ number_format(\App\Models\Penjualan::sum('total'), 0, ',', '.') }}</h3>
            <span class="badge bg-success bg-opacity-10 text-success mt-2 rounded-pill px-2">
                <i class="bi bi-arrow-up-short"></i> Stabil
            </span>
        </div>
    </div>
    
    <div class="col-md-6 col-xl-3">
        <div class="card-custom p-4 h-100 position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 p-3 opacity-10">
                <i class="bi bi-cart display-1 text-danger"></i>
            </div>
            <p class="text-muted fw-bold small text-uppercase mb-1">Pengeluaran Bahan</p>
            <h3 class="fw-bold text-dark mb-0">Rp {{ number_format(\App\Models\Pembelian::sum('total_beli'), 0, ',', '.') }}</h3>
            <span class="badge bg-danger bg-opacity-10 text-danger mt-2 rounded-pill px-2">
                Restock
            </span>
        </div>
    </div>
    @endif

    <div class="col-md-6 col-xl-3">
        <div class="card-custom p-4 h-100 position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 p-3 opacity-10">
                <i class="bi bi-box-seam display-1 text-warning"></i>
            </div>
            <p class="text-muted fw-bold small text-uppercase mb-1">Bahan Menipis</p>
            @php $lowStock = \App\Models\BahanBaku::whereColumn('stok', '<=', 'stok_min')->count(); @endphp
            <h3 class="fw-bold text-dark mb-0">{{ $lowStock }} Item</h3>
            @if($lowStock > 0)
                <span class="text-danger small fw-bold mt-2 d-block">Perlu perhatian segera!</span>
            @else
                <span class="text-success small fw-bold mt-2 d-block">Stok aman terkendali</span>
            @endif
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card-custom p-4 h-100 position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 p-3 opacity-10">
                <i class="bi bi-cake2 display-1 text-info"></i>
            </div>
            <p class="text-muted fw-bold small text-uppercase mb-1">Total Produk</p>
            <h3 class="fw-bold text-dark mb-0">{{ \App\Models\Produk::count() }} Varian</h3>
            <span class="text-muted small mt-2 d-block">Siap dijual di kasir</span>
        </div>
    </div>
</div>

<div class="row g-4">
    
    <div class="col-lg-8">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold m-0">Tren Penjualan (7 Hari Terakhir)</h6>
                <select class="form-select form-select-sm w-auto border-0 bg-light">
                    <option>Minggu Ini</option>
                    <option>Bulan Ini</option>
                </select>
            </div>
            <div id="chartPenjualan"></div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-custom p-4 mb-4">
            <h6 class="fw-bold mb-3">Aksi Cepat</h6>
            <div class="d-grid gap-2">
                <a href="{{ route('penjualan.create') }}" class="btn btn-primary py-2 text-start">
                    <i class="bi bi-basket me-2"></i> Kasir / Transaksi Baru
                </a>
                @if(Auth::user()->role == 'owner')
                <a href="{{ route('pembelian.create') }}" class="btn btn-outline-secondary py-2 text-start border-2">
                    <i class="bi bi-truck me-2"></i> Input Belanja Supplier
                </a>
                @endif
                <a href="{{ route('produk.index') }}" class="btn btn-light py-2 text-start text-muted">
                    <i class="bi bi-search me-2"></i> Cek Katalog Produk
                </a>
            </div>
        </div>

        <div class="card-custom p-4">
            <h6 class="fw-bold mb-3 text-danger"><i class="bi bi-exclamation-circle me-2"></i>Stok Kritis</h6>
            <div class="list-group list-group-flush">
                @forelse(\App\Models\BahanBaku::whereColumn('stok', '<=', 'stok_min')->take(3)->get() as $item)
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center border-0">
                        <div>
                            <div class="fw-bold text-dark">{{ $item->nama_bahan }}</div>
                            <small class="text-muted">Min: {{ $item->stok_min }} {{ $item->satuan }}</small>
                        </div>
                        <span class="badge bg-danger rounded-pill">{{ $item->stok }} {{ $item->satuan }}</span>
                    </div>
                @empty
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-check-circle text-success fs-1 mb-2 d-block"></i>
                        Semua stok aman!
                    </div>
                @endforelse
                @if($lowStock > 3)
                    <a href="{{ route('bahan-baku.index') }}" class="text-center small text-decoration-none mt-2">Lihat {{ $lowStock - 3 }} lainnya...</a>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Konfigurasi ApexCharts (Dummy Data untuk visualisasi)
    var options = {
        series: [{
            name: 'Total Penjualan',
            data: [150000, 230000, 180000, 320000, 250000, 400000, 380000] // Nanti diganti data real controller
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: { show: false },
            fontFamily: 'Plus Jakarta Sans, sans-serif'
        },
        colors: ['#E07A5F'], // Warna Terracotta
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: {
            categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return "Rp " + (value / 1000) + "k";
                }
            }
        },
        grid: {
            borderColor: '#f1f1f1',
        }
    };

    var chart = new ApexCharts(document.querySelector("#chartPenjualan"), options);
    chart.render();
</script>
@endpush
@endsection