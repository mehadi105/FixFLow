function initPasswordToggle(button) {
    const input = document.getElementById(button.dataset.target);

    if (!input) {
        return;
    }

    const showIcon = button.querySelector('.ff-password-icon-show');
    const hideIcon = button.querySelector('.ff-password-icon-hide');

    button.addEventListener('click', () => {
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        button.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
        button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
        showIcon?.classList.toggle('hidden', isHidden);
        hideIcon?.classList.toggle('hidden', !isHidden);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-password-toggle]').forEach(initPasswordToggle);
});
