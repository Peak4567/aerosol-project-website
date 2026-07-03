<?php
// --- 1. เริ่มต้น Session ---
session_start();

// หากผู้ใช้ล็อกอินอยู่แล้ว ให้ redirect ไปหน้า dashboard.php เลย
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../backend/dashboard.php");
    exit;
}

// ประกาศตัวแปรสำหรับเก็บสถานะ
$message = '';
$message_type = ''; // 'success', 'error', 'warning'
$login_success = false; // ตัวแปรสำหรับเช็คว่าล็อกอินผ่านไหม

// ตรวจสอบว่ามีข้อความแจ้งจากหน้า register หรือไม่
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $message = 'ลงทะเบียนสำเร็จ! กรุณาเข้าสู่ระบบ';
    $message_type = 'success';
}

// --- ตรวจสอบเมื่อมีการกดปุ่ม Login (ส่งข้อมูลแบบ POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- เชื่อมต่อฐานข้อมูล ---
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'aerosol');
    define('DB_PASSWORD', 'aerosol1234');
    define('DB_NAME', 'aerosol');
    
    try {
        $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
        $conn = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
    } catch (PDOException $e) {
        $message = "ERROR: ไม่สามารถเชื่อมต่อฐานข้อมูลได้. " . $e->getMessage();
        $message_type = 'error';
    }

    if (empty(trim($_POST["username"])) || empty(trim($_POST["password"]))) {
        $message = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
        $message_type = 'warning';
    } else {
        if (empty($message)) { // ถ้าไม่มี error จากการเชื่อมต่อ DB
            $username = trim($_POST['username']);
            $password = $_POST['password']; 
            
            try {
                $sql = "SELECT id, username, password, level FROM users WHERE username = :username";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":username", $username, PDO::PARAM_STR);
                $stmt->execute();

                if ($stmt->rowCount() == 1) {
                    $user = $stmt->fetch();
                    $hashed_password = $user["password"];
                    
                    if (password_verify($password, $hashed_password)) {
                        session_regenerate_id();
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $user["id"];
                        $_SESSION["username"] = $user["username"];
                        $_SESSION["user"]['level'] = $user["level"]; 
                        
                        // ** เปลี่ยนจาก header() เป็นตั้งค่าตัวแปรเพื่อให้ JS ทำงาน **
                        $login_success = true; 
                        
                    } else {
                        $message = 'รหัสผ่านไม่ถูกต้อง';
                        $message_type = 'error';
                    }
                } else {
                    $message = 'ไม่พบชื่อผู้ใช้นี้ในระบบ';
                    $message_type = 'error';
                }

            } catch (PDOException $e) {
                $message = "เกิดข้อผิดพลาด: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { 
            font-family: 'Sarabun', sans-serif; 
            background-image: url('https://source.unsplash.com/1600x900/?nature,forest,green'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            animation: fadeInBackground 2s ease-out forwards;
        }

        @keyframes fadeInBackground {
            from { opacity: 0.8; transform: scale(1.05); }
            to { opacity: 1; transform: scale(1); }
        }

        .login-card {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            animation: slideInUp 0.8s ease-out forwards;
            transform: translateY(20px);
            opacity: 0;
            animation-delay: 0.5s;
        }

        @keyframes slideInUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .input-icon-group {
            position: relative;
        }

        .input-icon-group input {
            padding-left: 3rem;
        }

        .input-icon-group .icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6B7280;
        }

        button:hover {
            box-shadow: 0 0 15px rgba(34, 197, 94, 0.6);
            transform: translateY(-2px);
        }
        
        body{
            overflow: hidden;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">

    <div class="login-card p-8 rounded-2xl shadow-2xl w-full max-w-md">
        
        <div class="text-center mb-8">
            <img src="../img/aerosol.png" alt="aerosol_logo" class="mx-auto h-45 w-auto">
            <h2 class="text-3xl font-bold mt-4 text-gray-800">เข้าสู่ระบบ</h2>
            <p class="text-gray-500 mt-2">สัมผัสธรรมชาติแห่งการเชื่อมต่อ</p> 
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-5 input-icon-group">
                <i class="fa fa-user icon"></i>
                <input type="text" name="username" placeholder="ชื่อผู้ใช้" class="w-full py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-300" required>
            </div>
            
            <div class="mb-6 input-icon-group">
                <i class="fa fa-key icon"></i>
                <input type="password" name="password" placeholder="รหัสผ่าน" class="w-full py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-300" required>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-green-600 text-white font-bold py-3 px-4 rounded-lg hover:from-emerald-600 hover:to-green-700 focus:outline-none focus:ring-4 focus:ring-emerald-300 transition-all duration-300 transform hover:scale-105">
                เข้าสู่ระบบ
            </button>

            <div class="text-center mt-6">
                <a href="#" class="text-sm text-emerald-600 hover:underline">ลืมรหัสผ่าน?</a>
            </div>

            <div class="text-center mt-4">
                <p class="text-gray-600">ยังไม่มีบัญชี? <a href="register.php" class="font-bold text-emerald-600 hover:underline">สมัครสมาชิกที่นี่</a></p>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // กรณีล็อกอินสำเร็จ
            <?php if ($login_success): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'เข้าสู่ระบบสำเร็จ!',
                    text: 'กำลังพาท่านไปที่หน้า Dashboard...',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    confirmButtonColor: '#10B981',
                }).then(() => {
                    window.location.href = '../backend/dashboard.php';
                });
            
            // กรณีมีข้อความแจ้งเตือน (Error / Warning / Success จาก register)
            <?php elseif (!empty($message)): ?>
                Swal.fire({
                    icon: '<?php echo $message_type; ?>', // success, error, warning, info
                    title: '<?php echo ($message_type == 'success') ? 'สำเร็จ' : (($message_type == 'error') ? 'ข้อผิดพลาด' : 'แจ้งเตือน'); ?>',
                    text: '<?php echo $message; ?>',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#10B981', // สีเขียว Emerald
                });
            <?php endif; ?>

        });
    </script>

</body>
</html>