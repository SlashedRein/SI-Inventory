<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Inventory')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        :root {
            --primary: #E07A5F;       /* Terracotta */
            --primary-hover: #D06348;
            --secondary: #264653;     /* Deep Slate */
            --bg-light: #FDFBF7;      /* Cream */
            
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 80px;
            --header-height: 70px;
            --radius-card: 16px;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-light); overflow-x: hidden; }

        /* === 1. SIDEBAR BASE === */
        .sidebar {
            width: var(--sidebar-width); 
            position: fixed; left: 0; top: 0; bottom: 0;
            background-color: var(--secondary); color: #fff; 
            z-index: 1050; /* Di atas konten */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap; overflow: hidden;
            box-shadow: 4px 0 20px rgba(0,0,0,0.05);
        }

        /* Elemen Sidebar */
        .sidebar-brand {
            height: var(--header-height); display: flex; align-items: center; padding: 0 25px;
            font-size: 1.25rem; font-weight: 700; color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        
        .nav-link {
            color: rgba(255,255,255,0.7); padding: 12px 20px; border-radius: 0 25px 25px 0;
            margin: 5px 15px 5px 0; display: flex; align-items: center; gap: 12px;
            transition: all 0.2s; font-weight: 500; text-decoration: none;
        }
        .nav-link:hover, .nav-link.active {
            background-color: var(--primary); color: #fff;
            box-shadow: 0 4px 15px rgba(224, 122, 95, 0.4);
        }
        .nav-title {
            font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;
            color: rgba(255,255,255,0.4); margin: 25px 25px 10px; font-weight: 700;
        }

        /* === 2. MODE DESKTOP (Layar Besar) === */
        @media (min-width: 992px) {
            /* Logic Collapse Desktop */
            body.sidebar-collapsed .sidebar { width: var(--sidebar-collapsed-width); }
            body.sidebar-collapsed .main-content { margin-left: var(--sidebar-collapsed-width); }
            
            /* Sembunyikan Teks saat collapsed */
            body.sidebar-collapsed .sidebar .nav-link span,
            body.sidebar-collapsed .sidebar .nav-title,
            body.sidebar-collapsed .sidebar .brand-text { display: none; opacity: 0; }
            
            /* Center Icon */
            body.sidebar-collapsed .sidebar .nav-link { justify-content: center; padding: 12px 0; }
            body.sidebar-collapsed .sidebar .nav-link i { font-size: 1.4rem; margin: 0; }
            body.sidebar-collapsed .sidebar .sidebar-brand { justify-content: center; padding: 0; }
        }

        /* === 3. MODE MOBILE (Layar Kecil) === */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%); /* Default sembunyi */
                width: var(--sidebar-width) !important; /* Paksa lebar penuh, JANGAN ikut collapsed */
            }
            
            /* Saat Mobile Open */
            body.mobile-sidebar-open .sidebar {
                transform: translateX(0);
                box-shadow: 10px 0 30px rgba(0,0,0,0.2);
            }

            .main-content { margin-left: 0 !important; }

            /* OVERLAY HITAM */
            .sidebar-overlay {
                position: fixed; top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0, 0, 0, 0.5); z-index: 1040;
                display: none; backdrop-filter: blur(2px);
            }
            body.mobile-sidebar-open .sidebar-overlay { display: block; animation: fadeIn 0.3s; }
        }

        /* === 4. MAIN CONTENT === */
        .main-content {
            margin-left: var(--sidebar-width); 
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh; display: flex; flex-direction: column;
        }

        /* Card & Animasi */
        .card-custom {
            background: #fff; border-radius: var(--radius-card); border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03); transition: transform 0.2s;
        }
        .card-custom:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.06); }
        
        .fade-in-up { animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(20px); }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @include('layouts.sidebar')

    <div class="main-content">
        @include('layouts.topbar')

        <div class="p-4" style="flex: 1;">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center fade-in-up">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i> {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const btnToggle = document.getElementById('btnToggle');
        const overlay = document.getElementById('sidebarOverlay');
        const body = document.body;

        // 1. Logic Tombol Burger
        if(btnToggle){
            btnToggle.addEventListener('click', function(e) {
                e.stopPropagation(); // Biar gak nembus
                
                if (window.innerWidth >= 992) {
                    // DESKTOP: Toggle Collapsed
                    body.classList.toggle('sidebar-collapsed');
                } else {
                    // MOBILE: Toggle Open
                    body.classList.toggle('mobile-sidebar-open');
                }
            });
        }

        // 2. Logic Klik Overlay (Tutup Sidebar Mobile)
        if(overlay){
            overlay.addEventListener('click', function() {
                body.classList.remove('mobile-sidebar-open');
            });
        }

        // 3. Reset saat Resize Layar
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                body.classList.remove('mobile-sidebar-open'); // Hapus mode mobile kalau layar dibesarkan
            }
        });
    </script>
    @stack('scripts')
</body>
</html>