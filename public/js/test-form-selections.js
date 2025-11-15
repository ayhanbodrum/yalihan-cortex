/**
 * Test Script: Kategori ve Adres Seçimleri
 * 
 * Kullanım: Tarayıcı konsolunda çalıştırın:
 * 
 * // Kategori seçimi
 * testKategoriSeçimi();
 * 
 * // Adres seçimi
 * testAdresSeçimi();
 * 
 * // Hepsini test et
 * testAll();
 */

// Kategori seçimi testi
async function testKategoriSeçimi() {
    console.log('🎯 Kategori seçimi testi başlıyor...');
    
    // 1. Ana Kategori seçimi
    const anaKategori = document.getElementById('ana_kategori');
    if (!anaKategori || anaKategori.options.length < 2) {
        console.error('❌ Ana kategori dropdown bulunamadı veya boş');
        return;
    }
    
    // İlk kategoriyi seç (boş değilse)
    const firstKategoriId = anaKategori.options[1]?.value;
    if (!firstKategoriId) {
        console.error('❌ Ana kategori seçeneği bulunamadı');
        return;
    }
    
    console.log('✅ Ana kategori seçiliyor:', anaKategori.options[1].text, '(ID:', firstKategoriId, ')');
    anaKategori.value = firstKategoriId;
    anaKategori.dispatchEvent(new Event('change', { bubbles: true }));
    
    // Alt kategorilerin yüklenmesini bekle
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // 2. Alt Kategori seçimi
    const altKategori = document.getElementById('alt_kategori');
    if (!altKategori || altKategori.options.length < 2) {
        console.error('❌ Alt kategori dropdown bulunamadı veya boş');
        return;
    }
    
    const firstAltKategoriId = altKategori.options[1]?.value;
    if (!firstAltKategoriId) {
        console.error('❌ Alt kategori seçeneği bulunamadı');
        return;
    }
    
    console.log('✅ Alt kategori seçiliyor:', altKategori.options[1].text, '(ID:', firstAltKategoriId, ')');
    altKategori.value = firstAltKategoriId;
    altKategori.dispatchEvent(new Event('change', { bubbles: true }));
    
    // Yayın tiplerinin yüklenmesini bekle
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // 3. Yayın Tipi seçimi
    const yayinTipi = document.getElementById('yayin_tipi_id');
    if (!yayinTipi || yayinTipi.options.length < 2) {
        console.error('❌ Yayın tipi dropdown bulunamadı veya boş');
        return;
    }
    
    const firstYayinTipiId = yayinTipi.options[1]?.value;
    if (!firstYayinTipiId) {
        console.error('❌ Yayın tipi seçeneği bulunamadı');
        return;
    }
    
    console.log('✅ Yayın tipi seçiliyor:', yayinTipi.options[1].text, '(ID:', firstYayinTipiId, ')');
    yayinTipi.value = firstYayinTipiId;
    yayinTipi.dispatchEvent(new Event('change', { bubbles: true }));
    
    console.log('✅ Kategori seçimi tamamlandı!');
    console.log('📊 Seçilen değerler:', {
        anaKategori: anaKategori.value,
        altKategori: altKategori.value,
        yayinTipi: yayinTipi.value
    });
}

// Adres seçimi testi
async function testAdresSeçimi() {
    console.log('📍 Adres seçimi testi başlıyor...');
    
    // 1. İl seçimi
    const ilSelect = document.getElementById('il_id');
    if (!ilSelect || ilSelect.options.length < 2) {
        console.error('❌ İl dropdown bulunamadı veya boş');
        return;
    }
    
    const firstIlId = ilSelect.options[1]?.value;
    if (!firstIlId) {
        console.error('❌ İl seçeneği bulunamadı');
        return;
    }
    
    console.log('✅ İl seçiliyor:', ilSelect.options[1].text, '(ID:', firstIlId, ')');
    ilSelect.value = firstIlId;
    ilSelect.dispatchEvent(new Event('change', { bubbles: true }));
    
    // İlçelerin yüklenmesini bekle
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // 2. İlçe seçimi
    const ilceSelect = document.getElementById('ilce_id');
    if (!ilceSelect || ilceSelect.options.length < 2) {
        console.error('❌ İlçe dropdown bulunamadı veya boş');
        return;
    }
    
    const firstIlceId = ilceSelect.options[1]?.value;
    if (!firstIlceId) {
        console.error('❌ İlçe seçeneği bulunamadı');
        return;
    }
    
    console.log('✅ İlçe seçiliyor:', ilceSelect.options[1].text, '(ID:', firstIlceId, ')');
    ilceSelect.value = firstIlceId;
    ilceSelect.dispatchEvent(new Event('change', { bubbles: true }));
    
    // Mahallelerin yüklenmesini bekle
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // 3. Mahalle seçimi
    const mahalleSelect = document.getElementById('mahalle_id');
    if (!mahalleSelect || mahalleSelect.options.length < 2) {
        console.error('❌ Mahalle dropdown bulunamadı veya boş');
        return;
    }
    
    const firstMahalleId = mahalleSelect.options[1]?.value;
    if (!firstMahalleId) {
        console.error('❌ Mahalle seçeneği bulunamadı');
        return;
    }
    
    console.log('✅ Mahalle seçiliyor:', mahalleSelect.options[1].text, '(ID:', firstMahalleId, ')');
    mahalleSelect.value = firstMahalleId;
    mahalleSelect.dispatchEvent(new Event('change', { bubbles: true }));
    
    console.log('✅ Adres seçimi tamamlandı!');
    console.log('📊 Seçilen değerler:', {
        il: ilSelect.value,
        ilce: ilceSelect.value,
        mahalle: mahalleSelect.value
    });
}

// Tüm seçimleri test et
async function testAll() {
    console.log('🚀 Tüm seçimler test ediliyor...');
    
    try {
        await testKategoriSeçimi();
        await new Promise(resolve => setTimeout(resolve, 1000));
        await testAdresSeçimi();
        
        console.log('✅ Tüm testler tamamlandı!');
    } catch (error) {
        console.error('❌ Test hatası:', error);
    }
}

// Global scope'a ekle
window.testKategoriSeçimi = testKategoriSeçimi;
window.testAdresSeçimi = testAdresSeçimi;
window.testAll = testAll;

console.log('✅ Test script yüklendi! Kullanım:');
console.log('  - testKategoriSeçimi() - Kategori seçimlerini test et');
console.log('  - testAdresSeçimi() - Adres seçimlerini test et');
console.log('  - testAll() - Hepsini test et');

