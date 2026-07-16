@extends('layouts.app')

@section('content')
<div class="p-8 w-full">
    <div class="pb-4 border-b">
        <h1 class="text-3xl font-bold text-gray-800">Home</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 transition-transform hover:scale-105">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Perlu Diverifikasi</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $perluDiverifikasi }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full text-yellow-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 transition-transform hover:scale-105">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Sedang Diproses</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $sedangDiproses }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full text-blue-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0011.667 0l3.181-3.183m-4.991-2.691V5.25a3.375 3.375 0 00-3.375-3.375H8.25a3.375 3.375 0 00-3.375 3.375v5.25m13.5 0H2.985" /></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 transition-transform hover:scale-105">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Sudah Selesai</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $sudahSelesai }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full text-green-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 bg-white p-8 rounded-xl shadow-md border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800">Statistik Permohonan (6 Bulan Terakhir)</h3>
        <div class="mt-4 h-80">
            <canvas id="staffChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script untuk Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('staffChart');
        if (ctx) {
            const chartLabels = @json($labels);
            const chartData = @json($data);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: '# Jumlah Permohonan',
                        data: chartData,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)', // Biru
                        borderColor: 'rgba(59, 130, 246, 1)',
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
                }
            });
        }
    });
</script>
@endpush