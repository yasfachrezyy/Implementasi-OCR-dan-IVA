@extends('layouts.app')

@section('content')
<div class="p-8 w-full">
    <h1 class="text-3xl font-bold text-gray-800">Laporan Pengajuan</h1>
    
    {{-- Form Filter --}}
    <div class="mt-6 bg-white p-6 rounded-xl shadow-md border border-gray-100">
        <form method="GET" action="">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>

                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700">Cari Nama / Layanan</label>
                    <div class="flex space-x-2">
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Masukkan kata kunci..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        <button type="submit" class="mt-1 px-4 py-2 bg-red-800 text-white font-semibold rounded-md hover:bg-red-700">Filter</button>
                        <a href="{{ url()->current() }}" class="mt-1 px-4 py-2 bg-gray-200 text-gray-800 font-semibold rounded-md hover:bg-gray-300">Reset</a>
                    </div>
                </div>
            </div>
        </form>

        {{-- Tombol Export --}}
        <div class="mt-6 pt-4 border-t flex space-x-2">
             @php
                $rolePrefix = Auth::user()->role == 'notaris' ? 'notaris' : 'staff';
            @endphp
            <a href="{{ route($rolePrefix . '.laporan.export.excel', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-semibold rounded-md hover:bg-green-500 text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>
            <a href="{{ route($rolePrefix . '.laporan.export.pdf', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-semibold rounded-md hover:bg-red-500 text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export PDF
            </a>
        </div>
    </div>

    <div class="mt-8 bg-white p-6 rounded-xl shadow-md border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Nama Pemohon</th>
                        <th scope="col" class="px-6 py-3">Jenis Layanan</th>
                        <th scope="col" class="px-6 py-3">Tanggal Pengajuan</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permohonans as $permohonan)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $loop->iteration + ($permohonans->currentPage() - 1) * $permohonans->perPage() }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ optional($permohonan->client)->name ?? '[Klien Dihapus]' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ optional($permohonan->service)->name ?? '[Layanan Dihapus]' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $permohonan->created_at->format('d F Y') }}
                        </td>
                        <td class="px-6 py-4">
                             <span @class([
                                'px-2 py-1 text-xs font-semibold rounded-full',
                                'bg-yellow-100 text-yellow-800' => $permohonan->status == 'Diajukan',
                                'bg-blue-100 text-blue-800'    => $permohonan->status == 'Diproses',
                                'bg-green-100 text-green-800'  => $permohonan->status == 'Selesai',
                                'bg-red-100 text-red-800'      => $permohonan->status == 'Ditolak',
                                'bg-gray-100 text-gray-800'    => !in_array($permohonan->status, ['Diajukan', 'Diproses', 'Selesai', 'Ditolak']),
                            ])>
                                {{ $permohonan->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data laporan untuk ditampilkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $permohonans->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection