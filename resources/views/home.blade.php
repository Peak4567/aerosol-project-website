@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section id="home" class="relative flex h-screen items-start justify-center pt-[150px] text-center">
    <div class="absolute inset-0 -z-10 h-full w-full">
        <img src="/img/background.png" alt="Farm background" class="h-full w-full object-cover brightness-75">
    </div>
    
    <div class="relative z-10 w-[92%] max-w-[595px] px-4">
        <h1 class="text-4xl font-bold text-white drop-shadow-sm md:text-6xl">
            Aerosol
        </h1>
        <h2 class="text-3xl font-bold text-white drop-shadow-sm md:text-4xl">
            AeroFogPonic Farming
        </h2>
        <p class="pt-5 text-white/90">
            สัมผัสนวัตกรรมการเกษตรไร้ดิน ที่เปลี่ยนอากาศให้กลายเป็นพื้นที่เพาะปลูกคุณภาพสูง
            ให้ผลผลิตที่โตเร็วขึ้น สะอาดกว่า และประหยัดน้ำสูงสุดถึง 95%
        </p>
        <a href="#about" class="mt-8 inline-block rounded-full bg-black px-10 py-3 font-semibold text-white transition-colors duration-300 hover:bg-gray-800">
            ดูเพิ่มเติม
        </a>
    </div>

    <!-- Wavy SVG Divider -->
    <div class="absolute bottom-[200px] left-0 w-full overflow-hidden leading-none pointer-events-none">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block h-[150px] w-full">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="fill-current text-white"></path>
        </svg>
    </div>
</section>

<!-- About Section -->
<section id="about" class="relative -mt-[200px] bg-white pb-20 pt-10 text-black">
    <div class="container mx-auto px-4 text-center">
        <div>
            <h2 class="mb-4 text-4xl font-bold md:text-5xl">
                Aerosol Project
            </h2>
            <p class="mb-16 text-lg text-gray-600">
                ส่วนประกอบพัฒนาสำคัญสำหรับโพรเจ็กต์เรา
            </p>
        </div>

        <div class="mx-auto max-w-4xl rounded-2xl bg-[#F5E6BE] p-8 text-gray-800 shadow-lg">
            <div class="flex flex-col items-center justify-around space-y-8 md:flex-row md:space-y-0">
                <div class="flex w-full flex-col items-center text-center md:w-1/3">
                    <i class="fa-solid fa-potato pb-2 text-7xl text-amber-800"></i>
                    <p class="text-3xl font-bold">มันฝรั่ง</p>
                    <p class="text-base text-gray-700">พืชใช้ในการเพาะปลูก</p>
                </div>
                <div class="flex w-full flex-col items-center text-center md:w-1/3">
                    <i class="fa-solid fa-square-terminal pb-2 text-7xl text-amber-800"></i>
                    <p class="text-3xl font-bold">แผงควบคุม</p>
                    <p class="text-base text-gray-700">สำหรับแสดงผลข้อมูล</p>
                </div>
                <div class="flex w-full flex-col items-center text-center md:w-1/3">
                    <i class="fa-solid fa-user-robot pb-2 text-7xl text-amber-800"></i>
                    <p class="text-3xl font-bold">ปัญญาประดิษฐ์</p>
                    <p class="text-base text-gray-700">ควบคุมอัตโนมัติ</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Content Pillars Section -->
<section class="py-16 md:py-24 bg-slate-50">
    <div class="container mx-auto space-y-20 px-6">
        <!-- 1: ทำไมถึงเลือกปลูกมันฝรั่ง -->
        <div class="flex flex-col items-center gap-12 lg:flex-row lg:gap-20">
            <div class="relative w-full lg:w-5/12">
                <div class="absolute -left-4 -top-4 z-0 h-full w-full -rotate-2 rounded-lg border-4 border-yellow-400"></div>
                <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-yellow-300 opacity-70"></div>
                <img src="/img/1.png" alt="ปลูกมันฝรั่ง" class="relative z-10 w-full rounded-lg shadow-xl object-cover">
            </div>
            <div class="w-full lg:w-7/12">
                <h2 class="text-3xl font-bold text-gray-800 md:text-4xl">
                    ทำไมเราถึงเลือกปลูกมันฝรั่ง?
                </h2>
                <p class="mt-4 text-lg text-gray-600 leading-relaxed">
                    ประเทศไทยกำลังเผชิญปัญหาอุทกภัย โดยเฉพาะในภาคเหนือ ซึ่งเป็นแหล่งปลูกมันฝรั่งสำคัญ
                    ส่งผลให้ดินเสียหายและกระทบต่อเศรษฐกิจและผลผลิตการเกษตร คณะผู้จัดทำจึงพัฒนานวัตกรรมนี้
                    เพื่อช่วยให้เพาะปลูกได้แม้ในสภาวะไม่เอื้ออำนวย พร้อมสนับสนุนเกษตรกรรมยั่งยืนในอนาคต
                </p>
                <a href="#what" class="mt-6 inline-block rounded-lg bg-yellow-500 px-8 py-3 font-semibold text-white transition-colors duration-300 hover:bg-yellow-600 shadow-md">
                    อ่านเพิ่มเติม
                </a>
            </div>
        </div>

        <!-- 2: วัตถุประสงค์ -->
        <div class="flex flex-col items-center gap-12 lg:flex-row-reverse lg:gap-20">
            <div class="relative w-full lg:w-5/12">
                <div class="absolute -right-4 -top-4 z-0 h-full w-full rotate-2 rounded-lg border-4 border-red-400"></div>
                <div class="absolute -bottom-6 -left-6 h-20 w-20 rounded-full bg-red-300 opacity-70"></div>
                <img src="/img/2.jpg" alt="วัตถุประสงค์" class="relative z-10 w-full rounded-lg shadow-xl object-cover">
            </div>
            <div class="w-full lg:w-7/12">
                <h2 class="text-3xl font-bold text-gray-800 md:text-4xl">
                    วัตถุประสงค์การปลูกมันฝรั่ง
                </h2>
                <p class="mt-4 text-lg text-gray-600 leading-relaxed">
                    เพื่อเพิ่มประสิทธิภาพการผลิต ลดการใช้ทรัพยากร จัดการความเสี่ยงจากสภาพแวดล้อม
                    ส่งเสริมความยั่งยืน สร้างความสะดวกให้เกษตรกร และผลิตมันฝรั่งคุณภาพสูงตอบสนอง
                    ความต้องการของตลาด
                </p>
                <a href="#what" class="mt-6 inline-block rounded-lg bg-red-500 px-8 py-3 font-semibold text-white transition-colors duration-300 hover:bg-red-600 shadow-md">
                    อ่านเพิ่มเติม
                </a>
            </div>
        </div>

        <!-- 3: SmartFarm IoT -->
        <div class="flex flex-col items-center gap-12 lg:flex-row lg:gap-20">
            <div class="relative w-full lg:w-5/12">
                <div class="absolute -left-4 -top-4 z-0 h-full w-full -rotate-2 rounded-lg border-4 border-blue-400"></div>
                <div class="absolute -bottom-6 -right-6 h-20 w-20 rounded-full bg-blue-300 opacity-70"></div>
                <img src="/img/4.jpg" alt="SmartFarm IoT" class="relative z-10 w-full rounded-lg shadow-xl object-cover">
            </div>
            <div class="w-full lg:w-7/12">
                <h2 class="text-3xl font-bold text-gray-800 md:text-4xl">
                    ระบบ SmartFarm IOT คืออะไร?
                </h2>
                <p class="mt-4 text-lg text-gray-600 leading-relaxed">
                    คือการนำเทคโนโลยี Internet of Things (IoT) มาประยุกต์ใช้ในภาคเกษตรกรรมเพื่อเพิ่ม
                    ประสิทธิภาพและความยั่งยืน โดยระบบนี้ช่วยให้เกษตรกรสามารถตรวจสอบและควบคุมปัจจัยต่างๆ
                    ผ่านเซ็นเซอร์และอุปกรณ์อัจฉริยะที่เชื่อมต่อกัน
                </p>
                <a href="#what" class="mt-6 inline-block rounded-lg bg-blue-500 px-8 py-3 font-semibold text-white transition-colors duration-300 hover:bg-blue-600 shadow-md">
                    อ่านเพิ่มเติม
                </a>
            </div>
        </div>
    </div>
</section>

<!-- What Section (AeroFogPonic Farming) -->
<section id="what" class="py-16 md:py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-20">
            <div class="relative">
                <div class="absolute -left-4 -top-4 z-0 h-full w-full -rotate-2 rounded-lg border-4 border-yellow-400"></div>
                <div class="relative z-10 overflow-hidden rounded-lg shadow-xl">
                    <img src="/img/smartfarm44.jpg" alt="Organic Farmer" class="h-auto w-full object-cover">
                </div>
            </div>

            <div>
                <p class="text-2xl font-medium text-gray-500">
                    ทำไมเราถึงเลือกใช้?
                </p>
                <h2 class="mt-1 text-2xl font-bold text-gray-800 md:text-4xl">
                    AeroFogPonic Farming
                </h2>
                <p class="mt-6 leading-relaxed text-gray-600 text-lg">
                    ระบบปลูกพืชไร้ดินขั้นสูง ที่ให้สารอาหารแก่รากพืชในรูปแบบของ "หมอก"
                    ซึ่งมีอนุภาคเล็กและละเอียดกว่าการพ่นสเปรย์แบบแอโรโพนิกส์ทั่วไป
                </p>

                <div class="mt-8 grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                    <div class="flex items-center">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-700 text-white shadow">
                            <i class="fa-solid fa-seedling text-lg"></i>
                        </div>
                        <span class="ml-4 font-semibold text-gray-700">ต้นพันธ์ปลอดเชื้อโรค</span>
                    </div>
                    <div class="flex items-center">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-700 text-white shadow">
                            <i class="fa-solid fa-cloud-rain text-lg"></i>
                        </div>
                        <span class="ml-4 font-semibold text-gray-700">ควบคุมสภาพแวดล้อม</span>
                    </div>
                    <div class="flex items-center">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-700 text-white shadow">
                            <i class="fa-solid fa-pot-food text-lg"></i>
                        </div>
                        <span class="ml-4 font-semibold text-gray-700">สร้างความมั่นคงทางอาหาร</span>
                    </div>
                    <div class="flex items-center">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-700 text-white shadow">
                            <i class="fa-solid fa-shield-check text-lg"></i>
                        </div>
                        <span class="ml-4 font-semibold text-gray-700">ปลอดสารพิษ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team & Advisors Section -->
<section id="myteam" class="relative overflow-hidden bg-gray-50 py-16 md:py-24">
    <div class="absolute left-0 top-0 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-yellow-200 opacity-30 mix-blend-multiply blur-3xl"></div>
    <div class="absolute bottom-0 right-0 h-64 w-64 translate-x-1/2 translate-y-1/2 rounded-full bg-blue-200 opacity-30 mix-blend-multiply blur-3xl"></div>

    <div class="container relative z-10 mx-auto px-6">
        <div class="mb-12 text-center">
            <span class="text-sm font-semibold uppercase tracking-wider text-yellow-600">
                Our Team
            </span>
            <h2 class="mt-2 text-3xl font-bold text-gray-800 md:text-5xl">
                คณะผู้จัดทำ
            </h2>
            <div class="mx-auto mt-4 h-1 w-24 rounded-full bg-yellow-400"></div>
        </div>

        <!-- Student Team -->
        <div class="mb-20 flex flex-wrap justify-center gap-8">
            @foreach($teamMembers as $member)
                <div class="group w-full min-w-[260px] overflow-hidden rounded-2xl border-b-4 border-transparent bg-white shadow-lg transition-all duration-300 hover:-translate-y-2 {{ $member['accent'] }} sm:w-[calc(50%-2rem)] lg:w-[calc(33.33%-2rem)] xl:w-[calc(20%-2rem)]">
                    <div class="relative h-48 overflow-hidden bg-gray-100">
                        <img src="{{ $member['img'] }}" alt="{{ $member['name'] }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-black/20 transition-all duration-300 group-hover:bg-black/10"></div>
                    </div>
                    <div class="relative p-4 text-center">
                        <div class="relative z-10 mx-auto -mt-14 h-20 w-20 overflow-hidden rounded-full border-4 border-white bg-white shadow-md">
                            <img src="{{ $member['img'] }}" alt="{{ $member['name'] }}" class="h-full w-full object-cover">
                        </div>
                        <h3 class="mt-3 text-lg font-bold text-gray-800">
                            {{ $member['name'] }}
                        </h3>
                        <p class="mb-2 text-xs font-medium uppercase tracking-wide text-yellow-600">
                            {{ $member['role'] }}
                        </p>
                        <p class="mb-3 text-xs text-gray-500">{{ $member['grade'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Advisors -->
        <div class="mb-10 mt-16 text-center">
            <h3 class="text-2xl font-bold text-gray-800 md:text-3xl">
                ครูที่ปรึกษาโครงงาน
            </h3>
            <div class="mx-auto mt-3 h-1 w-16 rounded-full bg-gray-300"></div>
        </div>

        <div class="mx-auto grid max-w-5xl grid-cols-1 gap-8 md:grid-cols-3">
            @foreach($advisors as $advisor)
                <div class="flex flex-col items-center rounded-xl border border-gray-100 bg-white p-6 text-center shadow-md transition-shadow duration-300 hover:shadow-lg">
                    <div class="mb-4 h-24 w-24 overflow-hidden rounded-full bg-gray-200 ring-4 ring-gray-50 shadow">
                        <img src="{{ $advisor['img'] }}" alt="{{ $advisor['name'] }}" class="h-full w-full object-cover">
                    </div>
                    <h4 class="text-lg font-bold text-gray-800">
                        {{ $advisor['name'] }}
                    </h4>
                    <p class="text-sm text-gray-500">{{ $advisor['role'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

