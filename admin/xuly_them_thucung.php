<?php
$ten = $_POST["ten"];
$loai = $_POST["loai"];
$giong = $_POST["giong"];
$mota = $_POST["mota"];

// Xử lý ảnh upload
$tenFile = time() . "_" . $_FILES["anh"]["name"];
move_uploaded_file($_FILES["anh"]["tmp_name"], "uploads/" . $tenFile);

// Lưu vào database (demo)
echo "<h3>Thêm thú cưng thành công!</h3>";
echo "<a href='index.php?p=thucung'>Quay lại danh sách</a>";
?>
