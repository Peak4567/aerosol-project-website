<?php
header('Content-Type: application/json');
require_once('../../system/config.php'); // ตรวจสอบ Path ไปยังไฟล์ config ของคุณ

$labels = [];
$data = [];
$datasetLabel = 'Unknown Sensor';

// รับค่า sensor type จาก URL (เช่น ?sensor=sht40_temp)
$sensor_type = isset($_GET['sensor']) ? $_GET['sensor'] : '';

// กำหนดคอลัมน์และชื่อจาก sensor_type ที่รับมา
$column_name = '';
switch ($sensor_type) {
    case 'tds':
        $column_name = 'tds_value';
        $datasetLabel = 'TDS (ppm)';
        break;
    case 'ec':
        $column_name = 'ec_value';
        $datasetLabel = 'EC (µS/cm)';
        break;
    case 'sht40_temp':
        $column_name = 'temperature';
        $datasetLabel = 'Temperature (°C)';
        break;
    case 'sht40_humi':
        $column_name = 'humidity';
        $datasetLabel = 'Humidity (%RH)';
        break;
    case 'ph':
        $column_name = 'ph_value';
        $datasetLabel = 'pH Value';
        break;
    default:
        // ถ้าไม่ระบุ sensor หรือระบุผิด, ส่งข้อมูลว่างกลับไป
        echo json_encode(['labels' => [], 'data' => [], 'datasetLabel' => 'Invalid Sensor']);
        exit();
}

try {
    $sql = "SELECT create_at, $column_name FROM (
                SELECT create_at, $column_name 
                FROM sensor 
                ORDER BY id DESC 
                LIMIT 100
            ) AS subquery 
            ORDER BY create_at ASC";

    $stmt = $conn->query($sql);

    while($row = $stmt->fetch()) {
        $labels[] = date("d/m/Y H:i:s", strtotime($row['create_at'])); 
        $data[] = $row[$column_name];
    }

} catch (PDOException $e) {
    // จัดการ Error (กรณีนี้จะส่งข้อมูลว่างกลับไป)
    $labels = [];
    $data = [];
    $datasetLabel = 'Database Error';
} finally {
    $conn = null;
}

echo json_encode([
    'labels' => $labels, 
    'data' => $data, 
    'datasetLabel' => $datasetLabel
]);
?>