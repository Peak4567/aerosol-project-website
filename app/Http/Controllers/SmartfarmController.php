<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmartfarmController extends Controller
{
    public function index()
    {
        $siteName = Setting::get('site_name', 'Aerosol');
        $user = Auth::user();
        $latestSensor = Sensor::latest('id')->first();

        return view('dashboard.smartfarm', compact('siteName', 'user', 'latestSensor'));
    }
}

