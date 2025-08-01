@extends('layouts.user_type.auth')

@section('content')

<div>
    <div>
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header">
                    <h5 class="mb-0">Data Market Lintasan</h5>
                </div>
                <div class="card-body">
                    <div class="col-sm-1">
                        <div class="form-group mb-2">
                            <label for="tahunDropdown">Pilih Tahun:</label>
                            <form action="{{ route('market-lintasan.index') }}" method="GET">
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
                            });

                        </script>
                    </div>
                    <ul class="nav nav-tabs" id="tabMenu1">
                        <li class="nav-item">
                            <a class="nav-link active" href="#IFCS"
                                onclick="activateTab(this, event, 'tab1')"><strong>IFCS</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#INDUSTRI"
                                onclick="activateTab(this, event, 'tab1')"><strong>INDUSTRI</strong></a>
                        </li>
                    </ul>

                    <div class="tab-content" style="overflow-x: auto;">
                        <div id="IFCS" class="tab-pane fade show active">
                            <!-- Tabel IFCS -->
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Golongan
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Merak
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Bakauheni
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Gabungan
                                        </th>
                                        <!-- <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Action
                                        </th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($market_lintasan as $data)
                                    @if($data->jenis === 'ifcs')
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
                                                {{ number_format($data->merak, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->bakauheni, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->gabungan, 0, ',', '.') }}</p>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div id="INDUSTRI" class="tab-pane fade">
                            <!-- Tabel INDUSTRI -->
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Golongan
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Merak
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Bakauheni
                                        </th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Gabungan
                                        </th>
                                        <!-- <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Action
                                        </th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($market_lintasan as $data)
                                    @if($data->jenis === 'industri')
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
                                                {{ number_format($data->merak, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->bakauheni, 0, ',', '.') }}</p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <p class="text-xs mb-0"
                                                style="{{ trim($data->golongan) === 'Total' ? 'font-weight: bold;' : '' }}">
                                                {{ number_format($data->gabungan, 0, ',', '.') }}</p>
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
                            if (activeTab1) {
                                var activeElement1 = document.querySelector('[href="' + activeTab1 + '"]');
                                if (activeElement1) {
                                    activateTab(activeElement1, new Event('click'), 'tab1');
                                }
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

                            document.querySelectorAll('.tab-content').forEach(function (content) {
                                if (content.previousElementSibling && content.previousElementSibling.id ===
                                    tabGroupId) {
                                    content.querySelectorAll('.tab-pane').forEach(function (tab) {
                                        tab.classList.remove('show', 'active');
                                    });
                                }
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

<!-- Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.btn-delete');

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const confirmDelete = confirm('Apakah Anda yakin ingin menghapus data ini?');

                if (confirmDelete) {
                    const formId = this.getAttribute('data-id');
                    const form = document.getElementById('form-delete-' + formId);

                    if (form) {
                        form.submit();
                    }
                }
            });
        });
    });

</script>
@endsection
