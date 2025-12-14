@extends('layouts.app')

@section('title', 'Produk Jadi')
@section('title_page', 'Katalog Produk')

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
            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                <i class="bi bi-cake2 fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Total Varian</h6>
                <h4 class="fw-bold mb-0">{{ $produks->count() }} Jenis</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                <i class="bi bi-bell fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Perlu Produksi</h6>
                <h4 class="fw-bold mb-0">{{ $produks->where('stok', '<=', 5)->count() }} Item</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                <i class="bi bi-cash-stack fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Nilai Aset Produk</h6>
                @php
                    $totalAset = $produks->sum(function($p){ return $p->harga_jual * $p->stok; });
                @endphp
                <h4 class="fw-bold mb-0">Rp {{ number_format($totalAset, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card-custom p-4 fade-in-up" style="animation-delay: 0.2s;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold mb-1">Daftar Produk Jadi</h5>
            <p class="text-muted small mb-0">Kelola harga dan stok kue siap jual.</p>
        </div>
        
        <div class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control bg-light border-start-0 rounded-end-3" placeholder="Cari kue...">
            </div>

            @if(Auth::user()->role == 'owner')
            <button class="btn btn-primary d-flex align-items-center gap-2 rounded-3 shadow-sm px-4" 
                    data-bs-toggle="modal" data-bs-target="#modalCreate">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-md-inline">Produk Baru</span>
            </button>
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tableProduk">
            <thead class="bg-light">
                <tr>
                    <th class="py-3 ps-3 rounded-start-3 text-secondary text-uppercase small fw-bold">Nama Produk</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Harga Jual</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Stok</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Satuan</th>
                    <th class="py-3 pe-3 rounded-end-3 text-end text-secondary text-uppercase small fw-bold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produks as $item)
                <tr class="border-bottom-0">
                    <td class="ps-3 fw-bold text-dark">{{ $item->nama_produk }}</td>
                    <td class="text-success fw-bold">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($item->stok <= 0)
                            <span class="badge-stock badge-danger"><i class="bi bi-x-circle me-1"></i> Habis</span>
                        @elseif($item->stok <= 5)
                            <span class="badge-stock badge-warning"><i class="bi bi-exclamation-circle me-1"></i> {{ $item->stok }} (Sisa Dikit)</span>
                        @else
                            <span class="badge-stock badge-safe">{{ $item->stok }}</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $item->satuan }}</td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-light text-primary border rounded-2" 
                                    onclick="editItem({{ $item }})" title="Edit Harga/Stok">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            
                            <a href="{{ route('resep.edit', $item->id_produk) }}" class="btn btn-sm btn-light text-info border rounded-2" title="Atur Resep">
                                <i class="bi bi-journal-text"></i>
                            </a>

                            @if(Auth::user()->role == 'owner')
                            <form action="{{ route('produk.destroy', $item->id_produk) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger border rounded-2" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-cake2 fs-1 d-block mb-2 text-secondary opacity-25"></i>
                        <p class="mt-2 mb-0">Belum ada produk kue.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary">Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('produk.store') }}" method="POST">
                @csrf
                <div class="modal-body pt-4">
                    <div class="form-floating mb-3">
                        <input type="text" name="nama_produk" class="form-control rounded-3" id="addName" placeholder="Nama" required>
                        <label for="addName">Nama Kue (Misal: Nastar Premium)</label>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" name="harga_jual" class="form-control rounded-3" id="addHarga" placeholder="0" required>
                                <label for="addHarga">Harga Jual (Rp)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" name="stok" class="form-control rounded-3" id="addStok" placeholder="0" value="0" required>
                                <label for="addStok">Stok Awal</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mt-3">
                        <select name="satuan" class="form-select rounded-3" id="addSatuan">
                            <option value="Toples">Toples</option>
                            <option value="Box">Box</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Paket">Paket</option>
                        </select>
                        <label for="addSatuan">Satuan Penjualan</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary">Edit Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body pt-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Nama Produk</label>
                        <input type="text" name="nama_produk" id="edit_nama" class="form-control rounded-3 py-2" required>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Harga Jual</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" name="harga_jual" id="edit_harga" class="form-control rounded-end-3 py-2" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase fw-bold text-primary">Stok Fisik</label>
                            <div class="input-group">
                                <input type="number" name="stok" id="edit_stok" class="form-control rounded-start-3 border-primary" required>
                                <span class="input-group-text bg-primary text-white border-primary"><i class="bi bi-pencil"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Satuan</label>
                        <select name="satuan" id="edit_satuan" class="form-select rounded-3 py-2">
                            <option value="Toples">Toples</option>
                            <option value="Box">Box</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Paket">Paket</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Fitur Search Table
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let tableRows = document.querySelectorAll('#tableProduk tbody tr');
        
        tableRows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // Fitur Edit Modal Populate
    function editItem(data) {
        // 1. Set URL Form Action
        document.getElementById('formEdit').action = "{{ route('produk.index') }}/" + data.id_produk;
        
        // 2. Isi Value ke Input
        document.getElementById('edit_nama').value = data.nama_produk;
        document.getElementById('edit_harga').value = data.harga_jual; // Pastikan format angka polos
        document.getElementById('edit_stok').value = data.stok;
        document.getElementById('edit_satuan').value = data.satuan;

        // 3. Show Modal
        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }

    // SweetAlert untuk Delete
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Produk?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
@endsection