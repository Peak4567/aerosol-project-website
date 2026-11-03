<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Aerosol | AeroFogPonic Farming' }}</title>
    <meta name="description" content="Aerosol นวัตกรรม AeroFogPonic Farming">
    
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

    <!-- Font Awesome Pro 6 (self-hosted) -->
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    
    <!-- Custom Application CSS -->
    <link rel="stylesheet" href="/css/app.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>
<body class="antialiased text-slate-800 bg-white">

    <!-- Public Navbar -->
    <nav class="fixed top-0 start-0 z-40 w-full border-b border-gray-200 bg-white p-3">
        <div class="relative mx-auto flex max-w-screen-xl flex-wrap items-center justify-between p-2">
            <!-- Center Logo Bubble -->
            <div class="absolute left-1/2 top-full flex h-24 w-24 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-4 border-white bg-white shadow-lg">
                <a href="{{ route('home') }}">
                    <img src="/img/aerosol_logo.png" alt="Aerosol Logo" class="h-20 w-20 object-contain">
                </a>
            </div>

            <!-- Nav Links (Desktop) -->
            <div id="navbar-default" class="hidden w-full md:flex md:w-auto md:items-center md:justify-between">
                <ul class="mt-4 flex flex-col rounded-lg border border-gray-100 bg-gray-50 p-4 font-medium md:mt-0 md:flex-row md:space-x-8 md:border-0 md:bg-white md:p-0">
                    <li>
                        <a href="{{ route('home') }}#home" class="block rounded px-3 py-2 text-gray-900 transition-colors hover:bg-gray-100 md:p-0 md:hover:bg-transparent md:hover:text-emerald-500">หน้าหลัก</a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}#about" class="block rounded px-3 py-2 text-gray-900 transition-colors hover:bg-gray-100 md:p-0 md:hover:bg-transparent md:hover:text-emerald-500">เกี่ยวกับเรา</a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}#what" class="block rounded px-3 py-2 text-gray-900 transition-colors hover:bg-gray-100 md:p-0 md:hover:bg-transparent md:hover:text-emerald-500">ข้อสงสัย</a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}#myteam" class="block rounded px-3 py-2 text-gray-900 transition-colors hover:bg-gray-100 md:p-0 md:hover:bg-transparent md:hover:text-emerald-500">คณะผู้จัดทำ</a>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden rounded-lg bg-gradient-to-r from-emerald-500 to-green-600 px-5 py-2.5 text-center text-sm font-medium text-white transition-all hover:from-emerald-600 hover:to-green-700 md:block">
                        Dashboard
                    </a>
                    <button type="button" onclick="confirmLogout()" class="hidden items-center rounded-lg bg-red-500 px-5 py-2.5 text-center text-sm font-medium text-white transition-colors hover:bg-red-600 md:inline-flex">
                        <i class="fa-solid fa-right-from-bracket mr-1"></i> Logout
                    </button>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-lg bg-gradient-to-r from-emerald-500 to-green-600 px-5 py-2.5 text-center text-sm font-medium text-white transition-all hover:from-emerald-600 hover:to-green-700 md:block">
                        เข้าสู่ระบบ
                    </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button type="button" id="mobile-toggle" onclick="toggleMobileMenu()" class="inline-flex h-10 w-10 items-center justify-center rounded-lg p-2 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none md:hidden">
                    <span class="sr-only">Open main menu</span>
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Container -->
        <div id="mobile-menu" class="hidden md:hidden px-4 pt-3 pb-4 space-y-2 border-t border-gray-100 mt-2">
            <a href="{{ route('home') }}#home" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 rounded">หน้าหลัก</a>
            <a href="{{ route('home') }}#about" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 rounded">เกี่ยวกับเรา</a>
            <a href="{{ route('home') }}#what" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 rounded">ข้อสงสัย</a>
            <a href="{{ route('home') }}#myteam" class="block py-2 px-3 text-gray-700 hover:bg-gray-50 rounded">คณะผู้จัดทำ</a>
            <div class="pt-2 border-t border-gray-100 flex flex-col gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full text-center py-2 px-4 bg-emerald-600 text-white rounded-lg font-medium">Dashboard</a>
                    <button type="button" onclick="confirmLogout()" class="w-full text-center py-2 px-4 bg-red-500 text-white rounded-lg font-medium">Logout</button>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center py-2 px-4 bg-emerald-600 text-white rounded-lg font-medium">เข้าสู่ระบบ</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main>
        @yield('content')
    </main>

    <!-- Public Footer -->
    <footer class="bg-[#4a5c42]">
        <div class="relative bg-cover bg-center">
            <div class="absolute inset-0 bg-black/60"></div>

            <div class="container relative z-10 mx-auto px-6 py-16">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <a href="{{ route('home') }}#home" class="flex items-center gap-2">
                            <img src="/img/aerosol_logo.png" alt="Aerosol Logo" class="h-12 w-12 object-contain">
                            <span class="text-2xl font-bold text-white">Aerosol</span>
                        </a>
                    </div>

                    <div>
                        <h3 class="mb-4 text-xl font-bold text-white">หน้าเว็บ</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('home') }}#home" class="text-gray-300 transition-colors hover:text-white">หน้าหลัก</a></li>
                            <li><a href="{{ route('home') }}#about" class="text-gray-300 transition-colors hover:text-white">เกี่ยวกับเรา</a></li>
                            <li><a href="{{ route('home') }}#what" class="text-gray-300 transition-colors hover:text-white">ข้อสงสัย</a></li>
                            <li><a href="{{ route('home') }}#myteam" class="text-gray-300 transition-colors hover:text-white">คณะผู้จัดทำ</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="mb-4 text-xl font-bold text-white">ช่องทางติดต่อ</h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-center gap-3">
                                <i class="fa-solid fa-phone w-5 shrink-0"></i>
                                <span>08 1567 4654</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <i class="fa-solid fa-envelope w-5 shrink-0"></i>
                                <span>aerosol@gmail.com</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-location-dot mt-1 w-5 shrink-0"></i>
                                <span>โรงเรียนชลประทานวิทยา นนทบุรี 11120</span>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="mb-4 text-xl font-bold text-white">ติดต่องาน</h3>
                        <div class="relative">
                            <input type="email" placeholder="Email Address" class="w-full rounded-full border border-white/20 bg-white/10 py-3 pl-4 pr-12 text-white placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-white/50">
                            <button class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white/20 transition-colors hover:bg-white/30">
                                <i class="fa-solid fa-arrow-right text-white"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-6 py-4">
            <div class="flex flex-col items-center justify-between text-sm text-gray-300 sm:flex-row">
                <p>&copy; Copyright {{ date('Y') }} By Aeroponic CPW</p>
                <div class="mt-4 flex gap-4 sm:mt-0">
                    <a href="#" class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 transition-colors hover:bg-white/20">
                        <i class="fa-brands fa-facebook-f text-white"></i>
                    </a>
                    <a href="#" class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 transition-colors hover:bg-white/20">
                        <i class="fa-brands fa-twitter text-white"></i>
                    </a>
                    <a href="#" class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 transition-colors hover:bg-white/20">
                        <i class="fa-brands fa-instagram text-white"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
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

