@extends('layouts.app')

@section('title', 'Daftar Resep')
@section('title_page', 'Resep Produk')

@section('content')

{{-- CARD INFO TAMBAHAN (AMAN) --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                <i class="bi bi-box-seam fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size:.75rem">Total Produk</h6>
                <h4 class="fw-bold mb-0">{{ $produks->count() }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                <i class="bi bi-journal-check fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size:.75rem">Produk Dengan Resep</h6>
                <h4 class="fw-bold mb-0">
                    {{ $produks->filter(fn($p) => $p->resep->count() > 0)->count() }}
                </h4>
            </div>
        </div>
    </div>
</div>

{{-- TABLE UTAMA (ASLI + TAMBAHAN) --}}
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Daftar Resep</h5>
            <p class="text-muted small mb-0">Klik ikon mata untuk melihat resep & estimasi produksi.</p>
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
                    <td class="text-success">
                        Rp {{ number_format($p->harga_jual,0,',','.') }}
                    </td>
                    <td class="text-center">{{ $p->stok }}</td>
                    <td class="text-center">{{ $p->resep->count() }}</td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-2">

                            {{-- BUTTON 👁️ (ESTIMASI PRODUKSI) --}}
                            <button type="button"
                                class="btn btn-sm btn-light text-info border rounded-2 btn-view-resep"
                                data-nama="{{ $p->nama_produk }}"
                                data-resep='@json($p->resep)'
                                title="Lihat Resep & Estimasi Produksi">
                                <i class="bi bi-eye"></i>
                            </button>

                            {{-- BUTTON LAMA (TETAP) --}}
                            <a href="{{ route('resep.edit', $p->id_produk) }}"
                               class="btn btn-sm btn-light text-info border rounded-2">
                                <i class="bi bi-journal-text me-1"></i>
                                @if(Auth::user()->role == 'owner') Atur @else Lihat @endif
                            </a>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        Belum ada produk.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL ESTIMASI PRODUKSI --}}
<div class="modal fade" id="modalResep" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-info text-white rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold" id="resepNamaProduk">Produk</h5>
                    <small class="opacity-75">Estimasi Produksi Berdasarkan Stok</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Bahan</th>
                                <th class="py-3 text-center">Stok</th>
                                <th class="py-3 text-center">Takaran / Produk</th>
                                <th class="pe-4 py-3 text-end">Maks Produk</th>
                            </tr>
                        </thead>
                        <tbody id="resepDetailList"></tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer border-0">
                <div class="me-auto fw-bold text-success d-none" id="hasilEstimasi"></div>
                <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-view-resep').forEach(btn => {
    btn.addEventListener('click', function () {

        const namaProduk = this.dataset.nama;
        let resep = [];

        try {
            resep = this.dataset.resep ? JSON.parse(this.dataset.resep) : [];
        } catch (e) {
            resep = [];
        }

        document.getElementById('resepNamaProduk').innerText = namaProduk;

        const list = document.getElementById('resepDetailList');
        const hasil = document.getElementById('hasilEstimasi');
        list.innerHTML = '';
        hasil.classList.add('d-none');

        let estimasi = null;

        if (resep.length > 0) {
            resep.forEach(r => {
                const stok = r.bahan?.stok ?? 0;
                const takaran = r.takaran ?? 0;
                const satuan = r.satuan ?? '';

                if (takaran <= 0) return;

                const max = Math.floor(stok / takaran);
                if (estimasi === null || max < estimasi) estimasi = max;

                list.innerHTML += `
                    <tr>
                        <td class="ps-4 fw-bold">${r.bahan?.nama_bahan ?? 'Bahan Dihapus'}</td>
                        <td class="text-center">${stok} ${satuan}</td>
                        <td class="text-center">${takaran} ${satuan}</td>
                        <td class="pe-4 text-end fw-bold">${max}</td>
                    </tr>
                `;
            });
        } else {
            list.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                        Resep belum tersedia.
                    </td>
                </tr>
            `;
        }

        if (estimasi !== null) {
            hasil.innerText = `Estimasi maksimal produksi: ${estimasi} ${namaProduk}`;
            hasil.classList.remove('d-none');
        }

        new bootstrap.Modal(document.getElementById('modalResep')).show();
    });
});
</script>
@endpush
