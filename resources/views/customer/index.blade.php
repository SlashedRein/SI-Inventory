@extends('layouts.app')

@section('title', 'Data Pelanggan')
@section('title_page', 'Manajemen Customer')

@section('content')
<style>
    /* Animasi Kustom */
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
            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                <i class="bi bi-people fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Total Pelanggan</h6>
                <h4 class="fw-bold mb-0">{{ $customers->count() }} Orang</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                <i class="bi bi-person-plus fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Baru Bulan Ini</h6>
                <h4 class="fw-bold mb-0">
                    {{ $customers->where('created_at', '>=', now()->startOfMonth())->count() }} Orang
                </h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                <i class="bi bi-star fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Status Member</h6>
                <h4 class="fw-bold mb-0">Aktif</h4>
            </div>
        </div>
    </div>
</div>

<div class="card-custom p-4 fade-in-up" style="animation-delay: 0.2s;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold mb-1">Daftar Pelanggan</h5>
            <p class="text-muted small mb-0">Database pelanggan setia Dewi Cookies.</p>
        </div>
        
        <div class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control bg-light border-start-0 rounded-end-3" placeholder="Cari nama / telp...">
            </div>

            <button class="btn btn-primary d-flex align-items-center gap-2 rounded-3 shadow-sm px-4" 
                    data-bs-toggle="modal" data-bs-target="#modalCreate">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-md-inline">Pelanggan Baru</span>
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tableCustomer">
            <thead class="bg-light">
                <tr>
                    <th class="py-3 ps-3 rounded-start-3 text-secondary text-uppercase small fw-bold">Nama Pelanggan</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Kontak (HP/WA)</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Alamat</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Bergabung</th>
                    <th class="py-3 pe-3 rounded-end-3 text-end text-secondary text-uppercase small fw-bold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $item)
                <tr class="border-bottom-0">
                    <td class="ps-3 fw-bold text-dark">{{ $item->nama }}</td>
                    <td>
                        @if($item->no_telp && $item->no_telp != '-')
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $item->no_telp)) }}" target="_blank" class="text-decoration-none badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                <i class="bi bi-whatsapp me-1"></i> {{ $item->no_telp }}
                            </a>
                        @else
                            <span class="text-muted small fst-italic">-</span>
                        @endif
                    </td>
                    <td class="text-muted small" style="max-width: 250px;">{{ Str::limit($item->alamat, 40) }}</td>
                    <td class="text-muted small">{{ $item->created_at->format('d M Y') }}</td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-sm btn-light text-primary border rounded-2" 
                                    onclick="editItem({{ $item }})" title="Edit Data">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            @if(Auth::user()->role == 'owner')
                            <form action="{{ route('customer.destroy', $item->id_cust) }}" method="POST" class="d-inline delete-form">
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
                        <i class="bi bi-people fs-1 d-block mb-2 text-secondary opacity-25"></i>
                        <p class="mt-2 mb-0">Belum ada data pelanggan.</p>
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
                <h5 class="modal-title fw-bold text-primary">Tambah Pelanggan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customer.store') }}" method="POST">
                @csrf
                <div class="modal-body pt-4">
                    <div class="form-floating mb-3">
                        <input type="text" name="nama" class="form-control rounded-3" id="addName" placeholder="Nama" required>
                        <label for="addName">Nama Lengkap</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="text" name="no_telp" class="form-control rounded-3" id="addTelp" placeholder="08xxx">
                        <label for="addTelp">No. Telepon / WA (Opsional)</label>
                    </div>

                    <div class="form-floating">
                        <textarea name="alamat" class="form-control rounded-3" id="addAlamat" placeholder="Alamat" style="height: 100px"></textarea>
                        <label for="addAlamat">Alamat (Opsional)</label>
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
                <h5 class="modal-title fw-bold text-primary">Edit Data Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body pt-4">
                    <div class="form-floating mb-3">
                        <input type="text" name="nama" class="form-control rounded-3" id="edit_nama" placeholder="Nama" required>
                        <label for="edit_nama">Nama Lengkap</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="text" name="no_telp" class="form-control rounded-3" id="edit_telp" placeholder="08xxx">
                        <label for="edit_telp">No. Telepon / WA</label>
                    </div>

                    <div class="form-floating">
                        <textarea name="alamat" class="form-control rounded-3" id="edit_alamat" placeholder="Alamat" style="height: 100px"></textarea>
                        <label for="edit_alamat">Alamat</label>
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
        let tableRows = document.querySelectorAll('#tableCustomer tbody tr');
        
        tableRows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // Fitur Populate Modal Edit (Perhatikan 'id_customer')
    function editItem(data) {
        // Ganti URL Action Form dengan ID CUSTOMER yang benar
        document.getElementById('formEdit').action = "{{ route('customer.index') }}/" + data.id_cust;
        
        // Isi input field
        document.getElementById('edit_nama').value = data.nama;
        document.getElementById('edit_telp').value = data.no_telp;
        document.getElementById('edit_alamat').value = data.alamat;

        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }

    // SweetAlert untuk Delete
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Pelanggan?',
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