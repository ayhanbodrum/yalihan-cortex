document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[action*="/portal-ids"]');
    if (!form) return;
    const status = document.createElement('div');
    status.className = 'text-sm text-gray-600 dark:text-gray-300 mt-2';
    status.setAttribute('role', 'status');
    status.setAttribute('aria-live', 'polite');
    form.appendChild(status);
    const sah = form.querySelector('input[name="sahibinden_id"]');
    function validate() {
        const v = ((sah && sah.value) || '').trim();
        if (!v) {
            status.textContent = '';
            return;
        }
        const ok = /^[0-9-]{6,}$/.test(v);
        status.textContent = ok
            ? 'Sahibinden ID formatı uygun'
            : 'Sahibinden ID formatı geçersiz (örn: 163868-6)';
    }
    if (sah) {
        sah.addEventListener('input', validate);
        validate();
    }
});
export default {};
