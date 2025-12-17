<div class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-cookie fs-3 text-warning"></i>
        <span class="brand-text ms-2">Dewi Cookies</span>
    </div>

    <div class="py-3" style="overflow-y: auto; height: calc(100vh - 70px);">
        
        <div class="px-3">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="nav-title">Inventory</div>
        <div class="px-3">
            <a href="{{ route('bahan-baku.index') }}" class="nav-link {{ request()->routeIs('bahan-baku.*') ? 'active' : '' }}" title="Bahan Baku">
                <i class="bi bi-box-seam-fill"></i>
                <span>Bahan Baku</span>
            </a>
            <a href="{{ route('produk.index') }}" class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}" title="Produk Jadi">
                <i class="bi bi-cake2-fill"></i>
                <span>Produk</span>
            </a>
            <a href="{{ route('resep.index') }}" class="nav-link {{ request()->routeIs('resep.*') ? 'active' : '' }}" title="Resep">
                <i class="bi bi-journal-text"></i>
                <span>Resep</span>
            </a>
            <a href="{{ route('supplier.index') }}" class="nav-link {{ request()->routeIs('supplier.*') ? 'active' : '' }}" title="Supplier">
                <i class="bi bi-truck"></i>
                <span>Supplier</span>
            </a>
        </div>

        <div class="nav-title">Transaksi</div>
        <div class="px-3">
            <a href="{{ route('penjualan.index') }}" class="nav-link {{ request()->routeIs('penjualan.*') ? 'active' : '' }}" title="Kasir">
                <i class="bi bi-receipt"></i>
                <span>Penjualan </span>
            </a>
            <a href="{{ route('pembelian.index') }}" class="nav-link {{ request()->routeIs('pembelian.*') ? 'active' : '' }}" title="Restock">
                <i class="bi bi-cart-plus-fill"></i>
                <span>Belanja Stok</span>
            </a>
            <a href="{{ route('customer.index') }}" class="nav-link {{ request()->routeIs('customer.*') ? 'active' : '' }}" title="Customer">
                <i class="bi bi-people-fill"></i>
                <span>Customer</span>
            </a>
        </div>

        @if(Auth::user()->role == 'owner')
        <div class="nav-title">Laporan</div>
        <div class="px-3">
            <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" title="Keuangan">
                <i class="bi bi-pie-chart-fill"></i>
                <span>Laporan</span>
            </a>
        </div>
        @endif

    </div>
</div>