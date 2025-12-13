@extends('layouts.app')

@section('title', 'Bahan Baku')
@section('title_page', 'Inventory Bahan Baku')

@section('content')
<style>
    /* Animasi Kustom untuk Halaman Ini */
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
    /* dst... */

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
                <i class="bi bi-box-seam fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Total Item</h6>
                <h4 class="fw-bold mb-0">{{ $bahans->count() }} Jenis</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                <i class="bi bi-exclamation-triangle fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Stok Menipis</h6>
                <h4 class="fw-bold mb-0">{{ $bahans->where('stok', '<=', 'stok_min')->count() }} Item</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                <i class="bi bi-check2-circle fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Status Gudang</h6>
                <h4 class="fw-bold mb-0">Aktif</h4>
            </div>
        </div>
    </div>
</div>

<div class="card-custom p-4 fade-in-up" style="animation-delay: 0.2s;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold mb-1">Daftar Bahan Baku</h5>
            <p class="text-muted small mb-0">Manage stok bahan mentah untuk produksi.</p>
        </div>
        
        <div class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control bg-light border-start-0 rounded-end-3" placeholder="Cari bahan...">
            </div>

            @if(Auth::user()->role == 'owner')
            <button class="btn btn-primary d-flex align-items-center gap-2 rounded-3 shadow-sm px-4" 
                    data-bs-toggle="modal" data-bs-target="#modalCreate">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-md-inline">Baru</span>
            </button>
            @endif
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tableBahan">
            <thead class="bg-light">
                <tr>
                    <th class="py-3 ps-3 rounded-start-3 text-secondary text-uppercase small fw-bold">Nama Bahan</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Stok Fisik</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Satuan</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold text-center">Min. Alert</th>
                    <th class="py-3 pe-3 rounded-end-3 text-end text-secondary text-uppercase small fw-bold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bahans as $item)
                <tr class="border-bottom-0">
                    <td class="ps-3 fw-bold text-dark">{{ $item->nama_bahan }}</td>
                    <td>
                        @if($item->stok <= 0)
                            <span class="badge-stock badge-danger"><i class="bi bi-x-circle me-1"></i> Habis</span>
                        @elseif($item->stok <= $item->stok_min)
                            <span class="badge-stock badge-warning"><i class="bi bi-exclamation-circle me-1"></i> {{ $item->stok }} (Low)</span>
                        @else
                            <span class="badge-stock badge-safe">{{ $item->stok }}</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $item->satuan }}</td>
                    <td class="text-center text-muted">{{ $item->stok_min }}</td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-light text-primary border rounded-2" 
                                    onclick="editItem({{ $item }})" title="Edit Stok/Info">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            @if(Auth::user()->role == 'owner')
                            <form action="{{ route('bahan-baku.destroy', $item->id_bahan) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus {{ $item->nama_bahan }}? Data yang dihapus tidak bisa dikembalikan.')">
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
                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="Empty" style="width: 60px; opacity: 0.5;">
                        <p class="mt-2 mb-0">Belum ada data bahan baku.</p>
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
                <h5 class="modal-title fw-bold text-primary">Tambah Bahan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('bahan-baku.store') }}" method="POST">
                @csrf
                <div class="modal-body pt-4">
                    <div class="form-floating mb-3">
                        <input type="text" name="nama_bahan" class="form-control rounded-3" id="addName" placeholder="Nama" required>
                        <label for="addName">Nama Bahan (Contoh: Tepung Segitiga)</label>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="satuan" class="form-select rounded-3" id="addSatuan">
                                    <option value="gram">Gram (gr)</option>
                                    <option value="kg">Kilogram (kg)</option>
                                    <option value="ml">Milliliter (ml)</option>
                                    <option value="liter">Liter (L)</option>
                                    <option value="pcs">Pcs / Butir</option>
                                    <option value="batang">Batang</option>
                                    <option value="bungkus">Bungkus</option>
                                </select>
                                <label for="addSatuan">Satuan</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" name="stok" class="form-control rounded-3" id="addStok" placeholder="0" value="0">
                                <label for="addStok">Stok Awal</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mt-3">
                        <input type="number" name="stok_min" class="form-control rounded-3" id="addMin" placeholder="10" value="10">
                        <label for="addMin">Batas Minimum (Alert Stok Menipis)</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary">Edit / Update Stok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body pt-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Nama Bahan</label>
                        <input type="text" name="nama_bahan" id="edit_nama" class="form-control rounded-3 py-2" 
                               {{ Auth::user()->role == 'kasir' ? 'readonly style=background-color:#f8f9fa;' : 'required' }}>
                        @if(Auth::user()->role == 'kasir')
                            <small class="text-muted" style="font-size: 10px;">*Hanya Owner yang dapat mengubah nama</small>
                        @endif
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Satuan</label>
                            @if(Auth::user()->role == 'owner')
                                <select name="satuan" id="edit_satuan" class="form-select rounded-3 py-2">
                                    <option value="gram">Gram</option>
                                    <option value="kg">Kg</option>
                                    <option value="ml">Ml</option>
                                    <option value="liter">Liter</option>
                                    <option value="pcs">Pcs</option>
                                    <option value="batang">Batang</option>
                                    <option value="bungkus">Bungkus</option>
                                </select>
                            @else
                                <input type="text" name="satuan" id="edit_satuan_text" class="form-control rounded-3 py-2" readonly style="background-color:#f8f9fa;">
                            @endif
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase fw-bold text-primary">Stok Fisik (Opname)</label>
                            <div class="input-group">
                                <input type="number" name="stok" id="edit_stok" class="form-control rounded-3 border-primary" required>
                                <span class="input-group-text bg-primary text-white border-primary rounded-end-3"><i class="bi bi-pencil"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Alert Minimum</label>
                        <input type="number" name="stok_min" id="edit_min" class="form-control rounded-3 py-2" 
                               {{ Auth::user()->role == 'kasir' ? 'readonly style=background-color:#f8f9fa;' : 'required' }}>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Fitur Search Sederhana
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let tableRows = document.querySelectorAll('#tableBahan tbody tr');
        
        tableRows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // Fitur Populate Modal Edit
    function editItem(data) {
        // 1. Set URL Form
        document.getElementById('formEdit').action = "{{ route('bahan-baku.index') }}/" + data.id_bahan;
        
        // 2. Isi Data
        document.getElementById('edit_nama').value = data.nama_bahan;
        document.getElementById('edit_stok').value = data.stok;
        document.getElementById('edit_min').value = data.stok_min;

        // 3. Handle Satuan (Select vs Textbox)
        const role = "{{ Auth::user()->role }}";
        if(role === 'owner') {
            document.getElementById('edit_satuan').value = data.satuan;
        } else {
            document.getElementById('edit_satuan_text').value = data.satuan;
        }

        // 4. Buka Modal
        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }
</script>
@endpush
@endsection