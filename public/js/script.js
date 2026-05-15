
function closeCartModal() {
    document.getElementById('cart-modal-overlay').style.display = 'none';
}

document.getElementById('cart-modal-overlay')?.addEventListener('click', function(e) {
    if (e.target === this) closeCartModal();
});

document.addEventListener('DOMContentLoaded', function() {
    const qtyInput = document.getElementById('quantity');
    const addToCart = document.getElementById('add-to-cart-btn');
    const btnMinus = document.getElementById('qty-minus');
    const btnPlus = document.getElementById('qty-plus');

    if(qtyInput && addToCart) {
        function updateCartUrl() {
            const qty = Math.max(1, parseInt(qtyInput.value) || 1);
            const baseUrl = addToCart.dataset.baseUrl;
            addToCart.href = baseUrl + '?quantity=' + qty;

            if(btnMinus) btnMinus.disabled = parseInt(qtyInput.value) <= 1;
        }

        if(btnMinus) {
            btnMinus.addEventListener('click', () => {
                const current = parseInt(qtyInput.value) || 1;
                if(current > 1) {
                    qtyInput.value = current - 1;
                    updateCartUrl();
                }
            });
        }

        if(btnPlus) {
            btnPlus.addEventListener('click', () => {
                const current = parseInt(qtyInput.value) || 1;
                const max = parseInt(qtyInput.max) || 99;
                if (current < max) {
                    qtyInput.value = current + 1;
                    updateCartUrl();
                }
            });
        }

        updateCartUrl();
    }

    const navToggle = document.querySelector('.nav-toggle');
    const navbar = document.querySelector('.navbar');

    if(navToggle && navbar) {
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
});

function closeAllDropdowns() {
    document.querySelectorAll('.admin-dropdown').forEach(d => {
        d.hidden = true;
        const id = d.id.replace('dropdown-', '');
        const btn = document.querySelector('[data-id="' + id + '"]');
        if(btn && d.parentElement === document.body) {
            btn.closest('.admin-actions-mobile').appendChild(d);
        }

        d.style.position = '';
        d.style.top = '';
        d.style.right = '';
        d.style.left = '';
    });
    document.querySelectorAll('.admin-menu-btn').forEach(b => {
        b.setAttribute('aria-expanded', 'false');
    });
}

document.querySelectorAll('.admin-menu-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();

        const id = this.dataset.id;
        const dropdown = document.getElementById('dropdown-' + id);
        const isOpen = !dropdown.hidden;

        closeAllDropdowns();

        if(!isOpen) {
            const rect = this.getBoundingClientRect();
            
            document.body.appendChild(dropdown);
            dropdown.style.position = 'fixed';
            dropdown.style.top = (rect.bottom + 4) + 'px';
            dropdown.style.right = (window.innerWidth - rect.right) + 'px';
            dropdown.style.left = 'auto';
            dropdown.hidden = false;
            this.setAttribute('aria-expanded', 'true');
        }
    });
});

document.addEventListener('click', closeAllDropdowns);
window.addEventListener('scroll', closeAllDropdowns, true);