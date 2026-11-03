<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ | Aerosol</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="font-body relative flex min-h-screen items-center justify-center overflow-hidden p-4">
    <!-- Background Image -->
    <div class="absolute inset-0 -z-10 h-full w-full">
        <img src="/img/background_2.png" alt="" class="h-full w-full object-cover brightness-[0.6]">
    </div>

    <div class="auth-card w-full max-w-md p-8 animate-fade-in">
        <div class="mb-8 text-center">
            <a href="{{ route('home') }}">
                <img src="/img/aerosol.png" alt="aerosol_logo" class="mx-auto h-28 w-auto object-contain">
            </a>
            <h2 class="mt-4 text-3xl font-bold text-gray-800">เข้าสู่ระบบ</h2>
            <p class="mt-2 text-gray-500">สัมผัสธรรมชาติแห่งการเชื่อมต่อ</p>
        </div>

        @if(request('status') === 'success')
            <div class="mb-5 rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-700 flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>ลงทะเบียนสำเร็จ! กรุณาเข้าสู่ระบบ</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-5 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            
            <div class="relative">
                <i class="fa fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input 
                    type="text" 
                    name="username" 
                    placeholder="ชื่อผู้ใช้" 
                    value="{{ old('username') }}" 
                    class="w-full rounded-lg border border-gray-300 py-3 pl-12 pr-4 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                    required 
                    autofocus
                >
            </div>

            <div class="relative">
                <i class="fa fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="รหัสผ่าน" 
                    class="w-full rounded-lg border border-gray-300 py-3 pl-12 pr-4 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                    required
                >
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center text-gray-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 mr-2">
                    จดจำการเข้าสู่ระบบ
                </label>
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-emerald-600 transition-colors">
                    กลับหน้าหลัก
                </a>
            </div>

            <button 
                type="submit" 
                class="w-full rounded-lg bg-gradient-to-r from-emerald-500 to-green-600 py-3 font-semibold text-white shadow-md transition-all duration-300 hover:from-emerald-600 hover:to-green-700 hover:shadow-lg"
            >
                เข้าสู่ระบบ
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            ยังไม่มีบัญชีใช่หรือไม่? 
            <a href="{{ route('register') }}" class="font-medium text-emerald-600 hover:underline">
                สมัครสมาชิกที่นี่
            </a>
        </p>
    </div>
</body>
</html>

