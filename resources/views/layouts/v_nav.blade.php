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

                <a class="nav-link {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-home-alt"></i></div>
                    Dashboard
                </a>

                <div class="sb-sidenav-menu-heading">Menu Admin</div>
                <a class="nav-link {{ Request::routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Manajemen Pengguna
                </a>
                <a class="nav-link {{ Request::routeIs('admin.pasien.*') ? 'active' : '' }}" href="{{ route('admin.pasien.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-notes-medical"></i></div>
                    Monitoring Kepatuhan
                </a>
                <a class="nav-link {{ Request::routeIs('admin.refill.*') ? 'active' : '' }}" href="{{ route('admin.refill.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-pills"></i></div>
                    Refill ARV
                </a>
                <a class="nav-link {{ Request::routeIs('admin.broadcast.*') ? 'active' : '' }}" href="{{ route('admin.broadcast.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-bullhorn"></i></div>
                    Broadcast
                </a>
                <a class="nav-link {{ Request::routeIs('admin.edukasi.*') ? 'active' : '' }}" href="{{ route('admin.edukasi.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-medical"></i></div>
                    Modul Edukasi
                </a>
                <a class="nav-link {{ Request::routeIs('admin.jadwal.*') ? 'active' : '' }}" href="{{ route('admin.jadwal.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    Jadwal Nakes
                </a>

                {{-- === Logout === --}}
                <div class="sb-sidenav-menu-heading">Akun</div>
                <a class="nav-link text-danger" href="#" onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin logout?')) { document.getElementById('sidebar-logout-form').submit(); }">
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
