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
            /* Palette Modern Artisan */
            --primary: #E07A5F;       /* Terracotta (Tombol/Aksen) */
            --primary-hover: #D06348;
            --secondary: #264653;     /* Deep Slate (Sidebar) */
            --bg-light: #FDFBF7;      /* Cream White (Background) */
            --text-dark: #2A2F35;
            --text-muted: #6c757d;
            
            --sidebar-width: 280px;   /* Sedikit lebih lebar biar lega */
            --header-height: 70px;
            --radius-card: 16px;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-light); 
            color: var(--text-dark);
        }
        
        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width); position: fixed; left: 0; top: 0; bottom: 0;
            background-color: var(--secondary);
            color: #fff; z-index: 1000; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-top-right-radius: 0px; 
            box-shadow: 4px 0 20px rgba(0,0,0,0.05);
        }
        
        .sidebar-brand {
            height: var(--header-height);
            display: flex; align-items: center; padding: 0 25px;
            font-size: 1.25rem; font-weight: 700; color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 5px;
            transition: all 0.2s ease;
            font-weight: 500;
            display: flex; align-items: center; gap: 12px;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: #fff;
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background-color: var(--primary);
            box-shadow: 0 4px 15px rgba(224, 122, 95, 0.4);
        }

        .nav-title {
            font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;
            color: rgba(255,255,255,0.4); margin: 25px 20px 10px; font-weight: 700;
        }

        /* Layout Content */
        .main-content {
            margin-left: var(--sidebar-width); transition: 0.3s; min-height: 100vh;
            display: flex; flex-direction: column;
        }
        
        .topbar {
            height: var(--header-height); background: rgba(255,255,255,0.8);
            backdrop-filter: blur(10px);
            padding: 0 30px; display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 99;
            border-bottom: 1px solid rgba(0,0,0,0.03);
        }
        
        .content-area { padding: 30px; flex: 1; }

        /* Card & UI Elements */
        .card-custom {
            background: #fff; border-radius: var(--radius-card); border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            transition: transform 0.2s;
        }
        .card-custom:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.06); }
        
        .btn-primary { 
            background-color: var(--primary); border: none; 
            padding: 10px 20px; border-radius: 10px; font-weight: 600;
        }
        .btn-primary:hover { background-color: var(--primary-hover); }

        /* Mobile */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
            .sidebar.active { transform: translateX(0); }
        }
    </style>
</head>
<body>

    @include('layouts.sidebar')

    <div class="main-content">
        @include('layouts.topbar')

        <div class="content-area">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i> {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i> {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

        

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('btnMobileToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Script Toggle Sidebar Desktop
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            // Toggle class 'collapsed'
            sidebar.classList.toggle('collapsed');
            
            // Sesuaikan margin konten utama
            if (sidebar.classList.contains('collapsed')) {
                sidebar.style.width = '0px'; // Atau 80px jika mau mode icon only
                sidebar.style.overflow = 'hidden';
                mainContent.style.marginLeft = '0px';
            } else {
                sidebar.style.width = 'var(--sidebar-width)';
                mainContent.style.marginLeft = 'var(--sidebar-width)';
            }
        }
    </script>

    @stack('scripts')
</body>
</html>