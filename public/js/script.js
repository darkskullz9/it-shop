const navToggle = document.querySelector('.nav-toggle');
const navbar = document.querySelector('.navbar');

if (navToggle && navbar) {

    navToggle.addEventListener('click', () => {
        const isOpen = navbar.classList.toggle('open');
        navToggle.classList.toggle('is-open', isOpen);
        navToggle.setAttribute('aria-expanded', isOpen);
    });

    navbar.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            navbar.classList.remove('open');
            navToggle.classList.remove('is-open');
            navToggle.setAttribute('aria-expanded', 'false');
        });
    });

    document.addEventListener('click', (e) => {
        if(!navToggle.contains(e.target) && !navbar.contains(e.target)) {
            navbar.classList.remove('open');
            navToggle.classList.remove('is-open');
            navToggle.setAttribute('aria-expanded', 'false');
        }
    });
}

function closeCartModal() {
    document.getElementById('cart-modal-overlay').style.display = 'none';
}

document.getElementById('cart-modal-overlay')?.addEventListener('click', function(e) {
    if (e.target === this) closeCartModal();
});

document.addEventListener('DOMContentLoaded', function () {
    const qtyInput  = document.getElementById('quantity');
    const addToCart = document.getElementById('add-to-cart-btn');

    if (!qtyInput || !addToCart) return;

    qtyInput.addEventListener('input', updateCartUrl);
    qtyInput.addEventListener('change', updateCartUrl);

    function updateCartUrl() {
        const qty     = Math.max(1, parseInt(qtyInput.value) || 1);
        const baseUrl = addToCart.dataset.baseUrl;
        addToCart.href = baseUrl + '?quantity=' + qty;
    }

    updateCartUrl();
});