@extends('layouts.app')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center pb-4 mb-4 border-b">
        <h1 class="text-3xl font-bold text-gray-800">Edit User: {{ $user->name }}</h1>
        <a href="{{ route('notaris.staff.index') }}" class="text-sm text-gray-600 hover:text-red-800">
            &larr; Kembali
        </a>
    </div>

    <div class="mt-8 bg-white p-8 rounded-xl shadow-md border border-gray-100">
        <form action="{{ route('notaris.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <div class="mb-6">
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Lengkap <span class="text-red-600">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required>
                        @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email <span class="text-red-600">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required>
                        @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <div class="mb-6">
                        <label for="role" class="block mb-2 text-sm font-medium text-gray-900">Peran (Role) <span class="text-red-600">*</span></label>
                        <select id="role" name="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required>
                            <option value="" disabled>Pilih Peran</option>
                            <option value="notaris" {{ old('role', $user->role) == 'notaris' ? 'selected' : '' }}>Notaris (Admin)</option>
                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="klien" {{ old('role', $user->role) == 'klien' ? 'selected' : '' }}>Klien</option>
                        </select>
                        @error('role') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password Baru</label>
                        <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                        <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password.</p>
                        @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900">Konfirmasi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5">
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t flex justify-end space-x-4">
                <a href="{{ route('notaris.staff.index') }}" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 text-sm font-medium text-white bg-red-800 rounded-md hover:bg-red-700">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection