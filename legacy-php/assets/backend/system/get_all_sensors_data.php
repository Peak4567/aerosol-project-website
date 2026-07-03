<?php
header('Content-Type: application/json');
require_once('../../system/config.php'); // **ตรวจสอบ Path ไปยังไฟล์ config ของคุณ**

// 1. ตรวจสอบว่ามีการร้องขอ sensor ตัวเดียวหรือไม่ (เช่น ?sensor=tds)
// ถ้าไม่มี จะถือว่าเป็นการร้องขอข้อมูลทั้งหมด (สำหรับกราฟหลัก)
$sensor_type = isset($_GET['sensor']) && !empty($_GET['sensor']) ? $_GET['sensor'] : 'all';

try {
    // 2. ถ้าเป็นการร้องขอข้อมูลทั้งหมด
    if ($sensor_type === 'all') {
        $labels = [];
        $datasets = [
            'tds'        => [], 'ec'         => [], 'sht40_temp' => [],
            'sht40_humi' => [], 'ph'         => []
        ];

        $sql = "SELECT create_at, tds_value, ec_value, temperature, humidity, ph_value FROM (
                    SELECT * FROM sensor ORDER BY create_at DESC LIMIT 100
                ) AS subquery ORDER BY create_at ASC";
        
        $stmt = $conn->query($sql);

        while($row = $stmt->fetch()) {
            $labels[] = date("H:i:s", strtotime($row['create_at'])); 
            $datasets['tds'][] = $row['tds_value'];
            $datasets['ec'][] = $row['ec_value'];
            $datasets['sht40_temp'][] = $row['temperature'];
            $datasets['sht40_humi'][] = $row['humidity'];
            $datasets['ph'][] = $row['ph_value'];
        }

        // ส่ง JSON สำหรับกราฟหลัก
        echo json_encode(['labels' => $labels, 'datasets' => $datasets]);

    } 
    // 3. ถ้าเป็นการร้องขอข้อมูลเพียงเซ็นเซอร์เดียว
    else {
        $labels = [];
        $data = [];
        $datasetLabel = 'Unknown Sensor';
        $column_name = '';

        // ตรวจสอบชื่อ sensor ที่ร้องขอ
        switch ($sensor_type) {
            case 'tds': $column_name = 'tds_value'; $datasetLabel = 'TDS (ppm)'; break;
            case 'ec': $column_name = 'ec_value'; $datasetLabel = 'EC (µS/cm)'; break;
            case 'sht40_temp': $column_name = 'temperature'; $datasetLabel = 'Temperature (°C)'; break;
            case 'sht40_humi': $column_name = 'humidity'; $datasetLabel = 'Humidity (%RH)'; break;
            case 'ph': $column_name = 'ph_value'; $datasetLabel = 'pH Value'; break;
            default:
                echo json_encode(['labels' => [], 'data' => [], 'datasetLabel' => 'Invalid Sensor']);
                exit();
        }

        $sql = "SELECT create_at, $column_name FROM (
                    SELECT create_at, $column_name FROM sensor ORDER BY id DESC LIMIT 100
                ) AS subquery ORDER BY create_at ASC";
        
        $stmt = $conn->query($sql);

        while($row = $stmt->fetch()) {
            $labels[] = date("d/m/Y H:i:s", strtotime($row['create_at'])); 
            $data[] = $row[$column_name];
        }

        // ส่ง JSON สำหรับตารางประวัติ
        echo json_encode(['labels' => $labels, 'data' => $data, 'datasetLabel' => $datasetLabel]);
    }

} catch (PDOException $e) {
    // จัดการ Error กรณีเชื่อมต่อฐานข้อมูลหรือ query ผิดพลาด
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} finally {
    $conn = null;
}
?>