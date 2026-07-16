@extends('layouts.app')

@section('content')
<div class="p-8">
    <h1 class="text-3xl font-bold text-gray-800">Detail Permohonan</h1>
    
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2 bg-white p-8 rounded-xl shadow-md border">
            <h3 class="text-xl font-bold text-gray-800 border-b pb-4">Informasi Pengajuan</h3>
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                <div><p class="text-gray-500">Jenis Layanan</p><p class="font-semibold text-gray-900">{{ $permohonan->service->name }}</p></div>
                <div><p class="text-gray-500">Tanggal Pengajuan</p><p class="font-semibold text-gray-900">{{ $permohonan->created_at->format('d F Y, H:i') }}</p></div>
                <div><p class="text-gray-500">Pihak Pertama</p><p class="font-semibold text-gray-900">{{ $permohonan->nama_pihak_pertama }}</p></div>
                <div><p class="text-gray-500">Pihak Kedua</p><p class="font-semibold text-gray-900">{{ $permohonan->nama_pihak_kedua ?: '-' }}</p></div>
                @if($permohonan->service->is_property_related)
                <div><p class="text-gray-500">Harga Aset</p><p class="font-semibold text-gray-900">Rp {{ number_format($permohonan->harga_aset, 0, ',', '.') }}</p></div>
                <div><p class="text-gray-500">NOP</p><p class="font-semibold text-gray-900">{{ $permohonan->nop ?: '-' }}</p></div>
                @endif
                <div class="sm:col-span-2"><p class="text-gray-500">Keterangan Tambahan</p><p class="font-semibold text-gray-900">{{ $permohonan->keterangan_tambahan ?: '-' }}</p></div>
            </div>
        </div>
        
        <div class="bg-white p-8 rounded-xl shadow-md border">
            <h3 class="text-xl font-bold text-gray-800 border-b pb-4">Data Pemohon</h3>
            <div class="mt-6 space-y-4 text-sm">
                <div><p class="text-gray-500">Nama</p><p class="font-semibold text-gray-900">{{ $permohonan->client->name }}</p></div>
                <div><p class="text-gray-500">Email</p><p class="font-semibold text-gray-900">{{ $permohonan->client->email }}</p></div>
            </div>
            <div class="mt-8">
                <a href="{{ Storage::url($permohonan->file_path) }}" target="_blank" class="w-full block text-center px-4 py-3 font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-500 transition">
                    Unduh Berkas Persyaratan
                </a>
                @php
                    $editRouteName = (auth()->user()->role == 'notaris') 
                        ? 'notaris.permohonan.edit' 
                        : 'staff.permohonan.edit';
                @endphp
                <a href="{{ route($editRouteName, $permohonan) }}" class="w-full block text-center mt-4 px-4 py-3 font-medium text-white bg-red-800 rounded-lg hover:bg-red-700 transition">
                    Update Status Permohonan
                </a>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TABEL DOKUMEN & TOMBOL PROSES OCR          -->
    <!-- ========================================== -->
    <div class="mt-8 bg-white p-8 rounded-xl shadow-md border">
        <h3 class="text-xl font-bold text-gray-800 border-b pb-4 mb-4">Dokumen Persyaratan & Ekstraksi OCR</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="p-3 text-sm font-semibold text-gray-600">No</th>
                        <th class="p-3 text-sm font-semibold text-gray-600">Nama Dokumen</th>
                        <th class="p-3 text-sm font-semibold text-gray-600">Status</th>
                        <th class="p-3 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 text-sm">1</td>
                        <td class="p-3 text-sm font-medium text-gray-800" id="namaFileDokumen">Berkas_Persyaratan_{{ str_replace(' ', '_', $permohonan->client->name) }}.pdf</td>
                        <td class="p-3 text-sm">
                            <span id="statusOcr" class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-md font-semibold">Belum Diproses</span>
                        </td>
                        <td class="p-3 text-center">
                            <button type="button" onclick="mulaiProsesOcr()" id="btnProsesOcr" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded shadow-sm hover:bg-gray-50 text-sm font-medium transition">
                                Proses OCR
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: PROGRESS LOADING OCR              -->
    <!-- ========================================== -->
    <div id="modalProgressOcr" class="hidden fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50 transition-opacity">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">Proses OCR - <span id="judulProgress">Berkas_Persyaratan_{{ str_replace(' ', '_', $permohonan->client->name) }}.pdf</span></h3>
            </div>
            <div class="p-6 flex flex-row gap-6">
                <div class="w-1/3 bg-gray-100 rounded flex flex-col items-center justify-center border border-gray-200 min-h-[200px]">
                    <div class="animate-spin rounded-full h-10 w-10 border-4 border-gray-200 border-t-blue-600 mb-4"></div>
                    <p class="text-sm text-gray-500 text-center font-medium">Membaca<br>Dokumen...</p>
                </div>
                <div class="w-2/3 space-y-4">
                    <p class="text-sm font-semibold text-gray-700 mb-2">Mengekstrak Data ...</p>
                    <div id="containerProgressBar" class="space-y-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: TINJAUAN & VERIFIKASI DATA (HITL) -->
    <!-- ========================================== -->
    <div id="modalReviewOcr" class="hidden fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50 transition-opacity">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-5xl overflow-hidden h-[90vh] flex flex-col">
            <div class="p-4 border-b flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Tinjauan Data Terpusat</h3>
                <button type="button" onclick="tutupModalReview()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>
            
            <div class="flex-1 flex overflow-hidden">
                <div class="w-1/2 bg-gray-800 border-r relative">
                    <!-- Iframe Viewer PDF -->
                    <iframe id="pdfViewerFrame" src="" class="w-full h-full border-none"></iframe>
                </div>
                
                <div class="w-1/2 p-6 overflow-y-auto bg-white">
                    <form id="formVerifikasiOcr" class="space-y-6">
                        <!-- KONTAINER DINAMIS: Form akan dirender ke sini menggunakan Javascript -->
                        <div id="dynamicFormContainer">
                            <p class="text-sm text-gray-500 italic">Menunggu data hasil ekstraksi...</p>
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-4 border-t bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="tutupModalReview()" class="px-5 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 text-sm font-medium transition">
                    Koreksi Manual / Batal
                </button>
                <button type="button" onclick="simpanDataTerverifikasi()" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition shadow-sm">
                    Gunakan Data Ini
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const pathLokalPdf = "{!! $permohonan->file_path !!}"; 
    const csrfToken = "{{ csrf_token() }}";
    
    // Menjadi objek kosong, bukan objek dengan NIK/Nama statis
    let kumpulanDataOcr = {};

    async function mulaiProsesOcr() {
        const modalProgress = document.getElementById('modalProgressOcr');
        modalProgress.classList.remove('hidden');
        modalProgress.classList.add('flex');
        const containerBar = document.getElementById('containerProgressBar');
        containerBar.innerHTML = '<p class="text-sm text-gray-500">Menghitung jumlah halaman...</p>'; 
        
        // Reset data OCR untuk proses baru
        kumpulanDataOcr = {};
        
        try {
            // 1. Minta Backend Menghitung Total Halaman
            const reqCount = await fetch("{{ route('ocr.count') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ file_path: pathLokalPdf }) 
            });
            const resCount = await reqCount.json();

            if(resCount.status !== 'success') throw new Error(resCount.message);

            const totalHalaman = resCount.total_pages;
            containerBar.innerHTML = ''; // Bersihkan loading

            // Siapkan UI Progress Bar
            for(let i=1; i<=totalHalaman; i++) {
                containerBar.innerHTML += `
                    <div id="progressItem_${i}" class="flex flex-col mb-2 opacity-50">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-gray-600" id="textProgress_${i}">Halaman ${i} - Menunggu...</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="barProgress_${i}" class="bg-blue-600 h-2 rounded-full transition-all duration-500 w-0"></div>
                        </div>
                    </div>
                `;
            }

            // 2. Mulai Looping Ekstraksi per Halaman
            await jalankanEkstraksiBeruntun(1, totalHalaman);

        } catch (error) {
            alert("Gagal memproses OCR: " + error.message);
            modalProgress.classList.add('hidden');
            modalProgress.classList.remove('flex');
        }
    }

    async function jalankanEkstraksiBeruntun(halaman, total) {
        if (halaman > total) {
            // Jika semua halaman selesai, buka modal review
            setTimeout(() => {
                const modalProgress = document.getElementById('modalProgressOcr');
                modalProgress.classList.add('hidden');
                modalProgress.classList.remove('flex');
                bukaModalReview();
            }, 800);
            return;
        }

        // Animasi Sedang Memproses
        document.getElementById(`progressItem_${halaman}`).classList.remove('opacity-50');
        document.getElementById(`textProgress_${halaman}`).innerText = `Halaman ${halaman} - Mengekstrak...`;
        document.getElementById(`textProgress_${halaman}`).classList.add('text-blue-600');
        document.getElementById(`barProgress_${halaman}`).style.width = '50%';

        try {
            // Tembak API Proses Halaman
            const requestOcr = await fetch("{{ route('ocr.process') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ file_path: pathLokalPdf, page_number: halaman })
            });
            const responseOcr = await requestOcr.json();

            if(responseOcr.status === 'success') {
                // ==========================================
                // LOGIKA BARU: MENGGABUNGKAN DATA DINAMIS
                // ==========================================
                const foundDocs = responseOcr.data;
                for (const [docName, fields] of Object.entries(foundDocs)) {
                    if (!kumpulanDataOcr[docName]) {
                        kumpulanDataOcr[docName] = {};
                    }
                    // Gabungkan semua field (KTP, NPWP, dll) yang ditemukan
                    Object.assign(kumpulanDataOcr[docName], fields);
                }

                // Animasi Sukses
                document.getElementById(`textProgress_${halaman}`).innerText = `Halaman ${halaman} - Selesai`;
                document.getElementById(`textProgress_${halaman}`).classList.replace('text-blue-600', 'text-green-600');
                document.getElementById(`barProgress_${halaman}`).classList.replace('bg-blue-600', 'bg-green-500');
                document.getElementById(`barProgress_${halaman}`).style.width = '100%';
            } else {
                throw new Error(responseOcr.message);
            }
        } catch(e) {
            document.getElementById(`textProgress_${halaman}`).innerText = `Halaman ${halaman} - Error/Lewati`;
            document.getElementById(`textProgress_${halaman}`).classList.replace('text-blue-600', 'text-red-500');
            document.getElementById(`barProgress_${halaman}`).classList.replace('bg-blue-600', 'bg-red-500');
        }

        // Lanjut proses halaman berikutnya secara sekuensial
        await jalankanEkstraksiBeruntun(halaman + 1, total);
    }

    function bukaModalReview() {
        const modalReview = document.getElementById('modalReviewOcr');
        modalReview.classList.remove('hidden');
        modalReview.classList.add('flex');
        
        // Membuka PDF menggunakan route view.pdf agar tidak 403
        document.getElementById('pdfViewerFrame').src = "{{ route('view.pdf') }}?path=" + encodeURIComponent(pathLokalPdf);

        // ==========================================
        // MENGGAMBAR FORM HTML SECARA DINAMIS
        // ==========================================
        const container = document.getElementById('dynamicFormContainer');
        container.innerHTML = ''; // Kosongkan form sebelumnya

        // Validasi jika tidak ada dokumen yang terdeteksi
        if (Object.keys(kumpulanDataOcr).length === 0) {
            container.innerHTML = '<p class="text-sm text-red-500 font-semibold p-4 border border-red-200 bg-red-50 rounded-lg">Tidak ada format dokumen (KTP/KK/NPWP/Sertifikat/dll) yang berhasil dikenali dalam file PDF ini.</p>';
            return;
        }

        // Looping untuk membuat UI kategori dokumen
        for (const [docName, fields] of Object.entries(kumpulanDataOcr)) {
            let formHtml = `
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 mb-5 shadow-sm">
                    <h4 class="text-sm font-bold text-blue-800 uppercase tracking-wider mb-4 border-b border-blue-200 pb-2">Data ${docName}</h4>
                    <div class="space-y-4">
            `;

            // Looping untuk membuat input teks per field
            for (const [fieldName, value] of Object.entries(fields)) {
                // Membuat ID unik yang aman dari karakter aneh
                const inputId = `input_${docName.replace(/\s+/g, '_')}_${fieldName.replace(/[^a-zA-Z0-9]/g, '_')}`;
                
                formHtml += `
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">${fieldName}</label>
                        <input type="text" id="${inputId}" name="ocr_data[${docName}][${fieldName}]" value="${value || ''}" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors">
                    </div>
                `;
            }

            formHtml += `</div></div>`;
            container.innerHTML += formHtml; // Suntikkan HTML ke dalam kontainer
        }
    }

    function tutupModalReview() {
        const modalReview = document.getElementById('modalReviewOcr');
        modalReview.classList.add('hidden');
        modalReview.classList.remove('flex');
        document.getElementById('pdfViewerFrame').src = ''; 
    }

    async function simpanDataTerverifikasi() {
        // Ambil data dinamis dari form menggunakan FormData
        const formElement = document.getElementById('formVerifikasiOcr');
        if (!formElement) {
            console.error('Form Verifikasi OCR tidak ditemukan.');
            return;
        }
        const formData = new window.FormData(formElement);
        
        // Console.log untuk melihat struktur data yang dikumpulkan (opsional)
        console.log("Data siap dikirim ke database: ", Object.fromEntries(formData.entries()));
        
        tutupModalReview();
        document.getElementById('statusOcr').innerText = 'Selesai (Terverifikasi)';
        document.getElementById('statusOcr').className = 'px-2 py-1 bg-green-100 text-green-700 text-xs rounded-md font-semibold';
        alert('Validasi selesai. Data siap digunakan dan disimpan.');
    }
</script>
@endpush