<!-- Basit Lokasyon Seçimi - Sadece İl-İlçe-Mahalle -->
<x-location-selector-simple
    :selected-province="$ilan->il_id ?? old('il_id') ?? ''"
    :selected-district="$ilan->ilce_id ?? old('ilce_id') ?? ''"
    :selected-neighborhood="$ilan->mahalle_id ?? old('mahalle_id') ?? ''"
    :required="true"
    :show-neighborhood="true"
    grid-cols="grid-cols-1 md:grid-cols-3"
    name-prefix=""
    class="mb-6" />
