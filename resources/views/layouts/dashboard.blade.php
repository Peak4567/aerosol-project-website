<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} | {{ $siteName ?? 'Aerosol' }}</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#16a34a',
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome Pro 6 -->
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    
    <!-- Custom Application CSS -->
    <link rel="stylesheet" href="/css/app.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased">

    <!-- Mobile Overlay Backdrop -->
    <div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 z-30 bg-black/40 hidden sm:hidden"></div>

    <!-- Sidebar Aside -->
    <aside id="sidebar" class="fixed left-0 top-0 z-40 h-screen w-64 border-r border-gray-200 bg-white shadow-xl transition-transform -translate-x-full sm:translate-x-0">
        <div class="flex items-center justify-center border-b border-gray-200 p-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <img src="/img/aerosol_logo.png" alt="Logo" class="h-8 w-8 object-contain">
                <span class="self-center whitespace-nowrap text-2xl font-bold text-gray-800">
                    {{ $siteName ?? 'Aerosol' }}
                </span>
            </a>
        </div>

        <div class="flex h-full flex-col justify-between overflow-y-auto px-3 pb-[100px] pt-4">
            <ul class="space-y-1 font-medium">
                <li class="px-2 pb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">
                    เมนูหลัก
                </li>
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center rounded-lg p-2 transition-colors {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fa-solid fa-table-columns h-6 w-6 flex items-center justify-center {{ request()->routeIs('dashboard') ? 'text-green-600' : 'text-gray-500' }}"></i>
                        <span class="ms-3">แผงหน้าปัด</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('smartfarm') }}" class="flex items-center rounded-lg p-2 transition-colors {{ request()->routeIs('smartfarm') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fa-solid fa-robot h-6 w-6 flex items-center justify-center {{ request()->routeIs('smartfarm') ? 'text-green-600' : 'text-gray-500' }}"></i>
                        <span class="ms-3">ปัญญาประดิษฐ์</span>
                    </a>
                </li>

                @if(Auth::check() && Auth::user()->isAdmin())
                    <li class="px-2 pb-1 pt-4 text-xs font-semibold uppercase tracking-wide text-gray-400">
                        ตั้งค่า
                    </li>
                    <li>
                        <a href="{{ route('settings') }}" class="flex items-center rounded-lg p-2 transition-colors {{ request()->routeIs('settings') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fa-solid fa-sliders h-6 w-6 flex items-center justify-center {{ request()->routeIs('settings') ? 'text-green-600' : 'text-gray-500' }}"></i>
                            <span class="ms-3">ตั้งค่าหน้าเว็บ</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users') }}" class="flex items-center rounded-lg p-2 transition-colors {{ request()->routeIs('users') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fa-solid fa-users h-6 w-6 flex items-center justify-center {{ request()->routeIs('users') ? 'text-green-600' : 'text-gray-500' }}"></i>
                            <span class="ms-3">จัดการผู้ใช้</span>
                        </a>
                    </li>
                @endif
            </ul>

            <ul class="mt-4 space-y-1 border-t border-gray-200 pt-4 font-medium">
                <li>
                    <a href="{{ route('simulator') }}" target="_blank" class="flex items-center rounded-lg p-2 text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">
                        <i class="fa-solid fa-gamepad h-6 w-6 flex items-center justify-center text-purple-500"></i>
                        <span class="ms-3 flex-1 whitespace-nowrap">ตัวจำลองข้อมูล (Simulator)</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('home') }}" class="group flex items-center rounded-lg p-2 text-gray-700 hover:bg-gray-100">
                        <i class="fa-solid fa-home h-6 w-6 flex items-center justify-center text-gray-500 group-hover:text-gray-900"></i>
                        <span class="ms-3 flex-1 whitespace-nowrap">กลับหน้าหลัก</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="sm:ml-64">
        <!-- Top Nav Bar -->
        <nav class="sticky top-0 z-20 w-full border-b border-gray-200 bg-white shadow-sm">
            <div class="px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <button onclick="toggleSidebar()" type="button" class="inline-flex items-center rounded-lg p-2 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none sm:hidden">
                            <span class="sr-only">Open sidebar</span>
                            <i class="fa-solid fa-bars text-xl"></i>
                        </button>
                        <h1 class="ml-2 text-xl font-semibold text-gray-800 sm:ml-0">
                            {{ $pageHeader ?? 'Dashboard' }}
                        </h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- DB Status Badge -->
                        <div id="db-status-badge" class="flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 border border-emerald-200 shadow-xs">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span id="db-status-text">DB Online</span>
                        </div>

                        <!-- Username Badge -->
                        <span class="hidden text-sm font-medium text-gray-700 sm:inline flex items-center gap-1.5 bg-gray-100 py-1 px-3 rounded-full">
                            <i class="fa-solid fa-user-circle text-gray-500 text-base"></i>
                            {{ Auth::user()->username ?? 'User' }}
                            @if(Auth::check() && Auth::user()->isAdmin())
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-1.5 py-0.5 rounded">Admin</span>
                            @endif
                        </span>

                        <!-- Logout Button -->
                        <button type="button" onclick="confirmLogout()" class="rounded-full p-2 text-gray-600 hover:bg-red-50 hover:text-red-600 transition-colors" title="ออกจากระบบ">
                            <i class="fa-solid fa-right-from-bracket text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Body -->
        <main class="p-4 md:p-6">
            @yield('content')
        </main>
    </div>

    <!-- Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        function confirmLogout() {
            Swal.fire({
                title: 'ยืนยันการออกจากระบบ?',
                text: 'คุณต้องการออกจากระบบใช่หรือไม่',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'ใช่, ออกจากระบบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        // DB Health poller
        async function checkDbHealth() {
            try {
                const res = await fetch('/api/health');
                const data = await res.json();
                const badge = document.getElementById('db-status-badge');
                const text = document.getElementById('db-status-text');
                if (data.status === 'ok') {
                    badge.className = "flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 border border-emerald-200 shadow-xs";
                    text.textContent = "DB Online";
                } else {
                    badge.className = "flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700 border border-red-200 shadow-xs";
                    text.textContent = "DB Offline";
                }
            } catch (e) {
                const badge = document.getElementById('db-status-badge');
                const text = document.getElementById('db-status-text');
                if (badge && text) {
                    badge.className = "flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-700 border border-red-200 shadow-xs";
                    text.textContent = "DB Offline";
                }
            }
        }
        setInterval(checkDbHealth, 15000);
    </script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: "{{ session('success') }}",
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด',
                text: "{{ session('error') }}"
            });
        </script>
    @endif

    @stack('scripts')
</body>
</html>

