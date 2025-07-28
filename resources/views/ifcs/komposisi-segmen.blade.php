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
                            <form action="{{ route('komposisi.index') }}" method="GET">
                                <div class="d-flex align-items-center">
                                    <select name="tahun" class="form-control" id="tahunDropdown"
                                        onchange="this.form.submit()">
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

                    <ul class="nav nav-tabs" id="tabMenu1">
                        <li class="nav-item">
                            <a class="nav-link active" href="#MERAK"
                                onclick="activateTab(this, event, 'tab1')"><strong>MERAK</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#BAKAUHENI"
                                onclick="activateTab(this, event, 'tab1')"><strong>BAKAUHENI</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#GABUNGAN"
                                onclick="activateTab(this, event, 'tab1')"><strong>GABUNGAN</strong></a>
                        </li>
                    </ul>

                    <div class="tab-content" style="overflow-x: auto;">
                        <div id="MERAK" class="tab-pane fade show active">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Golongan
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            IFCS + REDEEM
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            NON IFCS
                                        </th>
                                        <th
                                            class="text-center text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
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
                                                {{ $data->golongan }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->ifcs_redeem, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->nonifcs, 0, ',', '.') }}
                                            </p>
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
                        <div id="BAKAUHENI" class="tab-pane fade">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Golongan
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            IFCS + REDEEM
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            NON IFCS
                                        </th>
                                        <th
                                            class="text-center text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
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
                                                {{ $data->golongan }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->ifcs_redeem, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->nonifcs, 0, ',', '.') }}
                                            </p>
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
                        <div id="GABUNGAN" class="tab-pane fade">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Golongan
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            IFCS + REDEEM
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            NON IFCS
                                        </th>
                                        <th
                                            class="text-center text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
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
                                                {{ $data->golongan }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->ifcs_redeem, 0, ',', '.') }}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->nonifcs, 0, ',', '.') }}
                                            </p>
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
                        document.addEventListener('DOMContentLoaded', function () {
                            var activeTab1 = localStorage.getItem('activeTab1');
                            if (!activeTab1) {
                                activeTab1 = '#MERAK';
                                localStorage.setItem('activeTab1', activeTab1);
                            }
                            var activeElement1 = document.querySelector('[href="' + activeTab1 + '"]');
                            if (activeElement1) {
                                activateTab(activeElement1, new Event('click'), 'tab1');
                            }
                        });

                        function activateTab(clickedElement, event, tabGroup) {
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
                            localStorage.setItem('activeTab' + tabGroup.trim().charAt(tabGroup.trim().length - 1),
                                targetTabId);
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection