@extends('layouts.app')

@section('title', 'Edit Pembelian')
@section('title_page', 'Ubah Transaksi Pembelian')

@section('content')
<div class="card-custom p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Edit Pembelian Bahan Baku</h5>
            <p class="text-muted small mb-0">
                #PO-{{ str_pad($pembelian->id_beli, 5, '0', STR_PAD_LEFT) }}
            </p>
        </div>
        <a href="{{ route('pembelian.index') }}" class="btn btn-light">
            ← Kembali
        </a>
    </div>

    <form action="{{ route('pembelian.update', $pembelian->id_beli) }}" method="POST" id="formEditPembelian">
        @csrf
        @method('PUT')

        {{-- HEADER --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label fw-bold">Tanggal Pembelian</label>
                <input type="date" name="tgl" class="form-control" value="{{ $pembelian->tgl }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">Supplier</label>
                <select name="id_supp" class="form-select" required>
                    <option value="">-- Pilih Supplier --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id_supp }}" {{ $pembelian->id_supp == $s->id_supp ? 'selected' : '' }}>
                            {{ $s->nama_supplier }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">Catatan (Opsional)</label>
                <input type="text" name="note" class="form-control" value="{{ $pembelian->note }}" placeholder="Contoh: Nota Toko A">
            </div>
        </div>

        {{-- DETAIL BAHAN --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Detail Bahan Baku</label>
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="tableItems">
                    <thead class="bg-light">
                        <tr class="small text-uppercase">
                            <th width="30%">Bahan</th>
                            <th width="10%">Qty Pack</th>
                            <th width="10%">Isi/Pack</th>
                            <th width="15%">Harga/Pack</th>
                            <th width="10%">Total Qty</th>
                            <th width="20%" class="text-end">Subtotal</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembelian->detail as $i => $d)
                        <tr>
                            <td>
                                <select name="items[{{ $i }}][id_bahan]" class="form-select select2-bahan" required>
                                    @foreach($bahans as $b)
                                        <option value="{{ $b->id_bahan }}" {{ $d->id_bahan == $b->id_bahan ? 'selected' : '' }}>
                                            {{ $b->nama_bahan }} ({{ $b->satuan }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][qty_pack]" class="form-control qty-pack" step="any" placeholder="0">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][isi_pack]" class="form-control isi-pack" step="any" placeholder="0">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $i }}][harga_pack]" class="form-control harga-pack" step="any" placeholder="0">
                            </td>
                            <td class="text-center fw-bold">
                                {{-- Jika user ingin input langsung satuan base --}}
                                <input type="number" name="items[{{ $i }}][jumlah]" class="form-control jumlah-base" value="{{ $d->jumlah }}" required>
                            </td>
                            <td class="text-end">
                                <input type="hidden" name="items[{{ $i }}][harga_satuan]" class="harga-satuan-hidden" value="{{ $d->harga_satuan }}">
                                <span class="subtotal-text fw-bold text-danger">Rp {{ number_format($d->sub_total, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Grand Total</td>
                            <td class="text-end fw-bold text-danger fs-5" id="grandTotal">
                                Rp {{ number_format($pembelian->total_beli, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('pembelian.index') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4 fw-bold">
                Update Pembelian <i class="bi bi-check-circle ms-1"></i>
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
function formatRp(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
}

function recalcTotal() {
    let grandTotal = 0;
    document.querySelectorAll('#tableItems tbody tr').forEach(row => {
        const qtyPack = parseFloat(row.querySelector('.qty-pack').value) || 0;
        const isiPack = parseFloat(row.querySelector('.isi-pack').value) || 0;
        const hargaPack = parseFloat(row.querySelector('.harga-pack').value) || 0;
        
        const jumlahBaseInput = row.querySelector('.jumlah-base');
        let subtotal = 0;

        // Logic: Jika input Pack diisi, hitung otomatis jumlah base. 
        // Jika Pack kosong, gunakan input jumlah base langsung.
        if (qtyPack > 0 && hargaPack > 0) {
            const totalQty = qtyPack * isiPack;
            jumlahBaseInput.value = totalQty;
            subtotal = qtyPack * hargaPack;
        } else {
            // Mode input manual (seperti saat pertama load dari database)
            const jumlahBase = parseFloat(jumlahBaseInput.value) || 0;
            const hargaSatuan = parseFloat(row.querySelector('.harga-satuan-hidden').value) || 0;
            subtotal = jumlahBase * hargaSatuan;
        }

        grandTotal += subtotal;
        row.querySelector('.subtotal-text').innerText = formatRp(subtotal);
    });
    document.getElementById('grandTotal').innerText = formatRp(grandTotal);
}

// Event listener untuk perubahan input
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('qty-pack') || 
        e.target.classList.contains('isi-pack') || 
        e.target.classList.contains('harga-pack') ||
        e.target.classList.contains('jumlah-base')) {
        recalcTotal();
    }
});

// Remove row
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-remove')) {
        if (document.querySelectorAll('#tableItems tbody tr').length > 1) {
            e.target.closest('tr').remove();
            recalcTotal();
        } else {
            alert('Minimal harus ada 1 bahan.');
        }
    }
});

// Submit confirmation
document.getElementById('formEditPembelian').addEventListener('submit', function(e) {
    if (!confirm('Simpan perubahan pembelian ini? Stok bahan baku akan disesuaikan otomatis.')) {
        e.preventDefault();
    }
});
</script>
@endpush