@extends('layouts.app') 

@section('title', 'Tambah Pembelian Baru')

@section('content')

<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info">
                    <h6 class="m-0 font-weight-bold text-white">➕ Input Pembelian Baru</h6>
                </div>
                
                <div class="card-body p-4">
                    
                    <form action="{{ route('pembelian.store') }}" method="POST" id="formPembelian">
                        @csrf 

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Pembelian</label>
                            <input type="date" name="tgl" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Supplier</label>
                            <div class="input-group">
                                <select name="id_supp" id="selectSupplier" class="form-select" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supp)
                                        <option value="{{ $supp->id_supp }}">
                                            {{ $supp->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalQuickAdd">
                                    ➕ Baru
                                </button>
                            </div>
                            <div class="form-text text-muted">
                                Tidak nemu supplier? Klik tombol <b>+ Baru</b> untuk input cepat.
                            </div>
                        </div>

                        <!-- Items table -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Daftar Bahan Baku</label>
                            <table class="table table-sm table-bordered" id="tableItems">
                                <thead>
                                    <tr>
                                        <th width="45%">Bahan</th>
                                        <th width="15">Jumlah</th>
                                        <th width="20%">Harga</th>
                                        <th width="20%">Subtotal</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total</td>
                                        <td class="fw-bold text-end" id="grandTotal">Rp 0</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="d-flex gap-2 align-items-center">
                                <input id="searchBahan" class="form-control" placeholder="Cari bahan..." style="max-width:320px">
                                <select id="selectBahan" class="form-select">
                                    <option value="">-- Pilih Bahan --</option>
                                    @foreach($bahans as $b)
                                        <option value="{{ $b->id_bahan }}" data-stok="{{ $b->stok }}" data-satuan="{{ $b->satuan }}">{{ $b->nama_bahan }} (stok: {{ $b->stok }} {{ $b->satuan }})</option>
                                    @endforeach
                                </select>
                                <input type="number" id="inputJumlah" class="form-control" min="1" value="1" style="max-width:120px">
                                <input type="text" id="inputSatuanBeli" class="form-control" placeholder="Satuan beli (mis. pack/kg)" style="max-width:140px">
                                <input type="number" id="inputKonversi" class="form-control" min="0.0001" step="0.0001" value="1" title="Jumlah unit stok per 1 satuan beli" style="max-width:140px">
                                <input type="number" id="inputHarga" class="form-control" min="0" placeholder="Harga Satuan" style="max-width:150px">
                                <button type="button" id="btnAddItem" class="btn btn-info">Tambah</button>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-success px-4 fw-bold">Simpan Pembelian ✅</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalQuickAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Supplier Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formQuickAdd">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Supplier</label>
                        <input type="text" name="nama_supplier_baru" id="inputNamaBaru" class="form-control" placeholder="Contoh: CV Maju Jaya" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-info">Simpan & Pilih Otomatis</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('formQuickAdd').addEventListener('submit', function(e) {
        e.preventDefault();

        let nama = document.getElementById('inputNamaBaru').value;
        let token = document.querySelector('input[name="_token"]').value;

        fetch("{{ route('supplier.quick_store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token
            },
            body: JSON.stringify({ nama_supplier_baru: nama })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                let select = document.getElementById('selectSupplier');
                let option = new Option(data.nama_supplier, data.id_supp);
                select.add(option, undefined);
                select.value = data.id_supp;

                let modalElement = document.getElementById('modalQuickAdd');
                let modalInstance = bootstrap.Modal.getInstance(modalElement);
                modalInstance.hide();

                document.getElementById('inputNamaBaru').value = '';
                alert('Supplier berhasil ditambahkan! ✅');
            } else {
                alert('Gagal menyimpan data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan: ' + error.message);
        });
    });
</script>

<script>
    // Item row handling
    const btnAdd = document.getElementById('btnAddItem');
    const selectBahan = document.getElementById('selectBahan');
    const inputJumlah = document.getElementById('inputJumlah');
    const inputHarga = document.getElementById('inputHarga');
    const tableBody = document.querySelector('#tableItems tbody');
    const grandTotalEl = document.getElementById('grandTotal');

    function formatRp(n){
        return 'Rp ' + n.toLocaleString('id-ID');
    }

    function recalcTotal(){
        let total = 0;
        tableBody.querySelectorAll('tr').forEach(row => {
            const sub = parseFloat(row.querySelector('.subtotal').dataset.value) || 0;
            total += sub;
        });
        grandTotalEl.textContent = formatRp(total);
    }

    btnAdd.addEventListener('click', function(){
        const bahanId = selectBahan.value;
        if(!bahanId) return alert('Pilih bahan dulu');
        const opt = selectBahan.options[selectBahan.selectedIndex];
        const jumlah = parseFloat(inputJumlah.value) || 0;
        const harga = parseFloat(inputHarga.value) || 0;
        const satuanBeli = document.getElementById('inputSatuanBeli').value.trim();
        const konversi = parseFloat(document.getElementById('inputKonversi').value) || 1;
        const baseUnit = opt.dataset.satuan || '';

        if(jumlah <= 0) return alert('Jumlah minimal 1');
        if(harga <= 0) return alert('Harga harus lebih dari 0');
        if(konversi <= 0) return alert('Konversi harus lebih dari 0');

        // compute base values for storage
        const jumlahBase = Math.round(jumlah * konversi); // quantity in base unit (stok) - round to integer for stok
        const hargaPerBase = konversi > 0 ? (harga / konversi) : harga; // price per base unit
        const subtotalDisplay = harga * jumlah;

        // create row
        const tr = document.createElement('tr');
        const rowIndex = tableBody.querySelectorAll('tr').length;
        tr.innerHTML = `
            <td>
                ${opt.text.split('(')[0].trim()}
                <div class="small text-muted">Beli: ${jumlah} ${satuanBeli || ''} &middot; Stok +${jumlahBase} ${baseUnit}</div>
                <input type="hidden" name="items[${rowIndex}][id_bahan]" value="${bahanId}">
            </td>
            <td class="text-center">${jumlah} ${satuanBeli}
                <input type="hidden" name="items[${rowIndex}][qty_pack]" value="${jumlah}">
                <input type="hidden" name="items[${rowIndex}][isi_pack]" value="${konversi}">
            </td>
            <td class="text-end">${formatRp(harga)}
                <input type="hidden" name="items[${rowIndex}][harga_pack]" value="${harga}">
            </td>
            <td class="text-end subtotal" data-value="${subtotalDisplay}">${formatRp(subtotalDisplay)}</td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remove">✖</button></td>
        `;
        tableBody.appendChild(tr);
        recalcTotal();

        // clear selection
        selectBahan.selectedIndex = 0; inputJumlah.value = 1; inputHarga.value = ''; document.getElementById('inputSatuanBeli').value = ''; document.getElementById('inputKonversi').value = 1;
    });

    // Bahan search (simple client-side filter)
    const searchBahan = document.getElementById('searchBahan');
    const originalOptions = Array.from(selectBahan.options).slice();
    searchBahan.addEventListener('input', function(){
        const q = this.value.trim().toLowerCase();
        // clear current options
        selectBahan.options.length = 0;
        // always keep placeholder
        selectBahan.add(new Option('-- Pilih Bahan --', ''));
        originalOptions.slice(1).forEach(opt => {
            if(opt.text.toLowerCase().includes(q)) selectBahan.add(opt.cloneNode(true));
        });
    });

    // When bahan changes, set helpful placeholders for purchase unit and reset konversi
    selectBahan.addEventListener('change', function(){
        const opt = this.options[this.selectedIndex];
        const base = opt ? opt.dataset.satuan : '';
        const inputSatuan = document.getElementById('inputSatuanBeli');
        const inputKonv = document.getElementById('inputKonversi');
        if(inputSatuan) inputSatuan.placeholder = 'Satuan beli (mis. pack/kg) — stok base: ' + (base || '-');
        if(inputKonv) inputKonv.value = 1;
    });

    // Remove row
    document.addEventListener('click', function(e){
        if(e.target.classList.contains('btn-remove')){
            e.target.closest('tr').remove();
            recalcTotal();
        }
    });

    // On submit, ensure there is at least one item
    document.getElementById('formPembelian').addEventListener('submit', function(e){
        if(tableBody.querySelectorAll('tr').length == 0){
            e.preventDefault();
            alert('Tambahkan minimal 1 bahan ke pembelian');
        } else {
            // Show confirmation before submit
            if(!confirm('Yakin ingin menyimpan pembelian ini? Data akan langsung masuk ke sistem.')) {
                e.preventDefault();
            }
        }
    });
</script>

@endsection
