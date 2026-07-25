<?php

namespace App\Http\Controllers\Klien;

use App\Http\Controllers\Controller;
use App\Models\Permohonan;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PermohonanKlienController extends Controller
{
    public function index()
    {
        $permohonans = Permohonan::where('client_id', Auth::id())
                                ->with('service')
                                ->latest()
                                ->paginate(10);

        return view('klien.permohonan.index', compact('permohonans'));
    }

    public function create()
    {
        $allServices = Service::orderBy('name', 'asc')->get();

        $groupedServices = $allServices->groupBy('type');

        $servicesMap = $allServices->mapWithKeys(function ($service) {
            return [$service->id => ['is_property_related' => $service->is_property_related]];
        });

        return view('klien.permohonan.create', [
            'groupedServices' => $groupedServices,
            'servicesMap' => $servicesMap->toJson(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'nama_pihak_pertama' => 'required|string|max:255',
            'nama_pihak_kedua' => 'nullable|string|max:255',
            'keterangan_tambahan' => 'nullable|string',
            'berkas_pengajuan' => 'required|file|mimes:pdf,zip|max:20240',

            'harga_aset' => [
                Rule::requiredIf(function () use ($request) {
                    $service = Service::find($request->service_id);
                    return $service && $service->is_property_related;
                }),
                'nullable', 'numeric', 'min:0'
            ],

            'nop' => 'nullable|string|max:50',
        ]);

        $filePath = $request->file('berkas_pengajuan')->store('berkas_permohonan', 'public');

        Permohonan::create([
            'client_id' => Auth::id(),
            'service_id' => $request->service_id,
            'nama_pihak_pertama' => $request->nama_pihak_pertama,
            'nama_pihak_kedua' => $request->nama_pihak_kedua,
            'keterangan_tambahan' => $request->keterangan_tambahan,
            'harga_aset' => $request->harga_aset,
            'nop' => $request->nop,
            'file_path' => $filePath,
            'status' => 'Diajukan',
        ]);

        return redirect()->route('klien.dashboard')
                         ->with('success', 'Permohonan Anda telah berhasil diajukan.');
    }

    public function showReuploadForm(Permohonan $permohonan)
    {
        abort_if($permohonan->client_id !== auth()->id(), 403);

        return view('klien.permohonan.reupload', compact('permohonan'));
    }

    public function handleReupload(Request $request, Permohonan $permohonan)
    {
        abort_if($permohonan->client_id !== auth()->id(), 403);

        $request->validate([
            'berkas_pengajuan_baru' => 'required|file|mimes:pdf,zip|max:10240',
        ]);

        Storage::disk('public')->delete($permohonan->file_path);

        $newFilePath = $request->file('berkas_pengajuan_baru')->store('berkas_permohonan', 'public');

        $permohonan->file_path = $newFilePath;
        $permohonan->status = 'Diajukan';
        $permohonan->is_revision = true;
        $permohonan->save();

        return redirect()->route('klien.permohonan.index')->with('success', 'Berkas berhasil diperbarui dan telah diajukan kembali.');
    }

    public function showSyaratInformasi()
    {
        $allServices = \App\Models\Service::orderBy('type')->orderBy('name')->get();

        $groupedServices = $allServices->groupBy('type');

        $servicesMap = $allServices->keyBy('id');

        return view('klien.syarat-informasi', [
            'groupedServices' => $groupedServices,
            'servicesMap' => $servicesMap->toJson(),
        ]);
    }
}
