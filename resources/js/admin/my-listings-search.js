function $(sel) {
    return document.querySelector(sel);
}
function createRow(item) {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td class="px-6 py-4">${item.baslik || '-'}</td>
  <td class="px-6 py-4">${item.kategori || '-'}</td>
  <td class="px-6 py-4">${item.status || '-'}</td>
  <td class="px-6 py-4">${item.fiyat || '-'}</td>
  <td class="px-6 py-4">${item.goruntulenme || '-'}</td>
  <td class="px-6 py-4">${item.tarih || '-'}</td>
  <td class="px-6 py-4"><a href="${item.url || '#'}" class="text-blue-600">Görüntüle</a></td>`;
    return tr;
}
function normalizePhone(v) {
    return String(v || '').replace(/[^0-9]/g, '');
}
async function searchOwnerOrPhone(q, type) {
    const url = new URL('/api/ilanlar/search', window.location.origin);
    url.searchParams.set('q', q);
    if (type) url.searchParams.set('type', type);
    const res = await fetch(url.toString(), { headers: { Accept: 'application/json' } });
    const json = await res.json();
    const list = json && json.success && json.data ? json.data : json.results || [];
    return list.map((r) => ({
        baslik: r.baslik || r.text || '-',
        kategori: r.kategori || r.subtitle || '-',
        status: r.status || '-',
        fiyat: r.fiyat || '-',
        goruntulenme: r.goruntulenme || '-',
        tarih: r.created_at || '-',
        url: r.url || '#',
    }));
}
async function searchPortal(portal, id) {
    const url = new URL('/admin/ilanlar/by-portal', window.location.origin);
    url.searchParams.set('portal', portal);
    url.searchParams.set('id', id);
    const res = await fetch(url.toString(), { headers: { Accept: 'application/json' } });
    const json = await res.json();
    const d = json && json.success && json.data ? json.data : json;
    if (!d) return [];
    const item = {
        baslik: d.baslik || '-',
        kategori: d.kategori?.name || '-',
        status: d.status || '-',
        fiyat: d.fiyat ? String(d.fiyat) : '-',
        goruntulenme: d.goruntulenme || '-',
        tarih: d.created_at || '-',
        url: d.id ? `/admin/ilanlar/${d.id}` : '#',
    };
    return [item];
}
function render(list) {
    const tbody = document.getElementById('listings-table-body');
    if (!tbody) return;
    tbody.innerHTML = '';
    const frag = document.createDocumentFragment();
    list.forEach((i) => frag.appendChild(createRow(i)));
    tbody.appendChild(frag);
}
function init() {
    const form = document.getElementById('my-listings-search');
    const status = document.getElementById('my-listings-search-status');
    if (!form) return;
    status.setAttribute('role', 'status');
    status.setAttribute('aria-live', 'polite');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        status.textContent = 'Aranıyor…';
        const owner = form.querySelector('[name="owner"]');
        const phone = form.querySelector('[name="phone"]');
        const portal = form.querySelector('[name="portal"]');
        const portalId = form.querySelector('[name="portal_id"]');
        try {
            let results = [];
            if (portal && portal.value && portalId && portalId.value) {
                results = await searchPortal(portal.value, portalId.value);
            } else if (phone && phone.value) {
                const normalized = normalizePhone(phone.value);
                if (normalized.length < 10) {
                    status.textContent = 'Telefon numarası geçersiz';
                    return;
                }
                results = await searchOwnerOrPhone(normalized, 'phone');
            } else if (owner && owner.value) {
                results = await searchOwnerOrPhone(owner.value, 'owner');
            }
            render(results);
            status.textContent = 'Tamamlandı';
        } catch (err) {
            status.textContent = 'Hata: ' + (err && err.message ? err.message : 'Bilinmeyen hata');
        }
    });
}
document.addEventListener('DOMContentLoaded', init);
export default { init };
