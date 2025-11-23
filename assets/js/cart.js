// ===== CART SYSTEM =====

// Lấy giỏ hàng từ localStorage
let cart = JSON.parse(localStorage.getItem("cart")) || [];

// Dữ liệu sản phẩm
const productData = {
    "golden-dep-trai": { name: "GOLDEN ĐẸP TRAI", price: 15000000, img: "https://placehold.co/600x500?text=GOLDEN+ĐẸP+TRAI" },
    "samoyed-xinh": { name: "SAMOYED XINH", price: 14000000, img: "https://placehold.co/600x500?text=SAMOYED+XINH" },
    "alaska-xam-cung": { name: "ALASKA XÁM CƯNG", price: 24000000, img: "https://placehold.co/600x500?text=ALASKA+XÁM+CƯNG" },
    "bac-kinh-sieu-beo": { name: "BẮC KINH SIÊU BÉO", price: 7000000, img: "https://placehold.co/600x500?text=BAC+KINH+SIEU+BEO" },
    "bichon-trang": { name: "BICHON TRẮNG XINH XINH", price: 30000000, img: "https://placehold.co/600x500?text=BICHON+TRANG" },
    "phoc-soc": { name: "PHỐC SÓC BÉ XÍU CƯNG XĨU", price: 20000000, img: "https://placehold.co/600x500?text=PHOC+SOC" }
};

// Lưu giỏ hàng
function saveCart() {
    localStorage.setItem("cart", JSON.stringify(cart));
}

// Thêm sản phẩm vào giỏ
function addToCart(productId) {
    const product = productData[productId];
    if (!product) {
        alert("Sản phẩm không tìm thấy!");
        return;
    }

    const existingItem = cart.find(item => item.id === productId);
    if (existingItem) {
        existingItem.qty += 1;
    } else {
        cart.push({
            id: productId,
            name: product.name,
            price: product.price,
            img: product.img,
            qty: 1
        });
    }

    saveCart();
    updateMiniCart();
    alert(`Đã thêm "${product.name}" vào giỏ hàng!`);
}

// Cập nhật giao diện giỏ hàng trong cart.html
function renderCartPage() {
    const cartContainer = document.querySelector(".cart-page-items");
    const totalElement = document.querySelector(".cart-page-total");

    if (!cartContainer) return; // Không phải trang cart.html

    if (cart.length === 0) {
        cartContainer.innerHTML = "<p>Giỏ hàng của bạn đang trống.</p>";
        totalElement.textContent = "0₫";
        return;
    }

    cartContainer.innerHTML = cart.map(item => `
        <div class="cart-row">
            <img src="${item.img}" width="70">
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
    totalElement.textContent = total.toLocaleString() + "₫";
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

    // icon ở header
    document.querySelectorAll(".cart-count").forEach(e => e.textContent = count);

    const miniItems = document.querySelector(".mini-items");
    const miniTotal = document.querySelector(".mini-total strong");

    if (!miniItems) return;

    if (cart.length === 0) {
        miniItems.innerHTML = "Chưa có sản phẩm";
        miniTotal.textContent = "0₫";
        return;
    }

    miniItems.innerHTML = cart.map(item => `
        <div class="mini-item">
            <img src="${item.img}" width="50">
            <div>
                <p>${item.name}</p>
                <span>${item.qty} × ${item.price.toLocaleString()}₫</span>
            </div>
        </div>
    `).join("");

    miniTotal.textContent = total.toLocaleString() + "₫";
}

// Khi load trang
document.addEventListener("DOMContentLoaded", () => {
    updateMiniCart();
    renderCartPage();
});
