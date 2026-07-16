@extends('layouts.app')
@section('content')
<div class="p-8">
    <h1 class="text-3xl font-bold text-gray-800">Tambah Layanan Baru</h1>
    <div class="mt-8 bg-white p-8 rounded-xl shadow-md border w-full">
        <form action="{{ route('staff.layanan.store') }}" method="POST">
            @csrf
            {{-- Include file _form.blade.php --}}
            @include('staff.layanan._form', ['service' => new \App\Models\Service()])
        </form>
    </div>
</div>
@endsection