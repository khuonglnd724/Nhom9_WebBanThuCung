// Thanh trượt Banner
document.addEventListener('DOMContentLoaded', function() {
  const slides = document.querySelectorAll('.banner-slider .slide');
  const dots = document.querySelectorAll('.slider-dots .dot');
  const prevBtn = document.querySelector('.slider-btn.prev');
  const nextBtn = document.querySelector('.slider-btn.next');
  let current = 0;
  let timer;

  function showSlide(idx) {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === idx);
      dots[i].classList.toggle('active', i === idx);
    });
    current = idx;
  }

  function nextSlide() {
    showSlide((current + 1) % slides.length);
  }
  function prevSlide() {
    showSlide((current - 1 + slides.length) % slides.length);
  }

  if (nextBtn && prevBtn) {
    nextBtn.addEventListener('click', () => {
      nextSlide();
      resetTimer();
    });
    prevBtn.addEventListener('click', () => {
      prevSlide();
      resetTimer();
    });
  }
  dots.forEach((dot, i) => {
    dot.addEventListener('click', () => {
      showSlide(i);
      resetTimer();
    });
  });

  function autoSlide() {
    timer = setInterval(nextSlide, 4000);
  }
  function resetTimer() {
    clearInterval(timer);
    autoSlide();
  }
  showSlide(0);
  autoSlide();
});
document.addEventListener('DOMContentLoaded', function(){
  // Bật/tắt menu di động đơn giản (thêm class vào body)
  const mobileToggle = document.getElementById('mobileToggle');
  mobileToggle && mobileToggle.addEventListener('click', () => {
    document.body.classList.toggle('mobile-open');
    const nav = document.querySelector('.main-nav');
    if(nav) nav.style.display = nav.style.display === 'block' ? 'none' : 'block';
  });

  // Bật/tắt giỏ hàng nhỏ
  const cartToggle = document.getElementById('cartToggle');
  const miniCart = document.getElementById('miniCart');
  cartToggle && cartToggle.addEventListener('click', () => {
    if(!miniCart) return;
    const isHidden = miniCart.getAttribute('aria-hidden') === 'true';
    miniCart.setAttribute('aria-hidden', String(!isHidden));
  });

  // Thêm vào giỏ hàng
  const updateCartCount = (n) => {
    document.querySelectorAll('.cart-count').forEach(el => el.textContent = n);
  };
  let cartCount = 0;
    document.querySelectorAll('.main-nav .dropdown-toggle').forEach(function(toggle){
      toggle.addEventListener('click', function(e){
        e.preventDefault(); // Không chuyển trang, không mở tab mới
        // Đóng các dropdown khác
        document.querySelectorAll('.main-nav .dropdown').forEach(function(drop){
          if(drop.contains(toggle)){
            drop.classList.toggle('open');
          } else {
            drop.classList.remove('open');
          }
        });
      });
    });
  document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const productId = btn.getAttribute('data-id');
      addToCart(productId);
    });
  });

  // Làm nổi bật dropdown cha đang hoạt động
  const currentPage = window.location.pathname.split('/').pop();
  if (currentPage && currentPage !== 'index.php') {
    const activeLink = document.querySelector(`.main-nav .menu a[href$="${currentPage}"]`);
    if (activeLink) {
      const parentLi = activeLink.closest('li');

      if (parentLi) {
        // Nếu link nằm bên trong dropdown, kích hoạt dropdown cấp cao nhất
        const dropdownParent = parentLi.closest('li.dropdown');
        if (dropdownParent) {
          document.querySelectorAll('.main-nav .menu > li').forEach(li => li.classList.remove('active'));
          dropdownParent.classList.add('active');
        } else {
          // Đó là một link cấp cao nhất
          document.querySelectorAll('.main-nav .menu > li').forEach(li => li.classList.remove('active'));
          parentLi.classList.add('active');
        }
      }
    }
  }
});
