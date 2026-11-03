<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $siteName = Setting::get('site_name', 'Aerosol');
        $user = Auth::user();
        $users = User::orderBy('created_at', 'desc')->get();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['users' => $users]);
        }

        return view('dashboard.users', compact('siteName', 'user', 'users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'level' => 'required|in:member,admin',
        ], [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้',
            'username.unique' => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว',
            'username.min' => 'ชื่อผู้ใช้ต้องมีอย่างน้อย 3 ตัวอักษร',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
            'level.required' => 'กรุณาเลือกระดับผู้ใช้',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => $validator->errors()->first()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'level' => $request->level,
        ]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['status' => 'success', 'message' => 'เพิ่มผู้ใช้สำเร็จแล้ว!'], 201);
        }

        return back()->with('success', 'เพิ่มผู้ใช้สำเร็จแล้ว!');
    }

    public function update(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50|unique:users,username,' . $targetUser->id,
            'email' => 'required|email|max:255|unique:users,email,' . $targetUser->id,
            'password' => 'nullable|string|min:6',
            'level' => 'required|in:member,admin',
        ], [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้',
            'username.unique' => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'password.min' => 'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร',
            'level.required' => 'กรุณาเลือกระดับผู้ใช้',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => $validator->errors()->first()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $targetUser->username = $request->username;
        $targetUser->email = $request->email;
        $targetUser->level = $request->level;

        if ($request->filled('password')) {
            $targetUser->password = Hash::make($request->password);
        }

        $targetUser->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['status' => 'success', 'message' => 'แก้ไขผู้ใช้สำเร็จแล้ว!']);
        }

        return back()->with('success', 'แก้ไขผู้ใช้สำเร็จแล้ว!');
    }

    public function destroy(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);

        if ($targetUser->id === Auth::id()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'ไม่สามารถลบบัญชีของตัวเองได้'], 400);
            }
            return back()->with('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
        }

        $targetUser->delete();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['status' => 'success', 'message' => 'ลบผู้ใช้สำเร็จแล้ว']);
        }

        return back()->with('success', 'ลบผู้ใช้สำเร็จแล้ว');
    }
}

