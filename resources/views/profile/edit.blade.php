@extends('layouts.app')

@section('content')
<div class="p-8 w-full">
    <div class="pb-4 border-b">
        <h1 class="text-3xl font-bold text-gray-800">Profil Saya</h1>
    </div>

    <div class="mt-8 space-y-8">
        {{-- Form untuk Update Informasi Profil --}}
        <div class="bg-white p-8 rounded-xl shadow-md border w-full max-w-2xl">
            <h2 class="text-xl font-bold text-gray-800">Informasi Profil</h2>
            <p class="mt-1 text-sm text-gray-600">Perbarui informasi profil dan alamat email akun Anda.</p>

            <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                @csrf
                @method('patch')

                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama</label>
                    <input id="name" name="name" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                    @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                    <input id="email" name="email" type="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" value="{{ old('email', $user->email) }}" required autocomplete="username">
                    @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div class="flex items-center gap-4">
                    <button type="submit" class="px-6 py-3 text-sm font-medium text-white bg-red-800 rounded-md hover:bg-red-700">Simpan</button>
                    @if (session('status') === 'profile-updated')
                        <p class="text-sm text-gray-600">Berhasil disimpan.</p>
                    @endif
                </div>
            </form>
        </div>

        {{-- Form untuk Update Password --}}
        <div class="bg-white p-8 rounded-xl shadow-md border w-full max-w-2xl">
            <h2 class="text-xl font-bold text-gray-800">Ubah Password</h2>
            <p class="mt-1 text-sm text-gray-600">Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.</p>

            <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                @csrf
                @method('put')

                <div>
                    <label for="current_password" class="block mb-2 text-sm font-medium text-gray-900">Password Saat Ini</label>
                    <input id="current_password" name="current_password" type="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" autocomplete="current-password">
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                </div>

                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password Baru</label>
                    <input id="password" name="password" type="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" autocomplete="new-password">
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                </div>

                <div>
                    <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900">Konfirmasi Password Baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" autocomplete="new-password">
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="px-6 py-3 text-sm font-medium text-white bg-red-800 rounded-md hover:bg-red-700">Simpan</button>
                    @if (session('status') === 'password-updated')
                        <p class="text-sm text-gray-600">Berhasil disimpan.</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection