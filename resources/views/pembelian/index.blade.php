@extends('layouts.app')

@section('title', 'Riwayat Pembelian')
@section('title_page', 'Manajemen Pembelian')

@section('content')
<style>
    .fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(20px); }
    tbody tr { opacity: 0; animation: fadeInUp 0.5s ease-out forwards; }
    tbody tr:nth-child(1) { animation-delay: 0.1s; }
    tbody tr:nth-child(2) { animation-delay: 0.15s; }
    tbody tr:nth-child(3) { animation-delay: 0.2s; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .card-custom { background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: none; }
</style>

{{-- STATISTIC CARDS --}}
<div class="row g-4 mb-4 fade-in-up">
    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                <i class="bi bi-cash-coin fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Total Pengeluaran</h6>
                <h4 class="fw-bold mb-0">Rp {{ number_format($pembelians->sum('total_beli'), 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                <i class="bi bi-box-seam fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Pembelian Hari Ini</h6>
                <h4 class="fw-bold mb-0">{{ $pembelians->where('tgl', date('Y-m-d'))->count() }} PO</h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                <i class="bi bi-graph-up fs-4"></i>
            </div>
            <div>
                <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem;">Rata-rata Pembelian</h6>
                @php $avg = $pembelians->count() > 0 ? $pembelians->avg('total_beli') : 0; @endphp
                <h4 class="fw-bold mb-0">Rp {{ number_format($avg, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card-custom p-4 fade-in-up" style="animation-delay: 0.2s;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h5 class="fw-bold mb-1">Riwayat Pembelian</h5>
            <p class="text-muted small mb-0">Data pembelian bahan baku yang telah diproses.</p>
        </div>
        
        <div class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control bg-light border-start-0 rounded-end-3" placeholder="Cari ID / Supplier...">
            </div>

            @if(Auth::user()->role == 'owner')
            <button class="btn btn-success d-flex align-items-center gap-2 rounded-3 shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modalImport">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span class="d-none d-md-inline">Import Excel</span>
            </button>
            @endif

            <a href="{{ route('pembelian.create') }}" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 shadow-sm px-4">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-md-inline">Pembelian Baru</span>
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="tablePembelian">
            <thead class="bg-light">
                <tr>
                    <th class="py-3 ps-3 rounded-start-3 text-secondary text-uppercase small fw-bold">ID PO</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Tanggal</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Supplier</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Total Pembelian</th>
                    <th class="py-3 text-secondary text-uppercase small fw-bold">Petugas</th>
                    <th class="py-3 pe-3 rounded-end-3 text-end text-secondary text-uppercase small fw-bold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembelians as $item)
                <tr class="border-bottom-0">
                    <td class="ps-3 fw-bold text-primary">#PO-{{ str_pad($item->id_beli, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="text-muted">{{ date('d M Y', strtotime($item->tgl)) }}</td>
                    <td class="fw-bold text-dark">{{ optional($item->supplier)->nama_supplier ?? 'N/A' }}</td>
                    <td class="text-danger fw-bold">Rp {{ number_format($item->total_beli, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge bg-light text-secondary border">
                            <i class="bi bi-person me-1"></i> {{ $item->user->name ?? 'Admin' }}
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-2">
                            {{-- BUTTON DETAIL --}}
                            <button type="button" class="btn btn-sm btn-light text-info border rounded-2 btn-view-detail"
                                    data-id="{{ $item->id_beli }}"
                                    data-supplier="{{ optional($item->supplier)->nama_supplier ?? 'N/A' }}"
                                    data-details='@json($item->detail)'
                                    title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </button>

                            {{-- BUTTON EDIT --}}
                            <a href="{{ route('pembelian.edit', $item->id_beli) }}" class="btn btn-sm btn-light text-warning border rounded-2" title="Edit Transaksi">
                                <i class="bi bi-pencil"></i>
                            </a>

                            {{-- BUTTON HAPUS --}}
                            @if(Auth::user()->role == 'owner')
                            <form action="{{ route('pembelian.destroy', $item->id_beli) }}" method="POST" class="d-inline delete-form">
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
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-box-seam fs-1 d-block mb-2 text-secondary opacity-25"></i>
                        <p class="mt-2 mb-0">Belum ada transaksi pembelian.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL IMPORT --}}
<div class="modal fade" id="modalImport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-success"><i class="bi bi-file-earmark-excel me-2"></i>Import Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pembelian.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body pt-4">
                    <div class="alert alert-info border-0 small rounded-3 mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Data yang diimport akan otomatis menambah stok bahan baku.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">File Excel (.xlsx / .csv)</label>
                        <input type="file" name="file_excel" class="form-control form-control-lg rounded-3" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4">Upload & Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-info text-white rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold" id="detailId">#PO-00000</h5>
                    <small class="opacity-75" id="detailSupplier">Supplier: N/A</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Nama Bahan</th>
                                <th class="py-3 text-center">Qty</th>
                                <th class="pe-4 py-3 text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detailList"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Tutup</button>
                <button type="button" id="modalPrintBtn" class="btn btn-info rounded-3 px-4"><i class="bi bi-printer me-2"></i> Cetak</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Search Function
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tablePembelian tbody tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(searchText) ? '' : 'none';
        });
    });

    // Detail Logic
    document.querySelectorAll('.btn-view-detail').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const supplier = this.getAttribute('data-supplier');
            let details = [];
            try { details = JSON.parse(this.getAttribute('data-details')); } catch (e) { details = []; }
            
            document.getElementById('detailId').innerText = "#PO-" + String(id).padStart(5, '0');
            document.getElementById('detailSupplier').innerText = "Supplier: " + supplier;

            let list = document.getElementById('detailList');
            list.innerHTML = '';
            details.forEach(item => {
                let namaBahan = (item.bahan && item.bahan.nama_bahan) ? item.bahan.nama_bahan : 'Bahan Dihapus';
                let subtotal = new Intl.NumberFormat('id-ID').format(item.sub_total || item.subtotal || 0);
                list.innerHTML += `
                    <tr>
                        <td class="ps-4 fw-bold text-dark">${namaBahan}</td>
                        <td class="text-center">${item.jumlah}</td>
                        <td class="pe-4 text-end text-danger fw-bold">Rp ${subtotal}</td>
                    </tr>`;
            });

            document.getElementById('modalPrintBtn').onclick = () => {
                window.open("{{ url('/pembelian') }}/" + id + "/print", '_blank');
            };

            new bootstrap.Modal(document.getElementById('modalDetail')).show();
        });
    });

    // Delete Confirmation (SweetAlert)
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Pembelian?',
                text: "Stok akan dikurangi kembali dan data tidak bisa dipulihkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) this.submit();
            });
        });
    });
</script>
@endpush

@endsection