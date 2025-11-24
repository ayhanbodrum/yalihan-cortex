@php($title = 'İlan Detay')
<div class="p-4">
    <h2 class="text-lg font-semibold">{{ $title }}</h2>
    <div id="summary" class="mt-3 border rounded p-3"></div>
    <div class="mt-4">
        <h3 class="font-semibold">Fotoğraflar</h3>
        <div id="photos" class="grid grid-cols-4 gap-2 mt-2"></div>
    </div>
    <div class="mt-4">
        <h3 class="font-semibold">Etkinlikler</h3>
        <div id="events" class="mt-2 border rounded"></div>
    </div>
    <div class="mt-4">
        <h3 class="font-semibold">Belgeler</h3>
        <div id="docs" class="mt-2 border rounded"></div>
    </div>
    <script>
    (function(){
        var path = location.pathname;
        var id = (path.match(/ilan-detay\/(\d+)/) || [])[1] || '';
        var sum = document.getElementById('summary');
        var photos = document.getElementById('photos');
        var events = document.getElementById('events');
        function loadSummary(){
            fetch('/admin/crm/ilanlar?id='+encodeURIComponent(id))
                .then(function(r){ if(!r.ok) throw new Error(''+r.status); return r.json(); })
                .then(function(j){
                    var x = (j.data||[])[0] || {};
                    var admin = '/admin/ilanlar/' + (x.id||'');
                    sum.innerHTML = '<div class="grid grid-cols-3 gap-2">'
                        + '<div><div class="text-sm">Başlık</div><div class="font-medium">'+(x.baslik||'')+'</div></div>'
                        + '<div><div class="text-sm">Fiyat</div><div class="font-medium">'+(x.fiyat||'')+' '+(x.para_birimi||'')+'</div></div>'
                        + '<div><div class="text-sm">Durum</div><div class="font-medium">'+(x.status||'')+'</div></div>'
                        + '<div><div class="text-sm">Kategori</div><div class="font-medium">'+(x.kategori_id||'')+'</div></div>'
                        + '<div><div class="text-sm">Yayın Tipi</div><div class="font-medium">'+(x.yayin_tipi_id||'')+'</div></div>'
                        + '<div><div class="text-sm">Oluşturma</div><div class="font-medium">'+(x.created_at||'')+'</div></div>'
                        + '<div class="col-span-3 mt-2"><a href="'+admin+'" class="underline text-blue-600">Admin Detayına Git</a></div>'
                        + '</div>';
                }).catch(function(e){ sum.textContent = 'Özet yüklenemedi: '+e.message; });
        }
        function loadPhotos(){
            fetch('/api/admin/ilanlar/'+encodeURIComponent(id)+'/photos')
                .then(function(r){ if(!r.ok) throw new Error(''+r.status); return r.json(); })
                .then(function(j){
                    var d = Array.isArray(j.data) ? j.data : [];
                    photos.innerHTML = d.map(function(p){
                        var src = p.url || p.path || '';
                        return '<div class="border rounded overflow-hidden"><img src="'+src+'" class="w-full h-32 object-cover" /></div>';
                    }).join('');
                }).catch(function(e){ photos.textContent = 'Fotoğraflar yüklenemedi: '+e.message; });
        }
        function loadEvents(){
            fetch('/api/admin/ilanlar/'+encodeURIComponent(id)+'/events')
                .then(function(r){ if(!r.ok) throw new Error(''+r.status); return r.json(); })
                .then(function(j){
                    var d = Array.isArray(j.data) ? j.data : [];
                    events.innerHTML = d.map(function(ev){
                        return '<div class="px-3 py-2 border-t">'
                            + '<div class="text-sm">'+(ev.type||'')+'</div>'
                            + '<div class="text-xs text-gray-600">'+(ev.timestamp||'')+'</div>'
                            + '</div>';
                    }).join('');
                }).catch(function(e){ events.textContent = 'Etkinlikler yüklenemedi: '+e.message; });
        }
        function loadDocs(){
            fetch('/api/admin/ilanlar/'+encodeURIComponent(id)+'/documents')
                .then(function(r){ if(!r.ok) throw new Error(''+r.status); return r.json(); })
                .then(function(j){
                    var d = Array.isArray(j.data) ? j.data : [];
                    docs.innerHTML = d.length ? d.map(function(dc){
                        var link = dc.url || dc.download_url || '';
                        var a = link ? ('<a href="'+link+'" target="_blank" class="underline text-blue-600">Aç</a>') : '-';
                        return '<div class="px-3 py-2 border-t">'
                            + '<div class="text-sm">'+(dc.title||'')+'</div>'
                            + '<div class="text-xs text-gray-600">'+(dc.type||'')+' • '+(dc.created_at||'')+' • '+a+'</div>'
                            + '</div>';
                    }).join('') : '<div class="px-3 py-2">Belge bulunamadı</div>';
                }).catch(function(e){ docs.textContent = 'Belgeler yüklenemedi: '+e.message; });
        }
        loadSummary();
        loadPhotos();
        loadEvents();
        loadDocs();
    })();
    </script>
</div>