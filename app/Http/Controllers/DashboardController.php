<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $siteName = Setting::get('site_name', 'Aerosol');
        $user = Auth::user();

        // Get latest sensor record for initial render
        $latestSensor = Sensor::latest('id')->first();

        return view('dashboard.index', compact('siteName', 'user', 'latestSensor'));
    }
}

