@extends('layouts.app')

{{-- Pastikan Alpine.js sudah dimuat di layout utama --}}
@section('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>
@endsection

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center pb-4 mb-4 border-b">
        <h1 class="text-3xl font-bold text-gray-800">Ajukan Permohonan Baru</h1>
    </div>

    {{-- Inisialisasi Alpine.js di sini --}}
    <div x-data="{ selectedServiceId: '{{ old('service_id') }}', services: {{ $servicesMap ?? '{}' }} }" class="mt-8 bg-white p-8 rounded-xl shadow-md border border-gray-100">
        
        <form action="{{ route('klien.permohonan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div>
                    <div class="mb-6">
                        <label for="service_id" class="block mb-2 text-sm font-medium">Jenis Permohonan <span class="text-red-600">*</span></label>
                        <select id="service_id" name="service_id" x-model="selectedServiceId" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                            <option value="">Pilih Jenis Permohonan</option>
                            @foreach($groupedServices as $type => $services)
                                <optgroup label="Layanan {{ ucfirst($type) }}">
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('service_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label for="nama_pihak_pertama" class="block mb-2 text-sm font-medium">Nama Pihak Pertama <span class="text-red-600">*</span></label>
                        <input type="text" name="nama_pihak_pertama" value="{{ old('nama_pihak_pertama') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Masukkan Nama Pihak Pertama">
                        @error('nama_pihak_pertama') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label for="nama_pihak_kedua" class="block mb-2 text-sm font-medium">Nama Pihak Kedua</label>
                        <input type="text" name="nama_pihak_kedua" value="{{ old('nama_pihak_kedua') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Masukkan Nama Pihak Kedua (jika ada)">
                        @error('nama_pihak_kedua') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="keterangan_tambahan" class="block mb-2 text-sm font-medium">Keterangan Tambahan</label>
                        <textarea name="keterangan_tambahan" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500" placeholder="Tulis keterangan tambahan di sini...">{{ old('keterangan_tambahan') }}</textarea>
                        @error('keterangan_tambahan') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <div x-show="selectedServiceId && services[selectedServiceId] && services[selectedServiceId].is_property_related" x-transition>
                        <div class="mb-6">
                            <label for="harga_aset" class="block mb-2 text-sm font-medium">Harga Aset (Rp) <span class="text-red-600">*</span></label>
                            <input type="number" name="harga_aset" value="{{ old('harga_aset') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Contoh: 500000000">
                            @error('harga_aset') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-6">
                            <label for="nop" class="block mb-2 text-sm font-medium">Nomor Objek Pajak (NOP)</label>
                            <input type="text" name="nop" value="{{ old('nop') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Masukkan Nomor NOP (jika ada)">
                            @error('nop') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium" for="berkas_pengajuan">Upload Syarat Pengajuan <span class="text-red-600">*</span></label>
                        <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" name="berkas_pengajuan" type="file">
                        <p class="mt-1 text-xs text-gray-500">Jadikan seluruh berkas dalam satu file PDF atau ZIP (Maks. 10MB).</p>
                        @error('berkas_pengajuan') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t flex justify-end">
                <button type="submit" class="text-white bg-red-800 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-6 py-3 text-center">
                    Ajukan Permohonan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection