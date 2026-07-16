@extends('layouts.app')

@section('content')
<div class="p-8 w-full">
    <div class="flex justify-between items-center pb-4 mb-4 border-b">
        <h1 class="text-3xl font-bold text-gray-800">
            Kelola Layanan {{ $type ? ucfirst($type) : 'Semua' }}
        </h1>
        <a href="{{ route('staff.layanan.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-red-800 rounded-md hover:bg-red-700">
            + Tambah Layanan Baru
        </a>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Nama Layanan</th>
                        <th class="px-6 py-3">Durasi</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $service->name }}</td>
                        <td class="px-6 py-4">{{ $service->processing_duration }}</td>
                        <td class="px-6 py-4 flex space-x-4">
                            <a href="{{ route('staff.layanan.edit', $service) }}" class="font-medium text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('staff.layanan.destroy', $service) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus layanan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-medium text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center">Tidak ada layanan untuk tipe ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
         <div class="mt-4">{{ $services->links() }}</div>
    </div>
</div>
@endsection