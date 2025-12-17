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
</style>

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
                <h4 class="fw-bold mb-0">
                    {{ $pembelians->where('tgl', date('Y-m-d'))->count() }} PO
                </h4>
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
            <button class="btn btn-success d-flex align-items-center gap-2 rounded-3 shadow-sm px-3" 
                    data-bs-toggle="modal" data-bs-target="#modalImport">
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
                    <td class="fw-bold text-dark">
                        {{ optional($item->supplier)->nama_supplier ?? 'N/A' }}
                    </td>
                    <td class="text-danger fw-bold">Rp {{ number_format($item->total_beli, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge bg-light text-secondary border">
                            <i class="bi bi-person me-1"></i> {{ $item->user->name ?? 'Admin' }}
                        </span>
                    </td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-sm btn-light text-info border rounded-2 btn-view-detail"
                                    data-id="{{ $item->id_beli }}"
                                    data-supplier='@json($item->supplier->nama_supplier ?? "N/A")'
                                    data-details='@json($item->detail)'
                                    title="Lihat Detail Bahan">
                                <i class="bi bi-eye"></i>
                            </button>

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
                        Pastikan format Excel sesuai template. Data yang diimport akan otomatis menambah stok bahan baku.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">File Excel (.xlsx / .csv)</label>
                        <input type="file" name="file_excel" class="form-control form-control-lg rounded-3" accept=".xlsx, .xls, .csv" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="#" class="text-decoration-none small text-success fw-bold"><i class="bi bi-download me-1"></i> Download Template</a>
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
                        <tbody id="detailList">
                            </tbody>
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
    // Search Table
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let searchText = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tablePembelian tbody tr');
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // Attach click handler to view buttons (use data-attributes to safely carry JSON)
    document.querySelectorAll('.btn-view-detail').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            let supplier = 'N/A';
            try { supplier = this.dataset.supplier ? JSON.parse(this.dataset.supplier) : 'N/A'; } catch (e) { supplier = this.dataset.supplier || 'N/A'; }
            let details = [];
            try { details = this.dataset.details ? JSON.parse(this.dataset.details) : []; } catch (e) { details = []; }
            showDetail(id, supplier, details);
        });
    });

    // Show Detail Modal
    function showDetail(id, supplier, details) {
        document.getElementById('detailId').innerText = "#PO-" + String(id).padStart(5, '0');
        document.getElementById('detailSupplier').innerText = "Supplier: " + supplier;

        let list = document.getElementById('detailList');
        list.innerHTML = '';

        if(details && details.length > 0) {
            details.forEach(item => {
                let namaBahan = (item.bahan && item.bahan.nama_bahan) ? item.bahan.nama_bahan : 'Bahan Dihapus';
                // sub_total is used in DB; fallback to 0 if missing
                let rawSubtotal = Number(item.sub_total ?? item.subtotal ?? 0);
                if (Number.isNaN(rawSubtotal)) rawSubtotal = 0;
                let subtotal = new Intl.NumberFormat('id-ID').format(rawSubtotal);

                let row = `
                    <tr>
                        <td class="ps-4 fw-bold text-dark">${namaBahan}</td>
                        <td class="text-center">${item.jumlah}</td>
                        <td class="pe-4 text-end text-danger fw-bold">Rp ${subtotal}</td>
                    </tr>
                `;
                list.innerHTML += row;
            });
        } else {
            list.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted">Tidak ada detail item.</td></tr>';
        }

        // Set print button to open print-friendly route in new tab
        const modalPrintBtn = document.getElementById('modalPrintBtn');
        if(modalPrintBtn){
            modalPrintBtn.onclick = function(){
                const url = "{{ url('/pembelian') }}/" + id + "/print";
                window.open(url, '_blank');
            };
        }

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();
    }

    // Delete form confirmation
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if(!confirm('Yakin ingin menghapus pembelian ini? Stok bahan akan dikembalikan.')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush

@endsection
