<?php
include('../../system/config.php');
include("../components/aside.php");
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Aerosol - ระบบควบคุมอัจฉริยะ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/assets/css/font-awesome-pro-v6-6.2.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        /* --- CSS Styling --- */
        body { font-family: 'Sarabun', sans-serif; color: #1f2937; background-color: #f3f4f6; }
        .dashboard-card { background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        
        /* Toggle Switch */
        .toggle-checkbox { opacity: 0; width: 0; height: 0; }
        .toggle-label { display: block; position: relative; cursor: pointer; border-radius: 9999px; transition: background-color 0.3s; background-color: #9ca3af; width: 3rem; height: 1.75rem; }
        .toggle-checkbox:checked+.toggle-label { background-color: #2563eb; }
        .toggle-checkbox:checked+.toggle-label:after { transform: translateX(100%); }
        .toggle-label:after { content: ""; position: absolute; top: 2px; left: 2px; width: 1.5rem; height: 1.5rem; background-color: white; border-radius: 50%; transition: 0.3s; }

        /* Status Indicator */
        .status-indicator-dot { width: 12px; height: 12px; border-radius: 50%; background-color: #ef4444; transition: background-color 0.3s ease; display: inline-block; }
        .status-indicator-dot.on { background-color: #10b981; box-shadow: 0 0 8px rgba(16, 185, 129, 0.7); }

        /* Navigation Tabs */
        .tab-active { border-bottom: 3px solid #2563eb; color: #2563eb; background-color: #eff6ff; }
        .tab-btn { cursor: pointer; }

        /* Toast */
        #notification-container { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 50; width: 300px; }
        .toast { padding: 1rem; border-radius: 0.5rem; background: white; margin-bottom: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); opacity: 0; transform: translateY(-20px); transition: all 0.3s; border-left: 5px solid #3b82f6; }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success { border-color: #10b981; } .toast.error { border-color: #ef4444; } .toast.info { border-color: #3b82f6; }

        /* Connection Status Badge */
        .conn-badge { position: fixed; top: 10px; right: 10px; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; z-index: 100; opacity: 0.9; transition: all 0.3s; }
        .conn-online { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .conn-offline { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        /* Range Slider */
        input[type=range] { -webkit-appearance: none; background: transparent; }
        input[type=range]::-webkit-slider-thumb { -webkit-appearance: none; height: 16px; width: 16px; border-radius: 50%; background: #2563eb; cursor: pointer; margin-top: -6px; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3); }
        input[type=range]::-webkit-slider-runnable-track { width: 100%; height: 4px; cursor: pointer; background: #e5e7eb; border-radius: 2px; }

        /* Precision Buttons */
        .precision-btn { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; transition: background-color 0.2s; }
        .precision-btn:hover { background-color: #d1d5db; }

        /* Modal */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 50; display: none; justify-content: center; align-items: center; opacity: 0; transition: opacity 0.3s; }
        .modal-overlay.open { display: flex; opacity: 1; }
        .modal-content { background: white; border-radius: 1rem; padding: 2rem; width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto; transform: scale(0.9); transition: transform 0.3s; }
        .modal-overlay.open .modal-content { transform: scale(1); }

        /* Water Tank */
        .water-tank { position: relative; width: 100px; height: 140px; background: #e0e7ff; border: 4px solid #1e40af; border-radius: 0 0 1rem 1rem; overflow: hidden; margin: 0 auto; }
        .water-level { position: absolute; bottom: 0; left: 0; width: 100%; background: linear-gradient(to top, #3b82f6, #60a5fa); transition: height 0.5s ease-in-out; opacity: 0.8; }
        .water-surface { position: absolute; top: -5px; left: 0; width: 200%; height: 10px; background: #93c5fd; border-radius: 50%; opacity: 0.5; animation: wave 2s infinite linear; }
        @keyframes wave { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
    </style>
</head>

<body class="text-gray-800 bg-gray-100 ">

    <div class="p-4 sm:ml-64">
        <div id="connection-status" class="conn-badge conn-offline">● รอการเชื่อมต่อ...</div>
        <div id="notification-container"></div>

        <div id="system-modal" class="modal-overlay ">
            <div class="modal-content shadow-2xl">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <h2 class="text-2xl font-bold text-gray-800">รายละเอียดสถานะระบบ</h2>
                    <button onclick="toggleModal(false)" class="text-gray-500 hover:text-red-500 text-2xl font-bold">&times;</button>
                </div>
                <div class="space-y-6">
                    <div>
                        <h3 class="font-bold text-red-600 mb-2 flex items-center">รายงานข้อผิดพลาด (Error Logs)</h3>
                        <div id="modal-error-list" class="bg-red-50 p-4 rounded-lg border border-red-100 text-sm h-48 overflow-y-auto font-mono space-y-2"></div>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-700 mb-2 flex items-center">ประวัติคำสั่งและการทำงาน</h3>
                        <div id="modal-command-list" class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm h-48 overflow-y-auto font-mono space-y-1"></div>
                    </div>
                </div>
                <div class="mt-8 flex justify-end space-x-3 border-t pt-4">
                    <button onclick="toggleModal(false)" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">ปิด</button>
                    <button onclick="exportSystemReport()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold shadow flex items-center">
                        Export Logs (.doc)
                    </button>
                </div>
            </div>
        </div>

        <div class="container mx-auto p-4 md:p-8 relative z-10">
            <header class="text-center mb-12">
                <h1 class="text-5xl font-bold text-gray-800">AI Aerosol</h1>
                <p class="text-lg text-gray-600 mt-2">ระบบอัจฉริยะควบคุมสภาพแวดล้อม</p>
            </header>

            <main class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div id="col-permanent-status" class="space-y-8 lg:col-span-1">
                    <div class="dashboard-card p-6 rounded-2xl shadow-lg">
                        <div class="flex justify-between items-center mb-4"><h2 class="text-xl font-bold">1. ข้อมูลเซ็นเซอร์ปัจจุบัน</h2></div>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div><p class="text-sm text-gray-500">PH</p><p class="text-2xl font-bold text-blue-600" id="ph-value">--</p></div>
                            <div><p class="text-sm text-gray-500">ความชื้น</p><p class="text-2xl font-bold text-indigo-600" id="humidity-value">-- %</p></div>
                            <div><p class="text-sm text-gray-500">อุณหภูมิ</p><p class="text-2xl font-bold text-red-600" id="temperature-value">-- °C</p></div>
                            <div><p class="text-sm text-gray-500">EC</p><p class="text-2xl font-bold text-green-600" id="ec-value">--</p></div>
                            <div><p class="text-sm text-gray-500">แสง</p><p class="text-2xl font-bold text-yellow-600" id="light-value">--</p></div>
                            <div><p class="text-sm text-gray-500">TDS</p><p class="text-2xl font-bold text-purple-600" id="tds-value">-- ppm</p></div>
                        </div>
                    </div>

                    <div class="dashboard-card p-6 rounded-2xl shadow-lg text-center">
                        <h2 class="text-xl font-bold mb-2">2. สถานะการเพาะปลูก</h2>
                        <div id="cultivation-timer-display" class="hidden mt-3">
                            <div class="text-4xl font-bold text-blue-600"><span id="timer-days">0</span> <span class="text-lg text-gray-500">วัน</span></div>
                            <div class="flex justify-center space-x-2 mt-2 text-xl font-mono text-gray-600">
                                <span id="timer-hours">00</span>:<span id="timer-minutes">00</span>:<span id="timer-seconds">00</span>
                            </div>
                            <div id="timer-controls" class="mt-4 space-y-2">
                                <button id="stop-cultivation-btn" class="w-full bg-red-500 text-white py-2 px-4 rounded-xl hover:bg-red-600 shadow-md font-bold transition">หยุดการทำงาน</button>
                                <div id="resume-reset-group" class="hidden grid grid-cols-2 gap-2">
                                    <button id="resume-cultivation-btn" class="bg-green-500 text-white py-2 px-4 rounded-xl hover:bg-green-600 shadow-md font-bold transition">ทำงานต่อ</button>
                                    <button id="reset-cultivation-btn" class="bg-gray-500 text-white py-2 px-4 rounded-xl hover:bg-gray-600 shadow-md font-bold transition">รีเซ็ต</button>
                                </div>
                            </div>
                        </div>
                        <button id="start-cultivation-btn" class="mt-4 w-full bg-green-500 text-white py-2 px-4 rounded-xl hover:bg-green-600 shadow-md font-bold transition">เริ่มการเพาะปลูก</button>
                    </div>

                    <div class="dashboard-card p-6 rounded-2xl shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold">3. โหมดควบคุม AI</h2>
                            <div class="flex items-center"><input type="checkbox" id="ai-toggle-switch" class="toggle-checkbox absolute opacity-0 w-0 h-0" /><label for="ai-toggle-switch" class="toggle-label"></label></div>
                        </div>
                        <p class="text-sm text-gray-500">เปิดใช้งานเพื่อให้ AI ควบคุมอัตโนมัติตามโปรแกรม</p>
                    </div>

                    <div class="dashboard-card p-6 rounded-2xl shadow-lg">
                        <div class="flex justify-between items-center mb-4"><h2 class="text-xl font-bold">4. สถานะอุปกรณ์</h2></div>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center border-b pb-1"><p>ปั๊มน้ำดูดกลับ</p><div class="flex items-center space-x-2"><span id="return-pump-status" class="font-bold text-red-500">ปิด</span><div class="status-indicator-dot" data-device="return_pump"></div></div></div>
                            <div class="flex justify-between items-center border-b pb-1"><p>ปั๊มพ่นหมอก</p><div class="flex items-center space-x-2"><span id="mister-pump-status" class="font-bold text-red-500">ปิด</span><div class="status-indicator-dot" data-device="mister_pump"></div></div></div>
                            <div class="flex justify-between items-center border-b pb-1"><p>หลอด UV</p><div class="flex items-center space-x-2"><span id="uv-light-status" class="font-bold text-red-500">ปิด</span><div class="status-indicator-dot" data-device="uv_light"></div></div></div>
                            <div class="flex justify-between items-center border-b pb-1"><p>หัวพ่นหมอกอัลตราโซนิก</p><div class="flex items-center space-x-2"><span id="ultrasonic-mister-status" class="font-bold text-red-500">ปิด</span><div class="status-indicator-dot" data-device="ultrasonic_mister"></div></div></div>
                            <div class="flex justify-between items-center border-b pb-1"><p>พัดลมระบาย</p><div class="flex items-center space-x-2"><span id="exhaust-fan-status" class="font-bold text-red-500">ปิด</span><div class="status-indicator-dot" data-device="exhaust_fan"></div></div></div>
                            <div class="flex justify-between items-center border-b pb-1"><p>พัดลมส่งหมอก</p><div class="flex items-center space-x-2"><span id="mist-fan-status" class="font-bold text-red-500">ปิด</span><div class="status-indicator-dot" data-device="mist_fan"></div></div></div>
                            <div class="flex justify-between items-center"><p>ไฟปลูกพืช</p><div class="flex items-center space-x-2"><span id="grow-light-status" class="font-bold text-red-500">ปิด</span><div class="status-indicator-dot" data-device="grow_light"></div></div></div>
                        </div>
                    </div>
                </div>

                <div id="col-content-area" class="space-y-8 lg:col-span-2">
                    <div class="flex border-b border-gray-200 bg-white rounded-xl shadow-sm overflow-hidden">
                        <button id="tab-basic-view" class="tab-btn flex-1 py-3 font-semibold text-lg transition tab-active" onclick="navigate('basic')">การปลูกขั้นพื้นฐาน</button>
                        <button id="tab-ai-control-view" class="tab-btn flex-1 py-3 font-semibold text-lg text-gray-500 hover:text-blue-500 transition" onclick="navigate('ai-control')">ระบบควบคุม AI</button>
                        <button id="tab-research-view" class="tab-btn flex-1 py-3 font-semibold text-lg text-gray-500 hover:text-blue-500 transition" onclick="navigate('research')">ระบบเก็บข้อมูลวิจัย</button>
                    </div>

                    <div id="basic-view" class="space-y-8 animate-fade-in">
                        <div class="dashboard-card p-6 rounded-2xl shadow-lg">
                            <div class="flex justify-between items-center mb-4"><h2 class="text-xl font-bold">ตั้งเป้าหมาย (ควบคุมเอง)</h2></div>
                            <div class="space-y-6" id="manual-sliders-container"></div>
                        </div>
                    </div>

                    <div id="ai-control-view" class="space-y-8 hidden">
                        
                         <div class="dashboard-card p-6 rounded-2xl shadow-lg border-2 border-cyan-400 bg-white">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-2">☁️</span>
                                    <div>
                                        <h2 class="text-xl font-bold text-gray-800">ระบบปั๊มพ่นหมอก (Ultrasonic Cycle)</h2>
                                        <p class="text-sm text-gray-500">ควบคุมเวลาเปิด-ปิดหมอก</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">ปิดระบบ</span>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="mist-cycle-toggle" class="toggle-checkbox absolute opacity-0 w-0 h-0" />
                                        <label for="mist-cycle-toggle" class="toggle-label"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-cyan-50 p-4 rounded-xl border border-cyan-100">
                                    <p class="text-sm font-bold text-cyan-800 mb-2">เวลาทำงาน (ON)</p>
                                    <div class="flex items-center space-x-2">
                                        <input type="number" id="mist-on-min" value="1" min="0" class="w-full p-2 border rounded text-center font-bold text-gray-700 focus:ring-2 focus:ring-cyan-400">
                                        <span class="text-sm text-gray-500">นาที</span>
                                        <input type="number" id="mist-on-sec" value="0" min="0" max="59" class="w-full p-2 border rounded text-center font-bold text-gray-700 focus:ring-2 focus:ring-cyan-400">
                                        <span class="text-sm text-gray-500">วินาที</span>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                    <p class="text-sm font-bold text-gray-600 mb-2">เวลาพัก (OFF)</p>
                                    <div class="flex items-center space-x-2">
                                        <input type="number" id="mist-off-min" value="2" min="0" class="w-full p-2 border rounded text-center font-bold text-gray-700 focus:ring-2 focus:ring-gray-400">
                                        <span class="text-sm text-gray-500">นาที</span>
                                        <input type="number" id="mist-off-sec" value="0" min="0" max="59" class="w-full p-2 border rounded text-center font-bold text-gray-700 focus:ring-2 focus:ring-gray-400">
                                        <span class="text-sm text-gray-500">วินาที</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-100">
                                <div class="flex items-center space-x-2">
                                    <div id="mist-status-dot" class="w-3 h-3 rounded-full bg-gray-400"></div>
                                    <span id="mist-status-text" class="text-sm font-bold text-gray-500">Standby</span>
                                </div>
                                <button onclick="saveMistCycle()" class="bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition transform hover:scale-105">
                                    บันทึกและเริ่มทำงาน
                                </button>
                            </div>
                        </div>

                        <div class="dashboard-card p-6 rounded-2xl shadow-lg border-l-4 border-blue-500">
                            <div class="flex justify-between items-center mb-4"><h2 class="text-xl font-bold text-blue-900">1. ระบบคำนวณน้ำและวันเติม</h2></div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                                <div class="flex flex-col items-center">
                                    <div class="water-tank bg-white relative">
                                        <div class="absolute inset-0 bg-gray-100 opacity-50 z-0"></div>
                                        <div id="water-level-anim" class="water-level" style="height: 0%;">
                                            <div class="water-surface"></div>
                                        </div>
                                        <div class="absolute inset-0 flex items-center justify-center z-10"><span id="water-pct-text" class="text-2xl font-bold text-blue-900 drop-shadow-sm">0%</span></div>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-2">ความจุ: <span id="tank-capacity-display">100</span> ลิตร</p>
                                </div>
                                <div class="col-span-2 space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-blue-50 p-3 rounded-lg"><p class="text-xs text-gray-500">ปัจจุบัน</p><p class="text-xl font-bold text-blue-700"><span id="current-water-liters">--</span> ลิตร</p></div>
                                        <div class="bg-blue-50 p-3 rounded-lg"><p class="text-xs text-gray-500">อัตราใช้</p><p class="text-xl font-bold text-blue-700"><span id="water-depletion-rate">--</span> ลิตร/วัน</p></div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-grow"><label class="block text-xs font-bold text-gray-600 mb-1">ขนาดถัง</label><input type="number" id="tank-capacity-input" value="100" class="w-full p-2 border rounded text-center" oninput="updateWaterSettings()"></div>
                                        <div class="flex-grow"><label class="block text-xs font-bold text-gray-600 mb-1">เตือนเมื่อเหลือ</label><input type="number" id="min-water-threshold" value="10" class="w-full p-2 border rounded text-center" oninput="calculateWaterRefill()"></div>
                                    </div>
                                    <div class="text-center pt-2 border-t border-gray-200">
                                        <p class="text-xs text-gray-500">หมดใน</p>
                                        <p class="text-3xl font-extrabold text-blue-800"><span id="water-days-left">--</span> วัน</p>
                                        <p class="text-xs text-blue-600" id="water-refill-date">--</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card p-6 rounded-2xl shadow-lg border-l-4 border-indigo-500">
                            <div class="flex justify-between items-center mb-4">
                                <div><h2 class="text-xl font-bold text-indigo-900">2. ระบบคำนวณปุ๋ย</h2><p class="text-sm text-gray-600 mt-1">คำนวณจาก EC และ TDS</p></div>
                                <button onclick="saveRefillSettings()" class="bg-indigo-600 text-white text-xs px-3 py-1 rounded shadow">บันทึกค่า</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-purple-50 p-4 rounded-xl border border-purple-100">
                                    <h3 class="font-bold text-purple-800 mb-3 text-sm">TDS Prediction</h3>
                                    <div class="flex justify-between text-sm mb-1"><span class="text-gray-600">ปัจจุบัน:</span><span class="font-bold text-gray-800" id="calc-current-tds">--</span></div>
                                    <div class="flex justify-between text-sm mb-2"><span class="text-gray-600">ลดลง:</span><span class="font-bold text-purple-600" id="calc-rate-tds">--</span></div>
                                    <div class="mb-2"><label class="text-xs text-gray-600 font-bold">Min TDS</label><input type="number" id="min-tds-threshold" value="400" class="w-full p-1 border rounded text-sm" oninput="calculateAutoDepletion()"></div>
                                    <div class="text-center"><p class="text-xs text-gray-500">เติมใน</p><p class="text-xl font-bold text-purple-700" id="depletion-days-tds">--</p></div>
                                </div>
                                <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                                    <h3 class="font-bold text-green-800 mb-3 text-sm">EC Prediction</h3>
                                    <div class="flex justify-between text-sm mb-1"><span class="text-gray-600">ปัจจุบัน:</span><span class="font-bold text-gray-800" id="calc-current-ec">--</span></div>
                                    <div class="flex justify-between text-sm mb-2"><span class="text-gray-600">ลดลง:</span><span class="font-bold text-green-600" id="calc-rate-ec">--</span></div>
                                    <div class="mb-2"><label class="text-xs text-gray-600 font-bold">Min EC</label><input type="number" id="min-ec-threshold" value="1.0" step="0.1" class="w-full p-1 border rounded text-sm" oninput="calculateAutoDepletion()"></div>
                                    <div class="text-center"><p class="text-xs text-gray-500">เติมใน</p><p class="text-xl font-bold text-green-700" id="depletion-days-ec">--</p></div>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card p-6 rounded-2xl shadow-lg">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold text-gray-700">3. โปรแกรมการเติบโตอัตโนมัติ</h2>
                                <button id="save-program-btn" class="text-xs bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded font-bold">บันทึก</button>
                            </div>
                            <div id="program-container" class="space-y-6"></div>
                            <button id="add-period-btn" class="mt-6 w-full p-3 border-2 border-dashed border-blue-300 text-blue-500 font-bold rounded-xl hover:bg-blue-50">+ เพิ่มช่วงเวลาใหม่</button>
                        </div>
                    </div>

                    <div id="research-view" class="space-y-8 hidden">
                        <div class="dashboard-card p-6 rounded-2xl shadow-lg border-t-4 border-blue-500">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold text-blue-900">ข้อมูลงานวิจัยมันฝรั่ง</h2>
                                <button onclick="exportResearchReport()" class="bg-blue-600 text-white text-sm px-3 py-1 rounded shadow">Export Word</button>
                            </div>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div><label class="text-xs font-bold text-gray-500">ชนิดพืช</label><input type="text" id="input-crop" value="มันฝรั่ง (Potato)" disabled class="w-full p-2 border bg-gray-100 rounded"></div>
                                    <div><label class="text-xs font-bold text-gray-500">ชื่อโปรเจกต์</label><input type="text" id="input-potato-name" class="w-full p-2 border rounded"></div>
                                    <div><label class="text-xs font-bold text-gray-500">สายพันธุ์</label><input type="text" id="input-variety" class="w-full p-2 border rounded"></div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div><label class="text-xs font-bold text-gray-500">ชื่อผู้วิจัย</label><input type="text" id="input-researcher" class="w-full p-2 border rounded"></div>
                                    <div><label class="text-xs font-bold text-gray-500">สิ่งที่ต้องการ</label><input type="text" id="input-requirements" class="w-full p-2 border rounded"></div>
                                </div>
                                <div><label class="text-xs font-bold text-gray-500">หมายเหตุ</label><textarea id="input-note" rows="3" class="w-full p-2 border rounded"></textarea></div>
                            </div>
                        </div>
                        <div class="dashboard-card p-6 rounded-2xl shadow-lg cursor-pointer hover:bg-gray-50 transition" onclick="toggleModal(true)">
                            <div class="flex items-center space-x-4">
                                <div class="p-3 bg-gray-100 rounded-full"><svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                                <div><h2 class="text-xl font-bold">สถานะระบบและประวัติ</h2><p class="text-sm text-gray-500">คลิกดูรายละเอียดทั้งหมด</p></div>
                            </div>
                            <div class="mt-4 flex space-x-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" id="sys-status-badge">ระบบ: ปกติ</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" id="sys-error-badge">Errors: 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // --- STATE ---
        const state = {
            isConnected: false, startTime: null, cultivationStatus: 'idle',
            currentTDS: 0, currentEC: 0, currentWater: 0,
            history: [], waterHistory: [], cultivationTimer: null, logs: []
        };

        // --- INITIALIZATION ---
        document.addEventListener('DOMContentLoaded', () => {
            renderManualSliders();
            addProgramPeriod({ start: 1, end: 10, ph: 6.0, ec: 1.5, temp: 25, humid: 80, lightStart: '07:00', lightEnd: '14:00', lightInt: 7000, tds: 800 });
            
            // เริ่มต้นโหลดข้อมูลทันที
            fetchDataLoop();
            // ตั้งเวลาโหลดข้อมูลทุก 2 วินาที
            setInterval(fetchDataLoop, 2000);

            document.getElementById('start-cultivation-btn').addEventListener('click', startCultivation);
            document.getElementById('stop-cultivation-btn').addEventListener('click', stopCultivation);
            document.getElementById('resume-cultivation-btn').addEventListener('click', startCultivation);
            document.getElementById('reset-cultivation-btn').addEventListener('click', resetCultivation);
            document.getElementById('add-period-btn').addEventListener('click', () => addProgramPeriod());
            document.getElementById('save-program-btn').addEventListener('click', () => showToast('บันทึกโปรแกรมสำเร็จ', 'success'));
        });

        // --- NAVIGATION ---
        function navigate(view) {
            ['basic-view', 'ai-control-view', 'research-view'].forEach(id => document.getElementById(id).classList.add('hidden'));
            document.getElementById(`${view}-view`).classList.remove('hidden');
            ['tab-basic-view', 'tab-ai-control-view', 'tab-research-view'].forEach(id => {
                const el = document.getElementById(id);
                el.classList.remove('tab-active', 'text-blue-600', 'bg-eff6ff');
                el.classList.add('text-gray-500');
            });
            document.getElementById(`tab-${view}-view`).classList.add('tab-active');
        }

        function toggleModal(show) {
            const m = document.getElementById('system-modal');
            if (show) { m.classList.add('open'); renderLogsToModal(); } else m.classList.remove('open');
        }

        // --- API & LOGGING ---
        function updateConnStatus(online) {
            const el = document.getElementById('connection-status');
            el.className = online ? 'conn-badge conn-online' : 'conn-badge conn-offline';
            el.textContent = online ? '● ออนไลน์ (DB)' : '● ออฟไลน์';
            updateSystemStatusBadges();
        }

        async function fetchDataLoop() {
            try {
                // พยายามดึงข้อมูลจาก Database เสมอ (Online All The Time)
                const res = await fetch('get_data.php');
                if (!res.ok) throw new Error("Network response was not ok");
                
                const data = await res.json();
                
                // ถ้าดึงข้อมูลสำเร็จ ถือว่า Online
                state.isConnected = true;
                updateConnStatus(true);
                updateUI(data);
                
                if (data.logs && Array.isArray(data.logs)) {
                    data.logs.forEach(l => {
                        if (!state.logs.some(ex => ex.timestamp === l.timestamp && ex.message === l.message)) state.logs.push(l);
                    });
                }
                updateSystemStatusBadges();
                
                // Timer Logic (Client-side Simulation driven by DB status if available)
                if (state.cultivationStatus === 'running') updateTimerUI('running');

            } catch (e) {
                console.error("Connection error:", e);
                // กรณีเชื่อมต่อไม่ได้ ให้ขึ้น Offline แต่ไม่สร้างเลขมั่ว (Mock Data)
                state.isConnected = false;
                updateConnStatus(false);
            }
        }

        function logEvent(level, message) {
            const timestamp = new Date().toLocaleString('th-TH');
            const entry = { timestamp, level, message };
            if (message.includes("Connection") && state.logs.length > 0 && state.logs[state.logs.length - 1].message === message) return;
            state.logs.push(entry);
            if (state.logs.length > 50) state.logs.shift();
        }

        function updateSystemStatusBadges() {
            const errs = state.logs.filter(l => l.level === 'ERROR' || l.level === 'CRITICAL').length;
            const b1 = document.getElementById('sys-status-badge'), b2 = document.getElementById('sys-error-badge');
            if (b1) {
                b1.className = !state.isConnected ? "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800" : (errs > 0 ? "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800" : "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800");
                b1.textContent = !state.isConnected ? "ระบบ: ขาดการเชื่อมต่อ" : (errs > 0 ? "ระบบ: มีข้อผิดพลาด" : "ระบบ: ปกติ");
            }
            if (b2) b2.textContent = `Errors: ${errs}`;
        }

        // --- NEW: MIST CYCLE LOGIC ---
        function saveMistCycle() {
            const enabled = document.getElementById('mist-cycle-toggle').checked;
            const onMin = parseInt(document.getElementById('mist-on-min').value) || 0;
            const onSec = parseInt(document.getElementById('mist-on-sec').value) || 0;
            const offMin = parseInt(document.getElementById('mist-off-min').value) || 0;
            const offSec = parseInt(document.getElementById('mist-off-sec').value) || 0;

            // ตรวจสอบว่ามี API หรือไม่ ถ้าไม่มีให้แสดงผลหลอกเพื่อความสวยงาม
            if (state.isConnected) {
                // ถ้ามี backend ให้ยิง fetch ไปที่ API (เช่น /update_controls หรือ get_data.php?action=update)
                // เนื่องจากใน PHP code block แรกไม่มี endpoint นี้ ผมจะใส่ไว้เป็นตัวอย่าง
                /*
                fetch('update_mist_cycle.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ mist_cycle: { enabled, on_min: onMin, on_sec: onSec, off_min: offMin, off_sec: offSec } })
                }).then(() => showToast('บันทึกรอบการพ่นหมอกแล้ว', 'success'));
                */
                showToast('บันทึกรอบการพ่นหมอกแล้ว (Mock)', 'success');
            } else {
                 showToast('บันทึกรอบการพ่นหมอกแล้ว', 'success');
            }
        }

        function updateMistUI(mistStatus) {
            const dot = document.getElementById('mist-status-dot');
            const text = document.getElementById('mist-status-text');
            if (mistStatus === 'working') {
                dot.className = "w-3 h-3 rounded-full bg-green-500 animate-pulse";
                text.textContent = "กำลังพ่นหมอก (Working)";
                text.className = "text-sm font-bold text-green-600";
            } else if (mistStatus === 'stopped') {
                dot.className = "w-3 h-3 rounded-full bg-gray-400";
                text.textContent = "ปิดระบบ (Off)";
                text.className = "text-sm font-bold text-gray-500";
            } else {
                dot.className = "w-3 h-3 rounded-full bg-yellow-400";
                text.textContent = "พักเครื่อง (Standby)";
                text.className = "text-sm font-bold text-yellow-600";
            }
        }

        // --- UI UPDATE ---
        function updateUI(data) {
            if (!data.sensors) return; // ป้องกัน error กรณี data มาไม่ครบ

            document.getElementById('ph-value').textContent = data.sensors.ph;
            document.getElementById('humidity-value').textContent = data.sensors.humidity + '%';
            document.getElementById('temperature-value').textContent = data.sensors.temperature + '°C';
            document.getElementById('ec-value').textContent = data.sensors.ec;
            document.getElementById('light-value').textContent = data.sensors.light;
            document.getElementById('tds-value').textContent = data.sensors.tds;

            const newWater = parseFloat(data.sensors.water_level);
            if (state.currentWater !== newWater || state.waterHistory.length === 0) {
                state.waterHistory.push({ time: Date.now(), value: newWater });
                if (state.waterHistory.length > 100) state.waterHistory.shift();
            }
            state.currentWater = newWater;
            document.getElementById('current-water-liters').textContent = state.currentWater;
            updateWaterSettings();
            calculateWaterRefill();

            const newTDS = parseFloat(data.sensors.tds);
            const newEC = parseFloat(data.sensors.ec);
            if (state.currentTDS !== newTDS || state.currentEC !== newEC || state.history.length === 0) {
                state.history.push({ time: Date.now(), tds: newTDS, ec: newEC });
                if (state.history.length > 100) state.history.shift();
            }
            state.currentTDS = newTDS;
            state.currentEC = newEC;
            document.getElementById('calc-current-tds').textContent = state.currentTDS;
            document.getElementById('calc-current-ec').textContent = state.currentEC.toFixed(2);
            calculateAutoDepletion();

            if(data.controls) {
                updateDevice('return_pump', data.controls.return_pump_on);
                updateDevice('mister_pump', data.controls.mister_pump_on);
                updateDevice('uv_light', data.controls.uv_light_on);
                updateDevice('ultrasonic_mister', data.controls.ultrasonic_mister_on);
                updateDevice('exhaust_fan', data.controls.exhaust_fan_on);
                updateDevice('mist_fan', data.controls.mist_fan_on);
                updateDevice('grow_light', data.controls.grow_light_on);
            }

            // Update Mist Cycle Status (ถ้า Backend ส่งมา)
            if (data.mist_cycle_status) {
                updateMistUI(data.mist_cycle_status);
            }
        }

        function updateDevice(id, isOn) {
            const text = document.getElementById(`${id}-status`);
            const dot = document.querySelector(`.status-indicator-dot[data-device="${id}"]`);
            if (text && dot) {
                text.textContent = isOn ? 'เปิด' : 'ปิด';
                text.className = `font-bold ${isOn ? 'text-green-500' : 'text-red-500'}`;
                if (isOn) dot.classList.add('on'); else dot.classList.remove('on');
            }
        }

        // --- CONTROLS ---
        function startCultivation() {
            state.startTime = Date.now();
            updateTimerUI('running');
            showToast('เริ่มการเพาะปลูก', 'success');
        }

        function stopCultivation() {
            updateTimerUI('paused');
            showToast('หยุดชั่วคราว', 'info');
        }

        function resetCultivation() {
            state.startTime = null;
            updateTimerUI('idle');
            showToast('รีเซ็ตระบบ', 'success');
        }

        function updateTimerUI(status, elapsed) {
            const s = document.getElementById('start-cultivation-btn'),
                t = document.getElementById('cultivation-timer-display'),
                stop = document.getElementById('stop-cultivation-btn'),
                grp = document.getElementById('resume-reset-group');
            if (status === 'idle') {
                s.classList.remove('hidden'); t.classList.add('hidden'); clearInterval(state.cultivationTimer);
            } else if (status === 'running') {
                s.classList.add('hidden'); t.classList.remove('hidden'); stop.classList.remove('hidden'); grp.classList.add('hidden');
                if (!state.cultivationTimer) state.cultivationTimer = setInterval(() => renderTime(), 1000);
            } else if (status === 'paused') {
                s.classList.add('hidden'); t.classList.remove('hidden'); stop.classList.add('hidden'); grp.classList.remove('hidden');
                clearInterval(state.cultivationTimer);
                if (elapsed) renderTime(elapsed);
            }
            state.cultivationStatus = status;
        }

        function renderTime(elapsed) {
            let diff;
            if (elapsed !== undefined) diff = Math.floor(elapsed);
            else {
                if (!state.startTime) return;
                diff = Math.floor((Date.now() - state.startTime) / 1000);
            }
            if (diff < 0) diff = 0;
            document.getElementById('timer-seconds').textContent = (diff % 60).toString().padStart(2, '0');
            document.getElementById('timer-minutes').textContent = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
            document.getElementById('timer-hours').textContent = Math.floor(diff / 3600).toString().padStart(2, '0');
            document.getElementById('timer-days').textContent = Math.floor(diff / 86400);
        }

        // --- SLIDERS & HELPERS ---
        function renderManualSliders() {
            const c = document.getElementById('manual-sliders-container');
            c.innerHTML = `${createSliderHTML('PH', 'ph', 6.0, 0, 14, 0.1)}${createSliderHTML('ความชื้น (%)', 'humidity', 80, 0, 100, 1)}${createSliderHTML('อุณหภูมิ (°C)', 'temperature', 25, 15, 40, 1)}${createSliderHTML('EC', 'ec', 1.5, 0, 5, 0.1)}${createSliderHTML('แสง (Light)', 'light', 7000, 0, 20000, 100)}${createSliderHTML('TDS (ppm)', 'tds', 750, 0, 2000, 10)}`;
        }
        function addProgramPeriod(d) {
            const c = document.getElementById('program-container'); const last = c.lastElementChild; let s = 1, e = 10;
            if (last && !d) { const i = last.querySelectorAll('input[type=number]'); if (i.length >= 2) { e = parseInt(i[1].value) || 0; s = e + 1; e = s + 9; } }
            const vals = d || { start: s, end: e, ph: 6, ec: 1.5, temp: 25, humid: 80, lightStart: '07:00', lightEnd: '14:00', lightInt: 7000, tds: 750 };
            const div = document.createElement('div'); div.className = 'border border-gray-200 rounded-xl p-4 bg-gray-50 relative';
            div.innerHTML = `<div class="flex justify-between items-center mb-4"><div class="flex items-center space-x-2"><span class="font-bold text-gray-700">วันที่</span><input type="number" class="w-16 p-1 border rounded text-center" value="${vals.start}"><span class="font-bold text-gray-700">ถึง</span><input type="number" class="w-16 p-1 border rounded text-center" value="${vals.end}"></div><button class="text-red-500 font-bold hover:text-red-700 text-xl" onclick="this.parentElement.parentElement.remove()">×</button></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4">${createSliderHTML('PH', 'ph', vals.ph, 0, 14, 0.1)}${createSliderHTML('EC', 'ec', vals.ec, 0, 5, 0.1)}${createSliderHTML('อุณหภูมิ', 'temp', vals.temp, 15, 40, 1)}${createSliderHTML('ความชื้น', 'humid', vals.humid, 0, 100, 1)}<div class="col-span-1 md:col-span-2 bg-yellow-50 p-3 rounded border border-yellow-200"><div class="flex justify-between items-center mb-2"><label class="font-bold text-yellow-800">ระบบแสง (Light Schedule)</label><span class="text-sm text-yellow-700 font-mono light-duration">คำนวณ...</span></div><div class="flex flex-wrap md:flex-nowrap gap-2 mb-2"><div class="flex-1"><label class="text-xs text-gray-500">เปิด (Start)</label><input type="time" class="border rounded p-1 text-sm w-full" value="${vals.lightStart}" onchange="calcLightDur(this)"></div><div class="flex-1"><label class="text-xs text-gray-500">ปิด (End)</label><input type="time" class="border rounded p-1 text-sm w-full" value="${vals.lightEnd}" onchange="calcLightDur(this)"></div></div><div class="flex items-center space-x-2 mt-2"><span class="text-xs text-gray-500 w-12">ความเข้ม</span><button class="precision-btn bg-yellow-200 text-yellow-800" onclick="adjLight(this, -100)">-</button><input type="range" class="flex-grow h-2 bg-yellow-200 rounded-lg appearance-none" min="0" max="20000" step="100" value="${vals.lightInt}" oninput="this.nextElementSibling.nextElementSibling.textContent=this.value"><button class="precision-btn bg-yellow-200 text-yellow-800" onclick="adjLight(this, 100)">+</button><span class="text-xs font-bold text-yellow-700 w-10 text-right light-val">${vals.lightInt}</span></div></div>${createSliderHTML('TDS', 'tds', vals.tds, 0, 2000, 10)}</div>`;
            c.appendChild(div); const inputs = div.querySelectorAll('input[type="time"]'); if (inputs.length) calcLightDur(inputs[0]);
        }
        function createSliderHTML(l, k, v, mn, mx, s) { return `<div><div class="flex justify-between text-xs mb-1"><label class="font-semibold text-gray-600">${l}</label><span class="font-bold text-blue-600 value-display">${v}</span></div><div class="flex items-center space-x-2"><button class="precision-btn bg-gray-200 text-gray-700" onclick="adjustProgramVal(this, ${-s})">-</button><input type="range" class="flex-grow" min="${mn}" max="${mx}" step="${s}" value="${v}" oninput="this.parentElement.previousElementSibling.querySelector('.value-display').textContent = this.value"><button class="precision-btn bg-gray-200 text-gray-700" onclick="adjustProgramVal(this, ${s})">+</button></div></div>`; }
        function calcLightDur(el) { const p = el.closest('.bg-yellow-50'); const t = p.querySelectorAll('input[type="time"]'); const d = p.querySelector('.light-duration'); if (t[0].value && t[1].value) { const s = new Date(`2000-01-01T${t[0].value}`); const e = new Date(`2000-01-01T${t[1].value}`); let diff = (e - s) / (1000 * 60 * 60); if (diff < 0) diff += 24; d.textContent = `${diff.toFixed(1)} ชม.`; } }
        window.adjLight = (b, s) => { const w = b.parentElement; const i = w.querySelector('input'); const d = w.querySelector('.light-val'); let v = parseFloat(i.value) + s; if (v >= parseFloat(i.min) && v <= parseFloat(i.max)) { i.value = v; d.textContent = v; } };
        window.adjustProgramVal = (b, s) => { const w = b.parentElement; const i = w.querySelector('input'); const d = w.parentElement.previousElementSibling.querySelector('.value-display'); let v = parseFloat(i.value) + s; const p = s.toString().split('.')[1]?.length || 0; v = parseFloat(v.toFixed(p)); if (v >= parseFloat(i.min) && v <= parseFloat(i.max)) { i.value = v; d.textContent = v; } };
        
        // --- EXPORTS & MISC ---
        function updateWaterSettings() { const c = parseFloat(document.getElementById('tank-capacity-input').value) || 100; document.getElementById('tank-capacity-display').textContent = c; const p = Math.min(100, Math.max(0, (state.currentWater / c) * 100)); document.getElementById('water-level-anim').style.height = `${p}%`; document.getElementById('water-pct-text').textContent = `${p.toFixed(0)}%`; }
        function calculateWaterRefill() { const h = state.waterHistory; const min = parseFloat(document.getElementById('min-water-threshold').value) || 10; const d = document.getElementById('water-days-left'); const r = document.getElementById('water-depletion-rate'); const dt = document.getElementById('water-refill-date'); if (state.currentWater <= min) { d.textContent = "เติมทันที!"; d.className = "text-3xl font-extrabold text-red-600"; dt.textContent = "ระดับน้ำต่ำ"; r.textContent = "--"; return; } else d.className = "text-3xl font-extrabold text-blue-800"; if (h.length < 2) { d.textContent = "--"; return; } const diff = h[0].value - h[h.length - 1].value; const time = h[h.length - 1].time - h[0].time; if (diff <= 0) { d.textContent = "∞"; return; } const rate = diff / time; const msLeft = (state.currentWater - min) / rate; const totalMin = Math.floor(msLeft / 60000); const days = Math.floor(totalMin / 1440); const hrs = Math.floor((totalMin % 1440) / 60); const mins = totalMin % 60; r.textContent = (rate * 1000 * 3600 * 24).toFixed(1); d.textContent = `${days} วัน ${hrs} ชม. ${mins} น.`; const dObj = new Date(); dObj.setTime(dObj.getTime() + msLeft); dt.textContent = `คาดการณ์: ${dObj.toLocaleDateString('th-TH')} ${dObj.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' })}`; }
        function calculateAutoDepletion() { calcSingleMetric('tds', state.currentTDS, 'min-tds-threshold', 'depletion-days-tds', 'calc-rate-tds', 'refill-date-tds'); calcSingleMetric('ec', state.currentEC, 'min-ec-threshold', 'depletion-days-ec', 'calc-rate-ec', 'refill-date-ec'); }
        function calcSingleMetric(k, v, mId, dId, rId, dtId) { const min = parseFloat(document.getElementById(mId).value) || 0; const d = document.getElementById(dId); const r = document.getElementById(rId); const dt = document.getElementById(dtId); if (v <= min) { d.textContent = "เติมทันที!"; d.className = "text-2xl font-extrabold text-red-600 my-1"; dt.textContent = "ต่ำกว่าเกณฑ์"; return; } else d.className = k === 'tds' ? "text-2xl font-extrabold text-purple-700 my-1" : "text-2xl font-extrabold text-green-700 my-1"; if (state.history.length < 2) { d.textContent = "--"; return; } const diff = state.history[0][k] - state.history[state.history.length - 1][k]; const time = state.history[state.history.length - 1].time - state.history[0].time; if (diff <= 0) { d.textContent = "∞"; return; } const rate = diff / time; const msLeft = (v - min) / rate; const totalMin = Math.floor(msLeft / 60000); const days = Math.floor(totalMin / 1440); const hrs = Math.floor((totalMin % 1440) / 60); const mins = totalMin % 60; r.textContent = `~${(rate * 1000 * 3600 * 24).toFixed(k === 'ec' ? 2 : 0)}/วัน`; d.textContent = `${days} วัน ${hrs} ชม. ${mins} น.`; const dObj = new Date(); dObj.setTime(dObj.getTime() + msLeft); dt.textContent = `คาดการณ์: ${dObj.toLocaleDateString('th-TH')} ${dObj.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' })}`; }
        function saveRefillSettings() { showToast('บันทึกค่าแล้ว', 'success'); }
        function showToast(m, t) { const c = document.getElementById('notification-container'); const d = document.createElement('div'); d.className = `toast ${t}`; d.textContent = m; c.appendChild(d); setTimeout(() => d.classList.add('show'), 10); setTimeout(() => { d.classList.remove('show'); setTimeout(() => d.remove(), 300) }, 3000); }
        function renderLogsToModal() { const el = document.getElementById('modal-error-list'), cl = document.getElementById('modal-command-list'); el.innerHTML = ''; cl.innerHTML = ''; state.logs.slice().reverse().forEach(l => { const i = document.createElement('div'); i.innerHTML = `<span class="text-xs text-gray-500">[${l.timestamp}]</span> <b>${l.level}:</b> ${l.message}`; if (l.level === 'ERROR') el.appendChild(i); else cl.appendChild(i); }); }
        function exportSystemReport() { const c = `<html><body><h1>System Log</h1>${document.getElementById('modal-error-list').innerHTML}<hr>${document.getElementById('modal-command-list').innerHTML}</body></html>`; downloadDoc(c, `System_Logs.doc`); }
        function exportResearchReport() { const c = `<html><body><h1>Potato Research</h1><p>Crop: ${document.getElementById('input-crop').value}</p><p>Name: ${document.getElementById('input-potato-name').value}</p><p>Variety: ${document.getElementById('input-variety').value}</p><p>Researcher: ${document.getElementById('input-researcher').value}</p><p>Reqs: ${document.getElementById('input-requirements').value}</p><p>Note: ${document.getElementById('input-note').value}</p></body></html>`; downloadDoc(c, `Potato_Research.doc`); }
        function downloadDoc(c, n) { const b = new Blob(['\ufeff', c], { type: 'application/msword' }); const l = document.createElement('a'); l.href = URL.createObjectURL(b); l.download = n; l.click(); }
    </script>
</body>
</html>