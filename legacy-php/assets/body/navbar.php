<nav class="bg-white fixed w-full z-20 top-0 start-0 border-b border-gray-200 p-3">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-2 relative">

        <div class="absolute left-1/2 top-full -translate-x-1/2 -translate-y-1/2 
           w-24 h-24 bg-white rounded-full 
           flex items-center justify-center 
           shadow-lg border-4 border-white">
            <a href="/">
                <img src="/assets/img/aerosol_logo.png" alt="Aerosol Logo" class="w-25 h-25 object-contain">
            </a>
        </div>

        <div class="items-center justify-between hidden w-full md:flex md:w-auto" id="navbar-sticky">
            <ul class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white">
                <li><a href="#home" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-emerald-500 md:p-0 transition-colors">หน้าหลัก</a></li>
                <li><a href="#about" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-emerald-500 md:p-0 transition-colors">เกี่ยวกับเรา</a></li>
                <li><a href="#what" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-emerald-500 md:p-0 transition-colors">ข้อสงสัย</a></li>
                <li><a href="#myteam" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-emerald-500 md:p-0 transition-colors">คณะผู้จัดทํา</a></li>
            </ul>
        </div>

        <div class="flex items-center space-x-3 rtl:space-x-reverse">

            <?php if (isset($_SESSION['id'])): ?>


                <a href="assets/backend/dashboard.php" class="text-white bg-gradient-to-r from-emerald-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center hidden md:block">
                    Dashboard
                </a>

                <a href="assets/system/logout.php" onclick="confirmLogout(event)" class="text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center hidden md:block transition-colors">
                    <i class="fa-solid fa-right-from-bracket mr-1"></i> Logout
                </a>

            <?php else: ?>
                <a href="assets/page/login.php" class="text-white bg-gradient-to-r from-emerald-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center hidden md:block">
                    เข้าสู่ระบบ
                </a>
            <?php endif; ?>

            <button data-collapse-toggle="navbar-sticky" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200" aria-controls="navbar-sticky" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
        </div>

    </div>
</nav>