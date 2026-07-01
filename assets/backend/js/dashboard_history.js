// --- ส่วนควบคุมตารางข้อมูลประวัติ (ฉบับปรับปรุงใหม่) ---

let currentSensorType = 'sht40_temp'; // กำหนดเซ็นเซอร์เริ่มต้น
const ITEMS_PER_PAGE = 5; // กำหนดจำนวนข้อมูลต่อหน้า
let currentPage = 1;
let fullSensorData = []; // ตัวแปรสำหรับเก็บข้อมูลทั้งหมดที่ fetch มา
let tableUpdateInterval; // ตัวแปรสำหรับเก็บ interval

// [ใหม่] ฟังก์ชันสำหรับแสดงผลข้อมูลในตารางตามหน้าที่เลือก
function renderTablePage() {
    const tbody = document.getElementById('sensor-data-tbody');
    if (!tbody) return;
    tbody.innerHTML = '';

    // คำนวณหา index เริ่มต้นและสิ้นสุดของข้อมูลในหน้านั้นๆ
    const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
    const endIndex = startIndex + ITEMS_PER_PAGE;
    const pageData = fullSensorData.slice(startIndex, endIndex);

    if (pageData.length === 0 && currentPage === 1) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center py-10 text-gray-400">ไม่พบข้อมูล...</td></tr>';
        return;
    }

    // วนลูปเพื่อสร้างแถว (แสดงผลจากใหม่ไปเก่า)
    pageData.forEach((item, index) => {
        const rowNumber = fullSensorData.length - startIndex - index; // นับลำดับถอยหลัง
        const rowHtml = `
            <tr class="border-t border-gray-100">
                <td class="py-2 px-3 font-mono text-gray-400">${rowNumber}</td>
                <td class="py-2 px-3 font-semibold">${item.value}</td>
                <td class="py-2 px-3 text-right font-mono text-sm">${item.label}</td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', rowHtml);
    });
}

// [ใหม่] ฟังก์ชันสำหรับสร้างปุ่ม Pagination
function renderPaginationControls() {
    const container = document.getElementById('pagination-controls');
    if (!container) return;
    container.innerHTML = '';

    const totalPages = Math.ceil(fullSensorData.length / ITEMS_PER_PAGE);
    if (totalPages <= 1) return; // ไม่ต้องแสดงปุ่มถ้ามีแค่หน้าเดียว

    // สร้างปุ่ม "Previous"
    container.innerHTML += `<button data-page="${currentPage - 1}" class="pagination-btn" ${currentPage === 1 ? 'disabled' : ''}>&laquo;</button>`;

    // สร้างปุ่มตัวเลข
    for (let i = 1; i <= totalPages; i++) {
        container.innerHTML += `<button data-page="${i}" class="pagination-btn ${i === currentPage ? 'active' : ''}">${i}</button>`;
    }

    // สร้างปุ่ม "Next"
    container.innerHTML += `<button data-page="${currentPage + 1}" class="pagination-btn" ${currentPage === totalPages ? 'disabled' : ''}>&raquo;</button>`;
}

// [แก้ไข] ฟังก์ชันหลักสำหรับดึงข้อมูล (จะดึงมาทั้งหมดแล้วเก็บไว้)
async function fetchAndSetupTable() {
    try {
        const response = await fetch(`../backend/system/get_sensor_history.php?sensor=${currentSensorType}`);
        const result = await response.json();

        // อัปเดตหัวตาราง
        const headerValue = document.getElementById('table-header-value');
        if (headerValue) headerValue.textContent = `ค่า (${result.datasetLabel})`;

        // เก็บข้อมูลทั้งหมดและเรียงจากใหม่ไปเก่า
        fullSensorData = result.data.map((value, index) => ({
            value: value,
            label: result.labels[index]
        })).reverse();

        currentPage = 1; // กลับไปหน้าแรกเสมอเมื่อโหลดข้อมูลใหม่
        renderTablePage();
        renderPaginationControls();

    } catch (error) {
        console.error("Error fetching table data:", error);
        const tbody = document.getElementById('sensor-data-tbody');
        if (tbody) tbody.innerHTML = '<tr><td colspan="3" class="text-center py-10 text-red-500">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>';
    }
}

// --- การเริ่มต้นทำงานและ Event Listeners ---

// จัดการ Event Listener ของปุ่มเลือกเซ็นเซอร์
const sensorBtnGroup = document.getElementById('sensor-btn-group');
if (sensorBtnGroup) {
    sensorBtnGroup.addEventListener('click', (event) => {
        if (event.target.classList.contains('sensor-btn') && !event.target.classList.contains('active')) {
            currentSensorType = event.target.dataset.sensor;
            sensorBtnGroup.querySelectorAll('.sensor-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            fetchAndSetupTable(); // เรียกโหลดข้อมูลใหม่ทั้งหมด
        }
    });
}

// จัดการ Event Listener ของปุ่ม Pagination
const paginationContainer = document.getElementById('pagination-controls');
if (paginationContainer) {
    paginationContainer.addEventListener('click', (event) => {
        const button = event.target.closest('.pagination-btn');
        if (button && !button.disabled) {
            currentPage = parseInt(button.dataset.page);
            renderTablePage(); // แสดงผลหน้าใหม่
            renderPaginationControls(); // อัปเดตสไตล์ปุ่ม
        }
    });
}

// เริ่มการทำงานครั้งแรกและตั้งเวลาอัปเดต
fetchAndSetupTable();
clearInterval(tableUpdateInterval); // เคลียร์ interval เก่า (ถ้ามี)
tableUpdateInterval = setInterval(fetchAndSetupTable, 5000);