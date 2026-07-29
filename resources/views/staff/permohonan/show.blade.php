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
                <a href="{{ url('dokumen-pdf/' . $permohonan->file_path) }}" target="_blank" class="w-full block text-center px-4 py-3 font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-500 transition">
                    Lihat Berkas Persyaratan
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

    <div class="mt-8 bg-white p-8 rounded-xl shadow-md border">
        <h3 class="text-xl font-bold text-gray-800 border-b pb-4 mb-4">Dokumen Persyaratan & Ekstraksi OCR</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="p-3 text-sm font-semibold text-gray-600">No</th>
                        <th class="p-3 text-sm font-semibold text-gray-600">Nama Dokumen</th>
                        <th class="p-3 text-sm font-semibold text-gray-600">Status OCR</th>
                        <th class="p-3 text-sm font-semibold text-gray-600 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 text-sm">1</td>
                        <td class="p-3 text-sm font-medium text-gray-800" id="namaFileDokumen">Berkas_Persyaratan_{{ str_replace(' ', '_', $permohonan->client->name) }}.pdf</td>
                        <td class="p-3 text-sm">
                            @if($permohonan->ocr_status === 'terverifikasi')
                                <span id="statusOcr" class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-md font-semibold">✓ Terverifikasi</span>
                            @else
                                <span id="statusOcr" class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-md font-semibold">Belum Diproses</span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            <button type="button" onclick="mulaiProsesOcr()" id="btnProsesOcr" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded shadow-sm hover:bg-gray-50 text-sm font-medium transition">
                                {{ $permohonan->ocr_status === 'terverifikasi' ? 'Proses Ulang' : 'Proses OCR' }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalProgressOcr" class="hidden fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50 transition-opacity">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl overflow-hidden">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">Proses OCR — <span id="judulProgress">Berkas_Persyaratan_{{ str_replace(' ', '_', $permohonan->client->name) }}.pdf</span></h3>
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

    <div id="modalReviewOcr" class="hidden fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50 transition-opacity p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-7xl overflow-hidden h-[92vh] flex flex-col">
            <div class="p-4 border-b flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Tinjauan & Verifikasi Data OCR</h3>
                <button type="button" onclick="tutupModalReview()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>
            
            <div class="flex-1 flex overflow-hidden">
                <div class="w-1/2 bg-gray-800 border-r relative">
                    <iframe id="pdfViewerFrame" src="" class="w-full h-full border-none"></iframe>
                </div>
                
                <div class="w-1/2 p-6 overflow-y-auto bg-white">
                    <form id="formVerifikasiOcr" class="space-y-6">
                        <div id="dynamicFormContainer">
                            <p class="text-sm text-gray-500 italic">Menunggu data hasil ekstraksi...</p>
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-4 border-t bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="tutupModalReview()" class="px-5 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 text-sm font-medium transition">
                    Batal
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
    const pathLokalPdf   = "{!! $permohonan->file_path !!}";
    const pdfUtamaUrl = "{{ asset('storage/' . $permohonan->file_path) }}";
    
    const csrfToken      = "{{ csrf_token() }}";
    const permohonanId   = {{ $permohonan->id }};
    const saveUrl        = "{{ route('ocr.save') }}";

    let kumpulanDataOcr  = {};

    function tampilkanToast(pesan, tipe = 'success') {
        const existing = document.getElementById('ocrToast');
        if (existing) existing.remove();

        const warna   = tipe === 'success' ? 'bg-green-600' : 'bg-red-600';
        const ikon    = tipe === 'success' ? '✓' : '✗';
        const toast   = document.createElement('div');
        toast.id      = 'ocrToast';
        toast.className = `fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3 rounded-lg shadow-lg text-white text-sm font-medium ${warna} transition-all duration-300 opacity-0`;
        toast.innerHTML = `<span class="text-base font-bold">${ikon}</span><span>${pesan}</span>`;
        document.body.appendChild(toast);

        requestAnimationFrame(() => { toast.style.opacity = '1'; });
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 400);
        }, 3500);
    }

    async function mulaiProsesOcr() {
        const btn          = document.getElementById('btnProsesOcr');
        const modalProgress = document.getElementById('modalProgressOcr');
        const containerBar  = document.getElementById('containerProgressBar');

        btn.disabled  = true;
        btn.innerText = 'Memproses...';
        modalProgress.classList.remove('hidden');
        modalProgress.classList.add('flex');
        kumpulanDataOcr = {};

        containerBar.innerHTML = `
            <div class="flex flex-col items-center justify-center py-6 gap-4">
                <div class="relative w-16 h-16">
                    <div class="absolute inset-0 rounded-full border-4 border-blue-200"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-blue-600 border-t-transparent animate-spin"></div>
                </div>
                <div class="text-center">
                    <p class="text-sm font-semibold text-gray-700" id="statusOcrText">
                        Mengirim seluruh dokumen ke Gemini Vision AI...
                    </p>
                    <p class="text-xs text-gray-400 mt-1" id="subStatusOcrText">
                        Semua halaman diproses
                    </p>
                </div>
                <div id="timerDisplay" class="text-xs text-gray-400"></div>
            </div>`;

        let elapsed   = 0;
        const timerEl = document.getElementById('timerDisplay');
        const timer   = setInterval(() => {
            elapsed++;
            if (timerEl) timerEl.innerText = `⏱ ${elapsed}s berlalu...`;
            const statusEl = document.getElementById('statusOcrText');
            if (statusEl) {
                if (elapsed < 10)       statusEl.innerText = 'Mengirim seluruh dokumen ke Gemini Vision AI...';
                else if (elapsed < 30)  statusEl.innerText = 'Gemini sedang menganalisis semua halaman...';
                else if (elapsed < 60)  statusEl.innerText = 'Mengekstrak data dari tiap dokumen...';
                else                    statusEl.innerText = 'Hampir selesai, menyusun hasil ekstraksi...';
            }
        }, 1000);

        try {
            const controller = new window.AbortController();
            const timeoutId  = setTimeout(() => controller.abort(), 180000);

            const req = await fetch("{{ route('ocr.processDocument') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ file_path: pathLokalPdf }),
                signal: controller.signal,
            });
            clearTimeout(timeoutId);
            clearInterval(timer);

            const contentType = req.headers.get('Content-Type') || '';
            if (!contentType.includes('application/json')) {
                const htmlText = await req.text();
                console.error('Server kembalikan non-JSON:', htmlText.substring(0, 300));
                throw new Error(`Server error (HTTP ${req.status}). Cek log Laravel.`);
            }

            const res = await req.json();

            if (res.status === 'success') {
                kumpulanDataOcr = res.data || {};
                const totalDocs = Object.keys(kumpulanDataOcr).length;
                const totalHal  = res.total_pages || '?';

                containerBar.innerHTML = `
                    <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <span class="text-2xl">✅</span>
                        <div>
                            <p class="text-sm font-semibold text-green-700">Ekstraksi selesai dalam ${elapsed}s</p>
                            <p class="text-xs text-green-600 mt-0.5">
                                ${totalHal} halaman diproses • ${totalDocs} jenis dokumen ditemukan dalam 1 request API
                            </p>
                        </div>
                    </div>`;

                setTimeout(() => {
                    modalProgress.classList.add('hidden');
                    modalProgress.classList.remove('flex');
                    bukaModalReview();
                }, 1200);
            } else {
                throw new Error(res.message || 'Respon error dari server.');
            }

        } catch (e) {
            clearInterval(timer);
            const errMsg  = e.name === 'AbortError' ? 'Timeout (>3 menit)' : e.message;
            const is429   = errMsg.includes('429') || errMsg.includes('Rate limit') || errMsg.includes('quota');

            containerBar.innerHTML = `
                <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <span class="text-2xl">❌</span>
                    <div>
                        <p class="text-sm font-semibold text-red-700">Gagal memproses dokumen</p>
                        <p class="text-xs text-red-500 mt-0.5">${errMsg}</p>
                        ${is429 ? '<p class="text-xs text-orange-600 mt-1">💡 Kuota RPD habis. Coba besok jam 14.00 WIB atau isi GEMINI_OCR_API_KEY dengan key akun Google lain.</p>' : ''}
                    </div>
                </div>`;

            setTimeout(() => {
                modalProgress.classList.add('hidden');
                modalProgress.classList.remove('flex');
                btn.disabled  = false;
                btn.innerText = 'Proses OCR';
            }, 4000);
        }
    }

    function bukaModalReview() {
        const modalReview = document.getElementById('modalReviewOcr');
        modalReview.classList.remove('hidden');
        modalReview.classList.add('flex');
        
        document.getElementById('pdfViewerFrame').src = pdfUtamaUrl + "#navpanes=0&view=FitH";

        const container = document.getElementById('dynamicFormContainer');
        container.innerHTML = '';

        if (Object.keys(kumpulanDataOcr).length === 0) {
            container.innerHTML = `
                <div class="flex items-start gap-3 p-4 border border-red-200 bg-red-50 rounded-lg">
                    <span class="text-red-500 text-xl">⚠</span>
                    <div>
                        <p class="text-sm font-semibold text-red-700">Tidak ada dokumen yang dikenali</p>
                        <p class="text-xs text-red-500 mt-1">Dokumen yang didukung: KTP, Kartu Keluarga, Buku Nikah, Sertifikat Tanah, SPPT PBB, dll.</p>
                    </div>
                </div>`;
            return;
        }

        let totalField = 0, fieldKosong = 0;

        for (const [docName, fields] of Object.entries(kumpulanDataOcr)) {
            let fieldHtml = '';
            for (const [fieldName, value] of Object.entries(fields)) {
                totalField++;
                const isEmpty    = !value || value.toString().trim() === '';
                if (isEmpty) fieldKosong++;

                const inputId    = `input_${docName.replace(/\s+/g, '_')}_${fieldName.replace(/[^a-zA-Z0-9]/g, '_')}`;
                const borderCls  = isEmpty
                    ? 'border-red-400 bg-red-50 focus:ring-red-400 focus:border-red-400'
                    : 'border-gray-300 bg-white focus:ring-blue-500 focus:border-blue-500';
                const warningBadge = isEmpty
                    ? `<span class="ml-2 text-xs font-semibold text-red-500 bg-red-100 px-1.5 py-0.5 rounded">Perlu diisi manual</span>`
                    : '';

                const safeValue = (value || '').toString()
                    .replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

                const isLongText = fieldName.includes('Daftar') || 
                                   fieldName.includes('Kesimpulan') || 
                                   fieldName.includes('Waris') || 
                                   safeValue.length > 60 || 
                                   safeValue.includes('\n');

                const lineCount  = safeValue.split('\n').length;
                const dynamicRows = Math.min(12, Math.max(5, lineCount + 1));

                const inputControl = isLongText
                    ? `<textarea id="${inputId}"
                        name="ocr_data[${docName}][${fieldName}]"
                        rows="${dynamicRows}"
                        class="w-full px-3 py-2 border ${borderCls} rounded text-sm transition-colors font-sans whitespace-pre-wrap leading-relaxed"
                        oninput="this.classList.remove('border-red-400','bg-red-50'); this.classList.add('border-green-400','bg-green-50');">${safeValue}</textarea>`
                    : `<input type="text"
                        id="${inputId}"
                        name="ocr_data[${docName}][${fieldName}]"
                        value="${safeValue}"
                        class="w-full px-3 py-2 border ${borderCls} rounded text-sm transition-colors"
                        oninput="this.classList.remove('border-red-400','bg-red-50'); this.classList.add('border-green-400','bg-green-50');">`;

                fieldHtml += `
                    <div>
                        <label class="flex items-center text-xs font-semibold text-gray-700 mb-1">
                            ${fieldName}${warningBadge}
                        </label>
                        ${inputControl}
                    </div>`;
            }

            container.innerHTML += `
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 mb-5 shadow-sm">
                    <h4 class="text-sm font-bold text-blue-800 uppercase tracking-wider mb-4 border-b border-blue-200 pb-2">
                        📄 Data ${docName}
                    </h4>
                    <div class="space-y-4">${fieldHtml}</div>
                </div>`;
        }

        const pctOk  = Math.round(((totalField - fieldKosong) / totalField) * 100);
        const summCls = pctOk >= 80 ? 'bg-green-50 border-green-200 text-green-700'
                       : pctOk >= 50 ? 'bg-yellow-50 border-yellow-200 text-yellow-700'
                       : 'bg-red-50 border-red-200 text-red-700';
        const summHtml = `
            <div class="flex items-center justify-between p-3 mb-4 border rounded-lg text-xs font-medium ${summCls}">
                <span>📊 Kelengkapan data: ${totalField - fieldKosong}/${totalField} field terisi (${pctOk}%)</span>
                ${fieldKosong > 0 ? `<span>${fieldKosong} field perlu diisi manual</span>` : '<span>✓ Semua field terisi</span>'}
            </div>`;
        container.insertAdjacentHTML('afterbegin', summHtml);
    }

    function tutupModalReview() {
        document.getElementById('modalReviewOcr').classList.add('hidden');
        document.getElementById('modalReviewOcr').classList.remove('flex');
        document.getElementById('pdfViewerFrame').src = '';
    }

    async function simpanDataTerverifikasi() {
        const formElement = document.getElementById('formVerifikasiOcr');
        if (!formElement) return;

        const ocrData = {};
        formElement.querySelectorAll('input[name^="ocr_data"], textarea[name^="ocr_data"]').forEach(input => {
            const match = input.name.match(/ocr_data\[([^\]]+)\]\[([^\]]+)\]/);
            if (match) {
                const [, docName, fieldName] = match;
                if (!ocrData[docName]) ocrData[docName] = {};
                ocrData[docName][fieldName] = input.value;
            }
        });

        const btnSimpan = document.querySelector('[onclick="simpanDataTerverifikasi()"]');
        const textAsli  = btnSimpan ? btnSimpan.innerText : '';
        if (btnSimpan) { btnSimpan.disabled = true; btnSimpan.innerText = 'Menyimpan...'; }

        try {
            const response = await fetch(saveUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ permohonan_id: permohonanId, ocr_data: ocrData }),
            });
            const result = await response.json();

            if (result.status === 'success') {
                tutupModalReview();
                const statusBadge = document.getElementById('statusOcr');
                if (statusBadge) {
                    statusBadge.innerText  = '✓ Terverifikasi';
                    statusBadge.className  = 'px-2 py-1 bg-green-100 text-green-700 text-xs rounded-md font-semibold';
                }
                const btnOcr = document.getElementById('btnProsesOcr');
                if (btnOcr) { btnOcr.innerText = 'Proses Ulang'; btnOcr.disabled = false; }
                tampilkanToast('Data OCR berhasil disimpan ke database!', 'success');
            } else {
                throw new Error(result.message || 'Terjadi kesalahan pada server.');
            }
        } catch (e) {
            tampilkanToast('Gagal menyimpan: ' + e.message, 'error');
            if (btnSimpan) { btnSimpan.disabled = false; btnSimpan.innerText = textAsli; }
        }
    }
</script>
@endpush