@extends('layouts.dashboard', ['pageHeader' => 'จัดการผู้ใช้', 'title' => 'จัดการผู้ใช้งาน'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">จัดการผู้ใช้งานระบบ (User Management)</h1>
            <p class="text-sm text-gray-500">เพิ่ม ลบ แก้ไขสิทธิ์ และจัดการบัญชีผู้ใช้งาน</p>
        </div>

        <button onclick="openAddModal()" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-white font-semibold text-sm shadow-md transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-user-plus"></i>
            <span>เพิ่มผู้ใช้งานใหม่</span>
        </button>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Users Table Card -->
    <div class="card rounded-2xl overflow-hidden shadow-sm border border-slate-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs text-gray-500 uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4"># ID</th>
                        <th class="px-6 py-4">ชื่อผู้ใช้ (Username)</th>
                        <th class="px-6 py-4">อีเมล (Email)</th>
                        <th class="px-6 py-4">ระดับสิทธิ์ (Role)</th>
                        <th class="px-6 py-4">วันที่สร้าง (Created At)</th>
                        <th class="px-6 py-4 text-center">จัดการ (Actions)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-mono font-medium text-gray-500">{{ $u->id }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900 flex items-center gap-2">
                                <div class="h-8 w-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold uppercase">
                                    {{ substr($u->username, 0, 2) }}
                                </div>
                                <span>{{ $u->username }}</span>
                                @if($u->id === Auth::id())
                                    <span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-bold">คุณ</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $u->email }}</td>
                            <td class="px-6 py-4">
                                @if($u->level === 'admin')
                                    <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-1 rounded-full border border-amber-200">
                                        <i class="fa-solid fa-shield-halved text-[10px]"></i> ผู้ดูแลระบบ (Admin)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-1 rounded-full border border-gray-200">
                                        <i class="fa-solid fa-user text-[10px]"></i> สมาชิก (Member)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-gray-500">
                                {{ $u->created_at ? $u->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button 
                                        type="button" 
                                        onclick="openEditModal({{ json_encode($u) }})" 
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" 
                                        title="แก้ไข"
                                    >
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    @if($u->id !== Auth::id())
                                        <button 
                                            type="button" 
                                            onclick="confirmDeleteUser({{ $u->id }}, '{{ $u->username }}')" 
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" 
                                            title="ลบ"
                                        >
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">ยังไม่มีผู้ใช้งานในระบบ</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="add-user-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 sm:p-8 shadow-2xl animate-fade-in">
        <div class="mb-4 flex items-center justify-between border-b pb-3">
            <h3 class="text-xl font-bold text-gray-800">เพิ่มผู้ใช้งานใหม่</h3>
            <button onclick="closeAddModal()" class="text-2xl font-bold text-gray-400 hover:text-gray-600">&times;</button>
        </div>

        <form action="{{ route('users') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700">ชื่อผู้ใช้ (Username)</label>
                <input type="text" name="username" required class="mt-1 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700">อีเมล (Email)</label>
                <input type="email" name="email" required class="mt-1 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700">รหัสผ่าน (Password)</label>
                <input type="password" name="password" minlength="6" required class="mt-1 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700">ระดับสิทธิ์ (Role)</label>
                <select name="level" class="mt-1 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                    <option value="member">สมาชิก (Member)</option>
                    <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                </select>
            </div>

            <div class="mt-6 flex justify-end gap-2 border-t pt-4">
                <button type="button" onclick="closeAddModal()" class="rounded-lg bg-gray-100 hover:bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-600">ยกเลิก</button>
                <button type="submit" class="rounded-lg bg-emerald-600 hover:bg-emerald-700 px-5 py-2 text-sm font-semibold text-white shadow">สร้างผู้ใช้</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="edit-user-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 sm:p-8 shadow-2xl animate-fade-in">
        <div class="mb-4 flex items-center justify-between border-b pb-3">
            <h3 class="text-xl font-bold text-gray-800">แก้ไขข้อมูลผู้ใช้งาน</h3>
            <button onclick="closeEditModal()" class="text-2xl font-bold text-gray-400 hover:text-gray-600">&times;</button>
        </div>

        <form id="edit-user-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-gray-700">ชื่อผู้ใช้ (Username)</label>
                <input type="text" id="edit-username" name="username" required class="mt-1 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700">อีเมล (Email)</label>
                <input type="email" id="edit-email" name="email" required class="mt-1 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700">รหัสผ่านใหม่ (เว้นว่างไว้ถ้าไม่ต้องการเปลี่ยน)</label>
                <input type="password" name="password" minlength="6" placeholder="ปล่อยว่างเพื่อคงรหัสเดิม" class="mt-1 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700">ระดับสิทธิ์ (Role)</label>
                <select id="edit-level" name="level" class="mt-1 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                    <option value="member">สมาชิก (Member)</option>
                    <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                </select>
            </div>

            <div class="mt-6 flex justify-end gap-2 border-t pt-4">
                <button type="button" onclick="closeEditModal()" class="rounded-lg bg-gray-100 hover:bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-600">ยกเลิก</button>
                <button type="submit" class="rounded-lg bg-emerald-600 hover:bg-emerald-700 px-5 py-2 text-sm font-semibold text-white shadow">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="delete-user-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function openAddModal() {
        document.getElementById('add-user-modal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('add-user-modal').classList.add('hidden');
    }

    function openEditModal(user) {
        document.getElementById('edit-username').value = user.username;
        document.getElementById('edit-email').value = user.email;
        document.getElementById('edit-level').value = user.level;
        document.getElementById('edit-user-form').action = '/users/' + user.id;
        document.getElementById('edit-user-modal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('edit-user-modal').classList.add('hidden');
    }

    function confirmDeleteUser(userId, username) {
        Swal.fire({
            title: 'ยืนยันการลบผู้ใช้?',
            text: `ต้องการลบผู้ใช้ "${username}" ใช่หรือไม่ การกระทำนี้ไม่สามารถย้อนกลับได้`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'ใช่, ลบผู้ใช้',
            cancelButtonText: 'ยกเลิก'
        }).then(res => {
            if (res.isConfirmed) {
                const form = document.getElementById('delete-user-form');
                form.action = '/users/' + userId;
                form.submit();
            }
        });
    }
</script>
@endpush

