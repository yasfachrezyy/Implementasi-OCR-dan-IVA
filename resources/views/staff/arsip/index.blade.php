@extends('layouts.app')

@section('content')
<div class="p-8 w-full">
    <div class="flex justify-between items-center pb-4 mb-4 border-b">
        <h1 class="text-3xl font-bold text-gray-800">Arsip Dokumen Final</h1>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="mb-6">
        <form action="{{ route('staff.arsip.index') }}" method="GET">
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari Nama Pemohon atau Jenis Layanan..." 
                       class="w-full md:w-1/3 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block p-2.5">
                
                <button type="submit" class="px-4 py-2 bg-red-800 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                    Cari
                </button>
                
                @if(request('search'))
                    <a href="{{ route('staff.arsip.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors flex items-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama Pemohon</th>
                        <th scope="col" class="px-6 py-3">Jenis Layanan</th>
                        <th scope="col" class="px-6 py-3">Tgl. Selesai</th>
                        <th scope="col" class="px-6 py-3">Status Arsip</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permohonans as $permohonan)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $permohonan->client->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $permohonan->service->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $permohonan->updated_at->format('d F Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($permohonan->final_document_path)
                                <div class="flex flex-col">
                                    <span class="flex items-center text-sm font-medium text-green-800 mb-1">
                                        <span class="flex w-2.5 h-2.5 bg-green-500 rounded-full mr-2"></span>
                                        Sudah Diupload
                                    </span>
                                    
                                    <div class="flex space-x-3 text-xs">
                                        <a href="{{ Storage::url($permohonan->final_document_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Lihat
                                        </a>

                                        <a href="{{ route('staff.arsip.download', $permohonan) }}" class="text-green-700 hover:underline flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            Unduh
                                        </a>
                                    </div>
                                </div>
                            @else
                                <span class="flex items-center text-sm font-medium text-yellow-800">
                                    <span class="flex w-2.5 h-2.5 bg-yellow-500 rounded-full mr-2"></span>
                                    Belum Diupload
                                </span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4">
                            <a href="{{ route('staff.arsip.edit', $permohonan) }}" class="font-medium text-blue-600 hover:underline">
                                Kelola Arsip
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Belum ada permohonan yang selesai untuk diarsipkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $permohonans->links() }}
        </div>
    </div>
</div>
@endsection