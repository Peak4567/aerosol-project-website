<?php
session_start();
require_once('assets/system/check_maintenance.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- css -->
    <link rel="stylesheet" href="/assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/assets/css/font-awesome-pro-v6-6.2.0/css/all.min.css">

    <!-- Prompt Font -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- animation -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <!-- script tailwind css -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Aerosol | Home</title>
</head>

<body>
    <?php
    include('./assets/body/navbar.php');
    ?>
    <?php
    include('./assets/page/content.php')
    ?>

    <?php include('./assets/body/footer.php'); ?>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="/assets/js/text.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="tailwind.config.js"></script>
    <script>
        function confirmLogout(e) {
            // 1. หยุดการทำงานของลิงก์ไว้ก่อน (ไม่ให้เปลี่ยนหน้าทันที)
            e.preventDefault();

            // ดึง URL จาก href ของปุ่มที่กด
            var urlToRedirect = e.currentTarget.getAttribute('href');

            // 2. แสดง SweetAlert2
            Swal.fire({
                title: 'ยืนยันการออกจากระบบ?',
                text: "คุณต้องการออกจากระบบใช่หรือไม่",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', // สีแดงให้สื่อถึงการ Logout
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ออกจากระบบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                // 3. ถ้าผู้ใช้กดปุ่ม "ยืนยัน"
                if (result.isConfirmed) {
                    // ส่งผู้ใช้ไปยัง URL Logout
                    window.location.href = urlToRedirect;
                }
            });
        }
    </script>
</body>

</html>