<div class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('dashboard') }}" style="display: flex; justify-content: center; align-items: center;">
            <img src="../assets/img/asdp.png" class="navbar-brand-img h-100" alt="ASDP Logo">
            <span class="ms-3 font-weight-bold">V - IFCS</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div>
        <ul class="navbar-nav">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ url('dashboard') }}">
                    <div class="icon icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <img src="../assets/img/Dashboard_Icon.png" alt="Dashboard" width="20" height="20">
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            <!-- Section Pelabuhan -->
            @if(auth()->check() && auth()->user()->role == 'karyawan')
            <li class="nav-item mt-2">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Pelabuhan</h6>
            </li>

            <!-- Merak -->
            <li class="nav-item">
                <a class="nav-link {{ Request::is('merak') ? 'active' : '' }}" href="{{ url('merak') }}">
                    <div class="icon icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <img src="../assets/img/kapal.png" alt="Merak" width="20" height="20">
                    </div>
                    <span class="nav-link-text ms-1">Merak</span>
                </a>
            </li>

            <!-- Bakauheni -->
            <li class="nav-item">
                <a class="nav-link {{ Request::is('bakauheni') ? 'active' : '' }}" href="{{ url('bakauheni') }}">
                    <div class="icon icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <img src="../assets/img/kapal.png" alt="Bakauheni" width="20" height="20">
                    </div>
                    <span class="nav-link-text ms-1">Bakauheni</span>
                </a>
            </li>

            <!-- Section IFCS -->
            <li class="nav-item mt-2">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">IFCS</h6>
            </li>

            <!-- Kinerja IFCS -->
            <li class="nav-item">
                <a class="nav-link {{ Request::is('kinerja-ifcs') ? 'active' : '' }}" href="{{ url('kinerja-ifcs') }}">
                    <div class="icon icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <img src="../assets/img/KinerjaIFCS_Icon.jpg" alt="Kinerja IFCS" width="20" height="20">
                    </div>
                    <span class="nav-link-text ms-1">Kinerja IFCS</span>
                </a>
            </li>

            <!-- Market Lintasan -->
            <li class="nav-item">
                <a class="nav-link {{ Request::is('market-lintasan') ? 'active' : '' }}" href="{{ url('market-lintasan') }}">
                    <div class="icon icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <img src="../assets/img/MarketLintasan_Icon.png" alt="Market Lintasan" width="20" height="20">
                    </div>
                    <span class="nav-link-text ms-1">Market Lintasan</span>
                </a>
            </li>

            <!-- Komposisi Segmen -->
            <li class="nav-item">
                <a class="nav-link {{ Request::is('komposisi-segmen') ? 'active' : '' }}" href="{{ url('komposisi-segmen') }}">
                    <div class="icon icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <img src="../assets/img/KomposisiSegmen_Icon.jpg" alt="Komposisi Segmen" width="20" height="20">
                    </div>
                    <span class="nav-link-text ms-1">Komposisi Segmen</span>
                </a>
            </li>

            <!-- Laba Kapal -->
            <li class="nav-item">
                <a class="nav-link {{ Request::is('laba-kapal') ? 'active' : '' }}" href="{{ url('laba-kapal') }}">
                    <div class="icon icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <img src="../assets/img/LabaKapal_Icon.png" alt="Laba Kapal" width="20" height="20">
                    </div>
                    <span class="nav-link-text ms-1">Laba Kapal</span>
                </a>
            </li>
            @endif

            <!-- Section Account -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account</h6>
            </li>

            <!-- User Profile -->
            <li class="nav-item">
                <a class="nav-link {{ Request::is('user-profile') ? 'active' : '' }}" href="{{ url('user-profile') }}">
                    <div class="icon icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <img src="../assets/img/user.jpg" alt="User" width="20" height="20">
                    </div>
                    <span class="nav-link-text ms-1">User Profile</span>
                </a>
            </li>

            <!-- User Management (Admin Only) -->
            @if(auth()->check() && auth()->user()->role == 'admin')
            <li class="nav-item pb-2">
                <a class="nav-link {{ Request::is('user-management') ? 'active' : '' }}" href="{{ url('user-management') }}">
                    <div class="icon icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <img src="../assets/img/user.jpg" alt="User Management" width="20" height="20">
                    </div>
                    <span class="nav-link-text ms-1">User Management</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>
