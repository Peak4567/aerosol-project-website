<?php
// --- PHP Backend Logic ---
$message = '';
$message_type = ''; // 'success', 'error', 'warning'
$register_success = false;

// หากผู้ใช้ล็อกอินอยู่แล้ว ให้ redirect ไปหน้า Dashboard
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: /");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Fallback DB connection
    if (!isset($conn)) {
        define('DB_SERVER', 'localhost');
        define('DB_USERNAME', 'aerosol');
        define('DB_PASSWORD', 'aerosol1234');
        define('DB_NAME', 'aerosol');
        try {
            $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
            $conn = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
        } catch (PDOException $e) {
            $message = "ERROR: Could not connect. " . $e->getMessage();
            $message_type = 'error';
        }
    }

    if (empty($message)) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        $errors = [];

        if (empty($username) || empty($email) || empty($password)) {
            $errors[] = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "รูปแบบอีเมลไม่ถูกต้อง";
        }
        if (strlen($password) < 8) {
            $errors[] = "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร";
        }
        if ($password !== $confirm_password) {
            $errors[] = "รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน";
        }

        if (empty($errors)) {
            $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
            $stmt_check->execute(['username' => $username, 'email' => $email]);
            if ($stmt_check->fetch()) {
                $errors[] = "ชื่อผู้ใช้หรืออีเมลนี้มีอยู่ในระบบแล้ว";
            }
        }

        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $default_level = 'member';
            $sql_insert = "INSERT INTO users (username, email, password, level) VALUES (:username, :email, :password, :level)";
            $stmt_insert = $conn->prepare($sql_insert);

            try {
                $stmt_insert->execute([
                    'username' => $username,
                    'email' => $email,
                    'password' => $hashed_password,
                    'level' => $default_level
                ]);
                
                // สำเร็จ! ตั้งค่าตัวแปรเพื่อให้ JS แสดงผล
                $register_success = true;

            } catch (PDOException $e) {
                $message = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage();
                $message_type = 'error';
            }
        } else {
            // รวม Error ทั้งหมดเป็น String เดียวเพื่อแสดงใน Popup
            $message = implode("<br>", $errors);
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียน</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-image: url('https://source.unsplash.com/1600x900/?nature,green,leaves');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            animation: fadeInBackground 2s ease-out forwards;
        }

        @keyframes fadeInBackground {
            from { opacity: 0.8; transform: scale(1.05); }
            to { opacity: 1; transform: scale(1); }
        }

        .register-card {
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

        .input-icon-group { position: relative; }
        .input-icon-group input { padding-left: 2.5rem; }
        .input-icon-group .icon {
            position: absolute; left: 0.75rem; top: 50%;
            transform: translateY(-50%); color: #6B7280;
        }

        button:hover {
            box-shadow: 0 0 15px rgba(34, 197, 94, 0.6);
            transform: translateY(-2px);
        }
        
        body { overflow: hidden; }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4">

    <div class="register-card p-8 rounded-2xl shadow-2xl w-full max-w-md">

        <img src="../img/aerosol.png" alt="aerosol_logo" class="mx-auto h-40 w-auto mb-4">
        <h2 class="text-3xl font-bold text-center text-gray-800">สร้างบัญชีใหม่</h2>
        <p class="text-gray-500 text-center mt-2 mb-6">เริ่มต้นการเดินทางของคุณกับเรา</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="space-y-4">
            <div class="input-icon-group">
                <i class="fa fa-user icon"></i>
                <input type="text" name="username" placeholder="ชื่อผู้ใช้" class="w-full py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-300" required>
            </div>

            <div class="input-icon-group">
                <i class="fa fa-envelope icon"></i>
                <input type="email" name="email" placeholder="อีเมล" class="w-full py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-300" required>
            </div>

            <div class="input-icon-group">
                <i class="fa fa-key icon"></i>
                <input type="password" name="password" placeholder="รหัสผ่าน" class="w-full py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-300" required>
            </div>

            <div class="input-icon-group">
                <i class="fa fa-check-double icon"></i>
                <input type="password" name="confirm_password" placeholder="ยืนยันรหัสผ่าน" class="w-full py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all duration-300" required>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-green-600 text-white font-bold py-3 px-4 rounded-lg hover:from-emerald-600 hover:to-green-700 focus:outline-none focus:ring-4 focus:ring-emerald-300 transition-all duration-300 transform hover:scale-105 !mt-6">
                ลงทะเบียน
            </button>

            <div class="text-center mt-4">
                <p class="text-gray-600">เป็นสมาชิกอยู่แล้ว? <a href="login.php" class="font-bold text-emerald-600 hover:underline">เข้าสู่ระบบที่นี่</a></p>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // กรณีสมัครสมาชิกสำเร็จ
            <?php if ($register_success): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'ลงทะเบียนสำเร็จ!',
                    text: 'กำลังพาท่านไปที่หน้าเข้าสู่ระบบ...',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    confirmButtonColor: '#10B981',
                }).then(() => {
                    // ส่งค่า status=success ไปด้วยเพื่อให้หน้า login รู้ว่ามาจาก register
                    window.location.href = 'login.php?status=success';
                });
            
            // กรณีมีข้อผิดพลาด (เช่น รหัสไม่ตรง, อีเมลซ้ำ)
            <?php elseif (!empty($message)): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    html: '<?php echo $message; ?>', // ใช้ html เพราะ message อาจมี <br>
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#EF4444', // สีแดง
                });
            <?php endif; ?>

        });
    </script>

</body>
</html>