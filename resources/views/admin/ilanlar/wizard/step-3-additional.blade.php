{{-- STEP 3: EK BİLGİLER --}}
<div class="space-y-6">
    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">📋 Ek Bilgiler</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400">İlanınızı zenginleştirin ve yayınlayın</p>
    </div>

    {{-- Açıklama --}}
    <div>
        <label for="aciklama" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
            Açıklama <span class="text-red-500">*</span>
        </label>
        <div class="space-y-2">
            <textarea name="aciklama" id="aciklama" required rows="8" placeholder="İlanınızın detaylı açıklamasını yazın..."
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                             bg-white dark:bg-gray-800 text-black dark:text-white
                             focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                             transition-all duration-200">{{ old('aciklama') }}</textarea>
            <button type="button" onclick="generateDescriptionWithAI()"
                class="px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700
                           hover:scale-105 active:scale-95 transition-all duration-200 font-medium text-sm">
                🤖 AI ile Açıklama Üret (TKGM + POI kullanarak)
            </button>
        </div>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Minimum 50 karakter. AI ile otomatik açıklama üretebilirsiniz.
        </p>
    </div>

    {{-- Fotoğraflar --}}
    <div>
        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
            Fotoğraflar
        </label>
        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
            <input type="file" name="fotograflar[]" id="fotograflar" multiple accept="image/*" class="hidden"
                onchange="previewPhotos(this)">
            <label for="fotograflar"
                class="cursor-pointer inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg
                          hover:bg-blue-700 hover:scale-105 active:scale-95 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Fotoğraf Seç
            </label>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Birden fazla fotoğraf seçebilirsiniz (Minimum 1 fotoğraf önerilir)
            </p>
            <div id="photo-preview" class="mt-4 hidden grid grid-cols-4 gap-4"></div>
        </div>
    </div>

    {{-- İlan Sahibi --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div>
            <label for="ilan_sahibi_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                İlan Sahibi <span class="text-red-500">*</span>
            </label>
            <select name="ilan_sahibi_id" id="ilan_sahibi_id" required
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-800 text-black dark:text-white
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           transition-all duration-200">
                <option value="">Kişi Seçin</option>
                @foreach ($kisiler ?? [] as $kisi)
                    <option value="{{ $kisi->id }}" {{ old('ilan_sahibi_id') == $kisi->id ? 'selected' : '' }}>
                        {{ $kisi->ad }} {{ $kisi->soyad }} - {{ $kisi->telefon }}
                    </option>
                @endforeach
            </select>
            <button type="button" onclick="openNewPersonModal()"
                class="mt-2 text-sm text-blue-600 dark:text-blue-400 hover:underline">
                + Yeni Kişi Ekle
            </button>
        </div>

        <div>
            <label for="danisman_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                Danışman
            </label>
            <select name="danisman_id" id="danisman_id"
                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                           bg-white dark:bg-gray-800 text-black dark:text-white
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           transition-all duration-200">
                <option value="">Danışman Seçin (Opsiyonel)</option>
                @foreach ($danismanlar ?? [] as $danisman)
                    <option value="{{ $danisman->id }}"
                        {{ old('danisman_id', auth()->id()) == $danisman->id ? 'selected' : '' }}>
                        {{ $danisman->name }} - {{ $danisman->email }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Site/Apartman (Opsiyonel) --}}
    <div>
        <label for="site_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
            Site/Apartman (Opsiyonel)
        </label>
        <select name="site_id" id="site_id"
            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg
                       bg-white dark:bg-gray-800 text-black dark:text-white
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                       transition-all duration-200">
            <option value="">Site/Apartman Seçin (Opsiyonel)</option>
            @if (isset($sites))
                @foreach ($sites as $site)
                    <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                        {{ $site->name }}
                    </option>
                @endforeach
            @endif
        </select>
        <button type="button" onclick="openNewSiteModal()"
            class="mt-2 text-sm text-blue-600 dark:text-blue-400 hover:underline">
            + Yeni Site/Apartman Ekle
        </button>
    </div>

    {{-- Yayın Durumu --}}
    <div>
        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
            Yayın Durumu <span class="text-red-500">*</span>
        </label>
        <div class="flex gap-4">
            <label class="flex items-center">
                <input type="radio" name="status" value="Taslak"
                    {{ old('status', 'Taslak') == 'Taslak' ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Taslak</span>
            </label>
            <label class="flex items-center">
                <input type="radio" name="status" value="Aktif" {{ old('status') == 'Aktif' ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
            </label>
            <label class="flex items-center">
                <input type="radio" name="status" value="Pasif" {{ old('status') == 'Pasif' ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Pasif</span>
            </label>
        </div>
    </div>
</div>

<script>
    // Photo Preview
    function previewPhotos(input) {
        const preview = document.getElementById('photo-preview');
        preview.innerHTML = '';
        preview.classList.remove('hidden');

        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}"
                         class="w-full h-24 object-cover rounded-lg">
                    <button type="button" onclick="removePhoto(${index})"
                            class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">
                        ×
                    </button>
                `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    // AI Description Generation
    async function generateDescriptionWithAI() {
        const baslik = document.getElementById('baslik')?.value;
        const altKategoriId = document.getElementById('alt_kategori_id')?.value;
        const ilId = document.getElementById('il_id')?.value;
        const ilceId = document.getElementById('ilce_id')?.value;
        const altKategori = document.getElementById('alt_kategori_id')?.selectedOptions[0]?.text;
        const il = document.getElementById('il_id')?.selectedOptions[0]?.text;
        const ilce = document.getElementById('ilce_id')?.selectedOptions[0]?.text;
        const adaNo = document.getElementById('ada_no')?.value;
        const parselNo = document.getElementById('parsel_no')?.value;
        const fiyat = document.getElementById('fiyat')?.value;
        const paraBirimi = document.getElementById('para_birimi')?.value;

        if (!baslik || !altKategoriId || !ilId || !ilceId) {
            alert('Lütfen önce temel bilgileri doldurun');
            return;
        }

        const aciklamaTextarea = document.getElementById('aciklama');
        if (!aciklamaTextarea) return;

        // Loading state
        const originalValue = aciklamaTextarea.value;
        aciklamaTextarea.disabled = true;
        aciklamaTextarea.value = 'AI açıklama üretiliyor... (TKGM ve POI verileri kullanılıyor)';
        aciklamaTextarea.classList.add('opacity-50');

        try {
            const response = await fetch('/admin/ilanlar/generate-ai-description', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    context: {
                        baslik: baslik,
                        kategori: altKategori,
                        il: il,
                        ilce: ilce,
                        adaNo: adaNo,
                        parselNo: parselNo,
                        fiyat: fiyat,
                        paraBirimi: paraBirimi,
                    },
                    kategori_id: altKategoriId,
                    il_id: ilId,
                    ilce_id: ilceId,
                    baslik: baslik,
                    ada_no: adaNo,
                    parsel_no: parselNo,
                }),
            });

            const result = await response.json();

            if (result.success && result.description) {
                aciklamaTextarea.value = result.description;
                aciklamaTextarea.classList.remove('opacity-50');
                aciklamaTextarea.classList.add('border-green-500');

                // Success animation
                setTimeout(() => {
                    aciklamaTextarea.classList.remove('border-green-500');
                }, 2000);
            } else {
                aciklamaTextarea.value = originalValue;
                alert('Açıklama üretilemedi: ' + (result.message || 'Bilinmeyen hata'));
            }
        } catch (error) {
            console.error('AI açıklama üretimi hatası:', error);
            aciklamaTextarea.value = originalValue;
            alert('AI açıklama üretimi sırasında hata oluştu');
        } finally {
            aciklamaTextarea.disabled = false;
            aciklamaTextarea.classList.remove('opacity-50');
        }
    }

    // Modal Functions
    function openNewPersonModal() {
        // TODO: Yeni kişi ekleme modalı
        console.log('Yeni kişi ekleme modalı açılıyor...');
    }

    function openNewSiteModal() {
        // TODO: Yeni site ekleme modalı
        console.log('Yeni site ekleme modalı açılıyor...');
    }
</script>
