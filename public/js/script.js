const navToggle = document.querySelector('.nav-toggle');
const navbar = document.querySelector('.navbar');

navToggle?.addEventListener('click', () => {
  navbar.classList.toggle('open');
});

function closeCartModal() {
  document.getElementById('cart-modal-overlay').style.display = 'none';
}

document.getElementById('cart-modal-overlay')?.addEventListener('click', function(e) {
  if (e.target === this) closeCartModal();
});