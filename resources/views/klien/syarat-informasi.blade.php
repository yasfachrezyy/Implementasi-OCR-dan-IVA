@extends('layouts.app')

@push('scripts')
    {{-- Memuat Alpine.js untuk interaktivitas --}}
    <script src="//unpkg.com/alpinejs" defer></script>
@endpush

@section('content')
<div class="p-8 w-full">
    <div class="pb-4 border-b">
        <h1 class="text-3xl font-bold text-gray-800">Syarat dan Informasi Layanan</h1>
    </div>

    {{-- Inisialisasi Alpine.js dengan data layanan dari controller --}}
    <div x-data="{ selectedServiceId: '', services: {{ $servicesMap }} }" class="mt-8 bg-white p-8 rounded-xl shadow-md border border-gray-100">
        
        <p class="text-gray-600">Lihat Syarat dan Informasi Untuk Mengajukan Permohonan Layanan Notaris maupun Layanan PPAT.</p>

        <div class="mt-6">
            <label for="service_select" class="block mb-2 text-sm font-medium text-gray-900">Pilih Jenis Layanan dan Permohonan</label>
            {{-- x-model mengikat pilihan dropdown ke variabel selectedServiceId --}}
            <select id="service_select" x-model="selectedServiceId" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                <option value="" disabled>Silahkan Pilih...</option>
                @foreach($groupedServices as $type => $services)
                    <optgroup label="Layanan {{ ucfirst($type) }}">
                        @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <div x-show="selectedServiceId" x-transition class="mt-8 pt-6 border-t" x-cloak>
            {{-- Template ini mencegah error sebelum data siap --}}
            <template x-if="services[selectedServiceId]">
                <div>
                    <h3 class="text-xl font-bold text-gray-800" x-text="services[selectedServiceId].name"></h3>
                    
                    <div class="mt-4">
                        <h4 class="font-semibold text-gray-700">Persyaratan:</h4>
                        {{-- 'whitespace-pre-wrap' akan otomatis merapikan teks biasa (dengan Enter & spasi) --}}
                        <div class="mt-2 text-gray-600 whitespace-pre-wrap text-sm leading-relaxed" x-text="services[selectedServiceId].requirements"></div>
                    </div>

                    <div class="mt-6 text-sm">
                        <p><strong>Estimasi Proses Pengerjaan:</strong> 
                           <span class="font-medium" x-text="services[selectedServiceId].processing_duration"></span>
                        </p>
                    </div>
                </div>
            </template>
        </div>

    </div>
</div>
@endsection