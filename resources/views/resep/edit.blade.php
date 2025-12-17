@extends('layouts.app')

@section('title', 'Atur Resep')
@section('title_page', 'Atur Resep Produk')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Resep: {{ $produk->nama_produk }}</h5>
            <p class="text-muted small mb-0">Daftar bahan dan takaran untuk produk ini.</p>
        </div>
        <div>
            <a href="{{ route('resep.index') }}" class="btn btn-light">Kembali</a>
        </div>
    </div>

    <div class="table-responsive mb-3">
        <table class="table table-striped">
            <thead class="bg-light small text-uppercase text-secondary">
                <tr>
                    <th class="ps-3">Bahan</th>
                    <th class="text-center">Takaran</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produk->resep as $r)
                <tr>
                    <td class="ps-3">{{ $r->bahan->nama_bahan ?? 'Bahan Terhapus' }}</td>
                    <td class="text-center">{{ $r->takaran }}</td>
                    <td class="text-center">{{ $r->satuan }}</td>
                    <td class="text-end pe-3">
                        @if(Auth::user()->role == 'owner')
                        <form action="{{ route('resep.destroy', $r->id) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-light text-danger border rounded-2" type="submit"><i class="bi bi-trash"></i></button>
                        </form>
                        @else
                        <span class="text-muted small">Hanya owner dapat mengubah</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">Belum ada bahan di resep.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(Auth::user()->role == 'owner')
    <div class="border-top pt-3">
        <h6 class="fw-bold small mb-3">Tambah Bahan</h6>
        <form action="{{ route('resep.store', $produk->id_produk) }}" method="POST">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small">Pilih Bahan</label>
                    <select name="id_bahan" class="form-select" required>
                        <option value="">-- Pilih Bahan --</option>
                        @foreach($bahans as $b)
                        <option value="{{ $b->id_bahan }}">{{ $b->nama_bahan }} ({{ $b->satuan }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Takaran</label>
                    <input type="number" name="takaran" class="form-control" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Satuan</label>
                    <input type="text" name="satuan" class="form-control" required>
                </div>
            </div>

            <div class="mt-3 text-end">
                <button class="btn btn-light" type="reset">Reset</button>
                <button class="btn btn-primary" type="submit">Tambah Bahan</button>
            </div>
        </form>
    </div>
    @else
    <div class="alert alert-info">Hanya pengguna dengan peran <strong>owner</strong> yang dapat mengubah resep.</div>
    @endif
</div>

@push('scripts')
<script>
// SweetAlert confirmation for delete
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Hapus bahan dari resep?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33'
        }).then(result => { if(result.isConfirmed) this.submit(); });
    });
});
</script>
@endpush
@endsection
