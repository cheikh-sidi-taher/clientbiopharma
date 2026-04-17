<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Biopharma CRM') — Biopharma Mauritanie</title>
    <meta name="description" content="CRM Terrain Biopharma Mauritanie — Suivi des pharmacies à Nouakchott">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary: #1a6fbf;
            --primary-dark: #145a9e;
            --primary-light: #e8f0fa;
            --accent: #00b894;
            --accent-hover: #009e7e;
            --bg-main: #f0f4f9;
            --bg-sidebar: #0d1b2a;
            --sidebar-width: 260px;
            --topbar-height: 65px;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --card-bg: #ffffff;
            --shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.05);
            --radius: 12px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-main);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--bg-sidebar);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform .3s ease;
            overflow-y: auto;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-brand .logo-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: #fff;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-text h1 {
            font-size: 15px; font-weight: 700; color: #fff; line-height: 1.2;
        }
        .sidebar-brand .brand-text p {
            font-size: 11px; color: rgba(255,255,255,.45); font-weight: 400;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 0;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(255,255,255,.3);
            padding: 8px 20px 4px;
            margin-top: 8px;
        }

        .nav-item {
            margin: 2px 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 8px;
            color: rgba(255,255,255,.65);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all .2s ease;
        }

        .nav-link i {
            font-size: 18px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-link:hover {
            background: rgba(255,255,255,.08);
            color: #fff;
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary), #2980d9);
            color: #fff;
            box-shadow: 0 4px 12px rgba(26,111,191,.4);
        }

        .nav-badge {
            margin-left: auto;
            background: var(--accent);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }

        .user-info .user-name {
            font-size: 13px; font-weight: 600; color: #fff; line-height: 1.2;
        }
        .user-info .user-role {
            font-size: 11px; color: rgba(255,255,255,.45);
        }

        /* ===== TOPBAR ===== */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            z-index: 900;
            box-shadow: 0 1px 0 var(--border);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: var(--text-secondary);
        }

        .page-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-btn {
            width: 38px; height: 38px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 17px;
            position: relative;
            transition: all .2s;
            text-decoration: none;
        }
        .topbar-btn:hover { background: var(--bg-main); color: var(--primary); }

        .topbar-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 13px; font-weight: 700;
            cursor: pointer;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            min-height: 100vh;
        }

        .content-area {
            padding: 28px;
        }

        /* ===== CARDS ===== */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .card-header {
            padding: 18px 24px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }
        .card-body { padding: 24px; }

        /* ===== KPI STAT CARDS ===== */
        .stat-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 18px;
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,.1);
        }

        .stat-icon {
            width: 56px; height: 56px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .stat-value {
            font-size: 30px;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1;
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
            margin-top: 4px;
        }

        .stat-trend {
            font-size: 12px;
            font-weight: 600;
            margin-top: 6px;
        }
        .stat-trend.up { color: var(--accent); }
        .stat-trend.neutral { color: var(--text-muted); }

        /* ===== GRID ===== */
        .grid { display: grid; gap: 20px; }
        .grid-4 { grid-template-columns: repeat(4, 1fr); }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }

        /* ===== ALERTS ===== */
        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-info { background: #e8f4fd; color: #1a6fbf; border: 1px solid #bfdef7; }
        .alert-success { background: #e6f9f4; color: #00895e; border: 1px solid #b3eed9; }

        /* ===== BUTTONS ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all .2s;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #2980d9);
            color: #fff;
            box-shadow: 0 3px 10px rgba(26,111,191,.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            transform: translateY(-1px);
        }
        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        .btn-outline:hover { background: var(--primary-light); }

        /* ===== RESPONSIVE ===== */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 999;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .topbar { left: 0; }
            .main-content { margin-left: 0; }
            .menu-toggle { display: flex; }
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-3 { grid-template-columns: 1fr; }
            .grid-2 { grid-template-columns: 1fr; }
            .content-area { padding: 16px; }
        }

        @media (max-width: 480px) {
            .grid-4 { grid-template-columns: 1fr; }
        }

        /* Dropdown profile */
        .dropdown { position: relative; }
        .dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
            min-width: 180px;
            padding: 8px;
            display: none;
            z-index: 1100;
        }
        .dropdown-menu.show { display: block; }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            border-radius: 7px;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
        }
        .dropdown-item:hover { background: var(--bg-main); }
        .dropdown-item.text-danger { color: #e53e3e; }
        .dropdown-divider { height: 1px; background: var(--border); margin: 6px 0; }
    </style>

    @stack('styles')
</head>
<body>

{{-- Sidebar Overlay (mobile) --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

{{-- SIDEBAR --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-icon">
            <i class="bi bi-capsule-pill"></i>
        </div>
        <div class="brand-text">
            <h1>Biopharma</h1>
            <p>CRM Terrain — MRN</p>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Principal</div>

        <div class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="nav-section-label">Terrain</div>

        @canany(['view zones', 'manage zones'])
        <div class="nav-item">
            <a href="{{ route('zones.index') }}"
               class="nav-link {{ request()->routeIs('zones.*') ? 'active' : '' }}">
                <i class="bi bi-map-fill"></i>
                <span>Zones</span>
            </a>
        </div>
        @endcanany

        @canany(['view pharmacies', 'manage pharmacies'])
        <div class="nav-item">
            <a href="{{ route('pharmacies.index') }}"
               class="nav-link {{ request()->routeIs('pharmacies.*') ? 'active' : '' }}">
                <i class="bi bi-hospital-fill"></i>
                <span>Pharmacies</span>
            </a>
        </div>
        @endcanany

        @can('manage planning')
        <div class="nav-item">
            <a href="{{ route('planning.index') }}"
               class="nav-link {{ request()->routeIs('planning.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i>
                <span>Planning</span>
            </a>
        </div>
        @endcan

        @canany(['view visits', 'manage planning'])
        <div class="nav-item">
            <a href="{{ route('visits.index') }}"
               class="nav-link {{ request()->routeIs('visits.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-pulse-fill"></i>
                <span>Visites</span>
            </a>
        </div>
        @endcanany

        <div class="nav-section-label">Commercial</div>

        @canany(['view clients', 'manage clients'])
        <div class="nav-item">
            <a href="{{ route('clients.index') }}"
               class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Clients</span>
            </a>
        </div>
        @endcanany

        @canany(['view reports', 'export reports'])
        <div class="nav-item">
            <a href="{{ route('reports.index') }}"
               class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i>
                <span>Rapports</span>
            </a>
        </div>
        @endcanany

        @if(auth()->user()?->hasRole('Admin'))
        <div class="nav-section-label">Administration</div>
        <div class="nav-item">
            <a href="{{ route('profile.edit') }}"
               class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i>
                <span>Paramètres</span>
            </a>
        </div>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()?->name ?? 'Utilisateur' }}</div>
                <div class="user-role">
                    {{ auth()->user()?->getRoleNames()->first() ?? 'Utilisateur' }}
                </div>
            </div>
        </div>
    </div>
</aside>

{{-- TOPBAR --}}
<header class="topbar">
    <div class="topbar-left">
        <button class="menu-toggle" onclick="toggleSidebar()" aria-label="Menu">
            <i class="bi bi-list"></i>
        </button>
        <span class="page-title">@yield('page_title', 'Dashboard')</span>
    </div>
    <div class="topbar-right">
        @canany(['view reports', 'export reports'])
        <a href="{{ route('reports.index') }}" class="topbar-btn" title="Rapports">
            <i class="bi bi-bar-chart"></i>
        </a>
        @endcanany
        <div class="dropdown">
            <div class="topbar-avatar" onclick="toggleDropdown()" id="avatarBtn">
                {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
            </div>
            <div class="dropdown-menu" id="profileDropdown">
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="bi bi-person-circle"></i> Mon Profil
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger" style="background:none;border:none;width:100%;text-align:left;">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

{{-- MAIN CONTENT --}}
<main class="main-content">
    <div class="content-area">
        @if(session('success'))
            <div class="alert alert-success mb-4">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </div>
</main>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    }

    function toggleDropdown() {
        document.getElementById('profileDropdown').classList.toggle('show');
    }

    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('profileDropdown');
        const btn = document.getElementById('avatarBtn');
        if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
</script>

@stack('scripts')
</body>
</html>
