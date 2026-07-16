<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Layanan;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Layanan::create(['nama_layanan' => 'Akta Yayasan', 'tipe_layanan' => 'notaris']);
        Layanan::create(['nama_layanan' => 'Surat Keterangan Waris', 'tipe_layanan' => 'notaris']);
        Layanan::create(['nama_layanan' => 'Akta Perjanjian Pengikatan Jual Beli (PPJB)', 'tipe_layanan' => 'notaris']);
        Layanan::create(['nama_layanan' => 'Akta Perseroan Komanditer (CV)', 'tipe_layanan' => 'notaris']);
        Layanan::create(['nama_layanan' => 'Akta Pendirian dan Perubahan PT', 'tipe_layanan' => 'notaris']);

        Layanan::create(['nama_layanan' => 'Akta Hibah', 'tipe_layanan' => 'ppat']);
        Layanan::create(['nama_layanan' => 'Akta Jual Beli', 'tipe_layanan' => 'ppat']);
        Layanan::create(['nama_layanan' => 'Pembagian Hak Bersama (Termasuk Hak Waris)', 'tipe_layanan' => 'ppat']);
    }
}