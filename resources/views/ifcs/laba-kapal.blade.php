@extends('layouts.user_type.auth')

@section('content')

<style>
    /* CSS untuk membuat kolom 'Action' tetap */
    .sticky-action {
        position: sticky;
        right: 0;
        background-color: #f8f9fa; /* Sesuaikan dengan warna latar belakang tabel Anda */
        z-index: 10; /* Pastikan kolom ini di atas konten lain saat menggulir */
        border-left: 1px solid #dee2e6; /* Border untuk memisahkan dari kolom sebelumnya */
    }

    /* Pastikan header tabel memiliki latar belakang yang sama untuk kolom sticky */
    .table thead th {
        background-color: #f8f9fa; /* Sesuaikan dengan warna latar belakang header tabel Anda */
    }

    /* Pastikan sel body tabel memiliki latar belakang yang sama untuk kolom sticky */
    .table tbody td {
        background-color: #f8f9fa; /* Sesuaikan dengan warna latar belakang body tabel Anda */
    }
</style>

<div>
    <div>
        <div class="col-12">
            {{-- Bagian Form Edit (Awalnya Tersembunyi) --}}
            <div id="edit-form-section" class="card mb-4 mx-4" style="display: none;">
                <div class="card-header pb-0">
                    <div class="mb-4">
                        <h5 class="mb-0">Edit Data Laba Kapal</h5>
                    </div>
                </div>
                <div class="card-body pt-4 p-3">
                    {{-- ID form ditambahkan untuk memudahkan manipulasi JS --}}
                    <form id="edit-data-form" action="" method="POST" role="form text-left" enctype="multipart/form-data">
                        @csrf
                        @method('POST') {{-- Tetap POST, rute akan memprosesnya --}}

                        {{-- Menampilkan error validasi --}}
                        @if($errors->any())
                        <div class="mt-3 alert alert-primary alert-dismissible fade show" role="alert">
                            <span class="alert-text text-white">{{ $errors->first() }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </button>
                        </div>
                        @endif

                        {{-- Notifikasi update dipindahkan ke bagian tabel agar tidak tersembunyi --}}
                        
                        {{-- Input Form --}}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_kapal" class="form-control-label">{{ __('Kapal') }}</label>
                                    <div class="@error('kapal') border border-danger rounded-3 @enderror">
                                        <select class="form-control" id="edit_kapal" name="kapal">
                                            <option value="">Pilih Kapal</option>
                                            <option value="BATU MANDI">BATU MANDI</option>
                                            <option value="JATRA III">JATRA III</option>
                                            <option value="LEGUNDI">LEGUNDI</option>
                                            <option value="PORT LINK I">PORT LINK I</option>
                                            <option value="PORT LINK III">PORT LINK III</option>
                                            <option value="SEBUKU">SEBUKU</option>
                                            <option value="Total">Total</option>
                                        </select>
                                        @error('kapal')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_jenis" class="form-control-label">{{ __('Jenis') }}</label>
                                    <div class="@error('jenis') border border-danger rounded-3 @enderror">
                                        <select class="form-control" id="edit_jenis" name="jenis">
                                            <option value="">Pilih Jenis</option>
                                            <option value="pendapatan">pendapatan</option>
                                            <option value="labarugi">labarugi</option>
                                        </select>
                                        @error('jenis')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_januari" class="form-control-label">{{ __('Januari') }}</label>
                                    <div class="@error('januari') border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="number"
                                            placeholder="Januari" id="edit_januari" name="januari">
                                        @error('januari')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_februari" class="form-control-label">{{ __('Februari') }}</label>
                                    <div class="@error('februari') border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="number"
                                            placeholder="Februari" id="edit_februari" name="februari">
                                        @error('februari')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_maret" class="form-control-label">{{ __('Maret') }}</label>
                                    <div class="@error('maret') border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="number"
                                            placeholder="Maret" id="edit_maret" name="maret">
                                        @error('maret')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_april" class="form-control-label">{{ __('April') }}</label>
                                    <div class="@error('april') border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="number"
                                            placeholder="April" id="edit_april" name="april">
                                        @error('april')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_mei" class="form-control-label">{{ __('Mei') }}</label>
                                    <div class="@error('mei') border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="number"
                                            placeholder="Mei" id="edit_mei" name="mei">
                                        @error('mei')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_juni" class="form-control-label">{{ __('Juni') }}</label>
                                    <div class="@error('juni') border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="number"
                                            placeholder="Juni" id="edit_juni" name="juni">
                                        @error('juni')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_juli" class="form-control-label">{{ __('Juli') }}</label>
                                    <div class="@error('juli') border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="number"
                                            placeholder="Juli" id="edit_juli" name="juli">
                                        @error('juli')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_agustus" class="form-control-label">{{ __('Agustus') }}</label>
                                    <div class="@error('agustus') border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="number"
                                            placeholder="Agustus" id="edit_agustus" name="agustus">
                                        @error('agustus')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_september" class="form-control-label">{{ __('September') }}</label>
                                    <div class="@error('september') border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="number"
                                            placeholder="September" id="edit_september" name="september">
                                        @error('september')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_oktober" class="form-control-label">{{ __('Oktober') }}</label>
                                    <div class="@error('oktober') border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="number"
                                            placeholder="Oktober" id="edit_oktober" name="oktober">
                                        @error('oktober')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_november" class="form-control-label">{{ __('November') }}</label>
                                    <div class="@error('november') border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="number"
                                            placeholder="November" id="edit_november" name="november">
                                        @error('november')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_desember" class="form-control-label">{{ __('Desember') }}</label>
                                    <div class="@error('desember') border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="number"
                                            placeholder="Desember" id="edit_desember" name="desember">
                                        @error('desember')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="edit_tahun" class="form-control-label">{{ __('Tahun') }}</label>
                                    <div class="@error('tahun') border border-danger rounded-3 @enderror">
                                        <input class="form-control" type="number"
                                            placeholder="Tahun" id="edit_tahun" name="tahun">
                                        @error('tahun')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-danger btn-md mt-4 mb-4 me-2" onclick="hideEditForm()">
                                BATAL
                            </button>
                            <button type="submit" class="btn bg-warning btn-md mt-4 mb-4 me-2">
                                SIMPAN
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Bagian Tabel Data (Awalnya Terlihat) --}}
            <div id="data-table-section" class="card mb-4 mx-4">
                <div class="card-header">
                    <h5 class="mb-0">Data Laba Kapal</h5>
                </div>
                <div>
                    <div class="row mx-3">
                        <div class="col-sm-2">
                            <div class="form-group mb-2">
                                <label for="tahunDropdown">Pilih Tahun:</label>
                                {{-- Form untuk filter tahun, tambahkan ID --}}
                                <form id="year-filter-form" action="{{ route('laba-kapal.index') }}" method="GET">
                                    <div class="d-flex align-items-center">
                                        <select name="tahun" class="form-control" id="tahunDropdown">
                                            <option value="">Select All</option>
                                            @foreach($years as $year)
                                            <option value="{{ $year }}"
                                                {{ $selectedYear == $year ? 'selected' : '' }}>
                                                {{ $year }}</option>
                                            @endforeach
                                        </select>
                                        {{-- Hidden input to preserve selected tab --}}
                                        <input type="hidden" name="tab" id="hiddenSelectedTab" value="{{ $selectedTab }}">
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="csv-file">{{ 'Upload Data' }}</label>
                                        <form action="{{ route('laba-kapal.uploadcsv') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div
                                                class="@error('csv_file') border border-danger rounded-3 @enderror">
                                                <input class="form-control" type="file" id="csv-file"
                                                    name="csv_file" required>
                                                @error('csv_file')
                                                <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                                @enderror
                                            </div>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end justify-content-end">
                                    <button type="submit" class="btn btn-primary mt-3">Upload</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    {{-- NOTIFIKASI DISINI --}}
                    @if(session('laba_kapal_upload_success'))
                    <div class="m-3 alert alert-success alert-dismissible fade show" id="alert-upload-success"
                        role="alert">
                        <span class="alert-text text-white">
                            {{ session('laba_kapal_upload_success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                    @endif
                    @if(session('laba_kapal_upload_fail'))
                    <div class="m-3 alert alert-danger alert-dismissible fade show" id="alert-upload-danger" role="alert">
                        <span class="alert-text text-white">
                            {{ session('laba_kapal_upload_fail') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                    @endif

                    @if(session('laba_kapal_update_success'))
                    <div class="m-3 alert alert-success alert-dismissible fade show" id="alert-update-success"
                        role="alert">
                        <span class="alert-text text-white">
                            {{ session('laba_kapal_update_success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                    @endif
                    @if(session('laba_kapal_update_fail'))
                    <div class="m-3 alert alert-danger alert-dismissible fade show" id="alert-update-danger" role="alert">
                        <span class="alert-text text-white">
                            {{ session('laba_kapal_update_fail') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                    @endif
                    
                    @if(session('laba_kapal_delete_success'))
                    <div class="m-3 alert alert-success alert-dismissible fade show" id="alert-delete-success"
                        role="alert">
                        <span class="alert-text text-white">
                            {{ session('laba_kapal_delete_success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                    @endif
                    @if(session('laba_kapal_delete_fail'))
                    <div class="m-3 alert alert-danger alert-dismissible fade show" id="alert-delete-danger" role="alert">
                        <span class="alert-text text-white">
                            {{ session('laba_kapal_delete_fail') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                    @endif


                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ $selectedTab == 'Pendapatan' ? 'active' : '' }}" id="pendapatan-tab" data-bs-toggle="tab" href="#Pendapatan" role="tab" aria-controls="Pendapatan" aria-selected="{{ $selectedTab == 'Pendapatan' ? 'true' : 'false' }}"><strong>PENDAPATAN</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $selectedTab == 'LabaRugiReal' ? 'active' : '' }}" id="labarugi-tab" data-bs-toggle="tab" href="#LabaRugiReal" role="tab" aria-controls="LabaRugiReal" aria-selected="{{ $selectedTab == 'LabaRugiReal' ? 'true' : 'false' }}"><strong>LABA RUGI</strong></a>
                        </li>
                    </ul>

                    <div class="tab-content" style="overflow-x: auto;">
                        <div id="Pendapatan" class="tab-pane fade {{ $selectedTab == 'Pendapatan' ? 'show active' : '' }}" role="tabpanel" aria-labelledby="pendapatan-tab">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 sticky-action-header">
                                            Kapal
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Januari
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Februari
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Maret
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            April
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Mei
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Juni
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Juli
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Agustus
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            September
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Oktober
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            November
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Desember
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Total
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 sticky-action">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($laba_kapal as $data)
                                    @if($data->jenis === 'pendapatan')
                                    <tr
                                        style="font-size: 12px; {{ trim($data->kapal) === 'Total' ? 'background-color: yellow;' : '' }}">
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ $data->kapal }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->januari, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->februari, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->maret, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->april, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->mei, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->juni, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->juli, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->agustus, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->september, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->oktober, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->november, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->desember, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->total, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center sticky-action">
                                            <div class="d-flex justify-content-center align-items-center">
                                                {{-- Tombol Edit: Panggil fungsi JS dan kirim semua data --}}
                                                <a href="#" class="mx-2 my-1 edit-button"
                                                    data-id="{{ $data->id }}"
                                                    data-kapal="{{ $data->kapal }}"
                                                    data-jenis="{{ $data->jenis }}"
                                                    data-januari="{{ $data->januari }}"
                                                    data-februari="{{ $data->februari }}"
                                                    data-maret="{{ $data->maret }}"
                                                    data-april="{{ $data->april }}"
                                                    data-mei="{{ $data->mei }}"
                                                    data-juni="{{ $data->juni }}"
                                                    data-juli="{{ $data->juli }}"
                                                    data-agustus="{{ $data->agustus }}"
                                                    data-september="{{ $data->september }}"
                                                    data-oktober="{{ $data->oktober }}"
                                                    data-november="{{ $data->november }}"
                                                    data-desember="{{ $data->desember }}"
                                                    data-tahun="{{ $data->tahun }}"
                                                    data-bs-toggle="tooltip" data-bs-original-title="Edit data">
                                                    <img src="{{ asset('assets/img/edit-icon.png') }}" alt="Edit" style="width: 20px; height: 20px;">
                                                </a>

                                                {{-- Form Delete --}}
                                                <form id="form-delete-{{ $data->id }}"
                                                    action="{{ route('laba-kapal.delete', $data->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="mx-2 my-1 btn-delete"
                                                        style="background-color: transparent; border: none; padding: 0;"
                                                        data-id="{{ $data->id }}" data-bs-toggle="tooltip"
                                                        data-bs-original-title="Hapus Data">
                                                        <img src="{{ asset('assets/img/remove-icon.png') }}" alt="Hapus" style="width: 20px; height: 20px;">
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div id="LabaRugiReal" class="tab-pane fade {{ $selectedTab == 'LabaRugiReal' ? 'show active' : '' }}" role="tabpanel" aria-labelledby="labarugi-tab">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 sticky-action-header">
                                            Kapal
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Januari
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Februari
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Maret
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            April
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Mei
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Juni
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Juli
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Agustus
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            September
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Oktober
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            November
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Desember
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Total
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 sticky-action">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($laba_kapal as $data)
                                    @if($data->jenis === 'labarugi')
                                    <tr
                                        style="font-size: 12px; {{ trim($data->kapal) === 'Total' ? 'background-color: yellow;' : '' }}">
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ $data->kapal }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->januari, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->februari, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->maret, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->april, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->mei, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->juni, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->juli, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->agustus, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->september, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->oktober, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->november, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->desember, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->kapal) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ 'Rp ' . number_format($data->total, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center sticky-action">
                                            <div class="d-flex justify-content-center align-items-center">
                                                {{-- Tombol Edit: Panggil fungsi JS dan kirim semua data --}}
                                                <a href="#" class="mx-2 my-1 edit-button"
                                                    data-id="{{ $data->id }}"
                                                    data-kapal="{{ $data->kapal }}"
                                                    data-jenis="{{ $data->jenis }}"
                                                    data-januari="{{ $data->januari }}"
                                                    data-februari="{{ $data->februari }}"
                                                    data-maret="{{ $data->maret }}"
                                                    data-april="{{ $data->april }}"
                                                    data-mei="{{ $data->mei }}"
                                                    data-juni="{{ $data->juni }}"
                                                    data-juli="{{ $data->juli }}"
                                                    data-agustus="{{ $data->agustus }}"
                                                    data-september="{{ $data->september }}"
                                                    data-oktober="{{ $data->oktober }}"
                                                    data-november="{{ $data->november }}"
                                                    data-desember="{{ $data->desember }}"
                                                    data-tahun="{{ $data->tahun }}"
                                                    data-bs-toggle="tooltip" data-bs-original-title="Edit data">
                                                    <img src="{{ asset('assets/img/edit-icon.png') }}" alt="Edit" style="width: 20px; height: 20px;">
                                                </a>
                                                <form id="form-delete-{{ $data->id }}"
                                                    action="{{ route('laba-kapal.delete', $data->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="mx-2 my-1 btn-delete"
                                                        style="background-color: transparent; border: none; padding: 0;"
                                                        data-id="{{ $data->id }}" data-bs-toggle="tooltip"
                                                        data-bs-original-title="Hapus Data">
                                                        <img src="{{ asset('assets/img/remove-icon.png') }}" alt="Hapus" style="width: 20px; height: 20px;">
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
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

{{-- jQuery, Popper.js, dan Bootstrap JS (Pastikan sudah terintegrasi di layout utama Anda) --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
{{-- Bootstrap 5 JS Bundle (Pastikan ini dimuat untuk data-bs-toggle) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
    // Fungsi untuk menampilkan form edit dan mengisi datanya
    function showEditForm(element) {
        const data = element.dataset; // Mengambil semua data-attribute

        // Mengisi form fields dengan data dari baris tabel
        document.getElementById('edit_kapal').value = data.kapal;
        document.getElementById('edit_jenis').value = data.jenis;
        document.getElementById('edit_januari').value = data.januari;
        document.getElementById('edit_februari').value = data.februari;
        document.getElementById('edit_maret').value = data.maret;
        document.getElementById('edit_april').value = data.april;
        document.getElementById('edit_mei').value = data.mei;
        document.getElementById('edit_juni').value = data.juni;
        document.getElementById('edit_juli').value = data.juli;
        document.getElementById('edit_agustus').value = data.agustus;
        document.getElementById('edit_september').value = data.september;
        document.getElementById('edit_oktober').value = data.oktober;
        document.getElementById('edit_november').value = data.november;
        document.getElementById('edit_desember').value = data.desember;
        document.getElementById('edit_tahun').value = data.tahun;

        // Mengupdate action URL form edit
        const form = document.getElementById('edit-data-form');
        form.action = `/laba-kapal/${data.id}/update`; // Sesuaikan dengan rute updatePost Anda

        // Menyembunyikan bagian tabel dan menampilkan bagian form edit
        document.getElementById('data-table-section').style.display = 'none';
        document.getElementById('edit-form-section').style.display = 'block';

        // Menggulir ke atas halaman untuk fokus pada form edit
        window.scrollTo(0, 0);
    }

    // Fungsi untuk menyembunyikan form edit dan menampilkan bagian tabel
    function hideEditForm() {
        document.getElementById('edit-form-section').style.display = 'none';
        document.getElementById('data-table-section').style.display = 'block';
    }
    
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi Tooltip Bootstrap 5
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        const editButtons = document.querySelectorAll('.edit-button');
        editButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                showEditForm(this);
            });
        });

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

        // Handle year dropdown change to submit the form
        document.getElementById('tahunDropdown').addEventListener('change', function() {
            document.getElementById('year-filter-form').submit();
        });

        // Handle tab clicks to update hidden input and submit form
        document.querySelectorAll('#myTab .nav-link').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function (event) {
                const activeTabId = event.target.getAttribute('href').replace('#', '');
                document.getElementById('hiddenSelectedTab').value = activeTabId;
            });
        });
    });
</script>

@endsection