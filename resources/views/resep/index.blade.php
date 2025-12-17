@extends('layouts.app')

@section('title', 'Daftar Resep')
@section('title_page', 'Resep Produk')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Daftar Resep</h5>
            <p class="text-muted small mb-0">Lihat resep tiap produk. Owner dapat mengubah resep.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3">Nama Produk</th>
                    <th>Harga Jual</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Jumlah Bahan</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produks as $p)
                <tr>
                    <td class="ps-3 fw-bold">{{ $p->nama_produk }}</td>
                    <td class="text-success">Rp {{ number_format($p->harga_jual,0,',','.') }}</td>
                    <td class="text-center">{{ $p->stok }}</td>
                    <td class="text-center">{{ $p->resep->count() }}</td>
                    <td class="text-end pe-3">
                        <a href="{{ route('resep.edit', $p->id_produk) }}" class="btn btn-sm btn-light text-info border rounded-2">
                            <i class="bi bi-journal-text me-1"></i>
                            @if(Auth::user()->role == 'owner') Atur @else Lihat @endif
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">Belum ada produk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
