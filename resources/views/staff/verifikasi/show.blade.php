<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Permohonan: {{ $permohonan->kode_permohonan }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold border-b pb-2 mb-4">Informasi Permohonan</h3>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><strong>Kode:</strong> {{ $permohonan->kode_permohonan }}</div>
                            <div><strong>Tanggal Diajukan:</strong> {{ $permohonan->created_at->format('d M Y, H:i') }}</div>
                            <div><strong>Nama Klien:</strong> {{ $permohonan->klien->name }}</div>
                            <div><strong>Email Klien:</strong> {{ $permohonan->klien->email }}</div>
                            <div><strong>Jenis Layanan:</strong> {{ $permohonan->layanan->nama_layanan }} ({{ ucfirst($permohonan->layanan->tipe_layanan) }})</div>
                        </div>

                        <h3 class="text-lg font-bold border-b pb-2 mt-6 mb-4">Catatan dari Klien</h3>
                        <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-md">
                            {{ $permohonan->catatan_klien ?? 'Tidak ada catatan.' }}
                        </p>

                        <h3 class="text-lg font-bold border-b pb-2 mt-6 mb-4">Dokumen Terlampir</h3>
                        <ul class="list-disc pl-5 text-sm">
                            @forelse ($permohonan->dokumens as $dokumen)
                                <li>
                                    <a href="{{ Storage::url($dokumen->path_file) }}" target="_blank" class="text-indigo-600 hover:underline">
                                        {{ $dokumen->nama_file }} ({{ $dokumen->tipe_file }})
                                    </a>
                                </li>
                            @empty
                                <li class="text-gray-500">Klien belum mengunggah dokumen apapun.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="md:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold border-b pb-2 mb-4">Form Verifikasi</h3>
                        <form method="POST" action="{{ route('staff.verifikasi.update', $permohonan->id) }}">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="status" class="block font-medium text-sm text-gray-700">Ubah Status</label>
                                <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="Diverifikasi">Diverifikasi (Setujui)</option>
                                    <option value="Ditolak">Ditolak</option>
                                </select>
                            </div>

                            <div class="mt-4">
                                <label for="catatan_staff" class="block font-medium text-sm text-gray-700">Catatan Staff (Opsional)</label>
                                <textarea id="catatan_staff" name="catatan_staff" rows="5" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>