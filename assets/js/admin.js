/**
 * Admin JavaScript
 * SMK INFOKOM BOGOR
 */

document.addEventListener('DOMContentLoaded', function() {

    // ===== SIDEBAR TOGGLE (Mobile) =====
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('adminOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (toggleBtn && sidebar && overlay) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        });
    }

    // ===== AUTO DISMISS ALERTS =====
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // ===== CONFIRM DELETE =====
    document.querySelectorAll('[data-confirm]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const message = this.dataset.confirm || 'Apakah Anda yakin ingin menghapus data ini?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ===== IMAGE PREVIEW =====
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        input.addEventListener('change', function() {
            const previewId = this.dataset.preview;
            const previewEl = document.getElementById(previewId);
            if (previewEl && this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewEl.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    previewEl.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // ===== STATUS CHANGE MODAL =====
    window.openStatusModal = function(regId, currentStatus) {
        const modal = document.getElementById('statusModal');
        const select = document.getElementById('statusSelect');
        const regIdInput = document.getElementById('statusRegId');

        if (modal && select && regIdInput) {
            select.value = currentStatus;
            regIdInput.value = regId;
            modal.classList.add('open');
        }
    };

    window.closeStatusModal = function() {
        const modal = document.getElementById('statusModal');
        if (modal) modal.classList.remove('open');
    };

    // Close modal on overlay click
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('open');
            }
        });
    });

    // ===== SEARCH TABLE (Client-side) =====
    const searchInputs = document.querySelectorAll('[data-search-table]');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const tableId = this.dataset.searchTable;
            const table = document.getElementById(tableId);
            if (!table) return;

            const query = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    });

    // ===== CHARACTER COUNTER FOR TEXTAREA =====
    document.querySelectorAll('textarea[data-maxlength]').forEach(textarea => {
        const max = parseInt(textarea.dataset.maxlength);
        const counter = document.createElement('small');
        counter.style.color = '#9ca3af';
        counter.style.marginTop = '4px';
        counter.style.display = 'block';
        textarea.parentNode.appendChild(counter);

        function updateCounter() {
            const remaining = max - textarea.value.length;
            counter.textContent = remaining + ' karakter tersisa';
            counter.style.color = remaining < 50 ? '#dc2626' : '#9ca3af';
        }

        textarea.addEventListener('input', updateCounter);
        updateCounter();
    });
});