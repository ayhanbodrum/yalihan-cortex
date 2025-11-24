@php($title = 'İlanlar')
<div class="p-4">
    <h2 class="text-lg font-semibold">{{ $title }}</h2>
    <div class="mt-3 flex gap-2 items-end">
        <div>
            <label class="text-sm">Kategori ID</label>
            <input id="k" type="number" class="border rounded px-2 py-1 w-32" />
        </div>
        <div>
            <label class="text-sm">Yayın Tipi ID</label>
            <input id="y" type="number" class="border rounded px-2 py-1 w-32" />
        </div>
        <div>
            <label class="text-sm">Durum</label>
            <input id="s" type="text" class="border rounded px-2 py-1 w-32" placeholder="Aktif/Pasif" />
        </div>
        <div>
            <label class="text-sm">Arama</label>
            <input id="q" type="text" class="border rounded px-2 py-1 w-48" placeholder="başlık" />
        </div>
        <div>
            <label class="text-sm">Min Fiyat</label>
            <input id="min" type="number" class="border rounded px-2 py-1 w-32" />
        </div>
        <div>
            <label class="text-sm">Max Fiyat</label>
            <input id="max" type="number" class="border rounded px-2 py-1 w-32" />
        </div>
        <div>
            <label class="text-sm">Sıralama</label>
            <select id="sort" class="border rounded px-2 py-1">
                <option value="id:desc">ID ↓</option>
                <option value="id:asc">ID ↑</option>
                <option value="fiyat:desc">Fiyat ↓</option>
                <option value="fiyat:asc">Fiyat ↑</option>
                <option value="created_at:desc">Oluşturma ↓</option>
                <option value="created_at:asc">Oluşturma ↑</option>
            </select>
        </div>
        <div>
            <label class="text-sm">Sayfa</label>
            <input id="p" type="number" value="1" class="border rounded px-2 py-1 w-20" />
        </div>
        <div>
            <label class="text-sm">Adet</label>
            <input id="pp" type="number" value="25" class="border rounded px-2 py-1 w-20" />
        </div>
        <button id="apply" class="border rounded px-3 py-1">Uygula</button>
        <a id="csv" href="#" class="border rounded px-3 py-1">CSV</a>
    </div>
    <div id="table" class="mt-4 border rounded">
        <div class="grid grid-cols-7 gap-2 px-3 py-2 font-medium">
            <div>ID</div>
            <div>Başlık</div>
            <div>Fiyat</div>
            <div>Durum</div>
            <div>Kategori</div>
            <div>Yayın Tipi</div>
            <div>Oluşturma</div>
        </div>
        <div id="body"></div>
        <div id="pager" class="flex gap-2 px-3 py-2 border-t" role="navigation" aria-label="Sayfalama">
            <button id="prev" class="border rounded px-2 py-1">Önceki</button>
            <button id="next" class="border rounded px-2 py-1">Sonraki</button>
            <span id="info" class="text-sm" role="status" aria-live="polite"></span>
            <span id="status" class="text-sm text-gray-600" role="status" aria-live="polite"></span>
        </div>
    </div>
    <script>
    (function(){
        var body = document.getElementById('body');
        var info = document.getElementById('info');
        function buildQuery(){
            var k = document.getElementById('k').value || '';
            var y = document.getElementById('y').value || '';
            var s = document.getElementById('s').value || '';
            var q = document.getElementById('q').value || '';
            var p = document.getElementById('p').value || '1';
            var pp = document.getElementById('pp').value || '25';
            var min = document.getElementById('min').value || '';
            var max = document.getElementById('max').value || '';
            var sort = document.getElementById('sort').value || '';
            var qs = [];
            if (k) qs.push('kategoriId='+encodeURIComponent(k));
            if (y) qs.push('yayinTipiId='+encodeURIComponent(y));
            if (s) qs.push('status='+encodeURIComponent(s));
            if (q) qs.push('q='+encodeURIComponent(q));
            if (p) qs.push('page='+encodeURIComponent(p));
            if (pp) qs.push('perPage='+encodeURIComponent(pp));
            if (min) qs.push('minFiyat='+encodeURIComponent(min));
            if (max) qs.push('maxFiyat='+encodeURIComponent(max));
            if (sort) qs.push('sort='+encodeURIComponent(sort));
            return qs;
        }
        function applyCsvLink(){
            var qs = buildQuery();
            document.getElementById('csv').href = '/admin/crm/ilanlar-export.csv' + (qs.length ? ('?'+qs.join('&')) : '');
        }
        function load(){
            var qs = buildQuery();
            var url = '/admin/crm/ilanlar' + (qs.length ? ('?'+qs.join('&')) : '');
            var status = document.getElementById('status');
            status.textContent = 'Yükleniyor...';
            var prevBtn = document.getElementById('prev');
            var nextBtn = document.getElementById('next');
            prevBtn.disabled = true; nextBtn.disabled = true;
            status.setAttribute('aria-busy','true');
            fetch(url).then(function(r){
                if (!r.ok) throw new Error('İstek başarısız: '+r.status);
                return r.json();
            }).then(function(j){
                var d = Array.isArray(j.data) ? j.data : [];
                body.innerHTML = d.map(function(x){
                    var det = '/admin/crm/ilan-detay/' + (x.id||'');
                    return '<div class="grid grid-cols-7 gap-2 px-3 py-2 border-t" role="row" tabindex="0">'
                        + '<div role="gridcell">'+(x.id||'')+'</div>'
                        + '<div role="gridcell"><a href="'+det+'" class="underline text-blue-600" aria-label="İlan detayı: '+(x.baslik||'')+' (ID '+(x.id||'')+')">'+(x.baslik||'')+'</a></div>'
                        + '<div role="gridcell">'+(x.fiyat||'')+' '+(x.para_birimi||'')+'</div>'
                        + '<div role="gridcell">'+(x.status||'')+'</div>'
                        + '<div role="gridcell">'+(x.kategori_id||'')+'</div>'
                        + '<div role="gridcell">'+(x.yayin_tipi_id||'')+'</div>'
                        + '<div role="gridcell">'+(x.created_at||'')+'</div>'
                        + '</div>';
                }).join('');
                var m = j.meta || {page:1, perPage:25, total: d.length, totalPages:1};
                info.textContent = 'Sayfa '+m.page+' / '+m.totalPages+' • Toplam '+m.total;
                status.textContent = d.length ? '' : 'Kayıt bulunamadı';
                status.setAttribute('aria-busy','false');
                prevBtn.disabled = m.page <= 1;
                nextBtn.disabled = m.page >= m.totalPages;
                prevBtn.setAttribute('aria-disabled', prevBtn.disabled ? 'true' : 'false');
                nextBtn.setAttribute('aria-disabled', nextBtn.disabled ? 'true' : 'false');
                prevBtn.tabIndex = prevBtn.disabled ? -1 : 0;
                nextBtn.tabIndex = nextBtn.disabled ? -1 : 0;
            }).catch(function(err){
                status.textContent = 'Hata: ' + (err && err.message ? err.message : 'İstek başarısız');
                status.setAttribute('aria-busy','false');
            });
            applyCsvLink();
        }
        var applyBtn = document.getElementById('apply');
        var debounceTimer = null;
        function scheduleLoad(){
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(load, 250);
        }
        applyBtn.addEventListener('click', load);
        ['k','y','s','q','min','max','sort','p','pp'].forEach(function(id){
            var el = document.getElementById(id);
            if (el) el.addEventListener('input', scheduleLoad);
        });
        document.getElementById('prev').addEventListener('click', function(){
            var p = document.getElementById('p');
            var v = parseInt(p.value||'1',10);
            if (v>1){ p.value = String(v-1); load(); }
        });
        document.getElementById('next').addEventListener('click', function(){
            var p = document.getElementById('p');
            var v = parseInt(p.value||'1',10);
            p.value = String(v+1); load();
        });
        load();
    })();
    </script>
</div>