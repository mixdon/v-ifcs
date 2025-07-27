@extends('layouts.user_type.auth')

@section('content')

<style>
    /* Menambahkan style kustom untuk memastikan tombol memiliki ukuran yang sama */
    .btn-custom {
        width: 120px; /* Atur lebar sesuai kebutuhan */
    }
</style>

@if(auth()->user()->role == 'admin')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-3 pt-4">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        {{-- Memindahkan tombol Add User dan form search ke satu baris --}}
                        <div id="header-buttons" class="d-flex w-100 justify-content-between align-items-center">
                            <button type="button" id="show-add-user-form" class="btn btn-primary btn-sm me-2">Add User</button>
                            <form action="{{ route('user-management.index') }}" method="GET" class="d-flex">
                                <div class="form-group mb-0 me-2">
                                    <input type="text" name="search" class="form-control" placeholder="Search..."
                                        value="{{ request('search') }}">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm btn-custom">Search</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Form Add User (Awalnya Tersembunyi) --}}
                <div id="add-user-form-section" class="card-body pt-4 p-3" style="display: none;">
                    <h5 class="mb-3">Add New User</h5>
                    <form action="{{ route('user-management.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ __('Name') }}</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">{{ __('Email') }}</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">{{ __('Password') }}</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">{{ __('Role') }}</label>
                                    <select class="form-select" id="role" name="role" required>
                                        @foreach($roles as $role)
                                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-danger me-2" id="cancel-add-user-form">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add User</button>
                        </div>
                    </form>
                </div>
                
                {{-- Bagian Tabel All Users (Awalnya Terlihat) --}}
                <div id="all-users-section">
                    <div class="card-body pt-4 p-3">
                        <h5 class="mb-3">All Users</h5>
                        {{-- Notifikasi untuk semua operasi ditempatkan di sini --}}
                        @if(session('user_store_success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <span class="alert-text text-white">
                                {{ session('user_store_success') }}
                            </span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </button>
                        </div>
                        @endif
                        @if(session('user_store_fail'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <span class="alert-text text-white">
                                {{ session('user_store_fail') }}
                            </span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </button>
                        </div>
                        @endif
                        @if(session('user_update_role_success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <span class="alert-text text-white">
                                {{ session('user_update_role_success') }}
                            </span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </button>
                        </div>
                        @endif
                        @if(session('user_update_role_fail'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <span class="alert-text text-white">
                                {{ session('user_update_role_fail') }}
                            </span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </button>
                        </div>
                        @endif
                        @if(session('user_delete_success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <span class="alert-text text-white">
                                {{ session('user_delete_success') }}
                            </span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </button>
                        </div>
                        @endif
                        @if(session('user_delete_fail'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <span class="alert-text text-white">
                                {{ session('user_delete_fail') }}
                            </span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </button>
                        </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            ID
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Photo
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Name
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Email
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Phone
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Location
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Role
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            About Me
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $data)
                                    <tr>
                                        <td class="text-center align-middle">
                                            <p class="text-xs font-weight-bold mb-0">{{ $data->id }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div style="width: 50px; height: 50px;">
                                                @if($data->image)
                                                <img src="{{ asset('storage/image/' . $data->image) }}" alt="Profile Image"
                                                    class="border-radius-lg shadow-sm img-small"
                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                                @else
                                                <img src="{{ asset('assets/img/user.jpg') }}" alt="Default Image"
                                                    class="border-radius-lg shadow-sm img-small"
                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs font-weight-bold mb-0">{{ $data->name }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs font-weight-bold mb-0">{{ $data->email }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs font-weight-bold mb-0">{{ $data->phone }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs font-weight-bold mb-0">{{ $data->location }}</p>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('user-management.update', $data->id) }}" method="POST">
                                                @csrf
                                                <div class="input-group input-group-sm">
                                                    <select class="form-select form-select-sm" name="role"
                                                        onchange="this.form.submit()">
                                                        @foreach($roles as $role)
                                                        <option value="{{ $role }}"
                                                            {{ $data->role == $role ? 'selected' : '' }}>
                                                            {{ ucfirst($role) }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </form>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs font-weight-bold mb-0">{{ $data->about_me }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <form id="form-delete-{{ $data->id }}"
                                                    action="{{ route('user-management.delete', $data->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="mx-2 my-1 btn-delete"
                                                        style="background-color: transparent; border: none; padding: 0;"
                                                        data-id="{{ $data->id }}" data-bs-toggle="tooltip"
                                                        data-bs-original-title="Hapus Data">
                                                        <img src="{{ asset('assets/img/remove-icon.png') }}" alt="Hapus" style="width: 16px; height: 16px; cursor: pointer;">
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div>
    <p>Anda tidak memiliki akses ke halaman ini.</p>
</div>
@endif

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const confirmDelete = confirm(
                    'Apakah Anda yakin ingin menghapus data ini?');

                if (confirmDelete) {
                    const formId = this.getAttribute('data-id');
                    const form = document.getElementById('form-delete-' + formId);

                    if (form) {
                        form.submit();
                    }
                }
            });
        });

        const showFormBtn = document.getElementById('show-add-user-form');
        const cancelFormBtn = document.getElementById('cancel-add-user-form');
        const addUserFormSection = document.getElementById('add-user-form-section');
        const allUsersSection = document.getElementById('all-users-section');

        // Sembunyikan form Add User jika ada notifikasi dari operasi Store
        // Ini memastikan form tetap tersembunyi setelah redirect jika ada error validasi
        const isStoreFail = @json(session('user_store_fail') ? true : false);
        if(isStoreFail) {
            addUserFormSection.style.display = 'block';
            allUsersSection.style.display = 'none';
        }

        showFormBtn.addEventListener('click', function() {
            addUserFormSection.style.display = 'block';
            allUsersSection.style.display = 'none'; // Sembunyikan tabel saat form muncul
        });

        cancelFormBtn.addEventListener('click', function() {
            addUserFormSection.style.display = 'none';
            allUsersSection.style.display = 'block'; // Tampilkan kembali tabel
        });
    });
</script>
@endsection