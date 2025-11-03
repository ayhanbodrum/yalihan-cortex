<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YayinTipleriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Yayın Tipleri (Kategori Bazlı) - Context7 uyumlu
        $yayinTipleri = [
            // Konut Kategorisi (ID: 1)
            ['kategori_id' => 1, 'yayin_tipi' => 'Satılık', 'status' => 'Aktif', 'order' => 1],
            ['kategori_id' => 1, 'yayin_tipi' => 'Kiralık', 'status' => 'Aktif', 'order' => 2],
            ['kategori_id' => 1, 'yayin_tipi' => 'Devren Satılık', 'status' => 'Aktif', 'order' => 3],
            ['kategori_id' => 1, 'yayin_tipi' => 'Günlük Kiralık', 'status' => 'Aktif', 'order' => 4],

            // İşyeri Kategorisi (ID: 2)
            ['kategori_id' => 2, 'yayin_tipi' => 'Satılık', 'status' => 'Aktif', 'order' => 1],
            ['kategori_id' => 2, 'yayin_tipi' => 'Kiralık', 'status' => 'Aktif', 'order' => 2],
            ['kategori_id' => 2, 'yayin_tipi' => 'Devren Satılık', 'status' => 'Aktif', 'order' => 3],
            ['kategori_id' => 2, 'yayin_tipi' => 'Devren Kiralık', 'status' => 'Aktif', 'order' => 4],

            // Arsa Kategorisi (ID: 3)
            ['kategori_id' => 3, 'yayin_tipi' => 'Satılık', 'status' => 'Aktif', 'order' => 1],
            ['kategori_id' => 3, 'yayin_tipi' => 'Kiralık', 'status' => 'Aktif', 'order' => 2],

            // Bina Kategorisi (ID: 4)
            ['kategori_id' => 4, 'yayin_tipi' => 'Satılık', 'status' => 'Aktif', 'order' => 1],
            ['kategori_id' => 4, 'yayin_tipi' => 'Kiralık', 'status' => 'Aktif', 'order' => 2],
        ];

        DB::table('ilan_kategori_yayin_tipleri')->insert($yayinTipleri);
    }
}

