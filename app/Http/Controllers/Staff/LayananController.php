<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type');

        $query = \App\Models\Service::query();

        if ($type && in_array($type, ['notaris', 'ppat'])) {
            $query->where('type', $type);
        }

        $services = $query->latest()->paginate(10);

        return view('staff.layanan.index', compact('services', 'type'));
    }

    public function create()
    {
        return view('staff.layanan.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:notaris,ppat',
            'is_property_related' => 'boolean',
            'processing_duration' => 'required|string|max:255',
            'requirements' => 'required|string',
        ]);
        
        $validatedData['is_property_related'] = $request->has('is_property_related');

        Service::create($validatedData);

        return redirect()->route('staff.layanan.index', ['type' => $validatedData['type']])
                         ->with('success', 'Layanan baru berhasil ditambahkan.');
    }

    public function edit(Service $layanan)
    {
        return view('staff.layanan.edit', ['service' => $layanan]);
    }

    public function update(Request $request, Service $layanan)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:notaris,ppat',
            'is_property_related' => 'boolean',
            'processing_duration' => 'required|string|max:255',
            'requirements' => 'required|string',
        ]);

        $validatedData['is_property_related'] = $request->has('is_property_related');

        $layanan->update($validatedData);

        return redirect()->route('staff.layanan.index', ['type' => $layanan->type])
                         ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Service $layanan)
    {
        $layanan->delete();
        return redirect()->route('staff.layanan.index')->with('success', 'Layanan berhasil dihapus.');
    }
}