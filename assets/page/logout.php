<?php
// ตั้งค่าให้ใช้ Session
session_start();

// ทำลาย Session ทั้งหมดที่เกี่ยวข้องกับผู้ใช้คนนี้
$_SESSION = array();

// หากใช้ cookies ในการเก็บ Session ID ให้ลบคุ้กกี้ Session ID ด้วย
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ทำลาย Session
session_destroy();

// Redirect ผู้ใช้ไปยังหน้า Login (สมมติว่าเป็นที่ /login.php)
header("Location: /assets/page/login.php"); 
exit();
?>