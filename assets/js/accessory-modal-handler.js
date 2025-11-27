// File: assets/js/accessory-modal-handler.js
// Xử lý sự kiện click trên nút "Xem phụ kiện" và hiển thị modal PHP

document.addEventListener('DOMContentLoaded', function() {
  // Xử lý nút "Xem phụ kiện"
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('view-accessory-btn')) {
      e.preventDefault();
      
      const accessoryId = e.target.getAttribute('data-id');
      
      if (accessoryId) {
        // Gọi file accessory-modal.php với tham số accessory_id
        const modalContainer = document.getElementById('modalContainer');
        
        if (!modalContainer) {
          // Tạo container nếu chưa tồn tại
          const newContainer = document.createElement('div');
          newContainer.id = 'modalContainer';
          document.body.appendChild(newContainer);
        }
        
        // Fetch nội dung modal từ file PHP
        fetch('accessory-modal.php?accessory_id=' + encodeURIComponent(accessoryId))
          .then(response => response.text())
          .then(html => {
            const container = document.getElementById('modalContainer');
            container.innerHTML = html;
            
            // Hiển thị modal
            const modal = container.querySelector('.product-modal');
            if (modal) {
              modal.style.display = 'flex';
              document.body.style.overflow = 'hidden';
              
              // Xử lý đóng modal
              const closeBtn = modal.querySelector('.modal-close');
              if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                  modal.style.display = 'none';
                  document.body.style.overflow = 'auto';
                });
              }
              
              // Đóng modal khi click bên ngoài
              modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                  modal.style.display = 'none';
                  document.body.style.overflow = 'auto';
                }
              });

              // ===== THÊM EVENT LISTENER CHO NÚT BÊN TRONG MODAL =====
              // Nút "Thêm vào giỏ"
              const addToCartBtn = modal.querySelector('.add-to-cart');
              if (addToCartBtn) {
                addToCartBtn.addEventListener('click', function(e) {
                  e.preventDefault();
                  const aid = this.getAttribute('data-id');
                  if (aid && typeof addToCart === 'function') {
                    addToCart(aid);
                    // Đóng modal sau khi thêm vào giỏ
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                  }
                });
              }

              // Nút "Mua ngay"
              const buyNowBtns = modal.querySelectorAll('.modal-actions button:not(.add-to-cart)');
              if (buyNowBtns.length > 0) {
                buyNowBtns[0].addEventListener('click', function(e) {
                  e.preventDefault();
                  const accessoryId = addToCartBtn ? addToCartBtn.getAttribute('data-id') : null;
                  const maxStock = addToCartBtn ? addToCartBtn.getAttribute('data-stock') : 1;
                  if (accessoryId && typeof addToCartAndRedirect === 'function') {
                    addToCartAndRedirect(accessoryId, maxStock);
                  }
                });
              }
            }
          })
          .catch(error => {
            console.error('Lỗi khi tải modal:', error);
            alert('Không thể tải thông tin phụ kiện. Vui lòng thử lại.');
          });
      }
    }
  });
});
