document.addEventListener('DOMContentLoaded', function() {
    // Add simple micro-interactions to the save button
    const submitBtn = document.querySelector('.aida-btn-submit');
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="dashicons dashicons-update dashicons-spin"></span> Saving...';
            this.style.opacity = '0.8';
        });
    }

    // Highight input when it has a value on load
    const apiKeyInput = document.getElementById('aida_api_key');
    if (apiKeyInput && apiKeyInput.value.trim() !== '') {
        apiKeyInput.style.borderColor = 'var(--aida-primary)';
    }

    if (apiKeyInput) {
        apiKeyInput.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.style.borderColor = 'var(--aida-primary)';
            } else {
                this.style.borderColor = '';
            }
        });
    }
});
