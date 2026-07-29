<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Intent;
use App\Models\PolaBakuNlu;

class NluSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //knowledge base
        $nluData = [
            // KELOMPOK DEFINISI LAYANAN NOTARIS & PPAT
            [
                'nama_intent' => 'Definisi_PPJB_Notaris',
                'konteks_jawaban' => "PPJB (Perjanjian Pengikatan Jual Beli) adalah akta Notaris yang berfungsi sebagai perjanjian pendahuluan antara penjual dan pembeli.\n\nPPJB biasanya dibuat apabila syarat untuk membuat AJB belum terpenuhi, contohnya:\n1. Pembayaran rumah masih dicicil (termin).\n2. Sertifikat masih dalam proses pemecahan di BPN.\n3. Pajak penjual/pembeli belum dibayarkan lunas.",
                'pola_pertanyaan' => [
                    'Apa itu PPJB?',
                    'Kapan harus pakai PPJB bukan AJB?',
                    'bedanya ppjb sama ajb apa?',
                    'pengertian akta ppjb',
                    'kenapa harus bikin ppjb dulu?',
                    'ppjb singkatan dari apa?',
                    'perjanjian pengikatan jual beli itu apa min?',
                    'mau beli rumah tapi sertifikat belum pecah pake akta apa?',
                    'fungsi ppjb buat apa?',
                    'akta ppjb notaris'
                ]
            ],
            [
                'nama_intent' => 'Definisi_AJB_PPAT',
                'konteks_jawaban' => "AJB (Akta Jual Beli) adalah akta otentik yang dibuat oleh PPAT sebagai bukti sah bahwa telah terjadi transaksi jual beli dan pengalihan hak atas tanah atau bangunan.\n\nFungsi Utama AJB:\nSebagai syarat mutlak untuk melakukan proses balik nama sertifikat di Kantor Pertanahan (BPN) agar nama pemilik lama di sertifikat berubah menjadi nama pembeli baru.",
                'pola_pertanyaan' => [
                    'Apa itu AJB?',
                    'Fungsinya ajb untuk apa?',
                    'pengertian akta jual beli',
                    'kenapa harus bikin ajb?',
                    'bukti sah beli tanah apa namanya?',
                    'akta jual beli tanah',
                    'kapan ajb dibuat?',
                    'mau balik nama harus ada ajb?',
                    'kalau beli rumah akta nya apa?',
                    'ajb ppat',
                    'syarat sah jual beli tanah'
                ]
            ],
            [
                'nama_intent' => 'Definisi_Hibah_PPAT',
                'konteks_jawaban' => "Akta Hibah adalah dokumen resmi peralihan hak atas tanah/bangunan yang diberikan secara cuma-cuma dari seseorang KETIKA MASIH HIDUP (misalnya dari orang tua ke anak kandung).\n\nPerbedaan dengan Waris:\nHibah dilakukan saat pemberi masih hidup dan sehat, sedangkan Warisan baru beralih haknya setelah pemilik/pewaris meninggal dunia.",
                'pola_pertanyaan' => [
                    'Apa itu Akta Hibah?',
                    'Bedanya hibah dengan warisan?',
                    'pengertian hibah tanah',
                    'hibah ke anak itu apa?',
                    'mau ngasih tanah ke anak pake akta apa?',
                    'syarat sah hibah',
                    'kalo ortu ngasih rumah sebelum meninggal pake akta apa?',
                    'kasih tanah gratis ke keluarga',
                    'akta hibah ppat',
                    'apa bedanya dihibahkan dan diwariskan?'
                ]
            ],
            [
                'nama_intent' => 'Definisi_APHB_PPAT',
                'konteks_jawaban' => "APHB (Akta Pembagian Hak Bersama) adalah akta PPAT yang digunakan untuk melepaskan atau membagi hak bersama atas tanah/bangunan kepada salah satu atau beberapa pihak.\n\nContoh Kasus:\nSering digunakan oleh para ahli waris untuk membalik nama sertifikat warisan gabungan, menjadi hanya atas nama satu orang anak (atau istri) yang disepakati bersama oleh keluarga.",
                'pola_pertanyaan' => [
                    'Apa itu APHB?',
                    'pengertian akta pembagian hak bersama',
                    'fungsi aphb waris',
                    'cara mecah nama di sertifikat waris gmn?',
                    'turun waris ke satu anak pakai akta apa?',
                    'biar sertifikat waris jadi satu nama gimana?',
                    'aphb ppat',
                    'akta pelepasan hak waris',
                    'akta pembagian harta bersama'
                ]
            ],
            [
                'nama_intent' => 'Definisi_Hak_Tanggungan',
                'konteks_jawaban' => "APHT (Akta Pemberian Hak Tanggungan) adalah akta PPAT yang digunakan untuk membebankan jaminan atas tanah/sertifikat kepada pihak Bank atau Kreditur.\n\nProsesnya:\nAkta ini dibuat ketika Anda mengajukan pinjaman uang ke Bank dengan jaminan sertifikat rumah/tanah. Setelah pinjaman lunas, Anda wajib mengurus 'Roya' untuk menghapus catatan utang tersebut di BPN.",
                'pola_pertanyaan' => [
                    'Apa itu Hak Tanggungan?',
                    'pengertian apht',
                    'akta buat jaminan bank namanya apa?',
                    'fungsi akta pemberian hak tanggungan',
                    'mau gadai sertifikat ke bank pakenya akta apa?',
                    'jaminkan sertifikat rumah di notaris',
                    'roya itu apa',
                    'kredit bank pake sertifikat',
                    'pasang hak tanggungan di sertifikat'
                ]
            ],
            [
                'nama_intent' => 'Definisi_Legalisasi_Waarmaking',
                'konteks_jawaban' => "Legalisasi dan Waarmaking adalah layanan Notaris untuk pengesahan tanda tangan pada surat di bawah tangan (seperti perjanjian utang pribadi atau surat kuasa).\n\nPerbedaannya:\n\n**1. Legalisasi:**\nPara pihak menandatangani surat LANGSUNG di hadapan Notaris pada hari itu juga. Notaris menjamin kepastian tanggal dan identitas penandatangan.\n\n**2. Waarmaking:**\nSurat sudah ditandatangani sebelumnya di rumah. Notaris hanya mendaftarkan surat tersebut ke dalam buku register khusus tanpa menjamin kebenaran waktu tanda tangan dan isinya.",
                'pola_pertanyaan' => [
                    'apa bedanya legalisasi sama waarmaking?',
                    'kalau mau legalisir perjanjian di notaris gimana?',
                    'pengertian waarmaking',
                    'arti legalisasi notaris',
                    'sahkan tanda tangan surat perjanjian di notaris',
                    'mau legalisir surat kuasa',
                    'notaris bisa legalisir dokumen ga?',
                    'waarmerking notaris'
                ]
            ],
            [
                'nama_intent' => 'Definisi_Wasiat',
                'konteks_jawaban' => "Akta Wasiat (Testament) adalah pernyataan sah secara hukum dari seseorang tentang apa yang ingin dilakukannya terhadap harta kekayaannya SETELAH ia meninggal dunia nanti.\n\nSifat Akta:\nAkta ini bersifat sangat rahasia, dibuat langsung di hadapan Notaris tanpa boleh dihadiri calon ahli waris, dan akan didaftarkan ke Pusat Daftar Wasiat Kementerian Hukum dan HAM RI.",
                'pola_pertanyaan' => [
                    'apa itu akta wasiat?',
                    'cara bagi harta sebelum meninggal pakai apa?',
                    'pengertian surat wasiat notaris',
                    'syarat bikin akta wasiat',
                    'pesan bagi harta peninggalan',
                    'wasiat rahasia di notaris',
                    'kapan surat wasiat berlaku?',
                    'akta testament'
                ]
            ],

            // KELOMPOK PERSYARATAN DOKUMEN
            [
                'nama_intent' => 'Info_Syarat_AJB',
                'konteks_jawaban' => "Berikut adalah persyaratan dokumen untuk pembuatan Akta Jual Beli (AJB):\n\n**Syarat Pihak Penjual:**\n1. Sertifikat Asli\n2. KTP Suami & Istri\n3. Kartu Keluarga (KK)\n4. Buku Nikah / Akta Cerai (Jika ada)\n5. SPPT PBB 5 Tahun Terakhir & Bukti Lunasnya\n6. NPWP Penjual\n\n**Syarat Pihak Pembeli:**\n1. KTP Suami & Istri\n2. Kartu Keluarga (KK)\n3. NPWP Pembeli",
                'pola_pertanyaan' => [
                    'Apa syarat pengurusan AJB?',
                    'min, mau urus ajb gmn carana?',
                    'sarat jual tanah ke notaris donk',
                    'punten, klo mau bikin ajb tanah syaratnya apa aja ya?',
                    'berkas juaL beLi rumh yg harus disiapin apa min?',
                    'kalo mau bikin ajb syarat nya apa saja',
                    'syarat pembeli ajb',
                    'syarat penjual ajb',
                    'dokumen untuk ajb tanah',
                    'ktp kk npwp ajb',
                    'persyaratan jual beli tanah lengkap'
                ]
            ],
            [
                'nama_intent' => 'Info_Syarat_Hibah',
                'konteks_jawaban' => "Berikut adalah persyaratan dokumen untuk pembuatan Akta Hibah:\n\n**Syarat Pihak Pemberi Hibah:**\n1. Sertifikat Asli\n2. KTP Suami & Istri\n3. Kartu Keluarga (KK) & Buku Nikah\n4. SPPT PBB Lunas & NPWP\n5. Surat Persetujuan Mutlak dari ahli waris kandung lainnya (jika hibah diberikan hanya ke salah satu anak).\n\n**Syarat Pihak Penerima Hibah:**\n1. KTP & Kartu Keluarga (KK)\n2. Akta Kelahiran (Sangat penting untuk membuktikan garis keturunan lurus agar mendapat keringanan pajak hibah).",
                'pola_pertanyaan' => [
                    'syarat bikin akta hibah',
                    'persyaratan hibah tanah',
                    'apa aja yang disiapkan untuk hibah ke anak',
                    'berkas hibah rumah apa aja?',
                    'syarat penerima hibah',
                    'syarat pemberi hibah',
                    'harus ada persetujuan anak lain ga kalo hibah?',
                    'dokumen akta hibah ppat',
                    'syarat hibah tanah ke anak kandung'
                ]
            ],
            [
                'nama_intent' => 'Info_Syarat_Pendirian_PT',
                'konteks_jawaban' => "Untuk pendirian PT (Perseroan Terbatas), siapkan kelengkapan data berikut:\n\n**Data Pendiri & Pengurus:**\n1. Fotokopi KTP & NPWP para pendiri (Minimal 2 orang).\n2. Kartu Keluarga (KK) masing-masing pendiri.\n3. Susunan Direksi dan Komisaris.\n\n**Data Perusahaan:**\n1. Usulan 3 nama PT (Wajib minimal 3 kata dalam Bahasa Indonesia).\n2. Surat Keterangan Domisili Usaha / Alamat Lengkap PT.\n3. Rincian Modal (Modal Dasar, Modal Ditempatkan, & Modal Disetor).\n4. Rincian Bidang Usaha (Akan disesuaikan dengan KBLI terbaru oleh staf kami).",
                'pola_pertanyaan' => [
                    'syarat bikin pt apa saja?',
                    'dokumen untuk pendirian pt',
                    'gimana cara buat pt?',
                    'persyaratan bikin pt ke notaris',
                    'ktp pendiri pt',
                    'modal minimal pt',
                    'butuh apa aja buat pt',
                    'berkas pendirian pt baru',
                    'syarat buat pt perorangan',
                    'susunan direksi pt syaratnya apa?'
                ]
            ],
            [
                'nama_intent' => 'Info_Syarat_Pemecahan_Sertifikat',
                'konteks_jawaban' => "Untuk layanan Pemecahan Sertifikat di BPN melalui kantor kami, siapkan berkas berikut:\n\n**Kelengkapan Dokumen:**\n1. Sertifikat Asli yang akan dipecah.\n2. Fotokopi KTP dan KK Pemilik Sertifikat.\n3. SPPT PBB tahun berjalan beserta bukti lunas (STTS).\n4. NPWP Pemilik.\n5. Siteplan atau sketsa kasar rencana letak dan ukuran pemecahan tanah.",
                'pola_pertanyaan' => [
                    'syarat mecah sertifikat tanah',
                    'mau mecah tanah dokumennya apa aja?',
                    'persyaratan pemecahan sertifikat di bpn',
                    'dokumen pemecahan tanah',
                    'berkas mecah tanah bpn',
                    'cara mecah sertifikat jadi dua',
                    'syarat splitzing sertifikat',
                    'dokumen untuk bagi tanah kapling'
                ]
            ],
            [
                'nama_intent' => 'Info_Syarat_SKW',
                'konteks_jawaban' => "Syarat Pembuatan Surat Keterangan Waris (SKW) di Notaris:\n\n**Dari Pihak Pewaris (Almarhum):**\n1. Akta Kematian asli dari Disdukcapil.\n2. Buku Nikah / Akta Cerai.\n3. KTP & KK terakhir milik pewaris.\n\n**Dari Pihak Ahli Waris:**\n1. KTP & KK seluruh Ahli Waris.\n2. Akta Kelahiran seluruh Ahli Waris.\n3. Surat Pengantar / Keterangan Waris asli yang sudah ditandatangani RT, RW, dan Lurah/Kades setempat.",
                'pola_pertanyaan' => [
                    'syarat buat surat keterangan waris',
                    'dokumen bikin skw',
                    'berkas untuk penetapan ahli waris',
                    'syarat surat keterangan waris notaris',
                    'berkas skw kelurahan',
                    'dokumen ahli waris',
                    'apa aja buat skw',
                    'persyaratan turun waris'
                ]
            ],

            // KELOMPOK ALUR PROSEDUR & WAKTU
            [
                'nama_intent' => 'Info_Alur_Pertanahan',
                'konteks_jawaban' => "Alur Pembuatan Akta Tanah (AJB/Hibah/APHB) hingga selesai Balik Nama:\n\n**TAHAP 1: Persiapan (1-7 Hari)**\n- Pengecekan Sertifikat di BPN untuk memastikan tanah aman/tidak diblokir.\n- Validasi & Pembayaran Pajak PPh (Penjual) dan BPHTB (Pembeli).\n\n**TAHAP 2: Penandatanganan (1 Hari)**\n- Pembacaan dan Tanda Tangan Akta oleh penjual dan pembeli di hadapan PPAT.\n\n**TAHAP 3: Proses BPN (14-30 Hari Kerja)**\n- Pendaftaran berkas Balik Nama di loket Kantor Pertanahan (BPN).\n\n*Estimasi total waktu hingga buku sertifikat atas nama baru selesai adalah 1 hingga 2 bulan bergantung pada antrean di BPN.*",
                'pola_pertanyaan' => [
                    'berapa lama proses balik nama sertifikat?',
                    'alur urus ajb tanah',
                    'proses bikin sertifikat memakan waktu berapa hari?',
                    'tahapan jual beli tanah di ppat',
                    'waktu proses bpn',
                    'kapan sertifikat selesai',
                    'proses ajb ke balik nama',
                    'lama pengurusan sertifikat di notaris',
                    'step by step jual beli tanah'
                ]
            ],
            [
                'nama_intent' => 'Info_Alur_Badan_Usaha',
                'konteks_jawaban' => "Alur Pendirian Badan Usaha (PT / CV / Yayasan):\n\n**TAHAP 1: Pengecekan (1 Hari)**\n- Pengecekan dan Pemesanan Nama Perusahaan di sistem AHU Kemenkumham.\n\n**TAHAP 2: Pembuatan Akta (1-3 Hari)**\n- Pembuatan Draf Akta oleh Notaris.\n- Penandatanganan Akta oleh seluruh pendiri perusahaan.\n\n**TAHAP 3: Pengesahan (1-3 Hari)**\n- Pendaftaran Akta untuk mendapatkan SK Menteri Hukum dan HAM.\n- Pendaftaran NIB (Nomor Induk Berusaha) di sistem OSS.\n\n*Estimasi total waktu pengerjaan selesai adalah 3 hingga 7 hari kerja.*",
                'pola_pertanyaan' => [
                    'berapa lama bikin pt?',
                    'proses pendirian cv berapa hari',
                    'alur pembuatan pt dari awal',
                    'waktu tunggu bikin akta yayasan',
                    'proses pt selesai kapan?',
                    'tahapan bikin cv',
                    'sk kemenkumham kapan keluar',
                    'waktu proses pt di notaris'
                ]
            ],
            // KELOMPOK BIAYA LAYANAN
            [
                'nama_intent' => 'Info_Biaya_AJB_APHB',
                'konteks_jawaban' => "Biaya pembuatan AJB dan Balik Nama Sertifikat terdiri dari beberapa komponen wajib:\n\n**1. Komponen Pajak Negara:**\n- Pajak Penjual (PPh): 2,5% dari Nilai Transaksi.\n- Pajak Pembeli (BPHTB): 5% dari (Nilai Transaksi dikurangi Nilai Bebas Pajak Daerah).\n\n**2. Komponen Jasa & Pendaftaran:**\n- Biaya PNBP BPN: Sesuai rumus zona nilai tanah pemerintah.\n- Honorarium PPAT: Sesuai kode etik PPAT, maksimal 1% dari nilai transaksi.\n\n*Catatan: Karena hitungan BPHTB dan PNBP tiap daerah berbeda, silakan unggah foto Sertifikat dan SPPT PBB Anda di sistem. Staf kami akan menghitungkan rincian pastinya untuk Anda.*",
                'pola_pertanyaan' => [
                    'Berapa biaya AJB rumah seharga 500 juta?',
                    'biaya balik nama sertifikat berapa?',
                    'pajak ajb pembeli dan penjual',
                    'tarif ppat untuk jual beli',
                    'biaya ajb',
                    'pajak jual beli tanah',
                    'harga ajb notaris',
                    'cara hitung pajak pembeli tanah',
                    'pajak penjual pph berapa persen',
                    'biaya aphb waris'
                ]
            ],
            [
                'nama_intent' => 'Info_Biaya_Badan_Usaha',
                'konteks_jawaban' => "Biaya pendirian badan usaha (PT/CV/Yayasan) sangat bervariasi, mencakup komponen berikut:\n\n1. Honorarium Notaris pembuat akta.\n2. PNBP pendaftaran nama perusahaan di AHU Kemenkumham.\n3. Biaya cetak lembaran/berita negara.\n4. Pengurusan NIB di OSS (opsional jika melalui biro jasa notaris).\n\nBesaran tarif bergantung pada **Klasifikasi Modal Dasar** perusahaan yang Anda buat (Kecil, Menengah, atau Besar). Silakan tinggalkan pesan atau nomor HP Anda agar Staf Admin kami dapat mengirimkan proposal penawaran harga terbaik.",
                'pola_pertanyaan' => [
                    'Berapa biaya notaris bikin PT?',
                    'harganya brp bikin pt?',
                    'ppat lilis trima bikin pt ngga ya? biayanya brp kira2?',
                    'ongkos bikin pt cv brp?',
                    'harga pendirian cv',
                    'tarif notaris pt',
                    'biaya pt perorangan',
                    'berapa harga buat yayasan'
                ]
            ],

            // KELOMPOK KASUS KHUSUS (EDGE CASES)
            [
                'nama_intent' => 'Info_Edge_Sertifikat_Meninggal',
                'konteks_jawaban' => "⚠️ **TIDAK BISA LANGSUNG DIBUATKAN AJB.**\n\nBerdasarkan aturan hukum pertanahan, tanah warisan yang pemilik lamanya (nama di sertifikat) telah meninggal dunia harus **dibalik nama terlebih dahulu (Turun Waris)** kepada seluruh ahli waris yang sah menggunakan bukti Surat Keterangan Waris (SKW).\n\nSetelah buku sertifikat tersebut terbit dengan nama-nama ahli waris di dalamnya, barulah para ahli waris tersebut bisa bersama-sama menandatangani Akta Jual Beli (AJB) kepada pihak pembeli.",
                'pola_pertanyaan' => [
                    'Sertifikatnya masih nama kakek saya yang sudah meninggal, mau saya jual langsung ke orang lain pakai AJB bisa?',
                    'jual tanah warisan tanpa balik nama',
                    'bisa gak ajb tanah tapi pemilik di sertifikat udah wafat?',
                    'sertifikat atas nama bapak udh meninggal, bisa langsung dijual?',
                    'jual tanah tapi pemilik meninggal',
                    'sertifikat nama ortu wafat',
                    'cara jual warisan',
                    'turun waris dulu atau langsung jual'
                ]
            ],
            [
                'nama_intent' => 'Info_Edge_Sengketa_Waris',
                'konteks_jawaban' => "⚠️ **PERSETUJUAN MUTLAK 100% DIBUTUHKAN.**\n\nDalam hukum perdata, penjualan objek waris mensyaratkan persetujuan dari **seluruh ahli waris tanpa terkecuali**. Jika ada satu orang saja (walaupun anak bungsu/kakak tertua) yang menolak atau tidak mau hadir untuk menandatangani akta jual beli, PPAT dilarang keras membuat akta pengalihan hak tersebut.\n\nSolusinya:\nKami sangat menyarankan Bapak/Ibu untuk melakukan musyawarah dan mediasi keluarga secara internal terlebih dahulu sampai mencapai mufakat, sebelum memprosesnya di kantor Notaris.",
                'pola_pertanyaan' => [
                    'Ibu saya mau jual rumah waris, tapi kakak saya tidak mau tanda tangan karena minta bagian lebih besar.',
                    'ahli waris ga mau ttd ajb',
                    'gimana kalau ada keluarga yang gak setuju jual tanah waris',
                    'salah satu anak ga mau jual warisan',
                    'jual warisan tapi ada yang ga setuju',
                    'salah satu anak ga mau tanda tangan ajb',
                    'ahli waris menolak jual',
                    'jual tanah waris tanpa persetujuan'
                ]
            ],
            [
                'nama_intent' => 'Info_Edge_Yurisdiksi_Wilayah',
                'konteks_jawaban' => "Aturan batas wilayah kerja (Yurisdiksi) terbagi menjadi dua jenis:\n\n**1. Untuk Akta Tanah (AJB, Hibah, APHT, APHB):**\nKewenangan PPAT dibatasi oleh letak lokasi tanah berada. Jika tanah Anda di luar area kabupaten/kota kerja Kantor kami, maka akta mutlak harus dibuat oleh PPAT di wilayah tanah tersebut berada.\n\n**2. Untuk Akta Notaris (PPJB, PT, CV, Wasiat, Legalisasi):**\nNotaris berwenang membuat akta untuk objek/perusahaan di **Seluruh Indonesia**, DENGAN SYARAT mutlak para pihak wajib hadir dan menandatangani akta tersebut di dalam kantor Notaris / wilayah kedudukan Notaris yang bersangkutan.",
                'pola_pertanyaan' => [
                    'Saya beli tanah di Bogor, tapi domisili saya di Cianjur. Bisa urus AJB di kantor Ibu Lilis?',
                    'bisa bikin ajb buat tanah di jakarta gak min?',
                    'wilayah kerja ppat cianjur',
                    'bikin pt tapi kantornya di bandung bisa di notaris cianjur?',
                    'notaris cianjur bisa bikin pt jakarta?',
                    'ppat cianjur urus tanah bogor',
                    'batas wilayah kerja notaris ppat',
                    'bisa ajb beda kota?',
                    'tanah di bandung akta di cianjur'
                ]
            ]
        ];

        DB::beginTransaction();

        try {
            foreach ($nluData as $data) {
                $intent = Intent::create([
                    'nama_intent' => $data['nama_intent'],
                    'konteks_jawaban' => $data['konteks_jawaban'],
                    'is_active' => true,
                ]);

                $polaBakuRecords = [];
                foreach ($data['pola_pertanyaan'] as $pola) {
                    $polaBakuRecords[] = [
                        'intent_id' => $intent->id,
                        'pola_pertanyaan' => $pola,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                PolaBakuNlu::insert($polaBakuRecords);
            }

            DB::commit();
            $this->command->info('Dataset Lengkap NLU Notaris/PPAT berhasil disuntikkan ke database!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Terjadi kesalahan saat seeding data: ' . $e->getMessage());
        }
    }
}