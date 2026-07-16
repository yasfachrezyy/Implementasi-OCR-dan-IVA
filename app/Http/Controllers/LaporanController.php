<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PermohonanExport;
use PDF;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Permohonan::with(['client', 'service']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('client', function($subq) use ($search) {
                    $subq->where('name', 'like', "%{$search}%");
                })->orWhereHas('service', function($subq) use ($search) {
                    $subq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $permohonans = $query->latest()->paginate(15);

        return view('laporan.index', compact('permohonans'));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new PermohonanExport($request->all()), 'laporan-permohonan.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Permohonan::with(['client', 'service']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('client', function($subq) use ($search) {
                    $subq->where('name', 'like', "%{$search}%");
                })->orWhereHas('service', function($subq) use ($search) {
                    $subq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $permohonans = $query->latest()->get();

        $pdf = PDF::loadView('laporan.pdf', compact('permohonans'));
        return $pdf->download('laporan-permohonan.pdf');
    }
}