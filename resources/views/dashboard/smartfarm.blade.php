@extends('layouts.dashboard', ['pageHeader' => 'ปัญญาประดิษฐ์', 'title' => 'ระบบฟาร์มอัจฉริยะ'])

@section('content')
<div class="space-y-6">
    <!-- Header with AI Mode Badge & Tabs -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">ระบบฟาร์มอัจฉริยะ (Smart Farm AI)</h1>
            <p class="text-sm text-gray-500">ระบบควบคุมสภาพแวดล้อมและพ่นหมอกอัตโนมัติด้วย AI</p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="toggleSystemStatusModal()" class="rounded-lg bg-gray-100 hover:bg-gray-200 px-3.5 py-2 text-xs font-semibold text-gray-700 transition flex items-center gap-1.5 shadow-xs">
                <i class="fa-solid fa-list-check text-emerald-600"></i>
                <span>สถานะระบบ & บันทึก (Logs)</span>
            </button>
            <div class="flex items-center gap-2 bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-full border border-emerald-200 text-xs font-semibold">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                <span>AI Automation Active</span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex space-x-2 border-b border-gray-200">
        <button onclick="switchTab('basic')" id="tab-btn-basic" class="tab-btn active px-4 py-2.5 font-medium text-sm border-b-2 border-emerald-600 text-emerald-700">
            <i class="fa-solid fa-gauge mr-1.5"></i> พื้นฐาน (Overview)
        </button>
        <button onclick="switchTab('ai-control')" id="tab-btn-ai-control" class="tab-btn px-4 py-2.5 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-microchip-ai mr-1.5"></i> โปรแกรมการเติบโต & ควบคุม (Growth Programs)
        </button>
        <button onclick="switchTab('research')" id="tab-btn-research" class="tab-btn px-4 py-2.5 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-book-open-cover mr-1.5"></i> งานวิจัยมันฝรั่ง (Research)
        </button>
    </div>

    <!-- TAB 1: BASIC -->
    <div id="tab-content-basic" class="space-y-6">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- 1. Cultivation Timer Card -->
            <div class="card rounded-2xl p-6 text-center shadow-sm border border-slate-100">
                <h2 class="text-xl font-bold text-gray-800">สถานะการเพาะปลูก</h2>
                <div class="mt-4">
                    <div class="text-5xl font-bold text-emerald-600">
                        <span id="crop-days">14</span> <span class="text-lg text-gray-400 font-normal">วัน</span>
                    </div>
                    <div class="mt-3 flex justify-center space-x-2 font-mono text-2xl text-gray-600">
                        <span id="crop-hours">08</span>:<span id="crop-minutes">42</span>:<span id="crop-seconds">19</span>
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-2">
                    <button id="btn-toggle-crop" onclick="toggleCultivation()" class="w-full rounded-xl bg-red-500 hover:bg-red-600 px-4 py-2.5 font-bold text-white shadow-md transition">
                        หยุดการทำงานชั่วคราว
                    </button>
                    <button onclick="resetCultivation()" class="w-full rounded-xl bg-gray-100 hover:bg-gray-200 px-4 py-2 font-medium text-gray-600 transition text-sm">
                        รีเซ็ตเวลานับ
                    </button>
                </div>
            </div>

            <!-- 2. Device Status Card -->
            <div class="card rounded-2xl p-6 shadow-sm border border-slate-100 lg:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800">สถานะอุปกรณ์ทำงาน (Device Control)</h2>
                    <span class="text-xs text-gray-400">ควบคุมเปิด-ปิดอัตโนมัติ</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="font-medium text-gray-700">ปั๊มน้ำดูดกลับ</span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-emerald-600 text-xs" id="status-return-pump">เปิด</span>
                            <div class="h-3 w-3 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="font-medium text-gray-700">ปั๊มพ่นหมอกไฮดรอลิก</span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-emerald-600 text-xs" id="status-mister-pump">เปิด</span>
                            <div class="h-3 w-3 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="font-medium text-gray-700">หลอด UV ฆ่าเชื้อ</span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-red-500 text-xs" id="status-uv-light">ปิด</span>
                            <div class="h-3 w-3 rounded-full bg-red-400"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="font-medium text-gray-700">หัวพ่นหมอกอัลตราโซนิก</span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-emerald-600 text-xs" id="status-ultrasonic">เปิด</span>
                            <div class="h-3 w-3 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="font-medium text-gray-700">พัดลมระบายอากาศ</span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-emerald-600 text-xs" id="status-exhaust-fan">เปิด</span>
                            <div class="h-3 w-3 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="font-medium text-gray-700">ไฟปลูกพืช (Grow Light)</span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-emerald-600 text-xs" id="status-grow-light">เปิด</span>
                            <div class="h-3 w-3 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Water Tank & Depletion Calculation Card -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Water Tank -->
            <div class="card rounded-2xl border-l-4 border-blue-500 p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-blue-900">1. ระบบคำนวณน้ำและวันเติม</h2>
                    <span class="text-xs bg-blue-50 text-blue-700 px-2.5 py-1 rounded-full font-medium">Auto Estimate</span>
                </div>
                <div class="grid grid-cols-1 items-center gap-6 sm:grid-cols-3">
                    <div class="flex flex-col items-center">
                        <div class="relative mx-auto h-[140px] w-[100px] overflow-hidden rounded-b-2xl border-4 border-blue-800 bg-indigo-50">
                            <div id="tank-water-visual" class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-blue-600 to-cyan-400 opacity-80 transition-all duration-500" style="height: 78%;"></div>
                            <div class="absolute inset-0 z-10 flex items-center justify-center">
                                <span id="tank-water-percent" class="text-2xl font-bold text-blue-900 drop-shadow-sm">78%</span>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">ความจุ: 100 ลิตร</p>
                    </div>

                    <div class="sm:col-span-2 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-lg bg-blue-50 p-3">
                                <p class="text-xs text-gray-500">ปริมาณน้ำปัจจุบัน</p>
                                <p class="text-lg font-bold text-blue-700" id="current-water-val">78.0 ลิตร</p>
                            </div>
                            <div class="rounded-lg bg-blue-50 p-3">
                                <p class="text-xs text-gray-500">อัตราใช้เฉลี่ย</p>
                                <p class="text-lg font-bold text-blue-700">4.5 ลิตร/วัน</p>
                            </div>
                        </div>

                        <div class="rounded-lg bg-blue-50 p-3">
                            <p class="text-xs text-gray-500">น้ำจะลดถึงระดับเตือนในอีก</p>
                            <p class="text-xl font-bold text-blue-900" id="water-days-left">14 วัน 6 ชม. 20 น.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nutrient Tank -->
            <div class="card rounded-2xl border-l-4 border-emerald-500 p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-emerald-900">2. ระบบสารอาหารและแร่ธาตุ</h2>
                    <span class="text-xs bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full font-medium">EC & TDS Control</span>
                </div>
                <div class="grid grid-cols-1 items-center gap-6 sm:grid-cols-3">
                    <div class="flex flex-col items-center">
                        <div class="relative mx-auto h-[140px] w-[100px] overflow-hidden rounded-b-2xl border-4 border-emerald-800 bg-emerald-50">
                            <div id="tank-nutrient-visual" class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-emerald-600 to-green-400 opacity-80 transition-all duration-500" style="height: 85%;"></div>
                            <div class="absolute inset-0 z-10 flex items-center justify-center">
                                <span id="tank-nutrient-percent" class="text-2xl font-bold text-emerald-900 drop-shadow-sm">85%</span>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">ความจุ: 50 ลิตร</p>
                    </div>

                    <div class="sm:col-span-2 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-lg bg-emerald-50 p-3">
                                <p class="text-xs text-gray-500">ปริมาณสารละลาย</p>
                                <p class="text-lg font-bold text-emerald-700">42.5 ลิตร</p>
                            </div>
                            <div class="rounded-lg bg-emerald-50 p-3">
                                <p class="text-xs text-gray-500">EC ปัจจุบัน</p>
                                <p class="text-lg font-bold text-emerald-700">1.45 µS/cm</p>
                            </div>
                        </div>

                        <div class="rounded-lg bg-emerald-50 p-3">
                            <p class="text-xs text-gray-500">ต้องเติมสารอาหารเพิ่มในอีก</p>
                            <p class="text-xl font-bold text-emerald-900">18 วัน 12 ชม. 45 น.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: AI GROWTH PROGRAMS & CONTROL -->
    <div id="tab-content-ai-control" class="hidden space-y-6">
        <!-- Target Sliders Card -->
        <div class="card rounded-2xl p-6 shadow-sm border border-slate-100">
            <h2 class="text-xl font-bold text-gray-800 mb-4">ตั้งค่าเป้าหมายระบบ (Target Environmental Parameters)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- pH Slider -->
                <div class="space-y-2">
                    <div class="flex justify-between text-sm font-semibold text-gray-700">
                        <span>pH เป้าหมาย:</span>
                        <span id="val-target-ph" class="text-emerald-600 font-bold">6.0</span>
                    </div>
                    <input type="range" min="4" max="8" step="0.1" value="6.0" oninput="document.getElementById('val-target-ph').textContent = this.value" class="w-full accent-track">
                </div>

                <!-- Humidity Slider -->
                <div class="space-y-2">
                    <div class="flex justify-between text-sm font-semibold text-gray-700">
                        <span>ความชื้นสัมพัทธ์ (%):</span>
                        <span id="val-target-humi" class="text-blue-600 font-bold">80 %</span>
                    </div>
                    <input type="range" min="40" max="100" step="1" value="80" oninput="document.getElementById('val-target-humi').textContent = this.value + ' %'" class="w-full accent-track">
                </div>

                <!-- Temp Slider -->
                <div class="space-y-2">
                    <div class="flex justify-between text-sm font-semibold text-gray-700">
                        <span>อุณหภูมิ (°C):</span>
                        <span id="val-target-temp" class="text-amber-600 font-bold">25 °C</span>
                    </div>
                    <input type="range" min="18" max="35" step="0.5" value="25" oninput="document.getElementById('val-target-temp').textContent = this.value + ' °C'" class="w-full accent-track">
                </div>

                <!-- EC Slider -->
                <div class="space-y-2">
                    <div class="flex justify-between text-sm font-semibold text-gray-700">
                        <span>EC (µS/cm):</span>
                        <span id="val-target-ec" class="text-purple-600 font-bold">1.5</span>
                    </div>
                    <input type="range" min="0.5" max="3.0" step="0.1" value="1.5" oninput="document.getElementById('val-target-ec').textContent = this.value" class="w-full accent-track">
                </div>

                <!-- Light Slider -->
                <div class="space-y-2">
                    <div class="flex justify-between text-sm font-semibold text-gray-700">
                        <span>ความสว่าง (Lux):</span>
                        <span id="val-target-light" class="text-yellow-600 font-bold">7000 Lux</span>
                    </div>
                    <input type="range" min="1000" max="15000" step="500" value="7000" oninput="document.getElementById('val-target-light').textContent = this.value + ' Lux'" class="w-full accent-track">
                </div>

                <!-- TDS Slider -->
                <div class="space-y-2">
                    <div class="flex justify-between text-sm font-semibold text-gray-700">
                        <span>TDS (ppm):</span>
                        <span id="val-target-tds" class="text-teal-600 font-bold">750 ppm</span>
                    </div>
                    <input type="range" min="200" max="1500" step="25" value="750" oninput="document.getElementById('val-target-tds').textContent = this.value + ' ppm'" class="w-full accent-track">
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button onclick="saveTargets()" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 shadow-md transition">
                    บันทึกพารามิเตอร์เป้าหมาย
                </button>
            </div>
        </div>

        <!-- Growth Preset Programs Card -->
        <div class="card rounded-2xl p-6 shadow-sm border border-slate-100">
            <h2 class="text-xl font-bold text-gray-800 mb-4">โปรแกรมการเติบโตของพืช (Growth Programs)</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl border-2 border-emerald-500 bg-emerald-50/50 cursor-pointer">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-emerald-800 text-lg">🥔 มันฝรั่ง (Potato)</span>
                        <span class="text-xs bg-emerald-600 text-white px-2 py-0.5 rounded-full">Active</span>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">ช่วงสร้างหัวมันฝรั่ง (Tuberization): pH 5.8-6.2, EC 1.4-1.8, อุณหภูมิ 22-26°C</p>
                    <div class="text-xs font-semibold text-emerald-700">ระยะเวลา: 60-90 วัน</div>
                </div>

                <div class="p-4 rounded-xl border border-gray-200 hover:border-emerald-400 bg-white transition cursor-pointer" onclick="applyPreset('lettuce')">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-gray-800 text-lg">🥬 ผักกาดหอม (Lettuce)</span>
                        <span class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">Preset</span>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">เจริญเติบโตใบเร็ว: pH 6.0, EC 1.2, อุณหภูมิ 20-24°C</p>
                    <div class="text-xs font-semibold text-gray-500">ระยะเวลา: 35-45 วัน</div>
                </div>

                <div class="p-4 rounded-xl border border-gray-200 hover:border-emerald-400 bg-white transition cursor-pointer" onclick="applyPreset('strawberry')">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-gray-800 text-lg">🍓 สตรอว์เบอร์รี (Strawberry)</span>
                        <span class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">Preset</span>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">ช่วงออกดอกและติดผล: pH 5.5-6.5, EC 1.6-2.0, อุณหภูมิ 18-22°C</p>
                    <div class="text-xs font-semibold text-gray-500">ระยะเวลา: 90-120 วัน</div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: RESEARCH CARD -->
    <div id="tab-content-research" class="hidden space-y-6">
        <div class="card rounded-2xl border-t-4 border-blue-500 p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-blue-900">
                    ข้อมูลงานวิจัยมันฝรั่ง (Potato Research Documentation)
                </h2>
                <button onclick="exportResearchDoc()" class="rounded-lg bg-blue-600 hover:bg-blue-700 px-4 py-2 text-sm text-white font-medium shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-file-word"></i>
                    <span>Export Word (.doc)</span>
                </button>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="text-xs font-bold text-gray-500">ชนิดพืช</label>
                        <input disabled value="มันฝรั่ง (Potato - Solanum tuberosum)" class="mt-1 w-full rounded-lg border bg-gray-100 p-2.5 text-sm text-gray-700">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500">ชื่อโครงงาน</label>
                        <input id="res-project-name" value="AeroFogPonic Potato Cultivation" class="mt-1 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500">สายพันธุ์มันฝรั่ง</label>
                        <input id="res-variety" value="แอตแลนติก (Atlantic)" class="mt-1 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-bold text-gray-500">คณะผู้วิจัย</label>
                        <input id="res-researchers" value="ทีมงานวิจัย Aerosol Project CPW" class="mt-1 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500">เงื่อนไขการควบคุมพิเศษ</label>
                        <input id="res-requirements" value="พ่นหมอกสารอาหารระดับ 5-10 ไมครอน ทุก 15 นาที นาน 30 วินาที" class="mt-1 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-blue-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-500">บันทึกผลการทดลองและการสังเกต</label>
                    <textarea id="res-note" rows="4" class="mt-1 w-full rounded-lg border border-gray-300 p-3 text-sm focus:border-blue-500 focus:outline-none" placeholder="บันทึกรายละเอียดการเจริญเติบโตของรากและขนาดของหัวมันฝรั่ง..."></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Status & Logs Modal -->
<div id="system-status-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
    <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 sm:p-8 shadow-2xl animate-fade-in">
        <div class="mb-4 flex items-center justify-between border-b pb-4">
            <h2 class="text-2xl font-bold text-gray-800">
                รายละเอียดสถานะระบบ (System Logs)
            </h2>
            <button onclick="toggleSystemStatusModal()" class="text-2xl font-bold text-gray-400 hover:text-red-500 transition">
                &times;
            </button>
        </div>

        <div class="space-y-4">
            <div>
                <h3 class="font-bold text-red-600 mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>รายงานข้อผิดพลาด (Error Logs)</span>
                </h3>
                <div class="h-36 overflow-y-auto rounded-lg border border-red-100 bg-red-50 p-3 font-mono text-xs text-red-700 space-y-1">
                    <div>[08:12:04] INFO: เชื่อมต่อเซ็นเซอร์ SHT40 สำเร็จ</div>
                    <div>[09:30:15] WARNING: อัตราการใช้น้ำสูงกว่าปกติเล็กน้อย</div>
                    <div>[10:45:00] INFO: ปั๊มพ่นหมอกทำงานตามรอบเวลา 30 วินาที</div>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-terminal"></i>
                    <span>ประวัติคำสั่งระบบ (Command Logs)</span>
                </h3>
                <div class="h-44 overflow-y-auto rounded-lg border border-gray-200 bg-gray-900 p-3 font-mono text-xs text-green-400 space-y-1">
                    <div>[SYSTEM] Booted ESP32 v2.4 controller connected via WiFi</div>
                    <div>[AI] Target EC adjusted to 1.50 uS/cm</div>
                    <div>[MIST] Ultrasonic mister triggered for 30s</div>
                    <div>[SENSORS] Reading saved: Temp 26.2C, Humidity 78%</div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2 border-t pt-4">
            <button onclick="toggleSystemStatusModal()" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">
                ปิด
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchTab(tab) {
        document.getElementById('tab-content-basic').classList.add('hidden');
        document.getElementById('tab-content-ai-control').classList.add('hidden');
        document.getElementById('tab-content-research').classList.add('hidden');

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-emerald-600', 'text-emerald-700');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        document.getElementById('tab-content-' + tab).classList.remove('hidden');
        const activeBtn = document.getElementById('tab-btn-' + tab);
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
        activeBtn.classList.add('border-emerald-600', 'text-emerald-700');
    }

    function toggleSystemStatusModal() {
        const modal = document.getElementById('system-status-modal');
        modal.classList.toggle('hidden');
    }

    // Cultivation timer simulation
    let isRunning = true;
    let elapsedSeconds = 14 * 86400 + 8 * 3600 + 42 * 60 + 19;

    setInterval(() => {
        if (isRunning) {
            elapsedSeconds++;
            const days = Math.floor(elapsedSeconds / 86400);
            const hrs = Math.floor((elapsedSeconds % 86400) / 3600);
            const mins = Math.floor((elapsedSeconds % 3600) / 60);
            const secs = Math.floor(elapsedSeconds % 60);

            document.getElementById('crop-days').textContent = days;
            document.getElementById('crop-hours').textContent = String(hrs).padStart(2, '0');
            document.getElementById('crop-minutes').textContent = String(mins).padStart(2, '0');
            document.getElementById('crop-seconds').textContent = String(secs).padStart(2, '0');
        }
    }, 1000);

    function toggleCultivation() {
        isRunning = !isRunning;
        const btn = document.getElementById('btn-toggle-crop');
        if (isRunning) {
            btn.textContent = 'หยุดการทำงานชั่วคราว';
            btn.className = 'w-full rounded-xl bg-red-500 hover:bg-red-600 px-4 py-2.5 font-bold text-white shadow-md transition';
            Swal.fire({ icon: 'success', title: 'เริ่มการทำงานต่อ', timer: 1500, showConfirmButton: false });
        } else {
            btn.textContent = 'เริ่มการทำงานต่อ';
            btn.className = 'w-full rounded-xl bg-emerald-500 hover:bg-emerald-600 px-4 py-2.5 font-bold text-white shadow-md transition';
            Swal.fire({ icon: 'info', title: 'หยุดการทำงานชั่วคราวแล้ว', timer: 1500, showConfirmButton: false });
        }
    }

    function resetCultivation() {
        Swal.fire({
            title: 'รีเซ็ตเวลานับ?',
            text: 'ต้องการเริ่มนับเวลาการเพาะปลูกใหม่เป็น 0 วันใช่หรือไม่',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'ใช่, รีเซ็ต',
            cancelButtonText: 'ยกเลิก'
        }).then(res => {
            if (res.isConfirmed) {
                elapsedSeconds = 0;
            }
        });
    }

    function saveTargets() {
        Swal.fire({
            icon: 'success',
            title: 'บันทึกสำเร็จ',
            text: 'อัปเดตพารามิเตอร์เป้าหมายของ AI เรียบร้อยแล้ว',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function exportResearchDoc() {
        const title = document.getElementById('res-project-name').value;
        const variety = document.getElementById('res-variety').value;
        const researchers = document.getElementById('res-researchers').value;
        const reqs = document.getElementById('res-requirements').value;
        const note = document.getElementById('res-note').value;

        const content = `
            <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
            <head><meta charset='utf-8'><title>Research Doc</title></head>
            <body style='font-family: Arial, sans-serif;'>
                <h2>ข้อมูลงานวิจัยมันฝรั่ง (Potato Research Documentation)</h2>
                <hr/>
                <p><strong>ชื่อโครงงาน:</strong> ${title}</p>
                <p><strong>ชนิดพืช:</strong> มันฝรั่ง (Potato - Solanum tuberosum)</p>
                <p><strong>สายพันธุ์:</strong> ${variety}</p>
                <p><strong>คณะผู้วิจัย:</strong> ${researchers}</p>
                <p><strong>เงื่อนไขการควบคุม:</strong> ${reqs}</p>
                <p><strong>บันทึกผลการสังเกต:</strong> ${note || 'ไม่มีบันทึกเพิ่มเติม'}</p>
                <hr/>
                <p><small>Exported from Aerosol Smart Farm System on ${new Date().toLocaleString()}</small></p>
            </body>
            </html>
        `;

        const blob = new Blob(['\ufeff', content], { type: 'application/msword' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'Potato_Research_' + Date.now() + '.doc';
        a.click();
        URL.revokeObjectURL(url);
    }
</script>
@endpush

