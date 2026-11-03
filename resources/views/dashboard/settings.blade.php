@extends('layouts.dashboard', ['pageHeader' => 'ตั้งค่าหน้าเว็บ', 'title' => 'ตั้งค่าระบบ'])

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">ตั้งค่าระบบและเว็บไซต์</h1>
            <p class="text-sm text-gray-500">จัดการข้อมูลพื้นฐานและสถานะเปิด-ปิดเว็บไซต์</p>
        </div>
    </div>

    <div class="card rounded-2xl p-6 sm:p-8 shadow-sm">
        <form action="{{ route('settings') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: General -->
            <div class="border-b border-gray-100 pb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-globe text-emerald-600"></i>
                    <span>ข้อมูลทั่วไป (General)</span>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700">ชื่อเว็บไซต์</label>
                        <input 
                            type="text" 
                            name="site_name" 
                            id="site_name" 
                            value="{{ old('site_name', $settings['site_name'] ?? 'Aerosol') }}" 
                            class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                    </div>

                    <div>
                        <label for="site_logo" class="block text-sm font-medium text-gray-700">โลโก้เว็บไซต์</label>
                        <input 
                            type="file" 
                            name="site_logo" 
                            id="site_logo" 
                            accept="image/*"
                            onchange="previewLogo(event)"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100"
                        >
                        
                        <div class="mt-3 flex items-center gap-4">
                            @if(!empty($settings['site_logo']))
                                <div>
                                    <p class="text-xs text-gray-400 mb-1">โลโก้ปัจจุบัน:</p>
                                    <img id="logo-preview" src="/uploads/{{ $settings['site_logo'] }}" alt="Logo" class="h-16 w-16 object-contain rounded-lg border p-1 bg-white">
                                </div>
                            @else
                                <div>
                                    <p class="text-xs text-gray-400 mb-1">โลโก้เริ่มต้น:</p>
                                    <img id="logo-preview" src="/img/aerosol_logo.png" alt="Default Logo" class="h-16 w-16 object-contain rounded-lg border p-1 bg-white">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="site_description" class="block text-sm font-medium text-gray-700">คำอธิบายเว็บไซต์ (Description)</label>
                    <textarea 
                        name="site_description" 
                        id="site_description" 
                        rows="3" 
                        class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                    >{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                </div>
            </div>

            <!-- Section 2: Maintenance Mode -->
            <div class="border-b border-gray-100 pb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-screwdriver-wrench text-amber-600"></i>
                    <span>โหมดปรับปรุงระบบ (Maintenance Mode)</span>
                </h2>

                <div class="rounded-xl bg-amber-50/70 border border-amber-200 p-4">
                    <label class="relative flex items-start gap-3 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="maintenance_mode" 
                            value="1" 
                            {{ (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == '1') ? 'checked' : '' }} 
                            class="mt-1 h-5 w-5 rounded border-gray-300 text-amber-600 focus:ring-amber-500 cursor-pointer"
                        >
                        <div>
                            <span class="font-bold text-amber-900 block">เปิดใช้งานโหมดปิดปรับปรุงเว็บไซต์</span>
                            <span class="text-xs text-amber-700">เมื่อเปิดใช้งาน ผู้เข้าชมทั่วไปจะถูกนำไปที่หน้าปิดปรับปรุงระบบ แต่ผู้ดูแลระบบ (Admin) ยังสามารถเข้าใช้งานได้ตามปกติ</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 px-6 py-2.5 text-white font-semibold shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>บันทึกการตั้งค่า</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewLogo(event) {
        const file = event.target.files[0];
        if (file) {
            const preview = document.getElementById('logo-preview');
            preview.src = URL.createObjectURL(file);
        }
    }
</script>
@endpush

