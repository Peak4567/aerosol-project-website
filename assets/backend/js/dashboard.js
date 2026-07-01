
const UPDATE_INTERVAL = 5000;
let myMultiChart = null;

const SENSOR_CONFIG = {
    'tds': {
        label: 'TDS',
        borderColor: '#14b8a6',
        icon: 'fa-flask-vial',
        unit: 'ppm'
    },
    'ec': {
        label: 'EC',
        borderColor: '#38bdf8',
        icon: 'fa-bolt',
        unit: 'µS/cm'
    },
    'sht40_temp': {
        label: 'Temperature',
        borderColor: '#f59e0b',
        icon: 'fa-temperature-half',
        unit: '°C'
    },
    'sht40_humi': {
        label: 'Humidity',
        borderColor: '#f43f5e',
        icon: 'fa-droplet',
        unit: '%RH'
    },
    'ph': {
        label: 'pH',
        borderColor: '#8b5cf6',
        icon: 'fa-vial-circle-check',
        unit: ''
    }
};
const SENSOR_TYPES = Object.keys(SENSOR_CONFIG);

function createMultiSensorChart(labels, datasets) {
    // ... (This function remains unchanged) ...
    const ctx = document.getElementById('multiSensorChart').getContext('2d');
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false
                },
                x: {
                    ticks: {
                        maxRotation: 0,
                        minRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 10
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                title: {
                    display: false
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

function updateDashboardStats(datasetsData) {
    // Update main stat cards
    const latestTds = datasetsData.tds?.[datasetsData.tds.length - 1] ?? '--';
    const latestTemp = datasetsData.sht40_temp?.[datasetsData.sht40_temp.length - 1] ?? '--';
    const latestHumi = datasetsData.sht40_humi?.[datasetsData.sht40_humi.length - 1] ?? '--';
    const latestEc = datasetsData.ec?.[datasetsData.ec.length - 1] ?? '--';
    const latestPh = datasetsData.ph?.[datasetsData.ph.length - 1] ?? '--';

    document.getElementById('stat-tds').textContent = `${latestTds} ppm`;
    document.getElementById('stat-temp').textContent = `${latestTemp} °C`;
    document.getElementById('stat-humi').textContent = `${latestHumi} %RH`;
    document.getElementById('stat-ec').textContent = `${latestEc} µS/cm`;
    document.getElementById('stat-ph').textContent = `${latestPh}`;

    // Update summary list
    const summaryList = document.getElementById('summary-list');
    summaryList.innerHTML = ''; // Clear previous list

    SENSOR_TYPES.forEach(sensorType => {
        const config = SENSOR_CONFIG[sensorType];
        const latestValue = datasetsData[sensorType]?.[datasetsData[sensorType].length - 1] ?? 'N/A';

        const listItem = `
            <li class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-gray-100 h-10 w-10 flex items-center justify-center rounded-lg text-gray-500">
                        <i class="fa-solid ${config.icon}"></i>
                    </div>
                    <span class="font-medium text-gray-600">${config.label}</span>
                </div>
                <span class="font-bold text-gray-800">${latestValue} ${config.unit}</span>
            </li>
        `;
        summaryList.insertAdjacentHTML('beforeend', listItem);
    });
}

async function updateAllSensorsOnChart() {
    try {
        const response = await fetch('../backend/system/get_all_sensors_data.php'); // Check your path here
        const allData = await response.json();

        const labels = allData.labels || [];
        const datasetsData = allData.datasets;

        // [NEW] Call the function to update stats
        updateDashboardStats(datasetsData);

        if (!myMultiChart) {
            const datasets = SENSOR_TYPES.map(sensorType => {
                const config = SENSOR_CONFIG[sensorType];
                return {
                    label: config.label,
                    data: datasetsData[sensorType] || [],
                    borderColor: config.borderColor,
                    backgroundColor: `${config.borderColor}1A`,
                    fill: false,
                    borderWidth: 2,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 5
                };
            });
            myMultiChart = createMultiSensorChart(labels, datasets);
        } else {
            myMultiChart.data.labels = labels;
            SENSOR_TYPES.forEach((sensorType, index) => {
                myMultiChart.data.datasets[index].data = datasetsData[sensorType] || [];
            });
            myMultiChart.update('none');
        }

        const now = new Date();
        document.getElementById('last-updated').innerText = `Last updated: ${now.toLocaleTimeString()}`;
    } catch (error) {
        console.error('Error loading combined chart data:', error);
        document.getElementById('last-updated').innerText = 'Error loading data.';
    }
}

// ฟังก์ชันแปลงวันที่เป็นภาษาไทย
function formatThaiDate(date) {
    const days = ['วันอาทิตย์', 'วันจันทร์', 'วันอังคาร', 'วันพุธ', 'วันพฤหัสบดี', 'วันศุกร์', 'วันเสาร์'];
    const months = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    const day = days[date.getDay()];
    const dateNum = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear() + 543; // ปีพ.ศ.
    return `${day}, ${dateNum} ${month} ${year}`;
}

// ฟังก์ชันกำหนดช่วงเวลา (เช้า/บ่าย/เย็น)
function getTimePeriod(hour) {
    if (hour < 12) return 'เช้า';
    if (hour < 18) return 'บ่าย';
    return 'เย็น';
}

// ฟังก์ชันอัปเดตวันที่และเวลา
function updateDateTime() {
    const now = new Date();
    const dateElement = document.getElementById('current-date');
    const timeElement = document.getElementById('current-time');

    dateElement.textContent = formatThaiDate(now);
    const timeString = now.toLocaleTimeString('th-TH', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    timeElement.textContent = `${timeString} (${getTimePeriod(now.getHours())})`;
}

// Mock-up สภาพอากาศ (สามารถเปลี่ยนเป็น API call ได้)
function updateWeather() {
    const weatherElement = document.getElementById('weather-status');
    // Mock-up ข้อมูล (สมมติ)
    const weatherData = {
        condition: 'แดดจัด',
        icon: 'fa-sun' // ใช้ Font Awesome icon
    };
    weatherElement.previousElementSibling.querySelector('i').className = `fa-solid ${weatherData.icon}`;
    weatherElement.textContent = weatherData.condition;
}


window.onload = () => {
    updateAllSensorsOnChart();
    updateDateTime();
    updateWeather();
    setInterval(updateAllSensorsOnChart, UPDATE_INTERVAL);
};
