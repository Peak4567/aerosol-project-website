<?php
include('index.php');
?>
<style>
    /* * -- THEME: LIGHT --
     * Card: White background, light gray border, and a subtle shadow for depth.
     * Inputs: Light gray background, slightly darker border, and dark text.
     * Sliders & Toggles: Kept the green accent color for consistency, adapted for a light background.
    */
    .card { 
        background-color: #ffffff; 
        border: 1px solid #e2e8f0; /* slate-200 */
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    }
    .interactive-card:hover { 
        border-color: #cbd5e1; /* slate-300 */
        transform: translateY(-2px); 
    }
    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none; 
        appearance: none; 
        width: 20px; 
        height: 20px;
        background: #238636; 
        cursor: pointer; 
        border-radius: 50%; 
        border: 3px solid #ffffff; /* White border to stand out on the track */
    }
    /* Custom track color for range slider */
    input[type="range"]::-webkit-slider-runnable-track {
        background-color: #e2e8f0; /* slate-200 */
        height: 6px;
        border-radius: 3px;
    }
    .profile-input {
        background-color: #f8fafc; /* slate-50 */
        border: 1px solid #cbd5e1; /* slate-300 */
        color: #1e293b; /* slate-800 */
        border-radius: 6px; 
        padding: 8px 12px; 
        width: 100%;
    }
    /* AI Toggle Switch */
    #ai-toggle ~ .dot { background-color: #ffffff; }
    #ai-toggle ~ .block { background-color: #cbd5e1; } /* slate-300 */
    #ai-toggle:checked ~ .block { background-color: #238636; }
    #ai-toggle:checked ~ .dot { transform: translateX(1.5rem); }
</style>
<div class="p-4 sm:ml-64 bg-gray-100 min-h-screen">
    <div id="main-app">
        <div class="container mx-auto p-4 md:p-6">
            <header class="flex justify-between items-center text-center mb-8">
                <div class="w-1/3"></div>
                <div class="w-1/3">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800">AI อควาเซนต์</h1>
                    <p class="text-gray-600">ระบบอัจฉริยะควบคุมสภาพแวดล้อม</p>
                </div>
                <div class="w-1/3"></div> </header>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="card rounded-lg p-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">กราฟแสดงผลรวม</h2>
                        <div class="h-96"><canvas id="main-growth-chart"></canvas></div>
                    </div>
                    <div class="card rounded-lg p-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">โปรแกรมการเติบโตอัตโนมัติ</h2>
                        <div id="growth-profile-container" class="space-y-4"></div>
                        <button id="add-profile-stage-btn" class="mt-4 w-full text-center py-2 px-4 border-2 border-dashed border-gray-400 text-gray-500 hover:border-green-500 hover:text-green-500 rounded-lg transition-colors">+ เพิ่มช่วงเวลาใหม่</button>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="card rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-800">โหมดควบคุม AI</h2><label for="ai-toggle" class="flex items-center cursor-pointer">
                                <div class="relative"><input type="checkbox" id="ai-toggle" class="sr-only">
                                    <div class="block w-14 h-8 rounded-full"></div>
                                    <div class="dot absolute left-1 top-1 w-6 h-6 rounded-full transition"></div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="card rounded-lg p-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">สถานะระบบ</h2>
                        <div id="system-integrity-container" class="space-y-2 text-sm text-gray-700"></div>
                    </div>
                    <div id="control-panel" class="card rounded-lg p-4 space-y-4">
                        <h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">แผงควบคุม</h2>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        // --- SECTION 1: SIMULATION ENGINE & DATA STORAGE ---
        const SENSOR_CONFIG = {
            ph: { name: "PH", unit: "", min: 4, max: 8 },
            humidity: { name: "ความชื้น", unit: "%", min: 50, max: 95 },
            temperature: { name: "อุณหภูมิ", unit: "°C", min: 15, max: 30 },
            ec: { name: "ค่าปุ๋ย (EC)", unit: "", min: 1.0, max: 2.5 },
            light: { name: "ความเข้มแสง", unit: " lux", min: 2000, max: 12000 },
            shed: { name: "ความชื้นโรงเรือน", unit: "%", min: 60, max: 95 }
        };

        let plants = {};
        let sensorData = {};
        let sensorTargets = {};
        let sensorHistory = {};
        let day = 0;
        let growthProfiles = [];
        let systemErrors = {};
        let mainInterval;
        let isAI_On = true;

        function initializeData() {
            day = 0;
            isAI_On = true;
            plants = { "Plant_ID_01": { img: "https://placehold.co/400x225/1a2c1a/ffffff?text=Plant+A", history: { leafCount: [10], rootLength: [5], tuberCount: [0] }, events: [{ day: 0, event: "เริ่มการปลูก" }] } };
            growthProfiles = [
                { startDay: 1, endDay: 10, targets: { ph: 6.0, humidity: 80, temperature: 24, ec: 1.5, light: 7000, shed: 85 } }, 
                { startDay: 11, endDay: 20, targets: { ph: 7.0, humidity: 75, temperature: 22, ec: 1.8, light: 9000, shed: 80 } }
            ];
            Object.keys(SENSOR_CONFIG).forEach(key => {
                const config = SENSOR_CONFIG[key];
                const startValue = config.min + (config.max - config.min) * 0.6;
                sensorData[key] = startValue;
                sensorTargets[key] = startValue;
                sensorHistory[key] = [startValue];
            });
            systemErrors = {};
        }

        function applyAutoProfile() {
            if (!isAI_On) return;
            const activeProfile = growthProfiles.find(p => day >= p.startDay && day <= p.endDay);
            if (activeProfile) {
                Object.assign(sensorTargets, activeProfile.targets);
            }
        }

        function checkSystemIntegrity() {
            systemErrors = {};
            Object.keys(SENSOR_CONFIG).forEach(key => {
                const history = sensorHistory[key];
                if (history.length < 2) return;
                const config = SENSOR_CONFIG[key];
                const jumpThreshold = (config.max - config.min) * 0.5;
                if (Math.abs(history.at(-1) - history.at(-2)) > jumpThreshold) {
                    systemErrors[key] = "ค่าเปลี่ยนแปลงกะทันหัน!";
                }
                if (day > 5 && history.length > 3 && history.at(-1) === history.at(-2) && history.at(-2) === history.at(-3)) {
                    if (Math.random() < 0.2) systemErrors[key] = "เซ็นเซอร์อาจค้าง";
                }
            });
        }

        function simulateNewDay() {
            day++;
            applyAutoProfile();
            Object.keys(SENSOR_CONFIG).forEach(key => {
                const config = SENSOR_CONFIG[key];
                let currentValue = sensorData[key];
                if (isAI_On) {
                    currentValue += (sensorTargets[key] - currentValue) * 0.4;
                }
                currentValue += (Math.random() - 0.5) * (config.max - config.min) * 0.05;
                currentValue = Math.max(config.min, Math.min(config.max, currentValue));
                sensorData[key] = currentValue;
                sensorHistory[key].push(currentValue);
            });
            checkSystemIntegrity();
            updateUI();
        }

        // --- SECTION 2: UI RENDERING & UPDATES ---
        let mainGrowthChart;

        function updateUI() {
            document.getElementById('ai-toggle').checked = isAI_On;

            const integrityContainer = document.getElementById('system-integrity-container');
            integrityContainer.innerHTML = '';
            const errors = Object.keys(systemErrors);
            if (errors.length > 0) {
                errors.forEach(key => {
                    integrityContainer.innerHTML += `<div class="flex justify-between items-center"><span>${SENSOR_CONFIG[key].name}</span><span class="font-bold text-red-500">❌ ${systemErrors[key]}</span></div>`;
                });
            } else {
                integrityContainer.innerHTML = '<div class="text-center text-green-500 font-semibold">✅ ระบบทั้งหมดปกติ</div>';
            }

            for (const key in sensorData) {
                const currentValEl = document.getElementById(`current-val-${key}`);
                if (currentValEl) currentValEl.textContent = `${sensorData[key].toFixed(1)}${SENSOR_CONFIG[key].unit}`;

                const targetValEl = document.getElementById(`target-val-${key}`);
                if (targetValEl) targetValEl.textContent = `${sensorTargets[key].toFixed(1)}${SENSOR_CONFIG[key].unit}`;

                if (isAI_On) {
                    const slider = document.getElementById(`slider-${key}`);
                    if (slider) slider.value = sensorTargets[key];
                }
            }

            mainGrowthChart.data.labels = sensorHistory.ph.map((_, i) => `วันที่ ${i}`);
            Object.keys(sensorHistory).forEach((key, index) => {
                if (mainGrowthChart.data.datasets[index]) {
                    mainGrowthChart.data.datasets[index].data = sensorHistory[key];
                }
            });
            mainGrowthChart.update('none');
        }

        function renderControlPanel() {
            const panel = document.getElementById('control-panel');
            let html = `<h2 class="text-xl font-semibold text-gray-800 border-b border-gray-200 pb-2">แผงควบคุม</h2>`;
            for (const key in SENSOR_CONFIG) {
                const config = SENSOR_CONFIG[key];
                html += `
                <div class="space-y-2">
                    <div class="flex justify-between items-center"><label class="font-medium text-gray-700">${config.name}</label><span id="current-val-${key}" class="text-lg font-mono text-gray-800">${sensorData[key].toFixed(1)}${config.unit}</span></div>
                    <input type="range" id="slider-${key}" data-key="${key}" class="w-full h-[6px] rounded-lg appearance-none" min="${config.min}" max="${config.max}" step="${(config.max - config.min) / 100}" value="${sensorTargets[key]}">
                    <p class="text-right text-sm text-gray-500">เป้าหมาย: <span id="target-val-${key}" class="font-mono text-green-600">${sensorTargets[key].toFixed(1)}${config.unit}</span></p>
                </div>`;
            }
            panel.innerHTML = html;
        }

        function renderGrowthProfiles() {
            const container = document.getElementById('growth-profile-container');
            container.innerHTML = "";
            growthProfiles.forEach((profile, index) => {
                const stageEl = document.createElement("div");
                stageEl.className = "p-3 border border-gray-200 rounded-lg bg-gray-50";
                let targetsHtml = Object.keys(profile.targets).map(key =>
                    `<div class="flex items-center justify-between text-sm"><label class="text-gray-600">${SENSOR_CONFIG[key].name}</label><input type="number" data-key="${key}" value="${profile.targets[key]}" class="profile-input w-24"></div>`
                ).join('');
                stageEl.innerHTML = `<div class="flex justify-between items-center mb-2"><div class="flex items-center space-x-2 text-gray-800"><span>วันที่</span><input type="number" data-day="start" value="${profile.startDay}" class="profile-input w-16"><span>-</span><input type="number" data-day="end" value="${profile.endDay}" class="profile-input w-16"></div><button class="delete-stage-btn text-gray-400 hover:text-red-500 text-2xl font-bold" data-index="${index}">&times;</button></div><div class="grid grid-cols-2 gap-x-4 gap-y-1">${targetsHtml}</div>`;
                container.appendChild(stageEl);
            });
        }

        function initializeCharts() {
            // ** Chart.js colors updated for light theme **
            const chartOptions = {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    y: { ticks: { color: '#475569' /* slate-600 */ } },
                    x: { ticks: { color: '#475569' /* slate-600 */ } }
                },
                plugins: {
                    legend: { labels: { color: '#334155' /* slate-700 */ } }
                }
            };
            const colors = ['#238636', '#3b82f6', '#ca8a04', '#db2777', '#7c3aed', '#16a34a'];
            mainGrowthChart = new Chart(document.getElementById('main-growth-chart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: [],
                    datasets: Object.keys(SENSOR_CONFIG).map((key, index) => ({
                        label: SENSOR_CONFIG[key].name,
                        data: [],
                        borderColor: colors[index % colors.length],
                        tension: 0.3,
                        hidden: index > 1
                    }))
                },
                options: chartOptions
            });
        }

        // --- SECTION 3: EVENT LISTENERS & INITIALIZATION ---
        function attachAppEventListeners() {
            document.getElementById('ai-toggle').addEventListener('change', (e) => isAI_On = e.target.checked);

            const controlPanel = document.getElementById('control-panel');
            controlPanel.addEventListener('input', e => {
                if (e.target.type === 'range') {
                    const key = e.target.dataset.key;
                    const value = e.target.value;
                    document.getElementById(`target-val-${key}`).textContent = `${parseFloat(value).toFixed(1)}${SENSOR_CONFIG[key].unit}`;
                }
            });
            controlPanel.addEventListener('change', e => {
                if (e.target.type === 'range') {
                    isAI_On = false;
                    sensorTargets[e.target.dataset.key] = parseFloat(e.target.value);
                    updateUI();
                }
            });

            const profileContainer = document.getElementById('growth-profile-container');
            profileContainer.addEventListener("change", () => {
                growthProfiles = Array.from(profileContainer.querySelectorAll('.p-3.border')).map(el => {
                    let profile = { targets: {} };
                    profile.startDay = parseInt(el.querySelector('[data-day="start"]').value);
                    profile.endDay = parseInt(el.querySelector('[data-day="end"]').value);
                    el.querySelectorAll('input[data-key]').forEach(input => profile.targets[input.dataset.key] = parseFloat(input.value));
                    return profile;
                });
            });
            profileContainer.addEventListener("click", e => {
                if (e.target.classList.contains('delete-stage-btn')) {
                    growthProfiles.splice(e.target.dataset.index, 1);
                    renderGrowthProfiles();
                }
            });
            document.getElementById("add-profile-stage-btn").addEventListener("click", () => {
                const lastProfile = growthProfiles.at(-1) || { endDay: 0 };
                const newProfile = {
                    startDay: lastProfile.endDay + 1,
                    endDay: lastProfile.endDay + 10,
                    targets: Object.fromEntries(Object.keys(SENSOR_CONFIG).map(k => [k, SENSOR_CONFIG[k].min]))
                };
                growthProfiles.push(newProfile);
                renderGrowthProfiles();
            });
        }

        // --- MAIN EXECUTION: Start simulation on page load ---
        initializeData();
        initializeCharts();
        renderControlPanel();
        renderGrowthProfiles();
        attachAppEventListeners();
        updateUI();

        mainInterval = setInterval(simulateNewDay, 3000);
    });
</script>