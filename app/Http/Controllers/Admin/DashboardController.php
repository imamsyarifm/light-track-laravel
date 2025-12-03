<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Models\Lampu;
use App\Models\Iot;
use App\Models\Cctv;
use App\Models\ElectricPole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPoles = ElectricPole::count();
        $totalLampus = Lampu::count();
        $totalIots = Iot::count();
        $totalCctvs = Cctv::count();

        $latestPoles = ElectricPole::latest()->limit(5)->get(['id', 'nomor', 'provinsi', 'created_at']);
        
        $stats = [
            'total_poles' => $totalPoles,
            'total_lampus' => $totalLampus,
            'total_iots' => $totalIots,
            'total_cctvs' => $totalCctvs,
            'latest_poles' => $latestPoles->toArray(),
        ];
        
        return response()->json([
            'message' => 'Dashboard Data Fetched (Backend Only)',
            'statistics' => $stats
        ]);
    }
}