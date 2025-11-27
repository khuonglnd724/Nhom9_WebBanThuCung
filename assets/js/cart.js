// ===== CART SYSTEM =====

// Lấy user ID từ PHP session (được truyền qua data attribute)
function getUserId() {
    const body = document.body;
    return body.getAttribute('data-user-id') || null;
}

// Kiểm tra đăng nhập
function isLoggedIn() {
    return getUserId() !== null;
}

// Lấy giỏ hàng từ localStorage theo user
function getCartKey() {
    const userId = getUserId();
    return userId ? `cart_user_${userId}` : 'cart_guest';
}

let cart = JSON.parse(localStorage.getItem(getCartKey())) || [];

// Lưu giỏ hàng
function saveCart() {
    localStorage.setItem(getCartKey(), JSON.stringify(cart));
    
    // Nếu đã đăng nhập, đồng bộ lên server
    if (isLoggedIn()) {
        syncCartToServer();
    }
}

// Đồng bộ giỏ hàng lên server
async function syncCartToServer() {
    try {
        const response = await fetch('api/cart_sync.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ cart: cart })
        });
        
        if (!response.ok) {
            console.error('Failed to sync cart to server');
        }
    } catch (error) {
        console.error('Error syncing cart:', error);
    }
}

// Tải giỏ hàng từ server khi đăng nhập
async function loadCartFromServer() {
    if (!isLoggedIn()) return;
    
    try {
        const response = await fetch('api/cart_sync.php', {
            method: 'GET'
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.cart) {
                // Merge với cart hiện tại trong localStorage
                const localCart = JSON.parse(localStorage.getItem(getCartKey())) || [];
                
                // Nếu có cart local, ưu tiên cart local (merge)
                if (localCart.length > 0) {
                    // Merge logic: ưu tiên local cart
                    const mergedCart = [...localCart];
                    
                    data.cart.forEach(serverItem => {
                        const existingIndex = mergedCart.findIndex(item => item.id === serverItem.id);
                        if (existingIndex === -1) {
                            mergedCart.push(serverItem);
                        }
                    });
                    
                    cart = mergedCart;
                } else {
                    // Không có local cart, dùng server cart
                    cart = data.cart;
                }
                
                saveCart();
                updateMiniCart();
            }
        }
    } catch (error) {
        console.error('Error loading cart from server:', error);
    }
}

// Xóa giỏ hàng
function clearCart() {
    cart = [];
    localStorage.removeItem(getCartKey());
}

// Thêm sản phẩm vào giỏ - Lấy thông tin từ button data attributes
function addToCart(productId) {
    console.log('=== addToCart called with productId:', productId);
    
    // Kiểm tra đăng nhập
    if (!isLoggedIn()) {
        alert('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');
        window.location.href = 'login.php';
        return;
    }

    // Tìm button với data-id tương ứng
    const button = document.querySelector(`button.add-to-cart[data-id="${productId}"]`);
    console.log('Button found:', button);
    if (!button) {
        console.error(`Button not found for product ${productId}`);
        alert("Không tìm thấy sản phẩm!");
        return;
    }

    // Lấy stock từ data attribute của button
    const maxStock = parseInt(button.getAttribute('data-stock')) || 1;
    console.log('Max stock:', maxStock);

    // Tìm thông tin từ product card hoặc modal
    let name = 'Sản phẩm';
    let price = 0;
    let img = '';

    // Cố gắng lấy từ product-card trước
    const card = button.closest('.product-card');
    if (card) {
        console.log('Found in product-card');
        name = card.querySelector('.title')?.textContent || 'Sản phẩm';
        const priceText = card.querySelector('.price')?.textContent || '0₫';
        price = parseInt(priceText.replace(/[^\d]/g, '')) || 0;
        img = card.querySelector('.thumb img')?.src || '';
    } else {
        // Nếu không phải product-card, có thể là modal
        const modal = button.closest('.product-modal-content') || button.closest('.product-modal');
        console.log('Modal found:', modal);
        if (modal) {
            // Lấy từ modal
            name = modal.querySelector('h2')?.textContent || 
                   modal.querySelector('.modal-title')?.textContent || 'Sản phẩm';
            const priceText = modal.querySelector('.current-price')?.textContent || '0₫';
            price = parseInt(priceText.replace(/[^\d]/g, '')) || 0;
            img = modal.querySelector('img')?.src || '';
            console.log('Modal data - name:', name, 'price:', price, 'img:', img);
        }
    }

    const existingItem = cart.find(item => item.id === productId);
    if (existingItem) {
        // Kiểm tra stock trước khi tăng
        if (existingItem.qty >= maxStock) {
            alert(`Chỉ còn ${maxStock} sản phẩm trong kho!`);
            return;
        }
        existingItem.qty += 1;
    } else {
        // Kiểm tra stock khi thêm mới
        if (maxStock <= 0) {
            alert('Sản phẩm đã hết hàng!');
            return;
        }
        cart.push({
            id: productId,
            name: name,
            price: price,
            img: img,
            qty: 1,
            maxStock: maxStock
        });
    }

    saveCart();
    updateMiniCart();
    alert(`Đã thêm "${name}" vào giỏ hàng!`);
}

// Thêm vào giỏ hàng và redirect đến cart.php
function addToCartAndRedirect(productId, maxStock) {
    // Kiểm tra đăng nhập
    if (!isLoggedIn()) {
        alert('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');
        window.location.href = 'login.php';
        return;
    }

    // Kiểm tra stock
    if (maxStock <= 0) {
        alert('Sản phẩm đã hết hàng!');
        return;
    }

    const existingItem = cart.find(item => item.id === productId);
    if (existingItem) {
        // Kiểm tra stock trước khi tăng
        if (existingItem.qty >= maxStock) {
            alert(`Chỉ còn ${maxStock} sản phẩm trong kho!`);
            return;
        }
        existingItem.qty += 1;
    } else {
        // Tạo item mới - lấy thông tin từ button data
        const button = document.querySelector(`button.add-to-cart[data-id="${productId}"]`);
        if (button) {
            const card = button.closest('.product-card') || button.closest('.product-modal-content');
            const name = card?.querySelector('.title, h2')?.textContent || 'Sản phẩm';
            const priceText = card?.querySelector('.price, .current-price')?.textContent || '0₫';
            const price = parseInt(priceText.replace(/[^\d]/g, '')) || 0;
            const img = card?.querySelector('img')?.src || '';

            cart.push({
                id: productId,
                name: name,
                price: price,
                img: img,
                qty: 1,
                maxStock: maxStock
            });
        }
    }

    saveCart();
    updateMiniCart();
    
    // Redirect đến cart.php
    window.location.href = 'cart.php';
}

// Cập nhật giao diện giỏ hàng trong cart.html
function renderCartPage() {
    const cartContainer = document.querySelector(".cart-page-items");
    const totalElement = document.querySelector(".cart-page-total");

    if (!cartContainer) return; // Không phải trang cart.html

    if (cart.length === 0) {
        cartContainer.innerHTML = "<p>Giỏ hàng của bạn đang trống.</p>";
        if (totalElement) totalElement.textContent = "0₫";
        return;
    }

    cartContainer.innerHTML = cart.map(item => `
        <div class="cart-row">
            <img src="${item.img}" width="70" alt="${item.name}">
            <div class="cart-info">
                <h4>${item.name}</h4>
                <p>${item.price.toLocaleString()}₫</p>
            </div>

            <div class="qty-box">
                <button onclick="changeQty('${item.id}', -1)">−</button>
                <span>${item.qty}</span>
                <button onclick="changeQty('${item.id}', 1)">+</button>
            </div>

            <p class="subtotal">${(item.price * item.qty).toLocaleString()}₫</p>

            <button class="remove-btn" onclick="removeFromCart('${item.id}')">X</button>
        </div>
    `).join("");

    let total = cart.reduce((t, i) => t + i.qty * i.price, 0);
    if (totalElement) totalElement.textContent = total.toLocaleString() + "₫";
}

// Thay đổi số lượng
function changeQty(id, amount) {
    let item = cart.find(i => i.id === id);
    if (!item) return;

    item.qty += amount;
    if (item.qty <= 0) {
        cart = cart.filter(i => i.id !== id);
    }

    saveCart();
    renderCartPage();
    updateMiniCart();
}

// Xóa sản phẩm
function removeFromCart(id) {
    cart = cart.filter(i => i.id !== id);
    saveCart();
    renderCartPage();
    updateMiniCart();
}

// Cập nhật minicart
function updateMiniCart() {
    let count = cart.reduce((t, i) => t + i.qty, 0);
    let total = cart.reduce((t, i) => t + i.qty * i.price, 0);

    // icon ở header và cart label
    document.querySelectorAll(".cart-count").forEach(e => e.textContent = count);
    
    const cartLabel = document.querySelector(".cart-label");
    if (cartLabel) {
        cartLabel.innerHTML = `<strong>Giỏ hàng</strong><br>${count} sản phẩm - ${total.toLocaleString()}₫`;
    }

    const miniItems = document.querySelector(".mini-items");
    const miniTotal = document.querySelector(".mini-total strong");

    if (!miniItems) return;

    if (cart.length === 0) {
        miniItems.innerHTML = "Chưa có sản phẩm";
        if (miniTotal) miniTotal.textContent = "0₫";
        return;
    }

    miniItems.innerHTML = cart.map(item => `
        <div class="mini-item">
            <img src="${item.img}" width="50" alt="${item.name}">
            <div>
                <p>${item.name}</p>
                <span>${item.qty} × ${item.price.toLocaleString()}₫</span>
            </div>
        </div>
    `).join("");

    if (miniTotal) miniTotal.textContent = total.toLocaleString() + "₫";
}

// Khi load trang
document.addEventListener("DOMContentLoaded", () => {
    // Tải giỏ hàng từ server nếu đã đăng nhập
    loadCartFromServer().then(() => {
        updateMiniCart();
        renderCartPage();
    });

    // Event listener cho nút "Thêm vào giỏ hàng"
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-to-cart')) {
            e.preventDefault();
            const productId = e.target.getAttribute('data-id');
            if (productId) {
                addToCart(productId);
            }
        }
    });
});
