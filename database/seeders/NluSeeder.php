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
        // Dataset NLU Notaris & PPAT berdasarkan Knowledge Base IVA
        $nluData = [
            // --- KELOMPOK DEFINISI LAYANAN ---
            [
                'nama_intent' => 'Definisi_PPJB_Notaris',
                'konteks_jawaban' => 'PPJB (Perjanjian Pengikatan Jual Beli) adalah akta Notaris yang dibuat sebagai perjanjian awal antara penjual dan pembeli. PPJB digunakan apabila syarat pembuatan AJB belum terpenuhi, misalnya: pembayaran tanah masih dicicil, sertifikat masih dalam proses pemecahan di BPN, atau pajak belum dibayarkan.',
                'pola_pertanyaan' => [
                    'Apa itu PPJB?',
                    'Kapan harus pakai PPJB bukan AJB?',
                    'bedanya ppjb sama ajb apa?',
                    'pengertian akta ppjb'
                ]
            ],
            [
                'nama_intent' => 'Definisi_AJB_PPAT',
                'konteks_jawaban' => 'AJB (Akta Jual Beli) adalah akta otentik yang dibuat oleh PPAT sebagai bukti sah adanya transaksi jual beli dan pengalihan hak atas tanah/bangunan. AJB adalah syarat mutlak untuk melakukan proses balik nama sertifikat di Kantor Pertanahan (BPN).',
                'pola_pertanyaan' => [
                    'Apa itu AJB?',
                    'Fungsinya ajb untuk apa?',
                    'pengertian akta jual beli',
                    'kenapa harus bikin ajb?'
                ]
            ],
            [
                'nama_intent' => 'Definisi_Hibah_PPAT',
                'konteks_jawaban' => 'Akta Hibah adalah dokumen peralihan hak atas tanah/bangunan yang diberikan secara cuma-cuma dari seseorang KETIKA MASIH HIDUP (misalnya dari ayah ke anak). Berbeda dengan warisan yang baru beralih setelah pemilik meninggal dunia.',
                'pola_pertanyaan' => [
                    'Apa itu Akta Hibah?',
                    'Bedanya hibah dengan warisan?',
                    'pengertian hibah tanah',
                    'hibah ke anak itu apa?'
                ]
            ],
            [
                'nama_intent' => 'Definisi_APHB_PPAT',
                'konteks_jawaban' => 'APHB (Akta Pembagian Hak Bersama) adalah akta PPAT yang digunakan untuk melepaskan atau membagi hak bersama atas tanah/bangunan kepada salah satu atau beberapa pihak. Biasanya digunakan oleh ahli waris untuk membalik nama sertifikat warisan kepada satu nama anak/istri.',
                'pola_pertanyaan' => [
                    'Apa itu APHB?',
                    'pengertian akta pembagian hak bersama',
                    'fungsi aphb waris'
                ]
            ],
            [
                'nama_intent' => 'Definisi_PT_Notaris',
                'konteks_jawaban' => 'PT (Perseroan Terbatas) adalah badan usaha yang berstatus BADAN HUKUM. Kelebihannya adalah adanya pemisahan harta pribadi dengan harta perusahaan, serta kepemilikan modal dalam bentuk saham. Sangat cocok untuk usaha skala menengah ke atas yang mencari investor.',
                'pola_pertanyaan' => [
                    'Apa kelebihan bikin PT?',
                    'Apa itu PT?',
                    'pengertian pt perseroan terbatas',
                    'bedanya pt sama cv'
                ]
            ],
            [
                'nama_intent' => 'Definisi_CV_Notaris',
                'konteks_jawaban' => 'CV (Commanditaire Vennootschap) adalah badan usaha BUKAN badan hukum. Bedanya dengan PT: CV tidak ada pemisahan harta pribadi bagi pengurus aktif (sekutu komplementer), tidak ada batas minimal modal standar, dan proses pendiriannya lebih cepat serta biayanya lebih terjangkau. Cocok untuk UMKM/kontraktor pemula.',
                'pola_pertanyaan' => [
                    'Apa itu CV?',
                    'Bedanya cv dengan PT?',
                    'pengertian cv'
                ]
            ],
            [
                'nama_intent' => 'Definisi_SKW_Notaris',
                'konteks_jawaban' => 'SKW adalah dokumen resmi yang menetapkan siapa saja ahli waris yang sah secara hukum dari seseorang yang telah meninggal dunia. SKW wajib dibuat sebelum ahli waris dapat menjual tanah warisan atau mencairkan tabungan almarhum di bank.',
                'pola_pertanyaan' => [
                    'Apa itu Surat Keterangan Waris (SKW)?',
                    'fungsi skw',
                    'pengertian penetapan waris'
                ]
            ],

            // --- KELOMPOK PERSYARATAN ---
            [
                'nama_intent' => 'Info_Syarat_AJB',
                'konteks_jawaban' => 'Untuk AJB, Syarat Penjual: Sertifikat Asli, KTP Suami & Istri, KK, Buku Nikah, Surat Persetujuan Jual dari Pasangan, PBB Asli 5 Tahun Terakhir, dan NPWP. Syarat Pembeli (WNI): KTP (Suami Istri jika menikah), KK, dan NPWP.',
                'pola_pertanyaan' => [
                    'Apa syarat pengurusan AJB?',
                    'min, mau urus ajb gmn carana?',
                    'sarat jual tanah ke notaris donk',
                    'punten, klo mau bikin ajb tanah syaratnya apa aja ya?',
                    'berkas juaL beLi rumh yg harus disiapin apa min?',
                    'kalo mau bikin ajb syarat nya apa saja'
                ]
            ],
            [
                'nama_intent' => 'Info_Syarat_Hibah',
                'konteks_jawaban' => 'Syarat Pemberi Hibah: Sertifikat Asli, KTP Suami Istri, KK, Buku Nikah, PBB Lunas, NPWP, dan Surat Persetujuan dari ahli waris kandung lainnya (Mutlak). Syarat Penerima: KTP, KK, dan Akta Kelahiran.',
                'pola_pertanyaan' => [
                    'syarat bikin akta hibah',
                    'persyaratan hibah tanah',
                    'apa aja yang disiapkan untuk hibah ke anak'
                ]
            ],
            [
                'nama_intent' => 'Info_Syarat_Pendirian_PT',
                'konteks_jawaban' => 'Syarat Pendirian PT: 1. Fotokopi KTP & NPWP (Min. 2 pendiri), 2. KK, 3. Usulan 3 nama PT (min. 3 kata bahasa Indonesia), 4. Surat Keterangan Domisili, 5. Rincian Modal, 6. Rincian Bidang Usaha (KBLI).',
                'pola_pertanyaan' => [
                    'syarat bikin pt apa saja?',
                    'dokumen untuk pendirian pt',
                    'gimana cara buat pt?'
                ]
            ],
            [
                'nama_intent' => 'Info_Syarat_SKW',
                'konteks_jawaban' => 'Syarat SKW: Akta Kematian asli, Buku Nikah/Akta Cerai Pewaris, KTP & KK Pewaris, KTP & KK seluruh Ahli Waris, Akta Kelahiran seluruh Ahli Waris, dan Surat Pengantar RT/RW/Kelurahan.',
                'pola_pertanyaan' => [
                    'syarat buat surat keterangan waris',
                    'dokumen bikin skw'
                ]
            ],

            // --- KELOMPOK ALUR PROSEDUR ---
            [
                'nama_intent' => 'Info_Alur_Pertanahan',
                'konteks_jawaban' => 'Alur Pertanahan (AJB/Hibah/APHB): 1. Pengecekan Sertifikat di BPN (1-3 hari). 2. Pembayaran Pajak PPh/BPHTB (3-7 hari). 3. Tanda tangan akta di PPAT. 4. Balik Nama di BPN (14-30 hari kerja). Total estimasi 1-2 bulan.',
                'pola_pertanyaan' => [
                    'berapa lama proses balik nama sertifikat?',
                    'alur urus ajb tanah',
                    'proses bikin sertifikat memakan waktu berapa hari?'
                ]
            ],
            [
                'nama_intent' => 'Info_Alur_Badan_Usaha',
                'konteks_jawaban' => 'Alur Pendirian PT/CV/Yayasan: 1. Pengecekan Nama di sistem AHU (1 hari). 2. Pembuatan draf akta (1-3 hari). 3. Tanda tangan pendiri. 4. Pendaftaran akta untuk SK Kemenkumham (1-3 hari pasca tanda tangan). Total estimasi 3-7 hari kerja.',
                'pola_pertanyaan' => [
                    'berapa lama bikin pt?',
                    'proses pendirian cv berapa hari'
                ]
            ],

            // --- KELOMPOK BIAYA ---
            [
                'nama_intent' => 'Info_Biaya_AJB_APHB',
                'konteks_jawaban' => 'Biaya AJB dan balik nama terdiri dari komponen: Pajak Penjual (PPh 2.5%), Pajak Pembeli (BPHTB 5%), Biaya PNBP BPN, dan Honorarium PPAT (Maks 1% dari nilai transaksi). Untuk hitungan pasti, unggah foto sertifikat dan PBB Anda agar dihitung Staf.',
                'pola_pertanyaan' => [
                    'Berapa biaya AJB rumah seharga 500 juta?',
                    'biaya balik nama sertifikat berapa?',
                    'pajak ajb pembeli dan penjual'
                ]
            ],
            [
                'nama_intent' => 'Info_Biaya_Badan_Usaha',
                'konteks_jawaban' => 'Biaya pendirian badan usaha mencakup honor Notaris, PNBP pendaftaran nama di AHU Kemenkumham, dan biaya cetak lembaran negara. Bergantung pada Modal Dasar perusahaan. Silakan hubungi Staf kami untuk penawaran harga.',
                'pola_pertanyaan' => [
                    'Berapa biaya notaris bikin PT?',
                    'harganya brp bikin pt?',
                    'ppat lilis trima bikin pt ngga ya? biayanya brp kira2?',
                    'ongkos bikin pt cv brp?'
                ]
            ],

            // --- KELOMPOK KASUS KHUSUS (EDGE CASES) ---
            [
                'nama_intent' => 'Info_Edge_Sertifikat_Meninggal',
                'konteks_jawaban' => 'TIDAK BISA LANGSUNG AJB. Tanah warisan harus dibalik nama terlebih dahulu ke seluruh ahli waris sah menggunakan Surat Keterangan Waris. Setelah sertifikat berubah menjadi nama para ahli waris, baru bisa dibuatkan AJB.',
                'pola_pertanyaan' => [
                    'Sertifikatnya masih nama kakek saya yang sudah meninggal, mau saya jual langsung ke orang lain pakai AJB bisa?',
                    'jual tanah warisan tanpa balik nama',
                    'bisa gak ajb tanah tapi pemilik di sertifikat udah wafat?'
                ]
            ],
            [
                'nama_intent' => 'Info_Edge_Sengketa',
                'konteks_jawaban' => 'Dalam hukum perdata, penjualan objek waris memerlukan PERSETUJUAN MUTLAK dari 100% ahli waris. Jika ada satu saja yang menolak tanda tangan, PPAT dilarang membuat akta pengalihan. Kami sarankan mediasi keluarga terlebih dahulu.',
                'pola_pertanyaan' => [
                    'Ibu saya mau jual rumah waris, tapi kakak saya tidak mau tanda tangan karena minta bagian lebih besar.',
                    'ahli waris ga mau ttd ajb',
                    'gimana kalau ada keluarga yang gak setuju jual tanah waris'
                ]
            ],
            [
                'nama_intent' => 'Info_Edge_Yurisdiksi',
                'konteks_jawaban' => 'Untuk AJB (PPAT), yurisdiksi kerja dibatasi lokasi tanah. Jika tanah di luar wilayah kerja kami, akta harus dibuat oleh PPAT daerah tersebut. KECUALI untuk PPJB, Pendirian PT/CV, atau SKW (Notaris), kami berwenang untuk seluruh Indonesia selama tanda tangan di wilayah jabatan.',
                'pola_pertanyaan' => [
                    'Saya beli tanah di Bogor, tapi domisili saya di Cianjur. Bisa urus AJB di kantor Ibu Lilis?',
                    'bisa bikin ajb buat tanah di jakarta gak min?',
                    'wilayah kerja ppat cianjur'
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