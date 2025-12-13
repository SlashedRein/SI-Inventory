<a href="{{ route('dashboard') }}" class="..."> ```

---

### **2. Bikin Sidebar Buka-Tutup (Mode Clean / Icon Only)**

Kita akan update CSS dan JS di **Layout Utama** agar saat tombol ditekan, sidebar mengecil dan teksnya sembunyi (hanya icon yang tampil).

**Update File: `resources/views/layouts/app.blade.php`**
Timpa isinya dengan kode yang sudah di-update ini. Saya tambahkan CSS khusus `.sidebar-collapsed`.

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dewi Cookies')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        :root {
            --primary: #E07A5F; --primary-hover: #D06348;
            --secondary: #264653; --bg-light: #FDFBF7;
            --sidebar-width: 260px; --sidebar-collapsed-width: 80px; /* Ukuran saat tutup */
            --header-height: 70px;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg-light); overflow-x: hidden; }

        /* SIDEBAR UTAMA */
        .sidebar {
            width: var(--sidebar-width); position: fixed; left: 0; top: 0; bottom: 0;
            background-color: var(--secondary); color: #fff; z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap; /* Biar teks gak turun ke bawah saat mengecil */
            overflow: hidden;
        }

        /* HEADER SIDEBAR */
        .sidebar-brand {
            height: var(--header-height); display: flex; align-items: center; padding: 0 25px;
            font-size: 1.25rem; font-weight: 700; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.08);
            transition: all 0.3s;
        }

        /* LINK SIDEBAR */
        .nav-link {
            color: rgba(255,255,255,0.7); padding: 12px 20px; border-radius: 12px;
            margin: 5px 15px; display: flex; align-items: center; gap: 12px;
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: var(--primary); color: #fff; box-shadow: 0 4px 15px rgba(224, 122, 95, 0.4);
        }
        .nav-title {
            font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;
            color: rgba(255,255,255,0.4); margin: 25px 25px 10px; font-weight: 700;
            transition: opacity 0.3s;
        }

        /* KONTEN UTAMA */
        .main-content {
            margin-left: var(--sidebar-width); transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh; display: flex; flex-direction: column;
        }
        .topbar {
            height: var(--header-height); background: rgba(255,255,255,0.8); backdrop-filter: blur(10px);
            padding: 0 30px; display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 99; border-bottom: 1px solid rgba(0,0,0,0.03);
        }

        /* === MODE TUTUP (COLLAPSED) === */
        body.sidebar-collapsed .sidebar { width: var(--sidebar-collapsed-width); }
        body.sidebar-collapsed .main-content { margin-left: var(--sidebar-collapsed-width); }
        
        /* Sembunyikan Teks saat tutup */
        body.sidebar-collapsed .sidebar .nav-link span,
        body.sidebar-collapsed .sidebar .nav-title,
        body.sidebar-collapsed .sidebar .brand-text {
            opacity: 0; pointer-events: none; display: none;
        }
        
        /* Pusatkan Icon saat tutup */
        body.sidebar-collapsed .sidebar .nav-link { justify-content: center; padding: 12px 0; margin: 5px 10px; }
        body.sidebar-collapsed .sidebar .sidebar-brand { justify-content: center; padding: 0; }
        body.sidebar-collapsed .sidebar .nav-link i { font-size: 1.25rem; margin: 0; }

        /* Mobile Responsive */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
            body.sidebar-open .sidebar { transform: translateX(0); }
            body.sidebar-collapsed .sidebar { width: var(--sidebar-width); } /* Di HP gak usah mode icon */
        }
    </style>
</head>
<body>

    @include('layouts.sidebar')

    <div class="main-content">
        @include('layouts.topbar')

        <div class="content-area p-4">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i> {{ session('success') }}
                </div>
            @endif
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logic Buka Tutup Sidebar
        const btnToggle = document.getElementById('btnToggle');
        
        btnToggle?.addEventListener('click', function() {
            if (window.innerWidth >= 992) {
                // Mode Desktop: Tambah class ke Body
                document.body.classList.toggle('sidebar-collapsed');
            } else {
                // Mode Mobile: Slide sidebar
                document.body.classList.toggle('sidebar-open');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>