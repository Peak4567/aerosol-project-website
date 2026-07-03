<?php
// assets/system/logout.php

session_start(); // เริ่มต้น Session

// ล้างค่า Session ทั้งหมด
$_SESSION = array();

// ทำลาย Session Cookie (ถ้ามี)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ทำลาย Session
session_destroy();

// Redirect กลับไปหน้าแรก หรือหน้า Login
header("Location: ../../index.php"); 
exit;
?>