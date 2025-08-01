<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
                    {{ str_replace('-', ' ', Request::path()) }}</li>
            </ol>
            <h6 class="font-weight-bolder mb-0 text-capitalize">{{ str_replace('-', ' ', Request::path()) }}</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex justify-content-end" id="navbar">
            <div class="ms-md-3 pe-md-3 d-flex align-items-center">
            </div>
            <ul class="navbar-nav  justify-content-end">
                <li class="nav-item d-flex align-items-center">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a href="#" class="nav-link text-body font-weight-bold px-0"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-user me-sm-1"></i>
                        <span class="d-sm-inline d-none">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="avatar avatar-xl position-relative">
            <td class="text-center align-middle">
                <div class="avatar avatar-xl position-relative">
                    <div style="width: 50px; height: 50px;">
                        @if(Auth::user()->image)
                        <img src="{{ asset('storage/image/' . Auth::user()->image) }}" alt="Profile Image"
                            class="w-100 border-radius-lg shadow-sm" style="height: 100%; object-fit: cover;">
                        @else
                        <img src="{{ asset('assets/img/user.jpg') }}" alt="Default Image"
                            class="w-100 border-radius-lg shadow-sm" style="height: 100%; object-fit: cover;">
                        @endif
                    </div>
            </td>
        </div>
    </div>
</nav>
<!-- End Navbar -->
