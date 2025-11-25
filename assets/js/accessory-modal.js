// Popup Phụ Kiện
class AccessoryModal {
  constructor() {
    this.modal = null;
    this.init();
  }

  init() {
    const modalHTML = `
      <div id="accessoryModal" class="product-modal" style="display: none;">
        <div class="product-modal-content">
          <button class="modal-close" aria-label="Close">&times;</button>
          <div class="modal-body">
            <div class="modal-image">
              <img id="accModalImage" src="" alt="Accessory" />
            </div>
            <div class="modal-details">
              <h2 id="accModalName">Tên phụ kiện</h2>
              <div class="modal-rating">
                <span class="stars">⭐⭐⭐⭐⭐</span>
                <span id="accModalRating">(0 đánh giá)</span>
              </div>
              <div class="modal-price">
                <span class="current-price" id="accModalPrice">0₫</span>
              </div>
              <div class="modal-section">
                <h3>Mô tả</h3>
                <p id="accModalDescription">Chưa có thông tin. Vui lòng cập nhật sau.</p>
              </div>
              <div class="modal-section">
                <h3>Thông tin chi tiết</h3>
                <table class="modal-table">
                  <tr>
                    <td>Thương hiệu:</td>
                    <td id="accModalBrand">-</td>
                  </tr>
                  <tr>
                    <td>Chất liệu:</td>
                    <td id="accModalMaterial">-</td>
                  </tr>
                  <tr>
                    <td>Kích cỡ:</td>
                    <td id="accModalSize">-</td>
                  </tr>
                  <tr>
                    <td>Tình trạng:</td>
                    <td id="accModalStatus">-</td>
                  </tr>
                </table>
              </div>
              <div class="modal-actions">
                <button class="btn btn-primary" id="accModalAddToCart">Thêm vào giỏ</button>
                <button class="btn btn-outline" id="accModalBuyNow">Mua ngay</button>
              </div>
            </div>
          </div>
        </div>
      </div>`;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
    this.modal = document.getElementById('accessoryModal');
    this.attachEventListeners();
  }

  attachEventListeners() {
    const closeBtn = this.modal.querySelector('.modal-close');
    closeBtn.addEventListener('click', () => this.close());

    this.modal.addEventListener('click', (e) => {
      if (e.target === this.modal) this.close();
    });

    // Handle "Xem" for accessories
    document.addEventListener('click', (e) => {
      if (e.target.classList.contains('view-accessory-btn')) {
        e.preventDefault();
        const el = e.target;
        const data = {
          id: el.getAttribute('data-id'),
          name: el.getAttribute('data-name'),
          price: el.getAttribute('data-price'),
          image: el.getAttribute('data-image'),
          brand: el.getAttribute('data-brand'),
          material: el.getAttribute('data-material'),
          size: el.getAttribute('data-size'),
          status: el.getAttribute('data-status'),
          description: el.getAttribute('data-description')
        };
        this.show(data);
      }
    });

    this.modal.querySelector('#accModalAddToCart').addEventListener('click', () => {
      const id = this.modal.getAttribute('data-product-id');
      if (id && typeof addToCart === 'function') {
        addToCart(id);
        this.close();
      }
    });

    this.modal.querySelector('#accModalBuyNow').addEventListener('click', () => {
      const id = this.modal.getAttribute('data-product-id');
      if (id && typeof addToCart === 'function') {
        addToCart(id);
        window.location.href = 'cart.php';
      }
    });
  }

  show(data) {
    this.modal.setAttribute('data-product-id', data.id || '');
    this.modal.querySelector('#accModalName').textContent = data.name || '';
    this.modal.querySelector('#accModalPrice').textContent = data.price || '';
    this.modal.querySelector('#accModalImage').src = data.image || '';

    this.modal.querySelector('#accModalDescription').textContent = data.description || '—';
    this.modal.querySelector('#accModalBrand').textContent = data.brand || '—';
    this.modal.querySelector('#accModalMaterial').textContent = data.material || '—';
    this.modal.querySelector('#accModalSize').textContent = data.size || '—';
    this.modal.querySelector('#accModalStatus').textContent = data.status || '—';

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

// Init
document.addEventListener('DOMContentLoaded', () => {
  new AccessoryModal();
});
