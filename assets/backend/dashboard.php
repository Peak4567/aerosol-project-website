<?php
include('index.php');
?>
<div class="p-4 sm:ml-64">
    <main class="p-4 md:p-6 bg-slate-50">
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex flex-col gap-2">
                <h1 class="text-3xl font-bold text-gray-800">แผนหน้าปัด</h1>
                <div class="flex items-center gap-4 text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-calendar-day"></i>
                        <span id="current-date">กําลังโหลดวันที่</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-clock"></i>
                        <span id="current-time">กําลังโหลดเวลา</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-cloud-sun"></i>
                        <span id="weather-status">กําลังโหลดสภาพอากาศ</span>
                    </div>
                </div>
            </div>
            <button class="bg-green-500 text-white font-semibold py-2 px-5 rounded-lg shadow-md hover:bg-green-600 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-download"></i>
                <span>Export</span>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 xl:grid-cols-2 gap-6">

            <div class="grid grid-cols-2 gap-6">
                <!-- Temperature Card -->
                <div class="bg-white p-5 rounded-xl shadow-md flex justify-between items-center">
                    <div>
                        <p class="text-gray-400 text-sm">Temperature</p>
                        <p id="stat-temp" class="text-2xl font-bold text-gray-800 mt-1">-- °C</p>
                    </div>
                    <div class="bg-amber-100 text-amber-600 h-12 w-12 flex items-center justify-center rounded-full text-xl">
                        <i class="fa-solid fa-temperature-half"></i>
                    </div>
                </div>
                <!-- Humidity Card -->
                <div class="bg-white p-5 rounded-xl shadow-md flex justify-between items-center">
                    <div>
                        <p class="text-gray-400 text-sm">Humidity</p>
                        <p id="stat-humi" class="text-2xl font-bold text-gray-800 mt-1">-- %RH</p>
                    </div>
                    <div class="bg-blue-100 text-blue-600 h-12 w-12 flex items-center justify-center rounded-full text-xl">
                        <i class="fa-solid fa-droplet-degree"></i>
                    </div>
                </div>
                <!-- TDS Card -->
                <div class="bg-white p-5 rounded-xl shadow-md flex justify-between items-center">
                    <div>
                        <p class="text-gray-400 text-sm">Total Dissolved Solids (TDS)</p>
                        <p id="stat-tds" class="text-2xl font-bold text-gray-800 mt-1">-- ppm</p>
                    </div>
                    <div class="bg-teal-100 text-teal-600 h-12 w-12 flex items-center justify-center rounded-full text-xl">
                        <i class="fa-solid fa-flask-vial"></i>
                    </div>
                </div>
                <!-- EC Card -->
                <div class="bg-white p-5 rounded-xl shadow-md flex justify-between items-center">
                    <div>
                        <p class="text-gray-400 text-sm">Electrical Conductivity (EC)</p>
                        <p id="stat-ec" class="text-2xl font-bold text-gray-800 mt-1">-- µS/cm</p>
                    </div>
                    <div class="bg-purple-100 text-purple-600 h-12 w-12 flex items-center justify-center rounded-full text-xl">
                        <i class="fa-solid fa-moon-over-sun"></i>
                    </div>
                </div>
                <!-- pH Card -->
                <div class="bg-white p-5 rounded-xl shadow-md flex justify-between items-center col-span-2">
                    <div>
                        <p class="text-gray-400 text-sm">Potential of Hydrogen (pH)</p>
                        <p id="stat-ph" class="text-2xl font-bold text-gray-800 mt-1">--</p>
                    </div>
                    <div class="bg-red-100 text-red-600 h-12 w-12 flex items-center justify-center rounded-full text-xl">
                        <i class="fa-solid fa-atom-simple"></i>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 xl:col-span-3 bg-white p-4 sm:p-6 rounded-xl shadow-md">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Sensor Overview</h2>
                <div class="h-[340px]">
                    <canvas id="multiSensorChart"></canvas>
                </div>
                <p id="last-updated" class="text-xs text-gray-400 mt-3 text-center">Loading data...</p>
            </div>

            <div class="lg:col-span-3 xl:col-span-4 grid grid-cols-1 xl:grid-cols-3 gap-6">

                <div class="lg:col-span-3 xl:col-span-4 grid grid-cols-1 xl:grid-cols-2 gap-6">

                    <div class=" bg-white p-4 sm:p-6 rounded-xl shadow-md">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                            <h2 class="text-lg font-semibold text-gray-700">ประวัติข้อมูลเซ็นเซอร์</h2>

                            <div id="sensor-btn-group" class="flex flex-wrap gap-2">
                                <button data-sensor="sht40_temp" class="sensor-btn active">Temperature</button>
                                <button data-sensor="sht40_humi" class="sensor-btn">Humidity</button>
                                <button data-sensor="tds" class="sensor-btn">TDS</button>
                                <button data-sensor="ec" class="sensor-btn">EC</button>
                                <button data-sensor="ph" class="sensor-btn">pH</button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="text-gray-400 text-sm">
                                    <tr>
                                        <th class="py-2 px-3 w-16">#</th>
                                        <th id="table-header-value" class="py-2 px-3">ค่า (Value)</th>
                                        <th class="py-2 px-3 text-right">เวลา (Timestamp)</th>
                                    </tr>
                                </thead>
                                <tbody id="sensor-data-tbody" class="text-gray-600">
                                </tbody>
                            </table>
                        </div>

                        <div id="pagination-controls" class="flex justify-center items-center gap-1 mt-4">
                        </div>
                    </div>
                    <div class="xl:col-span-1 bg-white p-4 sm:p-6 rounded-xl shadow-md">
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">ข้อมูลเซนเซอร์</h2>
                        <ul id="summary-list" class="space-y-4">
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>
<script src="../backend/js/dashboard.js"></script>
<script src="../backend/js/dashboard_history.js"></script>