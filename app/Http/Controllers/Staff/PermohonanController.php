<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Permohonan;
use Illuminate\Http\Request;

class PermohonanController extends Controller
{
    public function index()
    {
        $permohonans = Permohonan::with(['client', 'service'])
                                 ->latest()
                                 ->paginate(10);
        return view('staff.permohonan.index', compact('permohonans'));
    }

    public function show(Permohonan $permohonan)
    {
        if ($permohonan->is_revision) {
            $permohonan->is_revision = false;
            $permohonan->save();
        }

        return view('staff.permohonan.show', compact('permohonan'));
}
    
    public function edit(Permohonan $permohonan)
    {
        return view('staff.permohonan.edit', compact('permohonan'));
    }

    public function update(Request $request, Permohonan $permohonan)
    {
        $request->validate([
            'status' => 'required|in:Diajukan,Diproses,Kurang Berkas,Selesai,Ditolak',
            'notes' => 'nullable|string',
        ]);

        $permohonan->status = $request->status;
        $permohonan->notes = $request->notes;
        $permohonan->save();

        return redirect()->route('staff.permohonan.index')->with('success', 'Status permohonan berhasil diperbarui.');
    }
}