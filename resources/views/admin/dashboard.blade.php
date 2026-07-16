@extends('layouts.app')

@section('content')
{{-- Div utama dengan padding, tanpa pembatas lebar --}}
<div class="p-8">
    
    <div class="pb-4 border-b">
        <h1 class="text-3xl font-bold text-gray-800">Home</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
        
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 transition-transform hover:scale-105">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Klien</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalKlien }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full text-green-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zm10.293 8.293c-2.628.83-5.543.83-8.171 0m0 0c-2.628.83-5.543.83-8.171 0m8.171 0c-2.22 0-4.32-.424-6.233-1.218m12.466 0c-1.913.794-4.013 1.218-6.233 1.218"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 transition-transform hover:scale-105">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Permohonan Baru</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $permohonanBaru }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full text-blue-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 transition-transform hover:scale-105">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Permohonan Selesai</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $permohonanSelesai }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full text-red-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 bg-white p-8 rounded-xl shadow-md border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800">Statistik Permohonan (6 Bulan Terakhir)</h3>
        <div class="mt-4">
            <canvas id="myChart"></canvas>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Script untuk Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('myChart');
        const chartLabels = @json($labels);
        const chartData = @json($data);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: '# Jumlah Permohonan',
                    data: chartData,
                    backgroundColor: 'rgba(127, 29, 29, 0.8)',
                    borderColor: 'rgba(127, 29, 29, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1 
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    });
</script>
@endpush