<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Data untuk Kartu Statistik
        $totalKlien = User::where('role', 'klien')->count();
        $permohonanBaru = Permohonan::where('status', 'Diajukan')->count();
        $permohonanSelesai = Permohonan::where('status', 'Selesai')->count();

        // Data untuk Grafik
        $chartData = Permohonan::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $labels = $chartData->map(function ($item) {
            return date('F Y', mktime(0, 0, 0, $item->month, 1, $item->year));
        });

        $data = $chartData->pluck('count');

        return view('admin.dashboard', compact('totalKlien', 'permohonanBaru', 'permohonanSelesai', 'labels', 'data'));
    }
}