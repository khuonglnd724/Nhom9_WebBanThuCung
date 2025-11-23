// ===== ORDERS MANAGEMENT =====

// Lấy danh sách đơn hàng
function getOrders() {
    return JSON.parse(localStorage.getItem("orders")) || [];
}

// Lưu đơn hàng
function saveOrders(orders) {
    localStorage.setItem("orders", JSON.stringify(orders));
}

// Hiển thị danh sách đơn hàng
function displayOrders(orderId = null) {
    const orders = getOrders();
    const container = document.getElementById("ordersContainer");

    if (!container) return;

    if (orders.length === 0) {
        container.innerHTML = "<p>Chưa có đơn hàng nào.</p>";
        return;
    }

    const filtered = orderId ? orders.filter(o => o.id === orderId) : orders;

    container.innerHTML = filtered.map(order => `
        <div style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h3>Đơn hàng: ${order.id}</h3>
                    <p><strong>Ngày:</strong> ${order.date}</p>
                    <p><strong>Khách hàng:</strong> ${order.customer.fullName}</p>
                    <p><strong>Email:</strong> ${order.customer.email}</p>
                    <p><strong>SĐT:</strong> ${order.customer.phone}</p>
                    <p><strong>Địa chỉ:</strong> ${order.customer.address}, ${order.customer.city}</p>
                    ${order.customer.notes ? `<p><strong>Ghi chú:</strong> ${order.customer.notes}</p>` : ''}
                </div>
                <div>
                    <p><strong>Phương thức:</strong> ${getPaymentMethodLabel(order.payment)}</p>
                    <p><strong>Trạng thái:</strong> <span style="background: ${getStatusColor(order.status)}; color: #fff; padding: 5px 10px; border-radius: 4px;">${order.status}</span></p>
                    <p><strong>Tổng tiền:</strong> <span style="font-size: 18px; color: var(--pink2); font-weight: 700;">${order.total.toLocaleString()}₫</span></p>
                </div>
            </div>
            <div style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                <h4>Chi tiết sản phẩm:</h4>
                ${order.items.map(item => `
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                        <span>${item.name} × ${item.qty}</span>
                        <span>${(item.qty * item.price).toLocaleString()}₫</span>
                    </div>
                `).join('')}
                <div style="display: flex; justify-content: space-between; padding: 10px 0; font-weight: 700;">
                    <span>Cộng:</span>
                    <span>${order.items.reduce((sum, item) => sum + item.qty * item.price, 0).toLocaleString()}₫</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 5px 0;">
                    <span>Vận chuyển:</span>
                    <span>30.000₫</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; font-weight: 700; border-top: 1px solid #eee;">
                    <span>Tổng cộng:</span>
                    <span style="color: var(--pink2);">${order.total.toLocaleString()}₫</span>
                </div>
            </div>
        </div>
    `).join("");
}

// Thay đổi trạng thái đơn hàng
function updateOrderStatus(orderId, newStatus) {
    const orders = getOrders();
    const order = orders.find(o => o.id === orderId);
    if (order) {
        order.status = newStatus;
        saveOrders(orders);
        displayOrders();
        alert("Cập nhật trạng thái thành công!");
    }
}

// Xóa đơn hàng
function deleteOrder(orderId) {
    if (confirm("Bạn chắc chắn muốn xóa đơn hàng này?")) {
        let orders = getOrders();
        orders = orders.filter(o => o.id !== orderId);
        saveOrders(orders);
        displayOrders();
        alert("Xóa đơn hàng thành công!");
    }
}

// Hỗ trợ hàm
function getPaymentMethodLabel(method) {
    const methods = {
        "cod": "Thanh toán khi nhận hàng",
        "transfer": "Chuyển khoản ngân hàng",
        "card": "Thẻ tín dụng/Ghi nợ"
    };
    return methods[method] || method;
}

function getStatusColor(status) {
    const colors = {
        "Chờ xác nhận": "#ff9800",
        "Đã xác nhận": "#2196f3",
        "Đang giao": "#9c27b0",
        "Đã giao": "#4caf50",
        "Hủy": "#f44336"
    };
    return colors[status] || "#999";
}

// Xuất dữ liệu đơn hàng (CSV)
function exportOrdersToCSV() {
    const orders = getOrders();
    if (orders.length === 0) {
        alert("Không có đơn hàng để xuất!");
        return;
    }

    let csv = "Mã đơn hàng,Ngày,Khách hàng,Email,SĐT,Địa chỉ,Tổng tiền,Trạng thái,Phương thức thanh toán\n";
    orders.forEach(order => {
        csv += `${order.id},"${order.date}","${order.customer.fullName}","${order.customer.email}","${order.customer.phone}","${order.customer.address}, ${order.customer.city}","${order.total}","${order.status}","${order.payment}"\n`;
    });

    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `orders_${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
}

// Lấy thống kê đơn hàng
function getOrderStats() {
    const orders = getOrders();
    const stats = {
        total: orders.length,
        pending: orders.filter(o => o.status === "Chờ xác nhận").length,
        confirmed: orders.filter(o => o.status === "Đã xác nhận").length,
        shipping: orders.filter(o => o.status === "Đang giao").length,
        delivered: orders.filter(o => o.status === "Đã giao").length,
        cancelled: orders.filter(o => o.status === "Hủy").length,
        revenue: orders.filter(o => o.status === "Đã giao").reduce((sum, o) => sum + o.total, 0)
    };
    return stats;
}

// Hiển thị thống kê
function displayOrderStats() {
    const stats = getOrderStats();
    const statsContainer = document.getElementById("orderStats");

    if (!statsContainer) return;

    statsContainer.innerHTML = `
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
            <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; text-align: center;">
                <div style="font-size: 28px; color: #2196f3; font-weight: 700;">${stats.total}</div>
                <div>Tổng đơn hàng</div>
            </div>
            <div style="background: #fff3e0; padding: 20px; border-radius: 8px; text-align: center;">
                <div style="font-size: 28px; color: #ff9800; font-weight: 700;">${stats.pending}</div>
                <div>Chờ xác nhận</div>
            </div>
            <div style="background: #e8f5e9; padding: 20px; border-radius: 8px; text-align: center;">
                <div style="font-size: 28px; color: #4caf50; font-weight: 700;">${stats.delivered}</div>
                <div>Đã giao</div>
            </div>
            <div style="background: #fce4ec; padding: 20px; border-radius: 8px; text-align: center;">
                <div style="font-size: 28px; color: #e91e63; font-weight: 700;">${stats.revenue.toLocaleString()}₫</div>
                <div>Doanh thu</div>
            </div>
        </div>
    `;
}
