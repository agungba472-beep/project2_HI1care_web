<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                {{-- === Core Section === --}}
                <div class="sb-sidenav-menu-heading">Core</div>

                @php
                    $role = auth()->check() ? auth()->user()->role : null;
                    $username = auth()->check() ? (auth()->user()->username ?? auth()->user()->name ?? 'User') : 'Guest';
                @endphp

                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-home-alt"></i></div>
                    Dashboard
                </a>
                <a class="nav-link" href="{{ url('/profile') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-user-alt"></i></div>
                    Profile
                </a>

                <div class="sb-sidenav-menu-heading">Menu Admin</div>
                <a class="nav-link" href="{{ route('admin.users.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Manajemen Pengguna
                </a>
                <a class="nav-link" href="{{ route('admin.pasien.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-notes-medical"></i></div>
                    Monitoring Kepatuhan
                </a>
                <a class="nav-link" href="{{ route('admin.refill.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-pills"></i></div>
                    Refill ARV
                </a>
                <a class="nav-link" href="{{ route('admin.broadcast.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-bullhorn"></i></div>
                    Broadcast
                </a>
                <a class="nav-link" href="{{ route('admin.edukasi.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-medical"></i></div>
                    Modul Edukasi
                </a>
                <a class="nav-link" href="{{ route('admin.laporan.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                    Laporan Pasien
                </a>

                {{-- === Logout === --}}
                <div class="sb-sidenav-menu-heading">Akun</div>
                <a class="nav-link text-danger" href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                    <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt text-danger"></i></div>
                    Logout
                </a>
                <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                <div class="sb-sidenav-footer mt-3">
                    <div class="small">Logged in as:</div>
                    {{ $username }} ({{ $role ?? 'Unknown' }})
                </div>
            </div>
        </div>

    </nav>
</div>