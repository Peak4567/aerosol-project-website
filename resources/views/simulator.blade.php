<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensor Data Simulator | Aerosol</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="font-body flex min-h-screen items-center justify-center bg-slate-100 p-4 antialiased">
    <div class="w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-8 shadow-xl animate-fade-in">
        <div class="mb-6 text-center">
            <h1 class="text-3xl font-bold text-slate-800">
                🎮 Data Simulator
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                ระบบจำลองการส่งค่าเซ็นเซอร์เข้า Database (ESP32 Simulation)
            </p>
        </div>

        <div class="mb-6 flex flex-col items-center space-y-6">
            <!-- Status Pill -->
            <div id="sim-status-pill" class="rounded-full border border-red-200 bg-red-100 text-red-600 px-6 py-2 text-sm font-bold shadow-xs transition-all duration-300">
                ● หยุดการทำงาน (Stopped)
            </div>

            <!-- Big Toggle Switch -->
            <label class="relative flex cursor-pointer items-center">
                <input type="checkbox" id="sim-toggle" onchange="toggleSimulator(this.checked)" class="peer sr-only">
                <div class="h-10 w-20 rounded-full bg-slate-300 transition-colors duration-300 peer-checked:bg-emerald-600 after:absolute after:left-1 after:top-1 after:h-8 after:w-8 after:rounded-full after:bg-white after:shadow-md after:transition-all after:duration-300 after:content-[''] peer-checked:after:translate-x-10"></div>
            </label>

            <p class="text-xs text-slate-400">
                ส่งข้อมูลสุ่มเสมือนจริงทุกๆ 3 วินาทีไปยัง <code class="bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded">/api/simulate</code>
            </p>
        </div>

        <!-- Terminal Log Screen -->
        <div class="rounded-xl bg-slate-900 p-4 font-mono text-xs shadow-inner">
            <div class="mb-2 flex items-center justify-between border-b border-slate-800 pb-2 text-slate-400">
                <span class="flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-red-500 inline-block"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-yellow-500 inline-block"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-green-500 inline-block"></span>
                    <span class="ml-1 text-slate-400">Terminal Log</span>
                </span>
                <button onclick="clearLogs()" class="text-[11px] text-slate-400 hover:text-white transition">Clear</button>
            </div>

            <div id="log-container" class="h-48 overflow-y-auto space-y-1.5 text-slate-300">
                <div class="text-slate-500 italic">พร้อมเริ่มการจำลอง... เปิดสวิตช์ด้านบนเพื่อเริ่มต้น</div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between text-xs text-slate-500">
            <a href="{{ route('dashboard') }}" target="_blank" class="text-emerald-600 hover:underline font-semibold flex items-center gap-1">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>เปิดดูบน Dashboard</span>
            </a>
            <a href="{{ route('home') }}" class="hover:underline">
                กลับหน้าหลัก
            </a>
        </div>
    </div>

    <script>
        let intervalId = null;
        let isRunning = false;

        function addLog(text, isError = false) {
            const container = document.getElementById('log-container');
            const el = document.createElement('div');
            el.className = isError ? 'text-red-400 font-semibold' : 'text-emerald-400';
            el.textContent = text;
            container.appendChild(el);
            container.scrollTop = container.scrollHeight;
        }

        function clearLogs() {
            document.getElementById('log-container').innerHTML = '<div class="text-slate-500 italic">Logs cleared.</div>';
        }

        async function insertSimData() {
            try {
                const res = await fetch('/api/simulate');
                const result = await res.json();
                if (result.status === 'success') {
                    const d = result.data;
                    const time = new Date(d.time).toLocaleTimeString();
                    addLog(`[${time}] 🌡️Temp:${d.temp}°C 💧Hum:${d.humid}% 🧪pH:${d.ph} ⚡EC:${d.ec} 🧪TDS:${d.tds}`);
                } else {
                    addLog(`❌ Error: ${result.message}`, true);
                }
            } catch (e) {
                addLog('⚠️ Connection Error!', true);
            }
        }

        function toggleSimulator(checked) {
            isRunning = checked;
            const pill = document.getElementById('sim-status-pill');

            if (intervalId) clearInterval(intervalId);

            if (isRunning) {
                pill.className = "rounded-full border border-green-200 bg-green-100 text-green-600 px-6 py-2 text-sm font-bold shadow-xs transition-all duration-300 animate-pulse";
                pill.textContent = "● กำลังจำลองข้อมูล... (Running)";
                addLog("🚀 ระบบเริ่มต้นส่งข้อมูลจำลอง...");
                insertSimData();
                intervalId = setInterval(insertSimData, 3000);
            } else {
                pill.className = "rounded-full border border-red-200 bg-red-100 text-red-600 px-6 py-2 text-sm font-bold shadow-xs transition-all duration-300";
                pill.textContent = "● หยุดการทำงาน (Stopped)";
                addLog("⏹️ ระบบหยุดการทำงาน.");
            }
        }
    </script>
</body>
</html>

