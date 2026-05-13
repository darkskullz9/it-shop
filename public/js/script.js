function closeCartModal() {
    document.getElementById('cart-modal-overlay').style.display = 'none';
}

document.getElementById('cart-modal-overlay')?.addEventListener('click', function(e) {
    if (e.target === this) closeCartModal();
});