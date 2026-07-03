<?php
// system/check_maintenance.php

// 1. เรียกใช้ Config เพื่อเชื่อมต่อฐานข้อมูล (ถ้ายังไม่ได้เรียก)
if (!isset($conn)) {
    require_once(__DIR__ . '/config.php'); 
}

// 2. ฟังก์ชันดึงค่า Setting (ถ้ายังไม่มี)
if (!function_exists('get_db_setting')) {
    function get_db_setting($conn, $key) {
        try {
            $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return false;
        }
    }
}

// 3. ตรวจสอบสถานะ
$maintenance_mode = get_db_setting($conn, 'maintenance_mode');

// 4. ตรวจสอบว่าเป็น Admin หรือไม่? (เพื่อยกเว้นให้ Admin เข้าได้)
// สมมติว่าคุณใช้ Session ในการเก็บสถานะล็อกอิน
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// **แก้ตรงนี้**: ตรวจสอบ Session ของระบบ Login คุณ
$is_admin = isset($_SESSION['user_id']) || isset($_SESSION['admin_logged_in']); 


// 5. เงื่อนไขการตัดเข้าหน้า Maintenance
// ถ้าเปิดโหมดซ่อมบำรุง (1) และ คนเข้าไม่ใช่ Admin
if ($maintenance_mode == '1' && !$is_admin) {
    
    // ตรวจสอบว่าไม่ได้อยู่ที่หน้า login (เพื่อให้ Admin ล็อกอินเข้าไปแก้ได้)
    $current_page = basename($_SERVER['PHP_SELF']);
    if ($current_page !== 'login.php' && $current_page !== 'admin.php') {
        
        // ส่ง HTTP 503 Service Unavailable (ดีต่อ SEO)
        header('HTTP/1.1 503 Service Unavailable');
        header('Retry-After: 3600');
        
        // เรียกไฟล์หน้า Design มาแสดง
        include(dirname(__DIR__) . '../../maintenance.php'); 
        exit(); // หยุดการทำงานทันที ไม่ให้โหลดเนื้อหาเว็บต่อ
    }
}
?>