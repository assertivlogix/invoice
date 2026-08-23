<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Assertiv Logix Invoice System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --al-sidebar-bg: #0f172a;
            --al-sidebar-hover: #1e293b;
            --al-sidebar-active: #3b82f6;
            --al-sidebar-text: #94a3b8;
            --al-sidebar-light: #f8fafc;
            --al-bg-main: #f1f5f9;
            --al-card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --al-primary: #2563eb;
            --al-success: #10b981;
            --al-warning: #f59e0b;
            --al-danger: #ef4444;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--al-bg-main);
            color: #334155;
        }

        /* Sidebar Styling */
        #sidebar-wrapper {
            min-height: 100vh;
            width: 260px;
            background-color: var(--al-sidebar-bg);
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-brand i {
            color: #60a5fa;
            font-size: 1.5rem;
            margin-right: 0.75rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
            list-style: none;
        }

        .sidebar-nav .nav-item {
            margin: 0.2rem 0.8rem;
        }

        .sidebar-nav .nav-link {
            color: var(--al-sidebar-text);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.925rem;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        .sidebar-nav .nav-link i {
            width: 24px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .sidebar-nav .nav-link:hover {
            color: #ffffff;
            background-color: var(--al-sidebar-hover);
        }

        .sidebar-nav .nav-link.active {
            color: #ffffff;
            background-color: var(--al-sidebar-active);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
        }

        /* Main Content Wrapper */
        #page-content-wrapper {
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar */
        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.85rem 1.5rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Cards & Components */
        .card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: var(--al-card-shadow);
        }

        .stat-card {
            padding: 1.25rem;
            border-radius: 0.75rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: var(--al-card-shadow);

            position: relative;
            overflow: hidden;
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .badge-status {
            font-size: 0.78rem;
            padding: 0.35em 0.7em;
            border-radius: 2rem;
            font-weight: 600;
        }

        .badge-draft { background-color: #f1f5f9; color: #475569; }
        .badge-sent { background-color: #dbeafe; color: #1e40af; }
        .badge-viewed { background-color: #e0e7ff; color: #3730a3; }
        .badge-partial { background-color: #fef3c7; color: #92400e; }
        .badge-paid { background-color: #d1fae5; color: #065f46; }
        .badge-overdue { background-color: #fee2e2; color: #991b1b; }
        .badge-cancelled { background-color: #f3f4f6; color: #6b7280; }

        .table-custom {
            margin-bottom: 0;
        }

        .table-custom th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.825rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-custom td {
            padding: 1rem;
            vertical-align: middle;
            font-size: 0.9rem;
            border-bottom: 1px solid #f1f5f9;
        }

        @media (max-width: 991.98px) {
            #sidebar-wrapper { left: -260px; }
            #sidebar-wrapper.toggled { left: 0; }
            #page-content-wrapper { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <div class="sidebar-brand">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span>ASSERTIV LOGIX</span>
        </div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> Clients
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects*') ? 'active' : '' }}">
                    <i class="fa-solid fa-diagram-project"></i> Projects
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services*') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i> Services
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice"></i> Invoices
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments*') ? 'active' : '' }}">
                    <i class="fa-solid fa-credit-card"></i> Payments
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> Reports
                </a>
            </li>
            @if(auth()->user() && auth()->user()->isAdmin())
            <li class="nav-item">
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i> Settings
                </a>
            </li>
            @endif
        </ul>
    </div>

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <!-- Top Navbar -->
        <nav class="top-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn btn-light d-lg-none me-3" id="sidebarToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h5 class="mb-0 fw-bold text-slate-800">@yield('page-title', 'Dashboard')</h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Quick Create Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm px-3 rounded-pill dropdown-toggle fw-medium" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-plus me-1"></i> Quick Action
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="{{ route('invoices.create') }}"><i class="fa-solid fa-file-invoice text-primary me-2"></i> New Invoice</a></li>
                        <li><a class="dropdown-item" href="{{ route('clients.create') }}"><i class="fa-solid fa-user-plus text-success me-2"></i> New Client</a></li>
                        <li><a class="dropdown-item" href="{{ route('payments.create') }}"><i class="fa-solid fa-money-bill-wave text-warning me-2"></i> Record Payment</a></li>
                        <li><a class="dropdown-item" href="{{ route('projects.create') }}"><i class="fa-solid fa-diagram-project text-info me-2"></i> New Project</a></li>
                    </ul>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="user-avatar me-2">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="d-none d-md-block text-start">
                            <div class="fw-semibold fs-14 text-dark lh-1">{{ auth()->user()->name ?? 'Administrator' }}</div>
                            <small class="text-muted text-capitalize fs-12">{{ auth()->user()->role ?? 'admin' }}</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><span class="dropdown-item-text fw-semibold border-bottom pb-2">{{ auth()->user()->email ?? '' }}</span></li>
                        @if(auth()->user() && auth()->user()->isAdmin())
                        <li><a class="dropdown-item mt-2" href="{{ route('settings.index') }}"><i class="fa-solid fa-sliders me-2 text-muted"></i> System Settings</a></li>
                        @endif
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Body -->
        <main class="p-4 flex-grow-1">
            <!-- Flash Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="bg-white border-top py-3 text-center text-muted fs-13">
            Assertiv Logix Client & Invoice Management System &copy; {{ date('Y') }}. All rights reserved.
        </footer>
    </div>
</div>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', function () {
        document.getElementById('sidebar-wrapper').classList.toggle('toggled');
    });
</script>
@stack('scripts')
</body>
</html>
