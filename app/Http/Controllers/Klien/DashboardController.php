<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permohonan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $permohonans = Permohonan::where('client_id', Auth::id())
            ->with('service')
            ->latest()
            ->get();
        return view('klien.dashboard', compact('permohonans'));
    }
}