<ul id="main-menu">
    <li class="<?php echo (!isset($_GET['p']) || $_GET['p']=='main') ? 'active':''; ?>">
        <a href="index.php"><i class="fas fa-home"></i> Trang Chủ</a>
    </li>
    <li class="<?php echo (isset($_GET['p']) && $_GET['p']=='thucung') ? 'active':''; ?>">
        <a href="index.php?p=thucung"><i class="fas fa-paw"></i> Thú Cưng</a>
    </li>
    <li class="<?php echo (isset($_GET['p']) && $_GET['p']=='phukien') ? 'active':''; ?>">
        <a href="index.php?p=phukien"><i class="fas fa-box-open"></i> Phụ Kiện</a>
    </li>
    <li class="<?php echo (isset($_GET['p']) && $_GET['p']=='donhang') ? 'active':''; ?>">
        <a href="index.php?p=donhang"><i class="fas fa-shopping-cart"></i> Giỏ Hàng</a>
    </li>
    <li class="<?php echo (isset($_GET['p']) && $_GET['p']=='thongke') ? 'active':''; ?>">
        <a href="index.php?p=thongke"><i class="fas fa-chart-bar"></i> Thống Kê</a>
    </li>
    <li class="<?php echo (isset($_GET['p']) && $_GET['p']=='user') ? 'active':''; ?>">
        <a href="index.php?p=user"><i class="fas fa-cog"></i> Người Dùng</a>
    </li>
</ul>
