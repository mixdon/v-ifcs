@extends('layouts.user_type.auth')

@section('content')

<div>
    <div>
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header">
                    <h5 class="mb-0">Data Komposisi Segmen</h5>
                </div>
                <div class="card-body">
                    <div class="col-sm-1">
                        <div class="form-group mb-2">
                            <label for="tahunDropdown">Pilih Tahun:</label>
                            <form id="yearForm" action="{{ route('komposisi.index') }}" method="GET">
                                <div class="d-flex align-items-center">
                                    <select name="tahun" class="form-control" id="tahunDropdown" onchange="this.form.submit()">
                                        <option value="">Select All</option>
                                        @foreach($years as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                            {{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Tombol untuk memicu perhitungan data --}}
                    <div class="mb-3">
                        {{-- Default tahun jika tidak ada yang dipilih adalah tahun saat ini --}}
                        <a href="{{ route('komposisi.calculate', ['tahun' => $selectedYear ?? date('Y')]) }}"
                           class="btn btn-info" id="calculateButton">
                            Trigger Perhitungan Data
                        </a>
                    </div>
                    
                    {{-- Tampilkan notifikasi jika ada (sukses atau error) --}}
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <ul class="nav nav-tabs" id="tabMenu">
                        <li class="nav-item">
                            <a class="nav-link active" href="#merak"
                                onclick="activateTab(this, event)"><strong>MERAK</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#bakauheni"
                                onclick="activateTab(this, event)"><strong>BAKAUHENI</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#gabungan"
                                onclick="activateTab(this, event)"><strong>GABUNGAN</strong></a>
                        </li>
                    </ul>

                    <div class="tab-content" style="overflow-x: auto;">
                        <div id="merak" class="tab-pane fade show active">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Golongan
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            IFCS REDEEM
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            NON IFCS
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($komposisi_segmen as $data)
                                    @if($data->jenis === 'merak')
                                    <tr
                                        style="font-size: 12px; {{ trim($data->golongan) === 'Total' ? 'background-color: yellow;' : '' }}">
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ $data->golongan }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->ifcs_redeem, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->nonifcs, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->total, 0, ',', '.') }}</p>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div id="bakauheni" class="tab-pane fade">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Golongan
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            IFCS REDEEM
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            NON IFCS
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($komposisi_segmen as $data)
                                    @if($data->jenis === 'bakauheni')
                                    <tr
                                        style="font-size: 12px; {{ trim($data->golongan) === 'Total' ? 'background-color: yellow;' : '' }}">
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ $data->golongan }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->ifcs_redeem, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->nonifcs, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->total, 0, ',', '.') }}</p>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div id="gabungan" class="tab-pane fade">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Golongan
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            IFCS REDEEM
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            NON IFCS
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($komposisi_segmen as $data)
                                    @if($data->jenis === 'gabungan')
                                    <tr
                                        style="font-size: 12px; {{ trim($data->golongan) === 'Total' ? 'background-color: yellow;' : '' }}">
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ $data->golongan }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->ifcs_redeem, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->nonifcs, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->total, 0, ',', '.') }}</p>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            var selectedYear = "{{ request()->get('tahun') }}";
                            var tahunDropdown = document.getElementById("tahunDropdown");
                            for (var i = 0; i < tahunDropdown.options.length; i++) {
                                if (tahunDropdown.options[i].value == selectedYear) {
                                    tahunDropdown.selectedIndex = i;
                                    break;
                                }
                            }
                            
                            // Memastikan href tombol sudah benar saat halaman pertama kali dimuat
                            const initialSelectedYear = "{{ $selectedYear }}";
                            const calculateButton = document.getElementById('calculateButton');
                            if (initialSelectedYear) {
                                calculateButton.href = "{{ url('komposisi-segmen/calculate') }}/" + initialSelectedYear;
                                calculateButton.style.display = ''; // Tampilkan tombol
                            } else {
                                // Sembunyikan tombol jika 'Select All' dipilih (tidak ada tahun spesifik)
                                calculateButton.style.display = 'none'; 
                            }

                            // Memperbarui href tombol saat dropdown tahun berubah
                            document.getElementById('tahunDropdown').addEventListener('change', function() {
                                var selectedYear = this.value;
                                if (selectedYear) {
                                    calculateButton.href = "{{ url('komposisi-segmen/calculate') }}/" + selectedYear;
                                    calculateButton.style.display = ''; // Tampilkan tombol
                                } else {
                                    calculateButton.style.display = 'none'; // Sembunyikan tombol
                                }
                            });
                        });

                        function activateTab(clickedElement, event) {
                            event.preventDefault();

                            var tabGroupId = clickedElement.closest('.nav-tabs').id;

                            document.querySelectorAll('#' + tabGroupId + ' .nav-link').forEach(function (element) {
                                element.classList.remove('active');
                            });

                            clickedElement.classList.add('active');

                            var targetTabId = clickedElement.getAttribute('href');

                            document.querySelectorAll('.tab-content .tab-pane').forEach(function (tab) {
                                tab.classList.remove('show', 'active');
                            });

                            document.querySelector(targetTabId).classList.add('show', 'active');
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection