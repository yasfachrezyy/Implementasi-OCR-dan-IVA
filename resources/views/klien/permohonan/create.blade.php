@extends('layouts.app')

@section('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>
@endsection

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center pb-4 mb-4 border-b">
        <h1 class="text-3xl font-bold text-gray-800">Ajukan Permohonan Baru</h1>
    </div>

    @php
        $namaLayananMap = [];
        if(isset($groupedServices)) {
            foreach($groupedServices as $type => $services_group) {
                foreach($services_group as $service) {
                    $namaLayananMap[$service->id] = strtolower($service->name);
                }
            }
        }
    @endphp

    <div x-data='{ 
            selectedServiceId: "{{ old('service_id') }}", 
            services: {!! $servicesMap ?? "{}" !!},
            serviceNames: @json($namaLayananMap),
            
            isServiceType(type) {
                if (!this.selectedServiceId || !this.serviceNames[this.selectedServiceId]) return false;
                
                let name = this.serviceNames[this.selectedServiceId];
                
                if (type === "pt") return name.includes("pt") || name.includes("perseroan terbatas");
                if (type === "cv") return name.includes("cv") || name.includes("komanditer");
                if (type === "yayasan") return name.includes("yayasan");
                if (type === "waris") return name.includes("waris");
                
                return false;
            },

            hasDynamicForm() {
                return this.isServiceType("pt") || this.isServiceType("cv") || this.isServiceType("yayasan") || this.isServiceType("waris");
            },

            showPropertyFields() {
                if (!this.selectedServiceId || !this.services[this.selectedServiceId]) return false;
                
                if (this.isServiceType("waris")) return false;
                
                return this.services[this.selectedServiceId].is_property_related == 1 || this.services[this.selectedServiceId].is_property_related === true;
            }
        }' 
        class="mt-8 bg-white p-8 rounded-xl shadow-md border border-gray-100">
        
        <form action="{{ route('klien.permohonan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- KOLOM KIRI --}}
                <div>
                    <div class="mb-6">
                        <label for="service_id" class="block mb-2 text-sm font-medium">Jenis Permohonan <span class="text-red-600">*</span></label>
                        <select id="service_id" name="service_id" x-model="selectedServiceId" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                            <option value="">Pilih Jenis Permohonan</option>
                            @foreach($groupedServices as $type => $services_group)
                                <optgroup label="Layanan {{ ucfirst($type) }}">
                                    @foreach($services_group as $service)
                                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('service_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- NAMA PIHAK 1 & 2 (Akan hilang jika layanan memiliki form dinamis) --}}
                    <div x-show="!hasDynamicForm()" x-transition>
                        <div class="mb-6">
                            <label for="nama_pihak_pertama" class="block mb-2 text-sm font-medium">Nama Pihak Pertama <span class="text-red-600">*</span></label>
                            {{-- PERUBAHAN: Ditambahkan x-bind:disabled agar tidak dikirim ke server saat form tersembunyi --}}
                            <input type="text" name="nama_pihak_pertama" value="{{ old('nama_pihak_pertama') }}" x-bind:required="!hasDynamicForm()" x-bind:disabled="hasDynamicForm()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Masukkan Nama Pihak Pertama">
                            @error('nama_pihak_pertama') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-6">
                            <label for="nama_pihak_kedua" class="block mb-2 text-sm font-medium">Nama Pihak Kedua</label>
                            <input type="text" name="nama_pihak_kedua" value="{{ old('nama_pihak_kedua') }}" x-bind:disabled="hasDynamicForm()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Masukkan Nama Pihak Kedua (jika ada)">
                            @error('nama_pihak_kedua') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <!-- JURUS RAHASIA 1: Mengirim Data Bayangan agar Lolos Validasi Database -->
                    <input type="hidden" name="nama_pihak_pertama" value="Sesuai Data Formulir Dinamis" x-bind:disabled="!hasDynamicForm()">
                </div>

                {{-- KOLOM KANAN --}}
                <div>
                    {{-- Form Dinamis Properti (Harga Aset & NOP) --}}
                    <div x-show="showPropertyFields()" x-transition style="display: none;">
                        
                        <div class="mb-6" x-data="{ 
                            rawHarga: '{{ old('harga_aset') }}', 
                            formattedHarga: '{{ old('harga_aset') ? number_format(floatval(old('harga_aset')), 0, ',', '.') : '' }}',
                            formatRupiah(e) {
                                let angka = e.target.value.replace(/[^,\d]/g, ''); 
                                this.rawHarga = angka; 
                                this.formattedHarga = angka ? new Intl.NumberFormat('id-ID').format(angka) : ''; 
                            }
                        }">
                            <label for="harga_aset_tampil" class="block mb-2 text-sm font-medium">Harga Aset (Rp) <span class="text-red-600">*</span></label>
                            <input type="text" id="harga_aset_tampil" x-model="formattedHarga" @input="formatRupiah" x-bind:required="showPropertyFields()" x-bind:disabled="!showPropertyFields()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Contoh: 500.000.000">
                            
                            {{-- PERUBAHAN: Disabled jika tidak tampil --}}
                            <input type="hidden" name="harga_aset" x-model="rawHarga" x-bind:disabled="!showPropertyFields()">
                            
                            @error('harga_aset') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-6">
                            <label for="nop" class="block mb-2 text-sm font-medium">Nomor Objek Pajak (NOP)</label>
                            <input type="text" name="nop" value="{{ old('nop') }}" x-bind:disabled="!showPropertyFields()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Masukkan Nomor NOP (jika ada)">
                            @error('nop') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- JURUS RAHASIA 2: Mengirim Angka 0 dan Strip jika form disembunyikan tapi diwajibkan server -->
                    <input type="hidden" name="harga_aset" value="0" x-bind:disabled="showPropertyFields() || !(selectedServiceId && services[selectedServiceId] && (services[selectedServiceId].is_property_related == 1 || services[selectedServiceId].is_property_related === true))">
                    <input type="hidden" name="nop" value="-" x-bind:disabled="showPropertyFields() || !(selectedServiceId && services[selectedServiceId] && (services[selectedServiceId].is_property_related == 1 || services[selectedServiceId].is_property_related === true))">
                    
                    <div class="mb-6">
                        <label for="keterangan_tambahan" class="block mb-2 text-sm font-medium">Keterangan Tambahan</label>
                        <textarea name="keterangan_tambahan" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500" placeholder="Tulis keterangan tambahan di sini...">{{ old('keterangan_tambahan') }}</textarea>
                        @error('keterangan_tambahan') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- FORM DINAMIS (PT / CV / YAYASAN / WARIS) --}}
            {{-- ========================================================== --}}
            
            <div x-show="hasDynamicForm()" x-transition class="mt-4 mb-6 border-t pt-6" style="display: none;">
                
                {{-- 1. FORM DATA PT --}}
                <div x-show="isServiceType('pt')" class="p-6 mb-4 rounded-lg bg-gray-50 border border-gray-200" style="display: none;">
                    <h5 class="text-lg font-bold text-red-800 mb-4">Informasi Data PT</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Nama PT (Saran 3 Suku Kata) <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[nama_entitas]" x-bind:required="isServiceType('pt')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Tempat & Kedudukan PT <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[kedudukan]" x-bind:required="isServiceType('pt')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Maksud dan Tujuan Usaha <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[tujuan_usaha]" x-bind:required="isServiceType('pt')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Modal Dasar & Modal Disetor <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[modal]" x-bind:required="isServiceType('pt')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Susunan Direksi & Komisaris <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[susunan_pengurus]" x-bind:required="isServiceType('pt')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Contoh: Direktur: Budi, Komisaris: Andi">
                        </div>
                    </div>
                </div>

                {{-- 2. FORM DATA CV --}}
                <div x-show="isServiceType('cv')" class="p-6 mb-4 rounded-lg bg-gray-50 border border-gray-200" style="display: none;">
                    <h5 class="text-lg font-bold text-red-800 mb-4">Informasi Data CV</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Nama CV <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[nama_entitas]" x-bind:required="isServiceType('cv')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Tempat & Kedudukan CV <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[kedudukan]" x-bind:required="isServiceType('cv')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Maksud dan Tujuan Usaha <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[tujuan_usaha]" x-bind:required="isServiceType('cv')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Susunan Pengurus (Pesero Aktif & Pasif) <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[susunan_pengurus]" x-bind:required="isServiceType('cv')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Contoh: Pesero Aktif: Budi, Pesero Pasif: Andi">
                        </div>
                    </div>
                </div>

                {{-- 3. FORM DATA YAYASAN --}}
                <div x-show="isServiceType('yayasan')" class="p-6 mb-4 rounded-lg bg-gray-50 border border-gray-200" style="display: none;">
                    <h5 class="text-lg font-bold text-red-800 mb-4">Informasi Data Yayasan</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Nama Yayasan <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[nama_entitas]" x-bind:required="isServiceType('yayasan')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Tempat & Kedudukan Yayasan <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[kedudukan]" x-bind:required="isServiceType('yayasan')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Maksud dan Tujuan (Sosial/Keagamaan) <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[tujuan_usaha]" x-bind:required="isServiceType('yayasan')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Jumlah Kekayaan Awal (Rp) <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[modal]" x-bind:required="isServiceType('yayasan')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900">Susunan Organ (Pembina / Pengurus / Pengawas) <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[susunan_pengurus]" x-bind:required="isServiceType('yayasan')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Contoh: Pembina: Ahmad, Pengurus: Budi, Pengawas: Citra">
                        </div>
                    </div>
                </div>

                {{-- 4. FORM DATA WARIS --}}
                <div x-show="isServiceType('waris')" class="p-6 mb-4 rounded-lg bg-gray-50 border border-gray-200" style="display: none;">
                    <h5 class="text-lg font-bold text-red-800 mb-4">Informasi Data Waris</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Nama Pewaris (Almarhum/ah) <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[nama_pewaris]" x-bind:required="isServiceType('waris')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal Meninggal Pewaris <span class="text-red-600">*</span></label>
                            <input type="date" name="data_tambahan[tanggal_meninggal]" x-bind:required="isServiceType('waris')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Nama Ahli Waris (Pemohon) <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[nama_ahli_waris]" x-bind:required="isServiceType('waris')" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Jumlah Ahli Waris <span class="text-red-600">*</span></label>
                            <input type="text" name="data_tambahan[jumlah_ahli_waris]" x-bind:required="isServiceType('waris')" placeholder="Contoh: Istri dan 2 Anak" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        </div>
                    </div>
                </div>

            </div>

            {{-- UPLOAD BERKAS --}}
            <div class="mt-4 bg-gray-50 p-6 rounded-lg border border-gray-200">
                <label class="block mb-2 text-base font-bold text-gray-900" for="berkas_pengajuan">Upload Syarat Pengajuan (Sesuai Layanan) <span class="text-red-600">*</span></label>
                <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none p-2" name="berkas_pengajuan" type="file" accept=".pdf" required>
                <p class="mt-2 text-sm text-gray-600">
                    * Pastikan Anda menggabungkan seluruh dokumen dalam <strong class="text-red-600">satu file PDF</strong> (Maks. 10MB).
                </p>
                @error('berkas_pengajuan') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="text-white bg-red-800 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-8 py-3 text-center transition-colors">
                    Ajukan Permohonan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection