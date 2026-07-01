/**
 * สร้างข้อมูลจำลอง (Mock Data) สำหรับกรณี Offline
 */
function generateMockData() {
    const now = Date.now();
    // Drop TDS/EC over time based on clock to simulate consumption
    const drop = Math.floor((now % 100000) / 2000);
    const dropEc = ((now % 100000) / 20000);

    return {
        sensors: {
            ph: "6.0",
            humidity: 80,
            temperature: 25,
            ec: (2.0 - dropEc).toFixed(2),
            light: 7000,
            tds: 1200 - drop,
            water_level: 80 // Simplified mock
        },
        controls: {
            fan_on: false, cooler_on: false, mister_on: false,
            return_pump_on: false, mister_pump_on: false, uv_light_on: false,
            ultrasonic_mister_on: false, exhaust_fan_on: false, mist_fan_on: false, grow_light_on: false
        }
    };
}

/**
 * คำนวณจำนวนวันที่เหลือ (Depletion Days)
 * @param {number} currentVal ค่าปัจจุบัน
 * @param {number} minVal ค่าขั้นต่ำ
 * @param {number} ratePerDay อัตราการลดลงต่อวัน
 * @returns {number} จำนวนวัน (ทศนิยม) หรือ Infinity ถ้าไม่ลดลง
 */
function calculateDaysRemaining(currentVal, minVal, ratePerDay) {
    if (ratePerDay <= 0) return Infinity;
    const remaining = currentVal - minVal;
    return remaining / ratePerDay;
}