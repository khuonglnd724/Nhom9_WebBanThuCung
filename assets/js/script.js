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
  // Chỉ chặn mặc định nếu link dùng '#' để làm toggle.
  document.querySelectorAll('.main-nav .dropdown-toggle').forEach(function(toggle){
    toggle.addEventListener('click', function(e){
      const href = toggle.getAttribute('href') || '';
      if (href === '#' || toggle.dataset.toggle === 'dropdown') {
        e.preventDefault();
        document.querySelectorAll('.main-nav .dropdown').forEach(function(drop){
          if(drop.contains(toggle)){
            drop.classList.toggle('open');
          } else {
            drop.classList.remove('open');
          }
        });
      }
      // Nếu href là trang hợp lệ (vd pet.php) thì để trình duyệt điều hướng bình thường
    });
  });
  // Bảo đảm click chữ "Thú cưng" sẽ điều hướng (không áp dụng khi nhấn caret)
  document.querySelectorAll('.main-nav .dropdown > a.dropdown-toggle').forEach(function(link){
    link.addEventListener('click', function(e){
      const isCaret = e.target && e.target.closest('.caret');
      if (isCaret) return; // caret đã có handler riêng
      const href = link.getAttribute('href');
      if (href && href !== '#') {
        if (e.defaultPrevented) {
          // Nếu có script khác đã chặn, ta vẫn điều hướng thủ công
          window.location.href = href;
        }
        // Ngược lại để điều hướng mặc định của trình duyệt
      }
    });
  });
  // Cho phép nhấn vào caret để chỉ mở dropdown, không điều hướng
  document.querySelectorAll('.main-nav .dropdown-toggle .caret').forEach(function(caret){
    caret.addEventListener('click', function(e){
      e.preventDefault();
      e.stopPropagation();
      const drop = caret.closest('li.dropdown');
      if (!drop) return;
      // Đóng các dropdown khác, mở/tắt dropdown hiện tại
      document.querySelectorAll('.main-nav .dropdown').forEach(function(item){
        if (item === drop) {
          item.classList.toggle('open');
        } else {
          item.classList.remove('open');
        }
      });
    });
  });
  
  // Event listener cho .add-to-cart đã được xử lý trong cart.js
  // Không cần thêm event listener ở đây để tránh duplicate

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
