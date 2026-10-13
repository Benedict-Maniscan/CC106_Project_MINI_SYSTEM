(() => {
    const sidebar = document.getElementById('sidebar'), overlay = document.getElementById('sidebarOverlay'), toggle = document.getElementById('menuToggle');
    const close = () => { sidebar?.classList.remove('open'); overlay?.classList.remove('show'); };
    toggle?.addEventListener('click', () => { sidebar?.classList.toggle('open'); overlay?.classList.toggle('show'); });
    overlay?.addEventListener('click', close);
    document.querySelectorAll('[data-confirm]').forEach(el => el.addEventListener('click', e => { if (!confirm(el.dataset.confirm || 'Are you sure?')) e.preventDefault(); }));
    document.querySelectorAll('[data-table-search]').forEach(input => input.addEventListener('input', () => { const table = document.querySelector(input.dataset.tableSearch); if (!table) return; const q = input.value.toLowerCase(); table.querySelectorAll('tbody tr').forEach(row => row.hidden = !row.innerText.toLowerCase().includes(q)); }));
})();