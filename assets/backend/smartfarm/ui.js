// --- Navigation & Modal ---
function navigate(view) {
    ['basic-view', 'ai-control-view', 'research-view'].forEach(id => document.getElementById(id).classList.add('hidden'));
    document.getElementById(`${view}-view`).classList.remove('hidden');

    ['tab-basic-view', 'tab-ai-control-view', 'tab-research-view'].forEach(id => {
        const el = document.getElementById(id);
        el.classList.remove('tab-active', 'text-blue-600', 'bg-eff6ff');
        el.classList.add('text-gray-500');
    });
    document.getElementById(`tab-${view}-view`).classList.add('tab-active');
    document.getElementById(`tab-${view}-view`).classList.remove('text-gray-500');
}

function toggleModal(show) {
    const modal = document.getElementById('system-modal');
    if (show) {
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
        renderLogsToModal();
    } else {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }
}

// --- Main UI Update Loop ---
function updateUI(data) {
    // Update Sensor Text
    const mappings = {
        'ph-value': data.sensors.ph,
        'humidity-value': data.sensors.humidity + '%',
        'temperature-value': data.sensors.temperature + '°C',
        'ec-value': data.sensors.ec,
        'light-value': data.sensors.light,
        'tds-value': data.sensors.tds
    };
    for (const [id, val] of Object.entries(mappings)) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    // Water Logic
    const newWater = parseFloat(data.sensors.water_level);
    if (state.currentWater !== newWater || state.waterHistory.length === 0) {
        state.waterHistory.push({ time: Date.now(), value: newWater });
        if (state.waterHistory.length > 100) state.waterHistory.shift();
    }
    state.currentWater = newWater;
    document.getElementById('current-water-liters').textContent = state.currentWater;
    updateWaterSettings();
    calculateWaterRefill();

    // Fertilizer Logic
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

    // Devices Status
    updateDevice('return_pump', data.controls.return_pump_on);
    updateDevice('mister_pump', data.controls.mister_pump_on);
    updateDevice('uv_light', data.controls.uv_light_on);
    updateDevice('ultrasonic_mister', data.controls.ultrasonic_mister_on);
    updateDevice('exhaust_fan', data.controls.exhaust_fan_on);
    updateDevice('mist_fan', data.controls.mist_fan_on);
    updateDevice('grow_light', data.controls.grow_light_on);
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

// --- Timer UI ---
function updateTimerUI(status, elapsed) {
    const s = document.getElementById('start-cultivation-btn');
    const t = document.getElementById('cultivation-timer-display');
    const stop = document.getElementById('stop-cultivation-btn');
    const grp = document.getElementById('resume-reset-group');

    if (status === 'idle') {
        s.classList.remove('hidden');
        t.classList.add('hidden');
        clearInterval(state.cultivationTimer);
    } else if (status === 'running') {
        s.classList.add('hidden');
        t.classList.remove('hidden');
        stop.classList.remove('hidden');
        grp.classList.add('hidden');
        if (!state.cultivationTimer) state.cultivationTimer = setInterval(() => renderTime(), 1000);
    } else if (status === 'paused') {
        s.classList.add('hidden');
        t.classList.remove('hidden');
        stop.classList.add('hidden');
        grp.classList.remove('hidden');
        clearInterval(state.cultivationTimer);
        if (elapsed) renderTime(elapsed);
    }
    state.cultivationStatus = status;
}

function renderTime(elapsed) {
    let diff;
    if (elapsed !== undefined) {
        diff = Math.floor(elapsed);
    } else {
        if (!state.startTime) return;
        diff = Math.floor((Date.now() - state.startTime * 1000) / 1000);
    }
    if (diff < 0) diff = 0;
    document.getElementById('timer-seconds').textContent = (diff % 60).toString().padStart(2, '0');
    document.getElementById('timer-minutes').textContent = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
    document.getElementById('timer-hours').textContent = Math.floor(diff / 3600).toString().padStart(2, '0');
    document.getElementById('timer-days').textContent = Math.floor(diff / 86400);
}

// --- Calculations & Display ---
function updateWaterSettings() {
    const capacity = parseFloat(document.getElementById('tank-capacity-input').value) || 100;
    document.getElementById('tank-capacity-display').textContent = capacity;
    const pct = Math.min(100, Math.max(0, (state.currentWater / capacity) * 100));
    document.getElementById('water-level-anim').style.height = `${pct}%`;
    document.getElementById('water-pct-text').textContent = `${pct.toFixed(0)}%`;
}

function calculateWaterRefill() {
    // Logic logic embedded for UI update simplicity
    const history = state.waterHistory;
    const minLevel = parseFloat(document.getElementById('min-water-threshold').value) || 10;
    const daysEl = document.getElementById('water-days-left');
    const rateEl = document.getElementById('water-depletion-rate');
    const dateEl = document.getElementById('water-refill-date');

    if (state.currentWater <= minLevel) {
        daysEl.textContent = "เติมทันที!";
        daysEl.className = "text-3xl font-extrabold text-red-600";
        dateEl.textContent = "ระดับน้ำต่ำกว่ากำหนด";
        rateEl.textContent = "--";
        return;
    } else {
        daysEl.className = "text-3xl font-extrabold text-blue-800";
    }

    if (history.length < 2) {
        daysEl.textContent = "--";
        rateEl.textContent = "รอข้อมูล...";
        return;
    }

    const oldest = history[0];
    const newest = history[history.length - 1];
    const timeDiffMs = newest.time - oldest.time;
    const valDiff = oldest.value - newest.value;

    if (valDiff <= 0 || timeDiffMs === 0) {
        daysEl.textContent = "∞";
        rateEl.textContent = "คงที่";
        return;
    }

    const ratePerMs = valDiff / timeDiffMs;
    const ratePerDay = ratePerMs * 1000 * 3600 * 24;
    const remaining = state.currentWater - minLevel;
    const msLeft = remaining / ratePerMs;

    rateEl.textContent = ratePerDay.toFixed(1);
    const totalMinutes = Math.floor(msLeft / 60000);
    const days = Math.floor(totalMinutes / (24 * 60));
    const hours = Math.floor((totalMinutes % (24 * 60)) / 60);

    if (days > 365) daysEl.textContent = "> 1 ปี";
    else daysEl.textContent = `${days} วัน ${hours} ชม.`;

    const d = new Date();
    d.setTime(d.getTime() + msLeft);
    dateEl.textContent = `คาดการณ์: ${d.toLocaleDateString('th-TH')}`;
}

function calculateAutoDepletion() {
    calcSingleMetric('tds', state.currentTDS, 'min-tds-threshold', 'depletion-days-tds', 'calc-rate-tds', 'refill-date-tds');
    calcSingleMetric('ec', state.currentEC, 'min-ec-threshold', 'depletion-days-ec', 'calc-rate-ec', 'refill-date-ec');
}

function calcSingleMetric(key, val, minId, dayId, rateId, dateId) {
    const history = state.history;
    const minVal = parseFloat(document.getElementById(minId).value) || 0;
    const daysEl = document.getElementById(dayId);
    const rateEl = document.getElementById(rateId);
    const dateEl = document.getElementById(dateId);
    const unit = key === 'tds' ? 'TDS' : 'EC';

    if (val <= minVal) {
        daysEl.textContent = "เติมทันที!";
        return;
    }
    if (history.length < 2) {
        daysEl.textContent = "--";
        return;
    }
    const oldest = history[0];
    const newest = history[history.length - 1];
    const diff = oldest[key] - newest[key];
    const timeDiff = newest.time - oldest.time;

    if (diff <= 0) {
        daysEl.textContent = "∞";
        return;
    }
    const rateMs = diff / timeDiff;
    const msLeft = (val - minVal) / rateMs;
    const days = Math.floor(msLeft / (1000 * 3600 * 24));
    daysEl.textContent = `${days} วัน`;
}

// --- DOM Generators ---
function renderLogsToModal() {
    const el = document.getElementById('modal-error-list');
    const cl = document.getElementById('modal-command-list');
    el.innerHTML = '';
    cl.innerHTML = '';
    state.logs.slice().reverse().forEach(l => {
        const i = document.createElement('div');
        i.innerHTML = `<span class="text-xs text-gray-500">[${l.timestamp}]</span> <b>${l.level}:</b> ${l.message}`;
        if (l.level === 'ERROR' || l.level === 'CRITICAL') el.appendChild(i);
        else cl.appendChild(i);
    });
}

function addProgramPeriod(data = null) {
    const container = document.getElementById('program-container');
    const lastPeriod = container.lastElementChild;
    let nextStart = 1;
    let nextEnd = 10;

    if (lastPeriod && !data) {
        const inputs = lastPeriod.querySelectorAll('input[type="number"]');
        if (inputs.length >= 2) {
            const lastEnd = parseInt(inputs[1].value) || 0;
            nextStart = lastEnd + 1;
            nextEnd = nextStart + 9;
        }
    }

    const vals = data || { start: nextStart, end: nextEnd, ph: 6.0, ec: 1.5, temp: 25, humid: 80, lightStart: '07:00', lightEnd: '14:00', lightInt: 7000, tds: 750 };
    const div = document.createElement('div');
    div.className = 'border border-gray-200 rounded-xl p-4 bg-gray-50 relative';

    div.innerHTML = `
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-2">
            <span class="font-bold text-gray-700">วันที่</span><input type="number" class="w-16 p-1 border rounded text-center" value="${vals.start}">
            <span class="font-bold text-gray-700">ถึง</span><input type="number" class="w-16 p-1 border rounded text-center" value="${vals.end}">
        </div>
        <button class="text-red-500 font-bold hover:text-red-700 text-xl" onclick="this.parentElement.parentElement.remove()">×</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        ${createSliderHTML('PH', 'ph', vals.ph, 0, 14, 0.1)}
        ${createSliderHTML('EC', 'ec', vals.ec, 0, 5, 0.1)}
        ${createSliderHTML('อุณหภูมิ', 'temp', vals.temp, 15, 40, 1)}
        ${createSliderHTML('ความชื้น', 'humid', vals.humid, 0, 100, 1)}
        <div class="col-span-1 md:col-span-2 bg-yellow-50 p-3 rounded border border-yellow-200">
            <div class="flex justify-between items-center mb-2">
                <label class="font-bold text-yellow-800">ระบบแสง (Light Schedule)</label>
                <span class="text-sm text-yellow-700 font-mono light-duration">คำนวณ...</span>
            </div>
            <div class="flex flex-wrap md:flex-nowrap gap-2 mb-2">
                <div class="flex-1"><label class="text-xs text-gray-500">เปิด (Start)</label><input type="time" class="border rounded p-1 text-sm w-full" value="${vals.lightStart}" onchange="calcLightDur(this)"></div>
                <div class="flex-1"><label class="text-xs text-gray-500">ปิด (End)</label><input type="time" class="border rounded p-1 text-sm w-full" value="${vals.lightEnd}" onchange="calcLightDur(this)"></div>
            </div>
            <div class="flex items-center space-x-2 mt-2">
                <span class="text-xs text-gray-500 w-12">ความเข้ม</span>
                <button class="precision-btn bg-yellow-200 text-yellow-800" onclick="adjLight(this, -100)">-</button>
                <input type="range" class="flex-grow h-2 bg-yellow-200 rounded-lg appearance-none" min="0" max="20000" step="100" value="${vals.lightInt}" oninput="this.nextElementSibling.nextElementSibling.textContent=this.value">
                <button class="precision-btn bg-yellow-200 text-yellow-800" onclick="adjLight(this, 100)">+</button>
                <span class="text-xs font-bold text-yellow-700 w-10 text-right light-val">${vals.lightInt}</span>
            </div>
        </div>
        ${createSliderHTML('TDS', 'tds', vals.tds, 0, 2000, 10)}
    </div>`;
    container.appendChild(div);

    // Init light calc
    const inputs = div.querySelectorAll('input[type="time"]');
    if (inputs.length) calcLightDur(inputs[0]);
}

function createSliderHTML(label, key, val, min, max, step) {
    return `<div><div class="flex justify-between text-xs mb-1"><label class="font-semibold text-gray-600">${label}</label><span class="font-bold text-blue-600 value-display">${val}</span></div><div class="flex items-center space-x-2"><button class="precision-btn bg-gray-200 text-gray-700" onclick="adjustProgramVal(this, ${-step})">-</button><input type="range" class="flex-grow" min="${min}" max="${max}" step="${step}" value="${val}" oninput="this.parentElement.previousElementSibling.querySelector('.value-display').textContent = this.value"><button class="precision-btn bg-gray-200 text-gray-700" onclick="adjustProgramVal(this, ${step})">+</button></div></div>`;
}

// --- Initial Sliders for Manual Control ---
function renderManualSliders() {
    const container = document.getElementById('manual-sliders-container');
    if (!container) return;
    container.innerHTML = `
        ${createSliderHTML('PH', 'ph', 6.0, 0, 14, 0.1)}
        ${createSliderHTML('ความชื้น (%)', 'humidity', 80, 0, 100, 1)}
        ${createSliderHTML('อุณหภูมิ (°C)', 'temperature', 25, 15, 40, 1)}
        ${createSliderHTML('EC', 'ec', 1.5, 0, 5, 0.1)}
        ${createSliderHTML('แสง (Light)', 'light', 7000, 0, 20000, 100)}
        ${createSliderHTML('TDS (ppm)', 'tds', 750, 0, 2000, 10)}
    `;
    // Re-bind event listeners for manual sliders specifically (simplified)
    container.querySelectorAll('input[type="range"]').forEach(input => {
        // In a full app, bind change event to backend update
        input.addEventListener('change', (e) => {
            // e.g., send update_controls to backend
        });
    });
}

// --- Helpers ---
function calcLightDur(el) {
    const parent = el.closest('.bg-yellow-50');
    const times = parent.querySelectorAll('input[type="time"]');
    const start = times[0].value;
    const end = times[1].value;
    const display = parent.querySelector('.light-duration');
    if (start && end) {
        const s = new Date(`2000-01-01T${start}`);
        const e = new Date(`2000-01-01T${end}`);
        let diff = (e - s) / (1000 * 60 * 60);
        if (diff < 0) diff += 24;
        display.textContent = `${diff.toFixed(1)} ชม.`;
    }
}

window.adjLight = (btn, step) => {
    const wrapper = btn.parentElement;
    const input = wrapper.querySelector('input');
    const display = wrapper.querySelector('.light-val');
    let val = parseFloat(input.value) + step;
    if (val >= parseFloat(input.min) && val <= parseFloat(input.max)) {
        input.value = val;
        display.textContent = val;
    }
};

window.adjustProgramVal = (btn, step) => {
    const wrapper = btn.parentElement;
    const slider = wrapper.querySelector('input[type="range"]');
    const display = wrapper.previousElementSibling.querySelector('.value-display');
    let newVal = parseFloat(slider.value) + step;
    const precision = step.toString().split('.')[1]?.length || 0;
    newVal = parseFloat(newVal.toFixed(precision));
    if (newVal >= parseFloat(slider.min) && newVal <= parseFloat(slider.max)) {
        slider.value = newVal;
        display.textContent = newVal;
    }
};

// Note: adjustVal and updateDisplay are for manual sliders (global scope for inline HTML onclick)
window.updateDisplay = (key, val) => { /* Assuming manual sliders use the same structure, tricky with generic HTML gen */ };
// Better to use the generic adjuster above for manual sliders too.

function showToast(msg, type) {
    const container = document.getElementById('notification-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = msg;
    container.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, 3000);
}

function updateConnStatus(online) {
    const el = document.getElementById('connection-status');
    el.className = online ? 'conn-badge conn-online' : 'conn-badge conn-offline';
    el.textContent = online ? '● ออนไลน์' : '● ออฟไลน์';
    // Also update badge in Research View
    const badge = document.getElementById('sys-status-badge');
    if (badge) {
        badge.className = online ? "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" : "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800";
        badge.textContent = online ? "ระบบ: ปกติ" : "ระบบ: ขาดการเชื่อมต่อ";
    }
}