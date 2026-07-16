@extends('layouts.app')
@section('content')
<div class="p-8">
    <h1 class="text-3xl font-bold text-gray-800">Update Status Permohonan</h1>
    <p class="mt-2 text-gray-600">
        Untuk: {{ $permohonan->client->name ?? '[Klien Dihapus]' }} - {{ $permohonan->service->name ?? '[Layanan Dihapus]' }}
    </p>

    <div class="mt-8 bg-white p-8 rounded-xl shadow-md border w-full max-w-lg">
        @php
            $userRole = auth()->user()->role ?? null;
            $updateRouteName = ($userRole == 'notaris')
                ? 'notaris.permohonan.update'
                : 'staff.permohonan.update';
        @endphp
        <form action="{{ route('staff.permohonan.update', $permohonan) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label for="status" class="block mb-2 text-sm font-medium">Status Permohonan <span class="text-red-600">*</span></label>
                <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                    <option value="Diajukan" {{ $permohonan->status == 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="Diproses" {{ $permohonan->status == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="Kurang Berkas" {{ $permohonan->status == 'Kurang Berkas' ? 'selected' : '' }}>Kurang Berkas</option>
                    <option value="Selesai" {{ $permohonan->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Ditolak" {{ $permohonan->status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div>
                <label for="notes" class="block mb-2 text-sm font-medium">Catatan (Opsional)</label>
                <textarea id="notes" name="notes" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500" placeholder="Tulis catatan untuk klien di sini...">{{ old('notes', $permohonan->notes) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Catatan ini akan dapat dilihat oleh klien.</p>
            </div>
            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-6 py-3 text-sm font-medium text-white bg-red-800 rounded-md hover:bg-red-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection