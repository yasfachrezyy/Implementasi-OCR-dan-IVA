@extends('layouts.app')
@section('content')
<div class="p-8">
    <h1 class="text-3xl font-bold text-gray-800">Lengkapi Berkas Permohonan</h1>
    <p class="mt-2 text-gray-600">Untuk layanan: {{ $permohonan->service->name }}</p>

    {{-- Menampilkan catatan dari Staff --}}
    @if ($permohonan->notes)
    <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800">
        <p class="font-bold">Catatan dari Staff:</p>
        <p>{{ $permohonan->notes }}</p>
    </div>
    @endif

    <div class="mt-8 bg-white p-8 rounded-xl shadow-md border w-full max-w-lg">
        <form action="{{ route('klien.permohonan.reupload.handle', $permohonan) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div>
                <label class="block mb-2 text-sm font-medium" for="berkas_pengajuan_baru">Upload Berkas Baru <span class="text-red-600">*</span></label>
                <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" name="berkas_pengajuan_baru" type="file" required>
                <p class="mt-1 text-xs text-gray-500">Jadikan seluruh berkas dalam satu file PDF atau ZIP (Maks. 10MB).</p>
                @error('berkas_pengajuan_baru') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-6 py-3 text-sm font-medium text-white bg-red-800 rounded-md hover:bg-red-700">
                    Upload & Kirim Ulang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection