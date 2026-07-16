@extends('layouts.app')
@section('content')
<div class="p-8">
    <h1 class="text-3xl font-bold text-gray-800">Arsipkan Dokumen Final</h1>
    <p class="mt-2 text-gray-600">
            Untuk: {{ optional($permohonan->client)->name ?? '[Klien Dihapus]' }} - {{ optional($permohonan->service)->name ?? '[Layanan Dihapus]' }}
    </p>
    <div class="mt-8 bg-white p-8 rounded-xl shadow-md border w-full max-w-lg">
        <form action="{{ route('staff.arsip.update', $permohonan) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <label class="block mb-2 text-sm font-medium" for="dokumen_final">Upload Dokumen Final (PDF)</label>
            <input class="block w-full text-sm ... " name="dokumen_final" type="file" required>
            @error('dokumen_final') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-6 py-3 text-sm font-medium text-white bg-red-800 rounded-md hover:bg-red-700">Simpan Arsip</button>
            </div>
        </form>
    </div>
</div>
@endsection