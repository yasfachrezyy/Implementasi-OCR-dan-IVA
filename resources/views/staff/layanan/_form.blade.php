<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <div>
        <div class="mb-6">
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Layanan <span class="text-red-600">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $service->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required>
            @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label for="type" class="block mb-2 text-sm font-medium text-gray-900">Tipe Layanan <span class="text-red-600">*</span></label>
            <select id="type" name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required>
                <option value="notaris" {{ old('type', $service->type) == 'notaris' ? 'selected' : '' }}>Notaris</option>
                <option value="ppat" {{ old('type', $service->type) == 'ppat' ? 'selected' : '' }}>PPAT</option>
            </select>
            @error('type') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label for="processing_duration" class="block mb-2 text-sm font-medium text-gray-900">Durasi Proses <span class="text-red-600">*</span></label>
            <input type="text" id="processing_duration" name="processing_duration" value="{{ old('processing_duration', $service->processing_duration) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Contoh: 14 Hari Kerja" required>
            @error('processing_duration') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center">
            <input id="is_property_related" name="is_property_related" type="checkbox" value="1" {{ old('is_property_related', $service->is_property_related) ? 'checked' : '' }} class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
            <label for="is_property_related" class="ml-2 text-sm font-medium text-gray-900">Terkait Properti?</label>
        </div>
        <p class="mt-1 text-xs text-gray-500">Centang jika layanan ini perlu menampilkan field Harga Aset & NOP.</p>
    </div>

    <div>
        <label for="requirements" class="block mb-2 text-sm font-medium text-gray-900">Persyaratan <span class="text-red-600">*</span></label>
        <textarea id="requirements" name="requirements" rows="15" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500">{{ old('requirements', $service->requirements) }}</textarea>
        <p class="mt-1 text-xs text-gray-500">Tulis sebagai teks biasa. Gunakan Enter untuk baris baru dan (-) untuk daftar.</p>
        @error('requirements') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-10 pt-6 border-t flex justify-end space-x-4">
    <a href="{{ route('staff.layanan.index') }}" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
        Batal
    </a>
    <button type="submit" class="px-6 py-3 text-sm font-medium text-white bg-red-800 rounded-md hover:bg-red-700">
        Simpan Layanan
    </button>
</div>