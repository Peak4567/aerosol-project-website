<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index()
    {
        $siteName = Setting::get('site_name', 'Aerosol');
        $user = Auth::user();
        $settings = Setting::getAll();

        return view('dashboard.settings', compact('siteName', 'user', 'settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'site_logo' => 'nullable|image|max:2048',
        ]);

        if ($request->has('site_name')) {
            Setting::set('site_name', $request->input('site_name'));
        }

        if ($request->has('site_description')) {
            Setting::set('site_description', $request->input('site_description'));
        }

        // Maintenance mode: checkboxes send 1 if checked, absent if unchecked
        $maintenanceMode = $request->has('maintenance_mode') && $request->input('maintenance_mode') == '1' ? '1' : '0';
        Setting::set('maintenance_mode', $maintenanceMode);

        if ($request->hasFile('site_logo')) {
            $file = $request->file('site_logo');
            $uploadDir = public_path('uploads');

            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }

            // Remove old logo if exists
            $oldLogo = Setting::get('site_logo');
            if ($oldLogo && File::exists($uploadDir . '/' . $oldLogo)) {
                File::delete($uploadDir . '/' . $oldLogo);
            }

            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move($uploadDir, $fileName);
            Setting::set('site_logo', $fileName);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'message' => 'บันทึกการตั้งค่าสำเร็จแล้ว!',
            ]);
        }

        return back()->with('success', 'บันทึกการตั้งค่าสำเร็จแล้ว!');
    }

    public function updateApi(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'ไม่ได้รับอนุญาต'], 403);
        }

        return $this->update($request);
    }
}

