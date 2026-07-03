<?php
session_start();

$userLevel = $_SESSION['user']['level'] ?? 'member';

$requiredLevel = 'admin';
$isAdmin = ($userLevel === $requiredLevel);
?>
<aside id="logo-sidebar"
    class="shadow-xl fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r border-gray-200 sm:translate-x-0 transition-transform -translate-x-full"
    aria-label="Sidebar">

    <div class="flex items-center justify-center p-5 mb-2 border-b border-gray-200">
        <span class="self-center text-2xl font-semibold whitespace-nowrap text-gray-800">
            <?php echo get_setting('site_name', 'Aerosol'); ?>
        </span>
    </div>
    <div class="h-full flex flex-col justify-between px-3 pb-[100px] overflow-y-auto bg-white">
        <ul class="space-y-2 font-medium">
            <h1 class="text-gray-700 rounded-lg hover:bg-gray-100">เมนูหลัก</h1>
            <li>
                <a href="/assets/backend/dashboard.php" class="flex items-center p-2 text-gray-700 rounded-lg hover:bg-gray-100">
                    <i class="fa-solid fa-table-columns w-6 h-6 text-gray-500"></i>
                    <span class="ms-3">แผงหน้าปัด</span>
                </a>
            </li>
            <li>
                <a href="/assets/backend/smartfarm/index.php" class="flex items-center p-2 text-gray-700 rounded-lg hover:bg-gray-100 group">
                    <i class="fa-solid fa-user-robot w-6 h-6 text-gray-500 group-hover:text-gray-900"></i>
                    <span class="flex-1 ms-3 whitespace-nowrap">ปัญญาประดิษฐ์</span>
                </a>
            </li>
            <?php if ($isAdmin): // ตรวจสอบ: ถ้าเป็น Admin ถึงจะแสดงส่วนตั้งค่า 
            ?>
                <h1 class="text-gray-700 rounded-lg hover:bg-gray-100">ตั้งค่า</h1>
                <li>
                    <a href="/assets/backend/settings.php" class="flex items-center p-2 text-gray-700 rounded-lg hover:bg-gray-100 group">
                        <i class="fa-solid fa-browser w-6 h-6 text-gray-500 group-hover:text-gray-900"></i>
                        <span class="flex-1 ms-3 whitespace-nowrap">ตั้งค่าหน้าเว็บ</span>
                    </a>
                </li>
                <li>
                    <a href="/assets/backend/users.php" class="flex items-center p-2 text-gray-700 rounded-lg hover:bg-gray-100 group">
                        <i class="fa-solid fa-users w-6 h-6 text-gray-500 group-hover:text-gray-900"></i>
                        <span class="flex-1 ms-3 whitespace-nowrap">จัดการผู้ใช้</span>
                    </a>
                </li>
            <?php endif; // สิ้นสุดการตรวจสอบ Admin 
            ?>
        </ul>

        <ul class="pt-4 mt-4 space-y-2 font-medium border-t border-gray-200">
            <li>
                <a href="/" class="flex items-center p-2 text-gray-700 rounded-lg hover:bg-gray-100 group">
                    <i class="fa-solid fa-home w-6 h-6 text-gray-500 group-hover:text-gray-900"></i>
                    <span class="flex-1 ms-3 whitespace-nowrap">กลับหน้าหลัก</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

<div class="sm:ml-64">

    <nav class="sticky top-0 z-30 w-full bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar"
                        aria-controls="logo-sidebar" type="button"
                        class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none">
                        <span class="sr-only">Open sidebar</span>
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800 ml-2 sm:ml-0">Dashboard</h1>
                </div>

                <div class="flex items-center space-x-4">
                    <button type="button" class="p-2 rounded-full text-gray-600 hover:bg-gray-100">
                        <span class="sr-only">Toggle dark mode</span>
                        <i class="fa-solid fa-sun-bright text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>
</div>