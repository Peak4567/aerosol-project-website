<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Aerosol - Data Simulator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <style> body { font-family: 'Sarabun', sans-serif; } </style>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-lg border border-slate-200">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">🎮 Data Simulator</h1>
            <p class="text-slate-500 text-sm mt-1">ระบบจำลองการส่งค่าเซ็นเซอร์เข้า Database</p>
        </div>

        <div class="flex flex-col items-center space-y-6 mb-6">
            <div id="status-badge" class="px-6 py-2 rounded-full bg-red-100 text-red-600 font-bold text-sm transition-all duration-300 shadow-sm border border-red-200">
                ● หยุดการทำงาน (Stopped)
            </div>

            <label for="toggle" class="flex items-center cursor-pointer relative">
                <input type="checkbox" id="toggle" class="sr-only" onchange="handleToggleChange()">
                <div class="w-20 h-10 bg-gray-300 rounded-full shadow-inner transition duration-300 ease-in-out toggle-bg"></div>
                <div class="dot absolute w-8 h-8 bg-white rounded-full shadow-md left-1 top-1 transition duration-300 ease-in-out transform"></div>
            </label>
            <p class="text-xs text-gray-400">กดปุ่มเพื่อ เปิด/ปิด การจำลองข้อมูล (ทุก 3 วินาที)</p>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <div class="flex justify-between items-center mb-2">
            <span class="text-xs font-bold text-gray-500 uppercase">Live Logs</span>
            <button onclick="document.getElementById('console-log').innerHTML=''" class="text-xs text-blue-500 hover:underline">Clear</button>
        </div>
        <div class="bg-slate-900 text-green-400 p-4 rounded-lg font-mono text-xs h-64 overflow-y-auto shadow-inner leading-relaxed" id="console-log">
            <p class="opacity-30">-- Ready to start --</p>
        </div>
    </div>

    <style>
        /* Custom Toggle Style */
        input:checked ~ .toggle-bg { background-color: #3b82f6; } /* สีฟ้า */
        input:checked ~ .dot { transform: translateX(100%); left: 2px; }
    </style>

    <script>
        let intervalId = null;
        const toggleSwitch = document.getElementById('toggle');
        const statusBadge = document.getElementById('status-badge');
        const consoleLog = document.getElementById('console-log');

        // --- 1. ตรวจสอบสถานะเมื่อโหลดหน้าเว็บ ---
        document.addEventListener('DOMContentLoaded', () => {
            // อ่านค่าจาก LocalStorage
            const isRunning = localStorage.getItem('sim_running') === 'true';
            
            if (isRunning) {
                toggleSwitch.checked = true; // ติ๊กถูกที่ปุ่ม
                toggleSimulation(true);      // สั่งเริ่มทำงาน (true = เป็นการโหลดครั้งแรก)
            }
        });

        // --- 2. ฟังก์ชันเมื่อกดปุ่ม ---
        function handleToggleChange() {
            // บันทึกสถานะลงเครื่องผู้ใช้ทันที
            localStorage.setItem('sim_running', toggleSwitch.checked);
            toggleSimulation(false);
        }

        // --- 3. ฟังก์ชันควบคุมการทำงาน ---
        function toggleSimulation(isAutoStart = false) {
            if (toggleSwitch.checked) {
                // เริ่มทำงาน
                statusBadge.className = "px-6 py-2 rounded-full bg-green-100 text-green-600 font-bold text-sm shadow-sm border border-green-200 animate-pulse";
                statusBadge.innerHTML = "● กำลังจำลองข้อมูล... (Running)";
                
                if (isAutoStart) {
                    log("🔄 System Resumed (Auto-start)...");
                } else {
                    log("🚀 System Started...");
                }
                
                // เรียกครั้งแรกทันที
                insertData();
                // เคลียร์ interval เก่าก่อนเสมอเพื่อกันการซ้อนทับ
                if (intervalId) clearInterval(intervalId);
                // ทำซ้ำทุก 3 วินาที
                intervalId = setInterval(insertData, 3000); 

            } else {
                // หยุดทำงาน
                statusBadge.className = "px-6 py-2 rounded-full bg-red-100 text-red-600 font-bold text-sm shadow-sm border border-red-200";
                statusBadge.innerHTML = "● หยุดการทำงาน (Stopped)";
                
                if (intervalId) clearInterval(intervalId);
                intervalId = null; // Reset ID
                
                if (!isAutoStart) {
                    log("🛑 System Stopped.");
                }
            }
        }

        async function insertData() {
            try {
                // เรียกไฟล์ API
                const response = await fetch('api_simulate.php');
                const result = await response.json();

                if (result.status === 'success') {
                    const d = result.data;
                    // Format ข้อความให้ดูง่าย
                    const msg = `[${d.time.split(' ')[1]}] 🌡️Temp:${d.temp} 💧Hum:${d.humid}% 🧪pH:${d.ph} ⚡EC:${d.ec}`;
                    log(msg);
                } else {
                    log("❌ Error: " + result.message, true);
                }
            } catch (error) {
                log("⚠️ Connection Error! (Check console)", true);
                console.error(error);
            }
        }

        function log(message, isError = false) {
            const p = document.createElement('div');
            p.textContent = "> " + message;
            p.className = isError ? "text-red-400 border-l-2 border-red-500 pl-2 mb-1" : "mb-1 hover:text-green-300";
            
            consoleLog.appendChild(p);
            consoleLog.scrollTop = consoleLog.scrollHeight; // Auto scroll
        }
    </script>
</body>
</html>