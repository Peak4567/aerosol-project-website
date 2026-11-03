<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เว็บไซต์ปิดปรับปรุงชั่วคราว | Aerosol</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body class="font-body flex min-h-screen flex-col items-center justify-center bg-gray-100 p-4 text-center">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl border border-gray-100">
        <div class="mb-4 text-6xl animate-bounce">🛠️</div>
        <h1 class="mb-2 text-2xl font-bold text-gray-800">
            เว็บไซต์ปิดปรับปรุงชั่วคราว
        </h1>
        <p class="mb-6 text-gray-600">
            เรากำลังเร่งแก้ไขปัญหาและอัปเดตระบบ โปรดรอสักครู่
        </p>
        <div class="mb-4 h-2.5 w-full rounded-full bg-gray-200 overflow-hidden">
            <div class="h-2.5 w-3/4 rounded-full bg-emerald-600 animate-pulse"></div>
        </div>
        <div class="mt-6 flex justify-center gap-4 text-sm text-gray-500">
            <a href="{{ route('login') }}" class="text-emerald-600 hover:underline">เข้าสู่ระบบสำหรับผู้ดูแล</a>
        </div>
        <p class="mt-6 text-xs text-gray-400">
            &copy; {{ date('Y') }} AI Aerosol Project
        </p>
    </div>
</body>
</html>

