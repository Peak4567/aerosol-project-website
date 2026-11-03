@extends('layouts.dashboard', ['pageHeader' => 'แผงหน้าปัด', 'title' => 'แดชบอร์ด'])

@section('content')
<div>
    <!-- Dashboard Header -->
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">แผงหน้าปัดควบคุม (Dashboard)</h1>
            <p class="text-sm text-gray-500">ภาพรวมสถานะเซ็นเซอร์และการเพาะปลูกแบบเรียลไทม์</p>
        </div>
        <div class="flex items-center gap-2">
            <span id="update-badge" class="inline-flex items-center gap-1.5 rounded-md bg-white border border-gray-200 px-3 py-1.5 text-xs text-gray-500 shadow-xs">
                <i class="fa-solid fa-arrows-rotate text-emerald-600 animate-spin" id="sync-spinner" style="display: none;"></i>
                <span id="last-update-text">กำลังโหลดข้อมูล...</span>
            </span>
            <button onclick="fetchAllSensorData()" class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700 shadow-xs transition">
                <i class="fa-solid fa-rotate-right mr-1"></i> รีเฟรช
            </button>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="space-y-6">
        <!-- Stat Cards Row -->
        <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4 xl:grid-cols-5">
            <!-- Temperature -->
            <div class="flex items-center justify-between rounded-xl bg-white p-5 shadow-sm border border-slate-100 interactive-card">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-400">Temperature</p>
                    <p class="mt-1 text-xl sm:text-2xl font-bold text-gray-800" id="card-temp">-- °C</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-xl text-amber-600">
                    <i class="fa-solid fa-temperature-half"></i>
                </div>
            </div>

            <!-- Humidity -->
            <div class="flex items-center justify-between rounded-xl bg-white p-5 shadow-sm border border-slate-100 interactive-card">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-400">Humidity</p>
                    <p class="mt-1 text-xl sm:text-2xl font-bold text-gray-800" id="card-humi">-- %RH</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-xl text-blue-600">
                    <i class="fa-solid fa-droplet"></i>
                </div>
            </div>

            <!-- TDS -->
            <div class="flex items-center justify-between rounded-xl bg-white p-5 shadow-sm border border-slate-100 interactive-card">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-400">TDS</p>
                    <p class="mt-1 text-xl sm:text-2xl font-bold text-gray-800" id="card-tds">-- ppm</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-teal-100 text-xl text-teal-600">
                    <i class="fa-solid fa-flask-vial"></i>
                </div>
            </div>

            <!-- EC -->
            <div class="flex items-center justify-between rounded-xl bg-white p-5 shadow-sm border border-slate-100 interactive-card">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-400">EC</p>
                    <p class="mt-1 text-xl sm:text-2xl font-bold text-gray-800" id="card-ec">-- µS/cm</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 text-xl text-purple-600">
                    <i class="fa-solid fa-bolt"></i>
                </div>
            </div>

            <!-- pH -->
            <div class="col-span-2 sm:col-span-2 lg:col-span-4 xl:col-span-1 flex items-center justify-between rounded-xl bg-white p-5 shadow-sm border border-slate-100 interactive-card">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-400">pH</p>
                    <p class="mt-1 text-xl sm:text-2xl font-bold text-gray-800" id="card-ph">--</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-xl text-red-600">
                    <i class="fa-solid fa-vial-circle-check"></i>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="card rounded-xl p-4 shadow-sm sm:p-6">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-lg font-semibold text-gray-800">กราฟภาพรวมเซ็นเซอร์ (Sensor Overview)</h2>
                <div class="text-xs text-gray-400">อัปเดตอัตโนมัติทุก 5 วินาที</div>
            </div>
            <div class="h-[340px] w-full">
                <canvas id="sensorOverviewChart"></canvas>
            </div>
        </div>

        <!-- History Table & Summary List Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Sensor History Table (2 cols) -->
            <div class="card rounded-xl p-4 shadow-sm sm:p-6 lg:col-span-2">
                <div class="mb-4 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                    <h2 class="text-lg font-semibold text-gray-800">ประวัติข้อมูลเซ็นเซอร์</h2>
                    
                    <!-- Filter buttons -->
                    <div class="flex flex-wrap gap-1.5" id="sensor-filter-buttons">
                        <button onclick="setHistoryFilter('sht40_temp')" id="btn-filter-sht40_temp" class="sensor-btn active">Temperature</button>
                        <button onclick="setHistoryFilter('sht40_humi')" id="btn-filter-sht40_humi" class="sensor-btn">Humidity</button>
                        <button onclick="setHistoryFilter('tds')" id="btn-filter-tds" class="sensor-btn">TDS</button>
                        <button onclick="setHistoryFilter('ec')" id="btn-filter-ec" class="sensor-btn">EC</button>
                        <button onclick="setHistoryFilter('ph')" id="btn-filter-ph" class="sensor-btn">pH</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs text-gray-400 border-b border-gray-100">
                            <tr>
                                <th class="w-16 px-3 py-3">#</th>
                                <th class="px-3 py-3">ค่า (<span id="table-dataset-label">Temperature</span>)</th>
                                <th class="px-3 py-3 text-right">เวลา (Timestamp)</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body" class="text-gray-600 divide-y divide-gray-100">
                            <tr>
                                <td colspan="3" class="py-10 text-center text-gray-400">กำลังโหลดข้อมูล...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3 text-xs text-gray-500">
                    <span id="pagination-info">แสดงหน้า 1 จาก 1</span>
                    <div class="flex gap-1" id="pagination-buttons">
                        <button onclick="changePage(-1)" id="btn-prev" class="pagination-btn" disabled>
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </button>
                        <button onclick="changePage(1)" id="btn-next" class="pagination-btn" disabled>
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary List (1 col) -->
            <div class="card rounded-xl p-4 shadow-sm sm:p-6 lg:col-span-1">
                <h2 class="mb-4 text-lg font-semibold text-gray-800">ข้อมูลเซนเซอร์ล่าสุด</h2>
                <ul class="space-y-4">
                    <li class="flex items-center justify-between border-b border-gray-50 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                                <i class="fa-solid fa-temperature-half"></i>
                            </div>
                            <span class="font-medium text-gray-700">Temperature</span>
                        </div>
                        <span class="font-bold text-gray-900" id="sum-temp">-- °C</span>
                    </li>

                    <li class="flex items-center justify-between border-b border-gray-50 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <i class="fa-solid fa-droplet"></i>
                            </div>
                            <span class="font-medium text-gray-700">Humidity</span>
                        </div>
                        <span class="font-bold text-gray-900" id="sum-humi">-- %RH</span>
                    </li>

                    <li class="flex items-center justify-between border-b border-gray-50 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-50 text-teal-600">
                                <i class="fa-solid fa-flask-vial"></i>
                            </div>
                            <span class="font-medium text-gray-700">TDS</span>
                        </div>
                        <span class="font-bold text-gray-900" id="sum-tds">-- ppm</span>
                    </li>

                    <li class="flex items-center justify-between border-b border-gray-50 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <span class="font-medium text-gray-700">EC</span>
                        </div>
                        <span class="font-bold text-gray-900" id="sum-ec">-- µS/cm</span>
                    </li>

                    <li class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-600">
                                <i class="fa-solid fa-vial-circle-check"></i>
                            </div>
                            <span class="font-medium text-gray-700">pH</span>
                        </div>
                        <span class="font-bold text-gray-900" id="sum-ph">--</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const SENSOR_CONFIG = {
        sht40_temp: { label: "Temperature", color: "#f59e0b", unit: "°C" },
        sht40_humi: { label: "Humidity", color: "#3b82f6", unit: "%RH" },
        tds: { label: "TDS", color: "#14b8a6", unit: "ppm" },
        ec: { label: "EC", color: "#8b5cf6", unit: "µS/cm" },
        ph: { label: "pH", color: "#ef4444", unit: "" }
    };

    let overviewChart = null;
    let currentFilter = 'sht40_temp';
    let historyRows = [];
    let currentPage = 1;
    const itemsPerPage = 6;

    // Initialize Chart
    function initChart() {
        const ctx = document.getElementById('sensorOverviewChart').getContext('2d');
        overviewChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    { label: 'Temperature (°C)', data: [], borderColor: '#f59e0b', backgroundColor: '#f59e0b1A', borderWidth: 2, tension: 0.4, pointRadius: 0, pointHoverRadius: 5 },
                    { label: 'Humidity (%RH)', data: [], borderColor: '#3b82f6', backgroundColor: '#3b82f61A', borderWidth: 2, tension: 0.4, pointRadius: 0, pointHoverRadius: 5 },
                    { label: 'TDS (ppm)', data: [], borderColor: '#14b8a6', backgroundColor: '#14b8a61A', borderWidth: 2, tension: 0.4, pointRadius: 0, pointHoverRadius: 5 },
                    { label: 'EC (µS/cm)', data: [], borderColor: '#8b5cf6', backgroundColor: '#8b5cf61A', borderWidth: 2, tension: 0.4, pointRadius: 0, pointHoverRadius: 5 },
                    { label: 'pH', data: [], borderColor: '#ef4444', backgroundColor: '#ef44441A', borderWidth: 2, tension: 0.4, pointRadius: 0, pointHoverRadius: 5 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                scales: {
                    x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10 } },
                    y: { beginAtZero: false }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    }

    // Fetch All Sensor Data
    async function fetchAllSensorData() {
        const spinner = document.getElementById('sync-spinner');
        if (spinner) spinner.style.display = 'inline-block';

        try {
            const res = await fetch('/api/sensors?type=all');
            const data = await res.json();

            if (data.labels && data.datasets) {
                // Update Chart
                if (overviewChart) {
                    overviewChart.data.labels = data.labels;
                    overviewChart.data.datasets[0].data = data.datasets.sht40_temp || [];
                    overviewChart.data.datasets[1].data = data.datasets.sht40_humi || [];
                    overviewChart.data.datasets[2].data = data.datasets.tds || [];
                    overviewChart.data.datasets[3].data = data.datasets.ec || [];
                    overviewChart.data.datasets[4].data = data.datasets.ph || [];
                    overviewChart.update('none');
                }

                // Update Stat Cards & Summary
                const lastVal = (arr) => arr && arr.length > 0 ? arr[arr.length - 1] : '--';
                
                const temp = lastVal(data.datasets.sht40_temp);
                const humi = lastVal(data.datasets.sht40_humi);
                const tds = lastVal(data.datasets.tds);
                const ec = lastVal(data.datasets.ec);
                const ph = lastVal(data.datasets.ph);

                document.getElementById('card-temp').textContent = temp + ' °C';
                document.getElementById('card-humi').textContent = humi + ' %RH';
                document.getElementById('card-tds').textContent = tds + ' ppm';
                document.getElementById('card-ec').textContent = ec + ' µS/cm';
                document.getElementById('card-ph').textContent = ph;

                document.getElementById('sum-temp').textContent = temp + ' °C';
                document.getElementById('sum-humi').textContent = humi + ' %RH';
                document.getElementById('sum-tds').textContent = tds + ' ppm';
                document.getElementById('sum-ec').textContent = ec + ' µS/cm';
                document.getElementById('sum-ph').textContent = ph;

                document.getElementById('last-update-text').textContent = 'ล่าสุด: ' + new Date().toLocaleTimeString();
            }
        } catch (e) {
            console.error('Fetch sensor data error:', e);
            document.getElementById('last-update-text').textContent = 'เชื่อมต่อล้มเหลว';
        } finally {
            if (spinner) spinner.style.display = 'none';
        }
    }

    // History Table logic
    function setHistoryFilter(key) {
        currentFilter = key;
        currentPage = 1;
        
        // Update button styles
        Object.keys(SENSOR_CONFIG).forEach(k => {
            const btn = document.getElementById('btn-filter-' + k);
            if (btn) {
                if (k === key) btn.classList.add('active');
                else btn.classList.remove('active');
            }
        });

        document.getElementById('table-dataset-label').textContent = SENSOR_CONFIG[key].label;
        fetchHistoryData();
    }

    async function fetchHistoryData() {
        try {
            const res = await fetch('/api/sensors?type=' + currentFilter);
            const data = await res.json();
            
            if (data.labels && data.data) {
                historyRows = data.data.map((value, idx) => ({
                    value: value,
                    label: data.labels[idx]
                })).reverse();

                renderHistoryTable();
            }
        } catch (e) {
            console.error('History fetch error:', e);
        }
    }

    function renderHistoryTable() {
        const tbody = document.getElementById('history-table-body');
        const totalPages = Math.max(1, Math.ceil(historyRows.length / itemsPerPage));
        if (currentPage > totalPages) currentPage = totalPages;

        const startIdx = (currentPage - 1) * itemsPerPage;
        const pageItems = historyRows.slice(startIdx, startIdx + itemsPerPage);

        if (pageItems.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="py-10 text-center text-gray-400">ไม่พบข้อมูล...</td></tr>';
        } else {
            tbody.innerHTML = pageItems.map((item, idx) => {
                const rowNum = historyRows.length - startIdx - idx;
                return `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-3 py-2.5 font-mono text-gray-400">${rowNum}</td>
                        <td class="px-3 py-2.5 font-semibold text-gray-800">${item.value}</td>
                        <td class="px-3 py-2.5 text-right font-mono text-xs text-gray-500">${item.label}</td>
                    </tr>
                `;
            }).join('');
        }

        document.getElementById('pagination-info').textContent = `แสดงหน้า ${currentPage} จาก ${totalPages} (ทั้งหมด ${historyRows.length} รายการ)`;
        document.getElementById('btn-prev').disabled = currentPage <= 1;
        document.getElementById('btn-next').disabled = currentPage >= totalPages;
    }

    function changePage(direction) {
        currentPage += direction;
        renderHistoryTable();
    }

    // On Load
    document.addEventListener('DOMContentLoaded', () => {
        initChart();
        fetchAllSensorData();
        fetchHistoryData();

        // 5-second polling interval
        setInterval(fetchAllSensorData, 5000);
        setInterval(fetchHistoryData, 5000);
    });
</script>
@endpush

