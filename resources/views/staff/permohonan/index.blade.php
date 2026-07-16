@extends('layouts.app')

@section('content')
<div class="p-8 w-full">
    <div class="flex justify-between items-center pb-4 mb-4 border-b">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Permohonan Masuk</h1>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama Pemohon</th>
                        <th scope="col" class="px-6 py-3">Jenis Layanan</th>
                        <th scope="col" class="px-6 py-3">Tgl. Masuk</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permohonans as $p)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $p->client->name ?? '[Klien Dihapus]' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $p->service->name ?? '[Layanan Dihapus]' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $p->created_at->format('d-m-Y') }}
                        </td>
                        <td class="px-6 py-4">
                            {{-- Badge Status Utama --}}
                            <span @class([
                                'px-2 py-1 text-xs font-semibold rounded-full',
                                'bg-yellow-100 text-yellow-800' => $p->status == 'Diajukan',
                                'bg-blue-100 text-blue-800'    => $p->status == 'Diproses',
                                'bg-green-100 text-green-800'  => $p->status == 'Selesai',
                                'bg-red-100 text-red-800'      => $p->status == 'Ditolak',
                                'bg-gray-100 text-gray-800'    => !in_array($p->status, ['Diajukan', 'Diproses', 'Selesai', 'Ditolak']),
                            ])>
                                {{ $p->status }}
                            </span>
                            
                            {{-- PENANDA REVISI DILETAKKAN DI SINI --}}
                            @if($p->is_revision)
                                <span class="ml-2 px-2 py-1 text-xs font-bold rounded-full bg-purple-100 text-purple-800 animate-pulse">
                                    Revisi
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $routeName = (Auth::user()->role == 'notaris') 
                                    ? 'notaris.permohonan.show' 
                                    : 'staff.permohonan.show';
                            @endphp
                            <a href="{{ route($routeName, $p) }}" class="font-medium text-blue-600 hover:underline">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        {{-- Colspan kembali menjadi 5 --}}
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada permohonan yang masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $permohonans->links() }}</div>
    </div>
</div>
@endsection