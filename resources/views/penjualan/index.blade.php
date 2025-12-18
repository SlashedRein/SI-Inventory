@extends('layouts.app')

@section('title', 'Riwayat Penjualan')
@section('title_page', 'Transaksi & Kasir')

@section('content')
<style>
    .fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(20px); }
    tbody tr { opacity: 0; animation: fadeInUp 0.5s ease-out forwards; }
    tbody tr:nth-child(1) { animation-delay: 0.1s; }
    tbody tr:nth-child(2) { animation-delay: 0.15s; }
    tbody tr:nth-child(3) { animation-delay: 0.2s; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="row g-4 mb-4 fade-in-up">
    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                <i class="bi bi-cash-coin fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Total Omzet</h6>
                <h4 class="fw-bold mb-0">Rp {{ number_format($penjualans->sum('total'), 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                <i class="bi bi-receipt fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Transaksi Hari Ini</h6>
                <h4 class="fw-bold mb-0">
                    {{ $penjualans->where('tgl_penjualan', date('Y-m-d'))->count() }} Struk
                </h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                <i class="bi bi-basket fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Rata-rata Transaksi</h6>
                @php $avg = $penjualans->count() > 0 ? $penjualans->avg('total') : 0; @endphp
                <h4 class="fw-bold mb-0">Rp {{ number_format($avg, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card-custom p-4 fade-in-up" style="animation-delay: 0.2s;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold mb-1">Riwayat Transaksi</h5>
            <p class="text-muted small mb-0">Data penjualan yang telah selesai.</p>
        </div>
        
        <div class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 rounded-start-3">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="searchInput" class="form-control bg-light border-start-0 rounded-end-3" placeholder="Cari ID / Pelanggan...">
            </div>

            <a href="{{ route('penjualan.create') }}" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 shadow-sm px-4">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-md-inline">Penjualan Baru</span>
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tablePenjualan">
            <thead class="bg-light">
                <tr>
                    <th class="py-3 ps-3 text-secondary text-uppercase small fw-bold">ID</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Tanggal</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Pelanggan</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Total</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Kasir</th>
                    <th class="py-3 pe-3 text-end text-secondary text-uppercase small fw-bold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualans as $item)
                <tr>
                    <td class="ps-3 fw-bold text-primary">#INV-{{ str_pad($item->id_penjualan, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ date('d M Y', strtotime($item->tgl_penjualan)) }}</td>
                    <td class="fw-bold">{{ $item->customer->nama_customer ?? 'Umum' }}</td>
                    <td class="fw-bold text-success">Rp {{ number_format($item->total,0,',','.') }}</td>
                    <td>{{ $item->user->name ?? 'Admin' }}</td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-2">

                            {{-- LIHAT DETAIL --}}
                            <button type="button" class="btn btn-sm btn-light text-info border rounded-2 btn-view-detail"
                                    data-id="{{ $item->id_penjualan }}"
                                    data-customer='@json($item->customer->nama ?? "Umum")'
                                    data-details='@json($item->detail)'
                                    title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </button>

                            {{-- EDIT --}}
                            <a href="{{ route('penjualan.edit', $item->id_penjualan) }}"
                               class="btn btn-sm btn-light text-warning border rounded-2"
                               title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>

                            {{-- HAPUS --}}
                            <form action="{{ route('penjualan.destroy', $item->id_penjualan) }}"
                                  method="POST" class="delete-form d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-sm btn-light text-danger border rounded-2"
                                        title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let val = this.value.toLowerCase();
    document.querySelectorAll('#tablePenjualan tbody tr').forEach(tr => {
        tr.style.display = tr.innerText.toLowerCase().includes(val) ? '' : 'none';
    });
});

// DETAIL MODAL (TETAP)
document.querySelectorAll('.btn-view-detail').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        let customer = JSON.parse(this.dataset.customer || '"Umum"');
        let details = JSON.parse(this.dataset.details || '[]');
        console.log(id, customer, details);
    });
});

// SWEETALERT HAPUS (TETAP)
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus Transaksi?',
            text: 'Data penjualan akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endpush
@endsection
