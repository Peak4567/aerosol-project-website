<?php
// ตั้งค่า Header ให้เป็น JSON เพื่อการตอบกลับที่เป็นมาตรฐาน
header('Content-Type: application/json');

// 1. เรียกใช้ไฟล์ config เพื่อให้ได้ตัวแปร $conn ที่เป็น PDO
require_once('../../system/config.php'); 

try {
    // 2. สร้าง Array สำหรับเก็บข้อมูลที่จะ Insert ในรูปแบบของ PDO
    $data_to_insert = [];

    // 3. ตรวจสอบข้อมูลจาก POST และเก็บลง Array
    if (isset($_POST["ph_value"])) {
        $data_to_insert['ph_value'] = floatval($_POST["ph_value"]);
    }
    if (isset($_POST["tds_value"])) {
        $data_to_insert['tds_value'] = floatval($_POST["tds_value"]);
    }
    if (isset($_POST["ec_value"])) {
        $data_to_insert['ec_value'] = floatval($_POST["ec_value"]);
    }
    if (isset($_POST["temperature"]) && floatval($_POST["temperature"]) > -999) {
        $data_to_insert['temperature'] = floatval($_POST["temperature"]);
    }
    if (isset($_POST["humidity"]) && floatval($_POST["humidity"]) > -999) {
        $data_to_insert['humidity'] = floatval($_POST["humidity"]);
    }

    // 4. ถ้ามีข้อมูลอย่างน้อย 1 ค่า
    if (!empty($data_to_insert)) {
        // 5. สร้าง SQL Query ด้วย Named Placeholders ของ PDO (เช่น :ph_value)
        $sqlColumns = implode(", ", array_keys($data_to_insert));
        $sqlPlaceholders = ":" . implode(", :", array_keys($data_to_insert));

        $sql = "INSERT INTO sensor ($sqlColumns) VALUES ($sqlPlaceholders)";
        
        $stmt = $conn->prepare($sql);

        // 6. Execute โดยส่ง Array ของข้อมูลเข้าไปได้เลย
        $stmt->execute($data_to_insert);

        // 7. ตอบกลับเป็น JSON
        http_response_code(201); // 201 Created
        echo json_encode(['status' => 'success', 'message' => 'Record created successfully.']);

    } else {
        http_response_code(400); // 400 Bad Request
        echo json_encode(['status' => 'error', 'message' => 'No valid sensor data received.']);
    }

} catch (PDOException $e) {
    // 8. ดักจับ PDOException และตอบกลับเป็น JSON
    http_response_code(500); // 500 Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);

} finally {
    // 9. ปิดการเชื่อมต่อของ PDO
    $conn = null;
}
?>