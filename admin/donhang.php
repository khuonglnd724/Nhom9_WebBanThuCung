<h2>Quản lý Đơn hàng</h2>

<div id="orderStats"></div>

<div style="margin: 30px 0;">
    <button onclick="exportOrdersToCSV()" class="btn btn-primary">Xuất CSV</button>
    <button onclick="location.reload()" class="btn">Làm mới</button>
</div>

<h3>Danh sách đơn hàng</h3>
<div id="ordersContainer"></div>

<script src="../assets/js/orders.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        displayOrderStats();
        displayOrders();
    });
</script>
