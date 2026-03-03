document.addEventListener('DOMContentLoaded', function() {
    // Add simple micro-interactions to the save button
    const submitBtn = document.querySelector('.aida-btn-submit');
    if (submitBtn) {
        submitBtn.addEventListener('click', function() {
        if (!submitBtn.classList.contains('loading')) {
            const originalText = submitBtn.innerHTML;
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<span class="dashicons dashicons-update spin"></span> در حال ذخیره...';
            
            setTimeout(() => {
                submitBtn.classList.remove('loading');
                submitBtn.innerHTML = '<span class="dashicons dashicons-yes"></span> ذخیره شد!';
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                }, 2000);
            }, 1000);
        }
    });
    }

    // Highight input when it has a value on load
    const apiKeyInput = document.getElementById('aida_api_key');
    if (apiKeyInput && apiKeyInput.value.trim() !== '') {
        apiKeyInput.style.borderColor = 'var(--aida-primary)';
    }

    // Live Preview Logic
    const positionSelect = document.getElementById('aida_position');
    const stateRadios = document.querySelectorAll('input[name="aida_initial_state"]');
    const previewChat = document.querySelector('.aida-preview-chat');

    function updatePreview() {
        if (!previewChat) return;
        
        // Position
        if (positionSelect.value === 'left') {
            previewChat.classList.add('pos-left');
            previewChat.classList.remove('pos-right');
        } else {
            previewChat.classList.add('pos-right');
            previewChat.classList.remove('pos-left');
        }

        // State
        const activeState = document.querySelector('input[name="aida_initial_state"]:checked').value;
        if (activeState === 'open') {
            previewChat.classList.add('state-open');
        } else {
            previewChat.classList.remove('state-open');
        }
    }

    if (positionSelect) {
        positionSelect.addEventListener('change', updatePreview);
    }
    stateRadios.forEach(radio => radio.addEventListener('change', updatePreview));
    
    // Initial call
    updatePreview();
});
