<?php
ob_start();
include('../system/config.php'); 

// ในไฟล์ assets/backend/index.php
$logoFileName = get_setting('site_logo'); 

if ($logoFileName) {
    // ใช้ Absolute Path ที่เริ่มต้นจาก Root เว็บไซต์
    $finalLogoPath = '/uploads/' . $logoFileName; 
} else {
    // โลโก้เริ่มต้นใน assets/img/
    $finalLogoPath = '/assets/img/aerosol_logo.png'; 
}

// โค้ด HTML/Header ที่คุณส่งมา
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
    <title><?php echo get_setting('site_name', 'Hrel'); ?></title>
    <!-- Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f5f5f5;
        }

        .card {
            background-color: #ffffff;
            border: 1px solid #d1d5db;
        }

        .interactive-card {
            transition: all 0.2s ease-in-out;
        }

        .interactive-card:hover {
            border-color: #6b7280;
            transform: translateY(-2px);
        }

        .modal {
            transition: opacity 0.3s ease;
        }

        .modal-content {
            transition: transform 0.3s ease;
        }

        input[type="range"] {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 6px;
            background: #d1d5db;
            border-radius: 3px;
            outline: none;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: #16a34a;
            cursor: pointer;
            border-radius: 50%;
            border: 3px solid #ffffff;
        }

        .profile-input {
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px 12px;
            width: 100%;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 8px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #16a34a;
            border: 2px solid #ffffff;
        }

        .sensor-btn {
            padding: 6px 12px;
            border-radius: 6px;
            background-color: #f3f4f6;
            /* gray-100 */
            color: #4b5563;
            /* gray-600 */
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .sensor-btn:hover {
            background-color: #e5e7eb;
            /* gray-200 */
        }

        .sensor-btn.active {
            background-color: #16a34a;
            /* green-600 */
            color: white;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        .pagination-btn {
            height: 32px;
            width: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background-color: #f3f4f6;
            /* gray-100 */
            color: #4b5563;
            /* gray-600 */
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .pagination-btn:hover {
            background-color: #e5e7eb;
            /* gray-200 */
        }

        .pagination-btn.active {
            background-color: #16a34a;
            /* green-600 */
            color: white;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
    <title>Aerosol | Home</title>
</head>

<body>

    <?php
    include("./components/aside.php");
    ?>
    
</body>