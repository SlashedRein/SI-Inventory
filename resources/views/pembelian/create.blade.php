@extends('layouts.app') 

@section('title', 'Tambah Pembelian Baru')

@section('content')
<div class="container-fluid">
<div class="row justify-content-center">
<div class="col-md-9">

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-info">
        <h6 class="m-0 fw-bold text-white">➕ Input Pembelian Baru</h6>
    </div>

    <div class="card-body p-4">
        <form action="{{ route('pembelian.store') }}" method="POST" id="formPembelian">
            @csrf

            {{-- Tanggal --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Tanggal Pembelian</label>
                <input type="date" name="tgl" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>

            {{-- Supplier --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Pilih Supplier</label>
                <div class="input-group">
                    <select name="id_supp" id="selectSupplier" class="form-select" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supp)
                            <option value="{{ $supp->id_supp }}">{{ $supp->nama }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalQuickAdd">
                        ➕ Baru
                    </button>
                </div>
            </div>

            {{-- TABLE ITEMS --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Daftar Bahan Baku</label>

                <table class="table table-bordered table-sm align-middle" id="tableItems">
                    <thead class="table-light">
                        <tr>
                            <th>Bahan</th>
                            <th width="20%">Jumlah Beli</th>
                            <th width="20%">Harga / Satuan Beli</th>
                            <th width="20%">Subtotal</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">TOTAL</td>
                            <td class="fw-bold text-end" id="grandTotal">Rp 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                {{-- INPUT BAR --}}
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <input id="searchBahan" class="form-control" placeholder="Cari bahan..." style="max-width:260px">

                    <select id="selectBahan" class="form-select" style="max-width:260px">
                        <option value="">-- Pilih Bahan --</option>
                        @foreach($bahans as $b)
                            <option value="{{ $b->id_bahan }}"
                                data-satuan="{{ $b->satuan }}">
                                {{ $b->nama_bahan }} (stok: {{ $b->stok }} {{ $b->satuan }})
                            </option>
                        @endforeach
                    </select>

                    <input type="number" id="inputQty" class="form-control" min="1" value="1" style="max-width:110px" title="Jumlah satuan beli">
                    <input type="text" id="inputSatuanBeli" class="form-control" placeholder="Satuan beli (pack/kg)" style="max-width:140px">
                    <input type="number" id="inputKonversi" class="form-control" min="0.0001" step="0.0001" value="1" title="Isi per satuan beli" style="max-width:140px">
                    <input type="number" id="inputHarga" class="form-control" min="0" placeholder="Harga / satuan beli" style="max-width:150px">

                    <button type="button" id="btnAddItem" class="btn btn-info">Tambah</button>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success fw-bold px-4">Simpan Pembelian ✅</button>
            </div>
        </form>
    </div>
</div>

</div>
</div>
</div>

{{-- MODAL QUICK ADD SUPPLIER --}}
<div class="modal fade" id="modalQuickAdd" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title fw-bold">Tambah Supplier Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <form id="formQuickAdd">
            @csrf
            <input type="text" id="inputNamaBaru" class="form-control mb-3" placeholder="Nama Supplier" required>
            <button class="btn btn-info w-100">Simpan & Pilih</button>
        </form>
    </div>
</div>
</div>
</div>

{{-- SCRIPT --}}
<script>
const tableBody = document.querySelector('#tableItems tbody');
const grandTotalEl = document.getElementById('grandTotal');

function rp(n){ return 'Rp ' + n.toLocaleString('id-ID'); }

function hitungTotal(){
    let total = 0;
    tableBody.querySelectorAll('tr').forEach(tr=>{
        total += parseFloat(tr.dataset.subtotal || 0);
    });
    grandTotalEl.innerText = rp(total);
}

document.getElementById('btnAddItem').onclick = () => {
    const bahanSel = document.getElementById('selectBahan');
    const bahanId = bahanSel.value;
    if(!bahanId) return alert('Pilih bahan dulu');

    const opt = bahanSel.options[bahanSel.selectedIndex];
    const qty = parseFloat(document.getElementById('inputQty').value) || 0;
    const harga = parseFloat(document.getElementById('inputHarga').value) || 0;
    const satuanBeli = document.getElementById('inputSatuanBeli').value || 'pack';
    const konversi = parseFloat(document.getElementById('inputKonversi').value) || 1;
    const baseUnit = opt.dataset.satuan;

    if(qty <= 0 || harga <= 0 || konversi <= 0) return alert('Input tidak valid');

    const subtotal = qty * harga;
    const qtyBase = Math.round(qty * konversi);

    const idx = tableBody.children.length;
    const tr = document.createElement('tr');
    tr.dataset.subtotal = subtotal;

    tr.innerHTML = `
        <td>
            ${opt.text.split('(')[0]}
            <div class="small text-muted">+${qtyBase} ${baseUnit}</div>
            <input type="hidden" name="items[${idx}][id_bahan]" value="${bahanId}">
            <input type="hidden" name="items[${idx}][qty_pack]" value="${qty}">
            <input type="hidden" name="items[${idx}][isi_pack]" value="${konversi}">
            <input type="hidden" name="items[${idx}][harga_pack]" value="${harga}">
        </td>
        <td class="text-center">${qty} ${satuanBeli}</td>
        <td class="text-end">${rp(harga)}</td>
        <td class="text-end">${rp(subtotal)}</td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-danger">✖</button></td>
    `;
    tr.querySelector('button').onclick = ()=>{ tr.remove(); hitungTotal(); };

    tableBody.appendChild(tr);
    hitungTotal();
};
</script>
@endsection
