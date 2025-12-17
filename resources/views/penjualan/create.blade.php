@extends('layouts.app') 

@section('title', 'Tambah Penjualan Baru')

@section('content')

<div class="container-fluid">

    <!-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Penjualan</h1>
        <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            ⬅️ Kembali ke Laporan
        </a>
    </div> -->

    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">➕ Input Penjualan Baru</h6>
                </div>
                
                <div class="card-body p-4">
                    
                    <form action="{{ route('penjualan.store') }}" method="POST" id="formPenjualan">
                        @csrf 

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Transaksi</label>
                            <input type="date" name="tgl_penjualan" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Pelanggan</label>
                            <div class="input-group">
                                <select name="id_cust" id="selectPelanggan" class="form-select" required>
                                    <option value="">-- Pilih Customer --</option>
                                    @foreach($customers as $cust)
                                        <option value="{{ $cust->id_cust }}">
                                            {{ $cust->nama }} 
                                            @if(preg_match('/(PT|CV|UD)/i', $cust->nama))
                                                (Perusahaan)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalQuickAdd">
                                    ➕ Baru
                                </button>
                            </div>
                            <div class="form-text text-muted">
                                Tidak nemu nama? Klik tombol <b>+ Baru</b> untuk input cepat.
                            </div>
                        </div>

                        <!-- Items table -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Daftar Barang</label>
                            <table class="table table-sm table-bordered" id="tableItems">
                                <thead>
                                    <tr>
                                        <th width="45%">Produk</th>
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
                                <input id="searchProduk" class="form-control" placeholder="Cari produk..." style="max-width:320px">
                                <select id="selectProduk" class="form-select">
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($produks as $p)
                                        <option value="{{ $p->id_produk }}" data-harga="{{ $p->harga_jual }}" data-stok="{{ $p->stok }}">{{ $p->nama_produk }} (stok: {{ $p->stok }})</option>
                                    @endforeach
                                </select>
                                <input type="number" id="inputJumlah" class="form-control" min="1" value="1" style="max-width:120px">
                                <button type="button" id="btnAddItem" class="btn btn-primary">Tambah</button>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-success px-4 fw-bold">Simpan Transaksi ✅</button>
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
                <h5 class="modal-title fw-bold">Tambah Pelanggan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formQuickAdd">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan_baru" id="inputNamaBaru" class="form-control" placeholder="Contoh: Budi Santoso / PT Maju Jaya" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Simpan & Pilih Otomatis</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('formQuickAdd').addEventListener('submit', function(e) {
        e.preventDefault(); // Mencegah form reload halaman

        let nama = document.getElementById('inputNamaBaru').value;
        let token = document.querySelector('input[name="_token"]').value;

        // Kirim data ke Controller via Fetch API
        fetch("{{ route('customer.quick_store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token
            },
            body: JSON.stringify({ nama_pelanggan_baru: nama })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // 1. Tambahkan Option baru ke Select (sesuaikan dengan field id_cust/nama)
                let select = document.getElementById('selectPelanggan');
                let option = new Option(data.nama, data.id_cust);
                select.add(option, undefined);

                // 2. Langsung pilih option tersebut
                select.value = data.id_cust;

                // 3. Tutup Modal
                let modalElement = document.getElementById('modalQuickAdd');
                let modalInstance = bootstrap.Modal.getInstance(modalElement);
                modalInstance.hide();

                // 4. Reset input nama
                document.getElementById('inputNamaBaru').value = '';

                alert('Pelanggan berhasil ditambahkan! ✅');
            } else {
                alert('Gagal menyimpan data.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem.');
        });
    });
</script>

<script>
    // Item row handling
    const btnAdd = document.getElementById('btnAddItem');
    const selectProduk = document.getElementById('selectProduk');
    const inputJumlah = document.getElementById('inputJumlah');
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
        const prodId = selectProduk.value;
        if(!prodId) return alert('Pilih produk dulu');
        const opt = selectProduk.options[selectProduk.selectedIndex];
        const harga = parseFloat(opt.dataset.harga) || 0;
        const stok = parseInt(opt.dataset.stok) || 0;
        const jumlah = parseInt(inputJumlah.value) || 0;
        if(jumlah <= 0) return alert('Jumlah minimal 1');
        if(jumlah > stok) return alert('Stok tidak cukup (stok: ' + stok + ')');

        // create row
        const tr = document.createElement('tr');
        const rowIndex = tableBody.querySelectorAll('tr').length;
        tr.innerHTML = `
            <td>
                ${opt.text}
                <input type="hidden" name="items[${rowIndex}][id_produk]" value="${prodId}">
            </td>
            <td class="text-center">${jumlah}<input type="hidden" name="items[${rowIndex}][jumlah]" value="${jumlah}"></td>
            <td class="text-end">${formatRp(harga)}</td>
            <td class="text-end subtotal" data-value="${(harga*jumlah)}">${formatRp(harga*jumlah)}</td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remove">✖</button></td>
        `;
        tableBody.appendChild(tr);
        recalcTotal();

        // clear selection
        selectProduk.selectedIndex = 0; inputJumlah.value = 1;
    });

    // Product search (simple client-side filter)
    const searchProduk = document.getElementById('searchProduk');
    const originalOptions = Array.from(selectProduk.options).slice();
    searchProduk.addEventListener('input', function(){
        const q = this.value.trim().toLowerCase();
        // clear current options
        selectProduk.options.length = 0;
        // always keep placeholder
        selectProduk.add(new Option('-- Pilih Produk --', ''));
        originalOptions.slice(1).forEach(opt => {
            if(opt.text.toLowerCase().includes(q)) selectProduk.add(opt.cloneNode(true));
        });
    });

    // Remove row
    document.addEventListener('click', function(e){
        if(e.target.classList.contains('btn-remove')){
            e.target.closest('tr').remove();
            recalcTotal();
        }
    });

    // On submit, ensure there is at least one item
    document.getElementById('formPenjualan').addEventListener('submit', function(e){
        if(tableBody.querySelectorAll('tr').length == 0){
            e.preventDefault();
            alert('Tambahkan minimal 1 produk ke transaksi');
        } else {
            // Show confirmation before submit
            if(!confirm('Yakin ingin menyimpan transaksi ini? Data akan langsung masuk ke sistem.')) {
                e.preventDefault();
            }
        }
    });
</script>

@endsection