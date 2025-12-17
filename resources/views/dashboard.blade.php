@extends('layouts.app')

@section('title', 'Dashboard')
@section('title_page', 'Dashboard')

@push('styles')

<style>
/* === CARD ANIMATION === */
.card-animate {
    opacity: 0;
    transform: translateY(30px) scale(0.97);
    animation: cardEnter 0.6s ease forwards;
}
.card-animate.delay-1 { animation-delay: .1s; }
.card-animate.delay-2 { animation-delay: .2s; }
.card-animate.delay-3 { animation-delay: .3s; }
.card-animate.delay-4 { animation-delay: .4s; }

@keyframes cardEnter {
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.card-custom {
    background: #fff;
    border-radius: 16px;
    border: none;
    box-shadow: 0 6px 22px rgba(0,0,0,0.04);
    transition: all .35s ease;
}

.card-custom:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 14px 40px rgba(224,122,95,.25);
}

.card-custom i {
    transition: transform .3s ease;
}

.card-custom:hover i {
    transform: scale(1.25) rotate(-5deg);
}

/* === CHART ANIMATION === */
.fade-up {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeUp .7s ease forwards;
    animation-delay: .5s;
}

@keyframes fadeUp {
    to { opacity: 1; transform: translateY(0); }
}
</style>

@endpush

@section('content')

<div class="row g-4 mb-4">
    {{-- Total Penjualan --}}
    <div class="col-md-3">
        <a href="{{ route('laporan.index') }}" class="text-decoration-none">
            <div class="card card-custom p-4 card-animate delay-1 h-100" style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Penjualan</p>
                        <h4 class="fw-bold text-dark">{{ 'Rp ' . number_format($total_penjualan, 0, ',', '.') }}</h4>
                    </div>
                    <i class="bi bi-cash-coin fs-2 text-success"></i>
                </div>
            </div>
        </a>
    </div>

    {{-- Produk --}}
    <div class="col-md-3">
        <a href="{{ route('produk.index') }}" class="text-decoration-none">
            <div class="card card-custom p-4 card-animate delay-2 h-100" style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Produk</p>
                        <h4 class="fw-bold text-dark">{{ $total_produk }} Item</h4>
                    </div>
                    <i class="bi bi-cake2-fill fs-2 text-warning"></i>
                </div>
            </div>
        </a>
    </div>

    {{-- Customer --}}
    <div class="col-md-3">
        <a href="{{ route('customer.index') }}" class="text-decoration-none">
            <div class="card card-custom p-4 card-animate delay-3 h-100" style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Customer</p>
                        <h4 class="fw-bold text-dark">{{ $total_customer }}</h4>
                    </div>
                    <i class="bi bi-people-fill fs-2 text-primary"></i>
                </div>
            </div>
        </a>
    </div>

    {{-- Stok Menipis --}}
    <div class="col-md-3">
        <a href="{{ route('produk.stok-minim') }}" class="text-decoration-none">
            <div class="card card-custom p-4 card-animate delay-4 h-100" style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Stok Menipis</p>
                        <h4 class="fw-bold text-dark">{{ $stok_minim_count }} Item</h4>
                    </div>
                    <i class="bi bi-exclamation-triangle-fill fs-2 text-danger"></i>
                </div>
            </div>
        </a>
    </div>

</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card card-custom p-4 fade-up">
            <h6 class="fw-bold mb-3">Penjualan Mingguan</h6>
            <div id="salesChart"></div>
        </div>
    </div>


<div class="col-md-4">
    <div class="card card-custom p-4 fade-up">
        <h6 class="fw-bold mb-3">Produk Terlaris</h6>
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between">Cookies Coklat <span class="fw-bold">120</span></li>
            <li class="list-group-item d-flex justify-content-between">Nastar <span class="fw-bold">95</span></li>
            <li class="list-group-item d-flex justify-content-between">Brownies <span class="fw-bold">80</span></li>
        </ul>
    </div>
</div>

</div>

@endsection

@push('scripts')

<script>
var options = {
    chart: { type: 'area', height: 300, toolbar: { show: false } },
    series: [{ name: 'Penjualan', data: [10, 18, 15, 22, 28, 30, 35] }],
    xaxis: { categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] },
    colors: ['#E07A5F'],
    stroke: { curve: 'smooth', width: 3 },
    fill: {
        type: 'gradient',
        gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 }
    }
};
new ApexCharts(document.querySelector("#salesChart"), options).render();
</script>

@endpush
