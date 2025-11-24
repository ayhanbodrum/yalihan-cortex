@php($title = 'Yayın Tipi Değişimleri')
<div class="p-4">
    <h2 class="text-lg font-semibold">{{ $title }}</h2>
    <div class="mt-3 flex gap-2 items-end">
        <div>
            <label class="text-sm">Kategori ID</label>
            <input id="f-kategori" type="number" class="border rounded px-2 py-1 w-32" />
        </div>
        <div>
            <label class="text-sm">Başlangıç</label>
            <input id="f-from" type="date" class="border rounded px-2 py-1" />
        </div>
        <div>
            <label class="text-sm">Bitiş</label>
            <input id="f-to" type="date" class="border rounded px-2 py-1" />
        </div>
        <button id="f-apply" class="border rounded px-3 py-1">Uygula</button>
    </div>
    <div id="changes-table" class="mt-4 border rounded">
        <div class="grid grid-cols-6 gap-2 px-3 py-2 font-medium">
            <div>Tür</div>
            <div>Kategori</div>
            <div>Kaynak</div>
            <div>Hedef</div>
            <div>Tarih</div>
            <div>Bağlantı</div>
        </div>
        <div id="changes-body"></div>
        <div id="changes-status" class="px-3 py-2 text-sm text-gray-600"></div>
    </div>
    <script>
    (function(){
        var body = document.getElementById('changes-body');
        function load(){
            var k = document.getElementById('f-kategori').value || '';
            var from = document.getElementById('f-from').value || '';
            var to = document.getElementById('f-to').value || '';
            var q = [];
            if (k) q.push('kategoriId='+encodeURIComponent(k));
            if (from) q.push('from='+encodeURIComponent(from));
            if (to) q.push('to='+encodeURIComponent(to));
            var url = '/admin/crm/publication-type-changes' + (q.length ? ('?'+q.join('&')) : '');
            var status = document.getElementById('changes-status');
            status.textContent = 'Yükleniyor...';
            fetch(url).then(function(r){
                if (!r.ok) throw new Error('İstek başarısız: '+r.status);
                return r.json();
            }).then(function(j){
                var d = Array.isArray(j.data) ? j.data : [];
                body.innerHTML = d.map(function(x){
                    var t = x.type || '';
                    var k = x.kategori_id || '';
                    var f = x.from_id || x.deleted_id || '';
                    var to = x.to_id || '';
                    var ts = x.ts || '';
                    var link = '/admin/property-type-manager/' + k;
                    var csvLink = '/admin/crm/ilanlar-export.csv' + (q.length ? ('?'+q.join('&')) : '');
                    return '<div class="grid grid-cols-6 gap-2 px-3 py-2 border-t">'
                        + '<div>'+t+'</div>'
                        + '<div>'+k+'</div>'
                        + '<div>'+f+'</div>'
                        + '<div>'+to+'</div>'
                        + '<div>'+ts+'</div>'
                        + '<div class="flex gap-2"><a href="'+link+'" class="text-blue-600 underline">Yönetici</a><a href="'+csvLink+'" class="text-green-700 underline">CSV İlanlar</a></div>'
                        + '</div>';
                }).join('');
                status.textContent = d.length ? ('Toplam '+d.length+' kayıt') : 'Kayıt bulunamadı';
            }).catch(function(err){
                status.textContent = 'Hata: '+err.message;
            });
        }
        document.getElementById('f-apply').addEventListener('click', load);
        load();
    })();
    </script>
</div>