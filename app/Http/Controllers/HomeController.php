<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $settings = Setting::getAll();
        $isLoggedIn = Auth::check();

        $teamMembers = [
            [
                'name' => 'ณัฐนันท์ สุมาลุย',
                'role' => 'หัวหน้าโครงงาน',
                'grade' => 'มัธยมศึกษาปีที่ 6',
                'img' => '/img/profile/ju.png',
                'accent' => 'hover:border-yellow-400',
            ],
            [
                'name' => 'ศรัณยกร เทพสุนทร',
                'role' => 'โปรแกรมเมอร์',
                'grade' => 'มัธยมศึกษาปีที่ 5',
                'img' => '/img/profile/peak.png',
                'accent' => 'hover:border-red-400',
            ],
            [
                'name' => 'ศุภกรณ์ สุภิมารส',
                'role' => 'ฝ่ายฮาร์ดแวร์',
                'grade' => 'มัธยมศึกษาปีที่ 5',
                'img' => '/img/profile/walid.png',
                'accent' => 'hover:border-blue-400',
            ],
            [
                'name' => 'พงศภาวิน ปานปิ่น',
                'role' => 'ฝ่ายฮาร์ดแวร์',
                'grade' => 'มัธยมศึกษาปีที่ 5',
                'img' => '/img/profile/rew.png',
                'accent' => 'hover:border-green-400',
            ],
            [
                'name' => 'กอบชัย ศรีทองกลาง',
                'role' => 'ฝ่ายปัญญาประดิษฐ์',
                'grade' => 'มัธยมศึกษาปีที่ 3',
                'img' => '/img/profile/kun2.png',
                'accent' => 'hover:border-purple-400',
            ],
        ];

        $advisors = [
            [
                'name' => 'วีระกิตติ์ รฐาโชคนิธิธนาดุล',
                'role' => 'หัวหน้าศูนย์สตรีม/อาจารย์ที่ปรึกษา',
                'img' => '/img/profile/Adam.png',
            ],
            [
                'name' => 'ชัยรัตน์ ศรีวิลาส',
                'role' => 'ตำแหน่ง อาจารย์ที่ปรึกษา',
                'img' => '/img/profile/doom.png',
            ],
            [
                'name' => 'พิจิตรา หงษ์จันทร์',
                'role' => 'ตำแหน่ง อาจารย์ที่ปรึกษา',
                'img' => '/img/profile/may.png',
            ],
        ];

        return view('home', compact('settings', 'isLoggedIn', 'teamMembers', 'advisors'));
    }

    public function maintenance()
    {
        return view('maintenance');
    }
}

