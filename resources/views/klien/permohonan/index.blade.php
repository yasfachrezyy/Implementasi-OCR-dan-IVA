@extends('layouts.app')

@section('content')
<div class="p-8 w-full">
    <div class="pb-4 border-b">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Permohonan Saya</h1>
    </div>

    <div class="mt-8 bg-white p-6 rounded-xl shadow-md border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Jenis Permohonan</th>
                        <th scope="col" class="px-6 py-3">Tanggal Pengajuan</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Berkas</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permohonans as $permohonan)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $loop->iteration + ($permohonans->currentPage() - 1) * $permohonans->perPage() }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $permohonan->service->name }}</td>
                        <td class="px-6 py-4">{{ $permohonan->created_at->format('d F Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{-- ... kelas badge status ... --}}">
                                {{ $permohonan->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ Storage::url($permohonan->file_path) }}" target="_blank" class="font-medium text-blue-600 hover:underline">Lihat Berkas</a>
                        </td>
                        <td class="px-6 py-4">
                            {{-- Tombol hanya muncul jika status 'Kurang Berkas' --}}
                            @if ($permohonan->status == 'Kurang Berkas')
                                <a href="{{ route('klien.permohonan.reupload.form', $permohonan) }}" class="font-medium text-red-600 hover:underline">Lengkapi Berkas</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Anda belum memiliki permohonan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $permohonans->links() }}</div>
    </div>
</div>
@endsection