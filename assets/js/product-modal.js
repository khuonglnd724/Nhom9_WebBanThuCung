// Popup Sản Phẩm
class ProductModal {
  constructor() {
    this.modal = null;
    this.init();
  }

  init() {
    // Tạo HTML modal
    const modalHTML = `
      <div id="productModal" class="product-modal" style="display: none;">
        <div class="product-modal-content">
          <button class="modal-close" aria-label="Close">&times;</button>
          <div class="modal-body">
            <div class="modal-image">
              <img id="modalProductImage" src="" alt="Product" />
            </div>
            <div class="modal-details">
              <h2 id="modalProductName">Tên sản phẩm</h2>
              <div class="modal-rating">
                <span class="stars">⭐⭐⭐⭐⭐</span>
                <span id="modalProductRating">(0 đánh giá)</span>
              </div>
              <div class="modal-price">
                <span class="current-price" id="modalProductPrice">0₫</span>
              </div>
              <div class="modal-section">
                <h3>Mô tả sản phẩm</h3>
                <p id="modalProductDescription">Chưa có thông tin. Vui lòng cập nhật sau.</p>
              </div>
              <div class="modal-section">
                <h3>Thông tin chi tiết</h3>
                <table class="modal-table">
                  <tr>
                    <td>Giống loài:</td>
                    <td id="modalProductBreed">-</td>
                  </tr>
                  <tr>
                    <td>Tuổi:</td>
                    <td id="modalProductAge">-</td>
                  </tr>
                  <tr>
                    <td>Giới tính:</td>
                    <td id="modalProductGender">-</td>
                  </tr>
                  <tr>
                    <td>Kích cỡ:</td>
                    <td id="modalProductWeight">-</td>
                  </tr>
                  <tr>
                    <td>Màu sắc:</td>
                    <td id="modalProductColor">-</td>
                  </tr>
                  <tr>
                    <td>Tình trạng:</td>
                    <td id="modalProductStatus">-</td>
                  </tr>
                </table>
              </div>
              <div class="modal-section">
                <h3>Chính sách bán hàng</h3>
                <ul class="modal-policy">
                  <li>✓ Đảm bảo sức khỏe 100%</li>
                  <li>✓ Giao hàng toàn quốc</li>
                  <li>✓ Hỗ trợ sau bán hàng 24/7</li>
                  <li>✓ Giấy chứng chỉ xuất xứ</li>
                </ul>
              </div>
              <div class="modal-actions">
                <button class="btn btn-primary" id="modalAddToCart">Thêm vào giỏ</button>
                <button class="btn btn-outline" id="modalBuyNow">Mua ngay</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;

    // Chèn modal vào body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    this.modal = document.getElementById('productModal');

    // Gắn sự kiện
    this.attachEventListeners();
  }

  attachEventListeners() {
    const closeBtn = this.modal.querySelector('.modal-close');
    closeBtn.addEventListener('click', () => this.close());

    // Đóng khi click bên ngoài modal
    this.modal.addEventListener('click', (e) => {
      if (e.target === this.modal) {
        this.close();
      }
    });

    // Xử lý nút "Xem"
    document.addEventListener('click', (e) => {
      if (e.target.classList.contains('view-product-btn')) {
        e.preventDefault();
        const productId = e.target.getAttribute('data-id');
        const productName = e.target.getAttribute('data-name');
        const productPrice = e.target.getAttribute('data-price');
        const productImage = e.target.getAttribute('data-image');
        
        // Lấy thông tin chi tiết từ data attributes
        const productData = {
          description: e.target.getAttribute('data-description'),
          breed: e.target.getAttribute('data-breed'),
          age: e.target.getAttribute('data-age'),
          color: e.target.getAttribute('data-color'),
          size: e.target.getAttribute('data-size'),
          gender: e.target.getAttribute('data-gender'),
          status: e.target.getAttribute('data-status')
        };
        
        this.showProduct(productId, productName, productPrice, productImage, productData);
      }
    });

    // Nút thêm vào giỏ
    this.modal.querySelector('#modalAddToCart').addEventListener('click', () => {
      const productId = this.modal.getAttribute('data-product-id');
      if (productId && typeof addToCart === 'function') {
        addToCart(productId);
        this.close();
      }
    });

    // Nút mua ngay
    this.modal.querySelector('#modalBuyNow').addEventListener('click', () => {
      const productId = this.modal.getAttribute('data-product-id');
      if (productId && typeof addToCart === 'function') {
        addToCart(productId);
        window.location.href = 'cart.php';
      }
    });
  }

  showProduct(productId, productName, productPrice, productImage, productData = {}) {
    // Đặt thông tin cơ bản (từ attributes/button data)
    this.modal.setAttribute('data-product-id', productId);
    this.modal.querySelector('#modalProductName').textContent = productName;
    this.modal.querySelector('#modalProductPrice').textContent = productPrice;
    this.modal.querySelector('#modalProductImage').src = productImage;

    // Cập nhật thông tin chi tiết từ data attributes
    if (productData.description) {
      this.modal.querySelector('#modalProductDescription').textContent = productData.description;
    }
    if (productData.breed) {
      this.modal.querySelector('#modalProductBreed').textContent = productData.breed;
    }
    if (productData.age) {
      this.modal.querySelector('#modalProductAge').textContent = productData.age;
    }
    if (productData.gender) {
      this.modal.querySelector('#modalProductGender').textContent = productData.gender;
    }
    if (productData.color) {
      this.modal.querySelector('#modalProductColor').textContent = productData.color;
    }
    if (productData.size) {
      this.modal.querySelector('#modalProductWeight').textContent = productData.size;
    }
    if (productData.status) {
      this.modal.querySelector('#modalProductStatus').textContent = productData.status;
    }

    // Hiển thị modal
    this.open();
  }

  open() {
    this.modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  close() {
    this.modal.style.display = 'none';
    document.body.style.overflow = 'auto';
  }
}

// Khởi tạo khi DOM sẵn sàng
document.addEventListener('DOMContentLoaded', () => {
  new ProductModal();
});
