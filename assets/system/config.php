<?php
// C:\xampp\htdocs\system\config.php

// กำหนดค่าคงที่สำหรับการเชื่อมต่อฐานข้อมูล
define('DB_SERVER', 'localhost'); 
define('DB_USERNAME', 'aerosol'); 
define('DB_PASSWORD', 'aerosol1234'); 
define('DB_NAME', 'aerosol'); 

/*
 * สร้างการเชื่อมต่อด้วย PDO ในรูปแบบ try...catch
 */
try {
    // 1. สร้าง DSN (Data Source Name)
    $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    // 2. กำหนด Options เพิ่มเติม (แนะนำ)
    $options = [
        PDO::ATTR_ERRMODE              => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES     => false,
    ];

    // 3. สร้าง Instance ของ PDO และเก็บในตัวแปร $conn
    $conn = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);

} catch (PDOException $e) {
    // หากการเชื่อมต่อล้มเหลว
    die("ERROR: ไม่สามารถเชื่อมต่อฐานข้อมูลได้. " . $e->getMessage());
}

// ------------------------------------------------------------------
// ** ส่วนที่ 1: ดึงข้อมูลการตั้งค่าทั้งหมดจาก Database **
// ------------------------------------------------------------------
$settings = [];
try {
    // ใช้ $conn ที่เชื่อมต่อแล้ว ดึง key และ value มาเก็บใน $settings array
    $stmt = $conn->query("SELECT setting_key, setting_value FROM settings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); 
} catch (PDOException $e) {
    // จัดการข้อผิดพลาดในการดึงข้อมูล (เช่น ตาราง settings ยังไม่มี)
    error_log("Error fetching settings from DB: " . $e->getMessage());
}

// ------------------------------------------------------------------
// ** ส่วนที่ 2: ประกาศฟังก์ชัน Helper ด้วยการป้องกันการประกาศซ้ำซ้อน **
// ------------------------------------------------------------------
if (!function_exists('get_setting')) { // <--- การแก้ไขที่สำคัญที่สุด!
    function get_setting($key, $default = '')
    {
        // ต้องประกาศ global $settings เพื่อเข้าถึงตัวแปรที่ดึงมาจาก DB
        global $settings; 
        // ตรวจสอบว่าคีย์มีใน $settings หรือไม่ ถ้าไม่มีให้ใช้ค่าเริ่มต้น (Default)
        return isset($settings[$key]) ? htmlspecialchars($settings[$key], ENT_QUOTES, 'UTF-8') : $default;
    }
}
?>