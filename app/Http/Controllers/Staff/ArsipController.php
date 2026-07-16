<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $query = Permohonan::where('status', 'Selesai')->with('client', 'service');

        if ($request->filled('search')) {
            $search = $request->search;
            
            $query->where(function($q) use ($search) {
                $q->whereHas('client', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('service', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                });
            });
        }

        $permohonans = $query->latest()->paginate(10);

        return view('staff.arsip.index', compact('permohonans'));
    }

    public function edit(Permohonan $permohonan)
    {
        return view('staff.arsip.edit', compact('permohonan'));
    }

    public function update(Request $request, Permohonan $permohonan)
    {
        $request->validate([
            'dokumen_final' => 'required|file|mimes:pdf|max:10240'
        ]);

        if ($permohonan->final_document_path) {
            Storage::disk('public')->delete($permohonan->final_document_path);
        }

        $path = $request->file('dokumen_final')->store('dokumen_final', 'public');
        
        $permohonan->final_document_path = $path;
        $permohonan->save();

        return redirect()->route('staff.arsip.index')->with('success', 'Dokumen final berhasil diarsipkan.');
    }

    public function download(Permohonan $permohonan)
    {
        if (!$permohonan->final_document_path || !Storage::disk('public')->exists($permohonan->final_document_path)) {
            return back()->with('error', 'File dokumen tidak ditemukan.');
        }

        return Storage::disk('public')->download($permohonan->final_document_path);
    }
}