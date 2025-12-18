@extends('layouts.app')

@section('title', 'Edit Penjualan')
@section('title_page', 'Ubah Transaksi')

@section('content')
<div class="card-custom p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Edit Penjualan</h5>
            <p class="text-muted small mb-0">
                #INV-{{ str_pad($penjualan->id_penjualan, 5, '0', STR_PAD_LEFT) }}
            </p>
        </div>
        <a href="{{ route('penjualan.index') }}" class="btn btn-light">
            ← Kembali
        </a>
    </div>

    <form action="{{ route('penjualan.update', $penjualan->id_penjualan) }}" method="POST" id="formEditPenjualan">
        @csrf
        @method('PUT')

        {{-- HEADER --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label fw-bold">Tanggal</label>
                <input type="date" name="tgl_penjualan"
                       class="form-control"
                       value="{{ $penjualan->tgl_penjualan }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">Pelanggan</label>
                <select name="id_cust" class="form-select" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id_cust }}"
                            {{ $penjualan->id_cust == $c->id_cust ? 'selected' : '' }}>
                            {{ $c->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- DETAIL BARANG --}}
        <div class="mb-3">
            <label class="form-label fw-bold">Detail Barang</label>

            <table class="table table-bordered align-middle" id="tableItems">
                <thead class="bg-light">
                    <tr>
                        <th width="45%">Produk</th>
                        <th width="15%" class="text-center">Qty</th>
                        <th width="20%" class="text-end">Harga</th>
                        <th width="20%" class="text-end">Subtotal</th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penjualan->detail as $i => $d)
                    <tr>
                        <td>
                            {{ $d->produk->nama_produk ?? 'Produk Dihapus' }}
                            <input type="hidden" name="items[{{ $i }}][id_produk]" value="{{ $d->id_produk }}">
                        </td>
                        <td class="text-center">
                            <input type="number"
                                   name="items[{{ $i }}][jumlah]"
                                   class="form-control text-center qty-input"
                                   min="1"
                                   value="{{ $d->jumlah }}">
                        </td>
                        <td class="text-end">
                            Rp {{ number_format($d->harga_satuan,0,',','.') }}
                        </td>
                        <td class="text-end subtotal"
                            data-harga="{{ $d->harga_satuan }}">
                            Rp {{ number_format($d->sub_total,0,',','.') }}
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger btn-remove">✖</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total</td>
                        <td class="text-end fw-bold text-success" id="grandTotal">
                            Rp {{ number_format($penjualan->total,0,',','.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn btn-warning px-4 fw-bold">
                Simpan Perubahan ✏️
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
function formatRp(n){
    return 'Rp ' + n.toLocaleString('id-ID');
}

function recalcTotal(){
    let total = 0;
    document.querySelectorAll('#tableItems tbody tr').forEach(row => {
        const harga = parseFloat(row.querySelector('.subtotal').dataset.harga) || 0;
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const sub = harga * qty;
        total += sub;
        row.querySelector('.subtotal').innerText = formatRp(sub);
    });
    document.getElementById('grandTotal').innerText = formatRp(total);
}

// update subtotal when qty changes
document.querySelectorAll('.qty-input').forEach(inp => {
    inp.addEventListener('input', recalcTotal);
});

// remove row
document.addEventListener('click', function(e){
    if(e.target.classList.contains('btn-remove')){
        e.target.closest('tr').remove();
        recalcTotal();
    }
});

// submit confirmation
document.getElementById('formEditPenjualan').addEventListener('submit', function(e){
    if(document.querySelectorAll('#tableItems tbody tr').length === 0){
        e.preventDefault();
        alert('Minimal harus ada 1 item.');
    } else {
        if(!confirm('Simpan perubahan penjualan ini? Stok akan disesuaikan ulang.')){
            e.preventDefault();
        }
    }
});
</script>
@endpush
