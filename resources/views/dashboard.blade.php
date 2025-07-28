@extends('layouts.user_type.auth')

@section('content')

<div class="row">
  {{-- Kolom 3-col untuk Filter Tahun --}}
  <div class="col-lg-3 mt-4">
      <div class="card">
          <div class="card-header pb-0 p-3">
              <div class="d-flex justify-content-between align-items-center">
                  <h6 class="mb-0">Filter Data</h6>
              </div>
          </div>
          <div class="card-body p-3">
              <form action="{{ route('dashboard') }}" method="GET">
                  <div class="d-flex align-items-center">
                      <label for="tahun" class="mb-0 me-2 text-sm">Pilih Tahun:</label>
                      <select name="tahun" id="tahun" class="form-control form-control-sm" onchange="this.form.submit()">
                          @foreach($validYears as $year)
                              <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                          @endforeach
                      </select>
                  </div>
                  {{-- Input hidden untuk mempertahankan mode tampilan pendapatan --}}
                  <input type="hidden" name="revenue_view_mode" id="revenue_view_mode_hidden" value="{{ $revenueViewMode }}">
                  {{-- Input hidden untuk mempertahankan mode tampilan produksi --}}
                  <input type="hidden" name="production_view_mode" id="production_view_mode_hidden" value="{{ $productionViewMode }}">
              </form>
          </div>
      </div>
  </div>

  {{-- Kolom 6-col kosong di tengah --}}
  <div class="col-lg-6 mt-4">
      </div>

  {{-- Kolom 3-col untuk Tombol Jalankan ETL --}}
  <div class="col-lg-3 mt-4">
</div>

  {{-- Area untuk Notifikasi (Opsional, tapi sangat disarankan) --}}
  @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
          <span class="alert-icon"><i class="fa fa-check"></i></span>
          <span class="alert-text">{{ session('success') }}</span>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
  @endif
  @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show text-white" role="alert">
          <span class="alert-icon"><i class="fa fa-exclamation-triangle"></i></span>
          <span class="alert-text">{{ session('error') }}</span>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
  @endif
  
  {{-- BARIS KARTU RINGKASAN --}}
  <div class="row mt-4">
    {{-- Card 1: Total Pendapatan IFCS (Semua Tahun) - Kiri Atas --}}
    <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Pendapatan IFCS (Semua Tahun)</p>
                <h5 class="font-weight-bolder mb-0">
                  Rp {{ number_format($totalRevenueIfcsAllYears, 0, ',', '.') }}
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Card 3: Total Produksi IFCS (Semua Tahun) - Kanan Atas --}}
    <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Produksi IFCS (Semua Tahun)</p>
                <h5 class="font-weight-bolder mb-0">
                  {{ number_format($totalProductionIfcsAllYears, 0, ',', '.') }} Unit
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    {{-- Card 2: Total Pendapatan IFCS (Tahun Saat Ini) - Kiri Bawah --}}
    <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Pendapatan IFCS (Tahun {{ $selectedYear }})</p>
                <h5 class="font-weight-bolder mb-0">
                  Rp {{ number_format($totalRevenueIfcsCurrentYear, 0, ',', '.') }}
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Card 4: Total Produksi IFCS (Tahun Saat Ini) - Kanan Bawah --}}
    <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Produksi IFCS (Tahun {{ $selectedYear }})</p>
                <h5 class="font-weight-bolder mb-0">
                  {{ number_format($totalProductionIfcsCurrentYear, 0, ',', '.') }} Unit
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    {{-- Chart Total Pendapatan Layanan IFCS (Per Tahun / Per Bulan) --}}
    <div class="col-lg-6 mb-lg-0 mb-4">
      <div class="card z-index-2">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
          <h6>Total Pendapatan Layanan IFCS</h6>
          <form action="{{ route('dashboard') }}" method="GET" class="d-flex align-items-center">
            <label for="revenue_view_mode" class="mb-0 me-2 text-sm">Tampilan:</label>
            <select name="revenue_view_mode" id="revenue_view_mode" class="form-control form-control-sm" onchange="this.form.submit()">
                <option value="yearly_summary" {{ $revenueViewMode == 'yearly_summary' ? 'selected' : '' }}>Per Tahun Saja</option>
                <option value="monthly_detail" {{ $revenueViewMode == 'monthly_detail' ? 'selected' : '' }}>Per Bulan (Tahun {{ $selectedYear }})</option>
            </select>
            {{-- Input hidden untuk mempertahankan tahun yang dipilih --}}
            <input type="hidden" name="tahun" id="tahun_hidden_revenue" value="{{ $selectedYear }}">
            {{-- Input hidden untuk mempertahankan mode tampilan produksi (jika ada) --}}
            <input type="hidden" name="production_view_mode" id="production_view_mode_hidden_revenue" value="{{ $productionViewMode }}">
          </form>
        </div>
        <div class="card-body p-3">
          <div class="chart">
            <canvas id="total-ifcs-revenue-chart" class="chart-canvas" height="300"></canvas>
          </div>
        </div>
      </div>
    </div>

    {{-- Chart Total Produksi Layanan IFCS (Per Tahun / Per Bulan) --}}
    <div class="col-lg-6">
      <div class="card z-index-2">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
          <h6>Total Produksi Layanan IFCS</h6>
          <form action="{{ route('dashboard') }}" method="GET" class="d-flex align-items-center">
            <label for="production_view_mode" class="mb-0 me-2 text-sm">Tampilan:</label>
            <select name="production_view_mode" id="production_view_mode" class="form-control form-control-sm" onchange="this.form.submit()">
                <option value="yearly_summary" {{ $productionViewMode == 'yearly_summary' ? 'selected' : '' }}>Per Tahun Saja</option>
                <option value="monthly_detail" {{ $productionViewMode == 'monthly_detail' ? 'selected' : '' }}>Per Bulan (Tahun {{ $selectedYear }})</option>
            </select>
            {{-- Input hidden untuk mempertahankan tahun yang dipilih --}}
            <input type="hidden" name="tahun" id="tahun_hidden_production" value="{{ $selectedYear }}">
            {{-- Input hidden untuk mempertahankan mode tampilan pendapatan (jika ada) --}}
            <input type="hidden" name="revenue_view_mode" id="revenue_view_mode_hidden_production" value="{{ $revenueViewMode }}">
          </form>
        </div>
        <div class="card-body p-3">
          <div class="chart">
            <canvas id="total-ifcs-production-chart" class="chart-canvas" height="300"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    {{-- Chart Market Lintasan (IFCS vs Industri) --}}
    <div class="col-lg-6 mb-lg-0 mb-4">
      <div class="card z-index-2">
        <div class="card-header pb-0">
          <h6>Market Lintasan (IFCS vs Industri)</h6>
          <p class="text-sm">
            Persentase produksi IFCS dibandingkan dengan industri penyeberangan dari tahun ke tahun untuk menggambarkan tren produktivitas.
          </p>
        </div>
        <div class="card-body p-3">
          <div class="chart d-flex justify-content-center">
            <canvas id="market-lintasan-chart" class="chart-canvas" height="300"></canvas>
          </div>
        </div>
      </div>
    </div>

    {{-- Chart Komposisi Produksi Tahunan (IFCS, NonIFCS, Reguler) --}}
    <div class="col-lg-6">
      <div class="card z-index-2">
        <div class="card-header pb-0">
          <h6>Komposisi Segmen Tahunan</h6>
          <p class="text-sm">
            Persentase kontribusi masing-masing jenis layanan terhadap total produksi serta distribusi pendapatan IFCS sepanjang tahun-tahun berjalan.         
          </p>
        </div>
        <div class="card-body p-3">
          <div class="chart d-flex justify-content-center">
            <canvas id="annual-production-composition-chart" class="chart-canvas" height="300"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>


{{-- Pastikan Chart.js dimuat sebelum skrip kustom --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@endsection

@push('dashboard')
<script>
    window.onload = function () {
        // Data yang diteruskan dari controller
        var selectedYear = {{ $selectedYear }};
        var revenueViewMode = "{{ $revenueViewMode }}"; // Ambil mode tampilan pendapatan dari controller
        var productionViewMode = "{{ $productionViewMode }}"; // Ambil mode tampilan produksi dari controller

        var ifcsRevenueChartData = @json($ifcsRevenueChartData); // Data pendapatan tahunan
        var ifcsMonthlyRevenueChartData = @json($ifcsMonthlyRevenueChartData); // Data pendapatan bulanan
        var ifcsMonthlyRevenueChartLabels = @json($ifcsMonthlyRevenueChartLabels); // Label bulan pendapatan

        var ifcsProductionChartData = @json($ifcsProductionChartData); // Data produksi tahunan
        var ifcsMonthlyProductionChartData = @json($ifcsMonthlyProductionChartData); // Data produksi bulanan
        var ifcsMonthlyProductionChartLabels = @json($ifcsMonthlyProductionChartLabels); // Label bulan produksi


        // --- Chart 1: Total Pendapatan Layanan IFCS (Per Tahun / Per Bulan) ---
        var labelsRevenue, dataRevenue;
        var chartTypeRevenue = "bar"; // Default type

        if (revenueViewMode === 'monthly_detail') {
            labelsRevenue = ifcsMonthlyRevenueChartLabels;
            dataRevenue = ifcsMonthlyRevenueChartData;
            // Jika data bulanan tidak ada, tampilkan pesan
            if (dataRevenue.length === 0) {
                document.getElementById("total-ifcs-revenue-chart").parentNode.innerHTML = "<p class='text-center text-muted mt-5'>Data pendapatan IFCS bulanan untuk tahun " + selectedYear + " tidak tersedia.</p>";
            }
        } else { // yearly_summary
            labelsRevenue = Object.keys(ifcsRevenueChartData);
            dataRevenue = Object.values(ifcsRevenueChartData);
            if (dataRevenue.length === 0) {
                 document.getElementById("total-ifcs-revenue-chart").parentNode.innerHTML = "<p class='text-center text-muted mt-5'>Data pendapatan IFCS tahunan tidak tersedia.</p>";
            }
        }

        // Hanya inisialisasi chart jika ada data
        if (dataRevenue.length > 0) {
            var ctxRevenue = document.getElementById("total-ifcs-revenue-chart").getContext("2d");
            new Chart(ctxRevenue, {
                type: chartTypeRevenue,
                data: {
                    labels: labelsRevenue,
                    datasets: [{
                        label: "Total Pendapatan IFCS",
                        tension: 0.4,
                        borderWidth: 0,
                        borderRadius: 4,
                        backgroundColor: 'rgb(75, 192, 192)',
                        data: dataRevenue,
                        maxBarThickness: 30,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                // PERBAIKAN: Title callback kondisional
                                title: function(context) {
                                    if (revenueViewMode === 'monthly_detail') {
                                        return labelsRevenue[context[0].dataIndex] + ' ' + selectedYear;
                                    } else {
                                        return labelsRevenue[context[0].dataIndex];
                                    }
                                },
                                label: function (context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'Rp ' + context.parsed.y.toLocaleString('id-ID'); // Format mata uang Indonesia
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                display: true,
                                drawOnChartArea: true,
                                drawTicks: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                display: true,
                                padding: 10,
                                color: '#b2b9bf',
                                font: { size: 11, family: "Open Sans", style: 'normal', lineHeight: 2 },
                                callback: function (value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        x: {
                            grid: {
                                drawBorder: false,
                                display: false,
                                drawOnChartArea: false,
                                drawTicks: false,
                            },
                            ticks: {
                                display: true,
                                color: '#b2b9bf',
                                padding: 20,
                                font: { size: 11, family: "Open Sans", style: 'normal', lineHeight: 2 },
                            }
                        },
                    },
                },
            });
        }


        // --- Chart 2: Total Produksi Layanan IFCS (Per Tahun / Per Bulan) ---
        var labelsProduction, dataProduction;
        var chartTypeProduction = "line"; // Default type

        if (productionViewMode === 'monthly_detail') {
            labelsProduction = ifcsMonthlyProductionChartLabels;
            dataProduction = ifcsMonthlyProductionChartData;
             if (dataProduction.length === 0) {
                document.getElementById("total-ifcs-production-chart").parentNode.innerHTML = "<p class='text-center text-muted mt-5'>Data produksi IFCS bulanan untuk tahun " + selectedYear + " tidak tersedia.</p>";
            }
        } else { // yearly_summary
            labelsProduction = Object.keys(ifcsProductionChartData);
            dataProduction = Object.values(ifcsProductionChartData);
            if (dataProduction.length === 0) {
                 document.getElementById("total-ifcs-production-chart").parentNode.innerHTML = "<p class='text-center text-muted mt-5'>Data produksi IFCS tahunan tidak tersedia.</p>";
            }
        }

        // Hanya inisialisasi chart jika ada data
        if (dataProduction.length > 0) {
            var ctxProduction = document.getElementById("total-ifcs-production-chart").getContext("2d");
            new Chart(ctxProduction, {
                type: chartTypeProduction,
                data: {
                    labels: labelsProduction,
                    datasets: [{
                        label: "Total Produksi IFCS",
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 3, // PERBAIKAN: Menampilkan titik
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true,
                        data: dataProduction,
                        maxBarThickness: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                // PERBAIKAN: Title callback kondisional
                                title: function(context) {
                                    if (productionViewMode === 'monthly_detail') {
                                        return labelsProduction[context[0].dataIndex] + ' ' + selectedYear;
                                    } else {
                                        return labelsProduction[context[0].dataIndex];
                                    }
                                },
                                label: function (context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y.toLocaleString('id-ID') + ' Unit';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                display: true,
                                drawOnChartArea: true,
                                drawTicks: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                display: true,
                                padding: 10,
                                color: '#b2b9bf',
                                font: { size: 11, family: "Open Sans", style: 'normal', lineHeight: 2 },
                                callback: function (value) {
                                    return value.toLocaleString('id-ID');
                                }
                            }
                        },
                        x: {
                            grid: {
                                drawBorder: false,
                                display: false,
                                drawOnChartArea: false,
                                drawTicks: false,
                            },
                            ticks: {
                                display: true,
                                color: '#b2b9bf',
                                padding: 20,
                                font: { size: 11, family: "Open Sans", style: 'normal', lineHeight: 2 },
                            }
                        },
                    },
                },
            });
        }


        // --- Chart 3: Komposisi Produksi Tahunan (IFCS, NonIFCS, Reguler) - Stacked Bar Chart ---
        var annualProductionCompositionData = @json($annualProductionCompositionData);
        var yearsComposition = annualProductionCompositionData.map(item => item.tahun);

        // Buat datasets untuk Stacked Bar Chart
        var ifcsDataComposition = annualProductionCompositionData.map(item => item.ifcs_percentage); // Menggunakan PERSENTASE
        var nonifcsDataComposition = annualProductionCompositionData.map(item => item.nonifcs_percentage); // Menggunakan PERSENTASE

        var ctxComposition = document.getElementById("annual-production-composition-chart").getContext("2d");
        new Chart(ctxComposition, {
            type: 'bar', // Mengubah ke bar chart
            data: {
                labels: yearsComposition,
                datasets: [{
                    label: 'IFCS',
                    data: ifcsDataComposition,
                    backgroundColor: 'rgb(255, 99, 132)', // Merah untuk IFCS
                    stack: 'ProductionComposition' // Penting untuk stacked bar chart
                }, {
                    label: 'NonIFCS',
                    data: nonifcsDataComposition,
                    backgroundColor: 'rgb(54, 162, 235)', // Biru untuk NonIFCS
                    stack: 'ProductionComposition' // Penting untuk stacked bar chart
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                            position: 'top',
                            labels: {
                                color: '#b2b9bf',
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            mode: 'index', // Tooltip akan menampilkan semua dataset pada titik yang sama
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    let value = context.parsed.y;
                                    let total = context.chart.data.datasets.map(dataset => dataset.data[context.dataIndex]).reduce((a, b) => a + b, 0);
                                    let percentage = (value / total * 100).toFixed(2);
                                    return label + value.toLocaleString('id-ID') + ' Unit (' + percentage + '%)';
                                }
                            }
                        }
                    },
                scales: {
                    x: {
                        stacked: true, // Membuat batang bertumpuk
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                        },
                        ticks: {
                            display: true,
                            color: '#b2b9bf',
                            padding: 20,
                            font: { size: 11, family: "Open Sans", style: 'normal', lineHeight: 2 },
                        }
                    },
                    y: {
                        stacked: true, // Membuat batang bertumpuk
                        beginAtZero: true,
                        min: 0,   // Set minimum ke 0%
                        max: 100, // Set maksimum ke 100%
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#b2b9bf',
                            font: { size: 11, family: "Open Sans", style: 'normal', lineHeight: 2 },
                            callback: function (value) {
                                return value.toFixed(0) + '%'; // Format sebagai persentase (tanpa desimal)
                            }
                        }
                    },
                },
            },
        });


        // --- Chart 4: Market Lintasan (IFCS vs Industri) - Stacked Bar Chart ---
        var marketLintasanData = @json($marketLintasanData);
        var yearsMarket = marketLintasanData.map(item => item.tahun);

        // Buat datasets untuk Stacked Bar Chart
        var ifcsMarketData = marketLintasanData.map(item => item.ifcs_percentage); // Menggunakan PERSENTASE
        var industriMarketData = marketLintasanData.map(item => item.industri_percentage); // Menggunakan PERSENTASE

        var ctxMarket = document.getElementById("market-lintasan-chart").getContext("2d");
        new Chart(ctxMarket, {
            type: 'bar', // Mengubah ke bar chart
            data: {
                labels: yearsMarket,
                datasets: [{
                    label: 'IFCS',
                    data: ifcsMarketData,
                    backgroundColor: 'rgb(153, 102, 255)', // Ungu untuk IFCS
                    stack: 'MarketComposition' // Penting untuk stacked bar chart
                }, {
                    label: 'Industri',
                    data: industriMarketData,
                    backgroundColor: 'rgb(201, 203, 207)',  // Abu-abu untuk Industri
                    stack: 'MarketComposition' // Penting untuk stacked bar chart
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#b2b9bf',
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                // Menggunakan data persentase langsung dari dataset
                                let value = context.parsed.y;
                                return label + value.toFixed(2) + '%'; // Menampilkan persentase di tooltip
                            }
                        }
                    },
                },
                scales: {
                    x: {
                        stacked: true, // Membuat batang bertumpuk
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                        },
                        ticks: {
                            display: true,
                            color: '#b2b9bf',
                            padding: 20,
                            font: { size: 11, family: "Open Sans", style: 'normal', lineHeight: 2 },
                        }
                    },
                    y: {
                        stacked: true, // Membuat batang bertumpuk
                        beginAtZero: true,
                        min: 0,   // Set minimum ke 0%
                        max: 100, // Set maksimum ke 100%
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#b2b9bf',
                            font: { size: 11, family: "Open Sans", style: 'normal', lineHeight: 2 },
                            callback: function (value) {
                                return value.toFixed(0) + '%'; // Format sebagai persentase (tanpa desimal)
                            }
                        }
                    },
                },
            },
        });

    } // End of window.onload
</script>
@endpush