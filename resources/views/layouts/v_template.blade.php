<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>WEAR</title>

    {{-- SB Admin CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="{{ asset('template/css/styles.css') }}" rel="stylesheet" />

    {{-- Font Awesome --}}
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

</head>

<body class="sb-nav-fixed">
    {{-- ===== Top Navbar ===== --}}
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3 d-flex align-items-center" href="#">
    <img src="{{ asset('template/assets/img/logo_wear.jpeg') }}" alt="Logo" class="me-2" style="height: 45px !important; max-height: 45px !important; width: auto !important; object-fit: contain; border-radius: 50%;">
    <span class="fw-bold fs-4">WEAR</span>
</a>

        <!-- Sidebar Toggle -->
        <button class="btn btn-link btn-sm order-1 order-lg-0 ms-2" id="sidebarToggle" href="#!">
            <i class="fas fa-bars"></i>
        </button>


        <!-- Navbar Search (hidden on mobile) -->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        <!-- User Dropdown -->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changeOwnPasswordModal">
                            <i class="fas fa-key me-1"></i> Ganti Password
                        </a>
                    </li>
                    <li><hr class="dropdown-divider" /></li>
                    <li>
                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin logout?')) { document.getElementById('topnav-logout-form').submit(); }">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                        <form id="topnav-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    {{-- ===== Layout Wrapper ===== --}}
    <div id="layoutSidenav">
        {{-- Sidebar (SB Admin) --}}
        @include('layouts.v_nav')

        {{-- ===== Main Content ===== --}}
        <div id="layoutSidenav_content">
            <main class="p-4">
                {{-- Notifikasi Global (Khusus Alert Sukses/Error) --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
                
            </main>

            {{-- ===== Footer ===== --}}
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">© WEAR {{ date('Y') }}</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    {{-- ===== Modal Ganti Password Global ===== --}}
    <div class="modal fade" id="changeOwnPasswordModal" tabindex="-1" aria-labelledby="changeOwnPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.change-own-password') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="changeOwnPasswordModalLabel"><i class="fas fa-key me-2"></i>Ganti Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" class="form-control" name="current_password" required placeholder="Masukkan password lama Anda">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" class="form-control" name="new_password" required minlength="6" placeholder="Minimal 6 karakter">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" name="new_password_confirmation" required minlength="6" placeholder="Ketik ulang password baru">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== JS Section ===== --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('template/js/scripts.js') }}"></script>
    
    <!-- Datatables -->
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('template/js/datatables-simple-demo.js') }}"></script>
    @stack('scripts')

    {{-- ===== GLOBAL RESPONSIVE UI INJECTOR ===== --}}
    <style>
        /* CSS Khusus Mobile */
        @media (max-width: 768px) {
            /* Tabel tidak gepeng, melainkan bisa di-scroll */
            .hi-table, .dataTable-table, .modern-table {
                min-width: 650px !important;
            }
            
            /* Susun elemen berjajar menjadi menurun agar muat di HP */
            .d-flex:not(.nav, .navbar-nav, .sb-topnav, .navbar-brand) {
                flex-wrap: wrap !important;
            }
            
            /* Tab navigasi (seperti di detail pasien) bisa digeser ke samping */
            .nav-tabs {
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                overflow-y: hidden !important;
                white-space: nowrap !important;
                -webkit-overflow-scrolling: touch;
            }
            
            /* Perkecil padding kotak form agar lebih lega */
            .hi-card-body {
                padding: 0.75rem !important;
            }
        }
    </style>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Auto-wrap SEMUA tabel ke dalam <div class="table-responsive">
            const tables = document.querySelectorAll('table.hi-table, table.dataTable-table, table.modern-table');
            tables.forEach(table => {
                // Pastikan belum dibungkus oleh table-responsive sebelumnya
                if (table.parentElement && !table.parentElement.classList.contains('table-responsive')) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'table-responsive w-100';
                    wrapper.style.overflowX = 'auto';
                    wrapper.style.webkitOverflowScrolling = 'touch';
                    
                    // Sisipkan wrapper sebelum tabel
                    table.parentNode.insertBefore(wrapper, table);
                    // Pindahkan tabel ke dalam wrapper
                    wrapper.appendChild(table);
                }
            });
        });
    </script>
</body>
</html>
