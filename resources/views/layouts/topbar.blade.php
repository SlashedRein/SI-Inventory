<div class="topbar">
    <div class="d-flex align-items-center">
        <!-- <button class="btn btn-light d-lg-none me-3" id="btnMobileToggle">
            <i class="bi bi-list fs-4"></i>
        </button> -->
        <button id="btnToggle" class="btn btn-light me-3">
            <i class="bi bi-list fs-4"></i>
        </button>
        <h5 class="mb-0 fw-bold text-brown">
            @yield('title_page', 'Dashboard')
        </h5>
    </div>

    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
            <div class="me-2 text-end d-none d-md-block">
                <span class="d-block fw-bold small">{{ Auth::user()->name }}</span>
                <span class="d-block text-muted" style="font-size: 10px;">{{ ucfirst(Auth::user()->role) }}</span>
            </div>
            <div class="bg-brown text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item text-danger" type="submit">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>