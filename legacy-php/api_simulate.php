<?php
// แสดง Error ทุกอย่างออกมาให้เห็น (ใช้เพื่อ Debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

// --- 1. ระบบค้นหาไฟล์ config.php อัตโนมัติ ---
$possible_paths = [
    './assets/system/config.php',
    './system/config.php',
    '../assets/system/config.php',
    '../system/config.php',
    'config.php'
];

$config_loaded = false;
foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $config_loaded = true;
        break;
    }
}

if (!$config_loaded) {
    echo json_encode(["status" => "error", "message" => "Critical Error: ไม่พบไฟล์ config.php กรุณาตรวจสอบตำแหน่งไฟล์"]);
    exit;
}

// --- 2. ตรวจสอบการเชื่อมต่อ ---
if (!isset($conn)) {
    echo json_encode(["status" => "error", "message" => "Database Connection Failed: ตัวแปร \$conn ไม่ถูกสร้างจาก config.php"]);
    exit;
}

// --- 3. ตั้งค่าชื่อตาราง (ต้องตรงกับใน Database เป๊ะๆ) ---
$table_name = "sensor"; 

// --- 4. สุ่มข้อมูล ---
$temp = mt_rand(2500, 3500) / 100;
$humid = mt_rand(5000, 8000) / 100;
$tds   = mt_rand(0, 500);
$ec    = mt_rand(0, 200) / 100;
$ph    = mt_rand(600, 800) / 100;
$create_at = date('Y-m-d H:i:s');

try {
    $sql = "INSERT INTO $table_name (temperature, humidity, tds_value, ec_value, ph_value, create_at) 
            VALUES (:temp, :humid, :tds, :ec, :ph, :create_at)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':temp', $temp);
    $stmt->bindParam(':humid', $humid);
    $stmt->bindParam(':tds', $tds);
    $stmt->bindParam(':ec', $ec);
    $stmt->bindParam(':ph', $ph);
    $stmt->bindParam(':create_at', $create_at);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success", 
            "data" => [
                "time" => $create_at,
                "temp" => $temp,
                "humid" => $humid,
                "ph" => $ph,
                "ec" => $ec
            ]
        ]);
    } else {
        $err = $stmt->errorInfo();
        echo json_encode(["status" => "error", "message" => "SQL Error: " . $err[2]]);
    }

} catch (PDOException $e) {
    // กรณีหาตารางไม่เจอ หรือ Column ผิด
    echo json_encode(["status" => "error", "message" => "Database Error: " . $e->getMessage()]);
}
?>