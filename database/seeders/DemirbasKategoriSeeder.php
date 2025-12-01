<?php

namespace Database\Seeders;

use App\Models\DemirbasKategori;
use Illuminate\Database\Seeder;

class DemirbasKategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * ✅ Context7: Hiyerarşik demirbaş kategorileri oluştur
     */
    public function run(): void
    {
        // Ana Kategoriler (parent_id = null)
        $mutfak = DemirbasKategori::create([
            'name' => 'Mutfak',
            'slug' => 'mutfak',
            'icon' => '🍳',
            'description' => 'Mutfak demirbaşları',
            'parent_id' => null,
            'kategori_id' => null, // Tüm ilan kategorileri için geçerli
            'yayin_tipi_id' => null, // Tüm yayın tipleri için geçerli
            'display_order' => 1,
            'status' => true,
        ]);

        $banyo = DemirbasKategori::create([
            'name' => 'Banyo',
            'slug' => 'banyo',
            'icon' => '🚿',
            'description' => 'Banyo demirbaşları',
            'parent_id' => null,
            'kategori_id' => null,
            'yayin_tipi_id' => null,
            'display_order' => 2,
            'status' => true,
        ]);

        $salon = DemirbasKategori::create([
            'name' => 'Salon',
            'slug' => 'salon',
            'icon' => '🛋️',
            'description' => 'Salon demirbaşları',
            'parent_id' => null,
            'kategori_id' => null,
            'yayin_tipi_id' => null,
            'display_order' => 3,
            'status' => true,
        ]);

        $yatakOdasi = DemirbasKategori::create([
            'name' => 'Yatak Odası',
            'slug' => 'yatak-odasi',
            'icon' => '🛏️',
            'description' => 'Yatak odası demirbaşları',
            'parent_id' => null,
            'kategori_id' => null,
            'yayin_tipi_id' => null,
            'display_order' => 4,
            'status' => true,
        ]);

        $bahce = DemirbasKategori::create([
            'name' => 'Bahçe',
            'slug' => 'bahce',
            'icon' => '🌳',
            'description' => 'Bahçe demirbaşları',
            'parent_id' => null,
            'kategori_id' => null,
            'yayin_tipi_id' => null,
            'display_order' => 5,
            'status' => true,
        ]);

        // Alt Kategoriler (Mutfak altında)
        DemirbasKategori::create([
            'name' => 'Beyaz Eşyalar',
            'slug' => 'mutfak-beyaz-esyalar',
            'icon' => '❄️',
            'description' => 'Mutfak beyaz eşyaları',
            'parent_id' => $mutfak->id,
            'kategori_id' => null,
            'yayin_tipi_id' => null,
            'display_order' => 1,
            'status' => true,
        ]);

        DemirbasKategori::create([
            'name' => 'Küçük Ev Aletleri',
            'slug' => 'mutfak-kucuk-ev-aletleri',
            'icon' => '🔌',
            'description' => 'Mutfak küçük ev aletleri',
            'parent_id' => $mutfak->id,
            'kategori_id' => null,
            'yayin_tipi_id' => null,
            'display_order' => 2,
            'status' => true,
        ]);

        // Alt Kategoriler (Banyo altında)
        DemirbasKategori::create([
            'name' => 'Banyo Aksesuarları',
            'slug' => 'banyo-aksesuarlari',
            'icon' => '🧴',
            'description' => 'Banyo aksesuarları',
            'parent_id' => $banyo->id,
            'kategori_id' => null,
            'yayin_tipi_id' => null,
            'display_order' => 1,
            'status' => true,
        ]);

        // Alt Kategoriler (Salon altında)
        DemirbasKategori::create([
            'name' => 'Oturma Grubu',
            'slug' => 'salon-oturma-grubu',
            'icon' => '🪑',
            'description' => 'Salon oturma grubu',
            'parent_id' => $salon->id,
            'kategori_id' => null,
            'yayin_tipi_id' => null,
            'display_order' => 1,
            'status' => true,
        ]);

        DemirbasKategori::create([
            'name' => 'TV ve Elektronik',
            'slug' => 'salon-tv-elektronik',
            'icon' => '📺',
            'description' => 'TV ve elektronik eşyalar',
            'parent_id' => $salon->id,
            'kategori_id' => null,
            'yayin_tipi_id' => null,
            'display_order' => 2,
            'status' => true,
        ]);

        // Alt Kategoriler (Yatak Odası altında)
        DemirbasKategori::create([
            'name' => 'Yatak ve Yatak Takımları',
            'slug' => 'yatak-odasi-yatak-takimlari',
            'icon' => '🛌',
            'description' => 'Yatak ve yatak takımları',
            'parent_id' => $yatakOdasi->id,
            'kategori_id' => null,
            'yayin_tipi_id' => null,
            'display_order' => 1,
            'status' => true,
        ]);

        DemirbasKategori::create([
            'name' => 'Dolap ve Depolama',
            'slug' => 'yatak-odasi-dolap-depolama',
            'icon' => '🚪',
            'description' => 'Dolap ve depolama üniteleri',
            'parent_id' => $yatakOdasi->id,
            'kategori_id' => null,
            'yayin_tipi_id' => null,
            'display_order' => 2,
            'status' => true,
        ]);

        // Alt Kategoriler (Bahçe altında)
        DemirbasKategori::create([
            'name' => 'Bahçe Mobilyaları',
            'slug' => 'bahce-mobilyalari',
            'icon' => '🪑',
            'description' => 'Bahçe mobilyaları',
            'parent_id' => $bahce->id,
            'kategori_id' => null,
            'yayin_tipi_id' => null,
            'display_order' => 1,
            'status' => true,
        ]);

        DemirbasKategori::create([
            'name' => 'Bahçe Ekipmanları',
            'slug' => 'bahce-ekipmanlari',
            'icon' => '🌿',
            'description' => 'Bahçe ekipmanları',
            'parent_id' => $bahce->id,
            'kategori_id' => null,
            'yayin_tipi_id' => null,
            'display_order' => 2,
            'status' => true,
        ]);

        $this->command->info('✅ Demirbaş kategorileri oluşturuldu: '.DemirbasKategori::count().' adet');
    }
}
