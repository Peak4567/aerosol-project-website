<?php
header('Content-Type: application/json; charset=utf-8');

// 1. ตั้งค่าการเชื่อมต่อ Database (XAMPP Default)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aerosol";

// เชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed"]);
    exit();
}

// 2. ดึงข้อมูลล่าสุด 1 แถว
$sql = "SELECT * FROM sensor ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

// ค่า Default กรณีไม่มีข้อมูล
$sensorData = [
    "ph" => 0,
    "humidity" => 0,
    "temperature" => 0,
    "ec" => 0,
    "light" => 0,      // ใน DB ไม่มีคอลัมน์นี้
    "tds" => 0,
    "water_level" => 0 // ใน DB ไม่มีคอลัมน์นี้
];

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // แมปชื่อคอลัมน์จาก DB ให้ตรงกับที่หน้าเว็บต้องการ
    $sensorData["ph"] = (float)$row['ph_value'];
    $sensorData["humidity"] = (float)$row['humidity'];
    $sensorData["temperature"] = (float)$row['temperature'];
    $sensorData["ec"] = (float)$row['ec_value'];
    $sensorData["tds"] = (float)$row['tds_value'];
    // light และ water_level ยังเป็น 0 เพราะในตารางไม่มีข้อมูล
}

// 3. ส่งค่ากลับเป็น JSON
$response = [
    "status" => "success",
    "timestamp" => time(),
    "sensors" => $sensorData,
    "controls" => [
        "return_pump_on" => false,
        "mister_pump_on" => false,
        "uv_light_on" => false,
        "ultrasonic_mister_on" => false,
        "exhaust_fan_on" => false,
        "mist_fan_on" => false,
        "grow_light_on" => false
    ],
    "cultivation_status" => "running",
    "start_time" => time(),
    "elapsed_time" => 0,
    "logs" => []
];

echo json_encode($response);
$conn->close();
?>