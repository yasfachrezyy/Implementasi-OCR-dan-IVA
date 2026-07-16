@extends('layouts.app')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center pb-4 border-b">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        
        <img src="{{ asset('images/Logo notaris bu lilis.png') }}" alt="Logo Notaris" class="w-12 h-12 object-contain">

    </div>
    
    <div class="mt-8 bg-white p-8 rounded-xl shadow-md border border-gray-100">
        <h3 class="text-2xl font-semibold text-gray-900">Selamat datang di LAJ Notary Hub</h3>
        <div class="mt-4 text-gray-600 leading-relaxed space-y-4">
            <p>
                Platform resmi layanan digital dari Notaris & PPAT Lilis Aenun Jariah S.H., M.Kn. Melalui dashboard ini, Anda dapat dengan mudah mengajukan permohonan dokumen hukum, memantau status proses secara real-time, serta mengelola seluruh dokumen Anda dalam satu tempat yang aman dan terorganisir.
            </p>
            <p>
                Kami berkomitmen untuk memberikan pelayanan yang cepat, terpercaya, dan sesuai dengan ketentuan hukum yang berlaku.
            </p>
        </div>
    </div>
</div>
@endsection