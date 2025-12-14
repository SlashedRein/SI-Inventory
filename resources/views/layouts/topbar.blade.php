<nav class="navbar navbar-expand bg-white border-bottom sticky-top shadow-sm" style="height: 70px; z-index: 99;">
    <div class="container-fluid px-4"> 
        
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light border-0 rounded-circle d-flex align-items-center justify-content-center p-2" id="btnToggle" style="width: 40px; height: 40px;">
                <i class="bi bi-list fs-4 text-secondary"></i>
            </button>

            <h5 class="mb-0 fw-bold text-dark d-none d-md-block">
                @yield('title_page', 'Dashboard')
            </h5>
        </div>

        <div class="ms-auto d-flex align-items-center gap-3">
            
            <div class="text-end d-none d-md-block lh-1">
                <span class="d-block fw-bold text-dark small mb-1">{{ Auth::user()->name ?? 'User' }}</span>
                <span class="d-block text-muted" style="font-size: 10px;">{{ ucfirst(Auth::user()->role ?? 'Admin') }}</span>
            </div>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center border border-primary border-opacity-25" style="width: 42px; height: 42px;">
                        <i class="bi bi-person-fill fs-5 text-primary"></i>
                    </div>
                </a>
                
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-4 mt-2" style="min-width: 200px;">
                    <li class="px-2 py-1">
                        <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">Akun Saya</small>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person-gear"></i> Edit Profil
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-2"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item rounded-3 py-2 text-danger d-flex align-items-center gap-2" type="submit">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</nav>