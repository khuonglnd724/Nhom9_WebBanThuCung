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
