<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::query()->delete();

        $services = [
            // LAYANAN NOTARIS
            [
                'name' => 'Akta Perjanjian Pengikatan Jual Beli (PPJB)',
                'type' => 'notaris',
                'is_property_related' => true,
                'processing_duration' => '21 Hari Kerja',
                'requirements' => "1. Dokumen Para Pihak (Penjual & Pembeli)
- Fotokopi KTP & Kartu Keluarga (KK)
- Fotokopi NPWP
- Fotokopi Surat Nikah (jika sudah menikah)

2. Dokumen Objek Jual Beli
- Fotokopi Sertifikat Tanah
- Fotokopi PBB 5 tahun terakhir beserta bukti lunasnya (STTS)
- Fotokopi Izin Mendirikan Bangunan (IMB) jika ada bangunan"
            ],
            [
                'name' => 'Akta Pendirian Dan Perubahan Perseroan Terbatas (PT)', 
                'type' => 'notaris', 
                'is_property_related' => false,
                'processing_duration' => '14 Hari Kerja', 
                'requirements' => "1. Data PT
- Nama PT (minimal 3 suku kata)
- Tempat dan Kedudukan PT
- Maksud dan Tujuan Usaha
- Struktur Permodalan (Modal Dasar & Modal Disetor)
- Struktur Kepengurusan (Direktur & Komisaris)

2. Dokumen Para Pendiri & Pengurus
- Fotokopi KTP & Kartu Keluarga (KK)
- Fotokopi NPWP pribadi"
            ],
            [
                'name' => 'Akta Yayasan', 
                'type' => 'notaris', 
                'is_property_related' => false,
                'processing_duration' => '14 Hari Kerja', 
                'requirements' => "1. Data Yayasan
- Nama Yayasan
- Tempat dan Kedudukan Yayasan
- Maksud dan Tujuan Yayasan di bidang sosial, keagamaan, atau kemanusiaan
- Jumlah kekayaan awal yang dipisahkan

2. Dokumen Pendiri, Pengurus, dan Pengawas
- Fotokopi KTP & NPWP (Pendiri, Ketua, Sekretaris, Bendahara, Pengawas)"
            ],
            [
                'name' => 'Akta Perseroan Komanditer (CV)', 
                'type' => 'notaris', 
                'is_property_related' => false,
                'processing_duration' => '7 Hari Kerja', 
                'requirements' => "1. Data CV
- Nama CV
- Tempat dan Kedudukan CV
- Maksud dan Tujuan Usaha

2. Dokumen Para Sekutu
- Fotokopi KTP & NPWP (Sekutu Aktif/Pengurus dan Sekutu Pasif/Komanditer)"
            ],
            [
                'name' => 'Surat Keterangan Waris', 
                'type' => 'notaris', 
                'is_property_related' => true,
                'processing_duration' => '3 Hari Kerja', 
                'requirements' => "1. Dokumen Pewaris (Almarhum/Almarhumah)
- Fotokopi KTP & Kartu Keluarga (KK)
- Fotokopi Akta Kematian
- Fotokopi Akta Perkawinan

2. Dokumen Ahli Waris
- Fotokopi KTP & Kartu Keluarga (KK) semua ahli waris
- Fotokopi Akta Kelahiran semua ahli waris

3. Dokumen Tambahan
- Surat Pengantar dari Kelurahan/Desa setempat"
            ],

            // LAYANAN PPAT
            [
                'name' => 'Akta Jual Beli (AJB)',
                'type' => 'ppat',
                'is_property_related' => true,
                'processing_duration' => '21 Hari Kerja',
                'requirements' => "1. Dokumen Para Pihak (Penjual & Pembeli)
- Fotokopi KTP & Kartu Keluarga (KK)
- Fotokopi NPWP
- Fotokopi Surat Nikah (jika sudah menikah)
- Surat Persetujuan Suami/Istri (jika diperlukan)

2. Dokumen Objek Tanah/Bangunan
- Sertifikat Tanah Asli
- PBB 5 tahun terakhir beserta bukti lunasnya (STTS)
- Izin Mendirikan Bangunan (IMB) Asli jika ada bangunan"
            ],
            [
                'name' => 'Akta Hibah', 
                'type' => 'ppat', 
                'is_property_related' => true,
                'processing_duration' => '14 Hari Kerja', 
                'requirements' => "1. Dokumen Para Pihak (Pemberi & Penerima Hibah)
- Fotokopi KTP & Kartu Keluarga (KK)
- Fotokopi NPWP Pemberi Hibah
- Fotokopi Surat Nikah (jika sudah menikah)

2. Dokumen Objek Hibah
- Sertifikat Tanah Asli
- PBB 5 tahun terakhir beserta bukti lunasnya (STTS)
- Izin Mendirikan Bangunan (IMB) Asli jika ada bangunan"
            ],
            [
                'name' => 'Akta Pembagian Hak Bersama (Termasuk Hak Waris)', 
                'type' => 'ppat', 
                'is_property_related' => true,
                'processing_duration' => '14 Hari Kerja', 
                'requirements' => "1. Dokumen Para Pihak
- Fotokopi KTP & Kartu Keluarga (KK) semua pemegang hak/ahli waris
- Fotokopi Surat Nikah (jika sudah menikah)

2. Dokumen Objek
- Sertifikat Tanah Asli
- PBB 5 tahun terakhir beserta bukti lunasnya (STTS)

3. Dokumen Pendukung
- Surat Keterangan Waris (jika pembagian waris)
- Dasar kepemilikan bersama (misal: AJB sebelumnya)"
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}