document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize UI Components
    renderManualSliders();

    // Add default program period
    addProgramPeriod({ start: 1, end: 10, ph: 6.0, ec: 1.5, temp: 25, humid: 80, lightStart: '07:00', lightEnd: '14:00', lightInt: 7000, tds: 800 });

    // 2. Start Data Fetch Loop
    checkConnection();
    setInterval(fetchDataLoop, 2000);

    // 3. Bind Event Listeners
    document.getElementById('start-cultivation-btn').addEventListener('click', startCultivation);
    document.getElementById('stop-cultivation-btn').addEventListener('click', stopCultivation);
    document.getElementById('resume-cultivation-btn').addEventListener('click', startCultivation);
    document.getElementById('reset-cultivation-btn').addEventListener('click', resetCultivation);

    document.getElementById('add-period-btn').addEventListener('click', () => addProgramPeriod());
    document.getElementById('save-program-btn').addEventListener('click', () => showToast('บันทึกโปรแกรมสำเร็จ', 'success'));
});

// --- API Interaction ---

async function checkConnection() {
    try {
        await fetch('http://localhost:5000/get_latest_data', { method: 'HEAD' });
        state.isConnected = true;
        updateConnStatus(true);
    } catch (e) {
        state.isConnected = false;
        updateConnStatus(false);
    }
}

async function fetchDataLoop() {
    if (!state.isConnected) {
        checkConnection();
        // If offline, use Mock Data logic from logic.js (assumed to be available if included)
        updateUI(generateMockData());
        return;
    }
    try {
        const res = await fetch('http://localhost:5000/get_latest_data');
        const data = await res.json();
        updateUI(data);

        if (data.logs) {
            data.logs.forEach(l => {
                // Avoid duplicates
                const exists = state.logs.some(loc => loc.timestamp === l.timestamp && loc.message === l.message);
                if (!exists) state.logs.push(l);
            });
        }

        if (data.cultivation_status) {
            if (data.cultivation_status === 'running') {
                state.startTime = data.start_time;
                updateTimerUI('running');
            } else if (data.cultivation_status === 'paused') {
                state.startTime = data.start_time;
                updateTimerUI('paused', data.elapsed_time);
            } else {
                state.startTime = null;
                updateTimerUI('idle');
            }
        }
    } catch (e) {
        state.isConnected = false;
        updateConnStatus(false);
    }
}

// --- Actions ---

function startCultivation() {
    if (state.isConnected) {
        fetch('/start_cultivation', { method: 'POST' })
            .then(res => res.json())
            .then(d => { if (d.status === 'success') showToast('เริ่มการเพาะปลูก', 'success'); });
    } else {
        state.startTime = state.startTime || Date.now();
        updateTimerUI('running');
        showToast('เริ่มการเพาะปลูก (Offline)', 'success');
    }
}

function stopCultivation() {
    if (state.isConnected) {
        fetch('/stop_cultivation', { method: 'POST' })
            .then(res => res.json())
            .then(d => { if (d.status === 'success') showToast('หยุดชั่วคราว', 'info'); });
    } else {
        updateTimerUI('paused');
        showToast('หยุดชั่วคราว (Offline)', 'info');
    }
}

function resetCultivation() {
    if (state.isConnected) {
        fetch('/reset_cultivation', { method: 'POST' })
            .then(res => res.json())
            .then(d => { if (d.status === 'success') showToast('รีเซ็ตระบบแล้ว', 'success'); });
    } else {
        state.startTime = null;
        updateTimerUI('idle');
        showToast('รีเซ็ตระบบแล้ว (Offline)', 'success');
    }
}

function saveRefillSettings() {
    showToast('บันทึกค่าแจ้งเตือนแล้ว', 'success');
}

// --- Exports ---

function exportSystemReport() {
    const date = new Date().toLocaleString('th-TH');
    const errors = document.getElementById('modal-error-list').innerHTML;
    const commands = document.getElementById('modal-command-list').innerHTML;
    const content = `
        <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
        <head><meta charset='utf-8'><title>System Log Report</title></head><body>
        <h1>รายงานสถานะระบบ (System Log Report)</h1>
        <p><strong>วันที่:</strong> ${date}</p>
        <hr><h2>1. รายงานข้อผิดพลาด</h2>${errors}
        <hr><h2>2. ประวัติคำสั่ง</h2>${commands}
        </body></html>
    `;
    downloadDoc(content, `System_Logs_${new Date().toISOString().slice(0, 10)}.doc`);
}

function exportResearchReport() {
    const crop = document.getElementById('input-crop').value;
    const pName = document.getElementById('input-potato-name').value || "-";
    const date = new Date().toLocaleString('th-TH');
    const content = `
        <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
        <head><meta charset='utf-8'><title>Potato Research Report</title></head><body>
        <h1>รายงานการวิจัยมันฝรั่ง</h1><p><strong>วันที่:</strong> ${date}</p>
        <hr><h2>1. ข้อมูลโครงการ</h2>
        <p>พืช: ${crop}</p><p>ชื่อโปรเจกต์: ${pName}</p>
        </body></html>
    `;
    downloadDoc(content, `Potato_Research_${new Date().toISOString().slice(0, 10)}.doc`);
}

function downloadDoc(content, filename) {
    const blob = new Blob(['\ufeff', content], { type: 'application/msword' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    showToast("ดาวน์โหลดไฟล์ Word สำเร็จ", "success");
}