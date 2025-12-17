@extends('layouts.app')

@section('title', 'Produk Stok Minim')
@section('title_page', 'Produk Stok Minim')

@section('content')
<style>
    /* Animasi Kustom */
    .fade-in-up {
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
        transform: translateY(20px);
    }
    
    /* Delay bertingkat untuk baris tabel */
    tbody tr { opacity: 0; animation: fadeInUp 0.5s ease-out forwards; }
    tbody tr:nth-child(1) { animation-delay: 0.1s; }
    tbody tr:nth-child(2) { animation-delay: 0.15s; }
    tbody tr:nth-child(3) { animation-delay: 0.2s; }
    tbody tr:nth-child(4) { animation-delay: 0.25s; }
    tbody tr:nth-child(5) { animation-delay: 0.3s; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Styling Badge Stok */
    .badge-stock {
        padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    .badge-safe { background-color: rgba(42, 157, 143, 0.1); color: #2A9D8F; border: 1px solid rgba(42, 157, 143, 0.2); }
    .badge-warning { background-color: rgba(233, 196, 106, 0.15); color: #B08925; border: 1px solid rgba(233, 196, 106, 0.3); }
    .badge-danger { background-color: rgba(231, 111, 81, 0.1); color: #E76F51; border: 1px solid rgba(231, 111, 81, 0.2); }
</style>

<div class="row g-4 mb-4 fade-in-up">
    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                <i class="bi bi-exclamation-triangle fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Total Stok Minim</h6>
                <h4 class="fw-bold mb-0">{{ $produks->count() }} Item</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                <i class="bi bi-bell fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Kritis (Stok &lt; 5)</h6>
                <h4 class="fw-bold mb-0">{{ $produks->where('stok', '<', 5)->count() }} Item</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                <i class="bi bi-box-seam fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Perlu Restok</h6>
                <h4 class="fw-bold mb-0">Segera</h4>
            </div>
        </div>
    </div>
</div>

<div class="card-custom p-4 fade-in-up" style="animation-delay: 0.2s;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <h6 class="fw-bold">Daftar Produk dengan Stok Minim (< 10 Unit)</h6>
        <a href="{{ route('produk.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    @if($produks->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr style="background-color: rgba(224,122,95,0.08); border-bottom: 2px solid rgba(224,122,95,0.15);">
                        <th class="text-uppercase fw-bold" style="font-size: 0.8rem; color: #666;">No</th>
                        <th class="text-uppercase fw-bold" style="font-size: 0.8rem; color: #666;">Nama Produk</th>
                        <th class="text-uppercase fw-bold" style="font-size: 0.8rem; color: #666;">Stok Saat Ini</th>
                        <th class="text-uppercase fw-bold" style="font-size: 0.8rem; color: #666;">Harga Jual</th>
                        <th class="text-uppercase fw-bold" style="font-size: 0.8rem; color: #666;">Status</th>
                        <th class="text-uppercase fw-bold" style="font-size: 0.8rem; color: #666;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produks as $produk)
                        <tr>
                            <td class="fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-600">{{ $produk->nama_produk }}</span>
                            </td>
                            <td>
                                <span class="fw-bold">{{ $produk->stok }} {{ $produk->satuan ?? 'Unit' }}</span>
                            </td>
                            <td>
                                <span>Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                @if($produk->stok < 5)
                                    <span class="badge-stock badge-danger">🔴 KRITIS</span>
                                @elseif($produk->stok < 10)
                                    <span class="badge-stock badge-warning">🟡 MINIM</span>
                                @else
                                    <span class="badge-stock badge-safe">🟢 AMAN</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('produk.edit', $produk->id_produk) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="{{ route('penjualan.index') }}" class="btn btn-outline-success" title="Lihat Penjualan">
                                        <i class="bi bi-cart-check"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Semua Stok Aman!</strong> Tidak ada produk dengan stok minim saat ini.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<style>
    .card-custom {
        background: #fff;
        border-radius: 12px;
        border: 1px solid rgba(224,122,95,0.1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: all 0.3s ease;
    }
    
    .card-custom:hover {
        box-shadow: 0 4px 16px rgba(224,122,95,0.15);
    }
    
    .table tbody tr {
        border-bottom: 1px solid rgba(224,122,95,0.1);
    }
    
    .table tbody tr:hover {
        background-color: rgba(224,122,95,0.05);
    }
    
    .btn-group-sm .btn {
        padding: 4px 8px;
        font-size: 0.85rem;
    }
</style>

@endsection
