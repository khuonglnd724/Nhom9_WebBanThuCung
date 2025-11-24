<?php
include "../config.php";

$category_id = $_POST['category_id'];
$name = $_POST['name'];
$brand = $_POST['brand'];
$material = $_POST['material'];
$size = $_POST['size'];
$description = $_POST['description'];
$price = $_POST['price'];
$stock = $_POST['stock'];

$imgName = "";
if (!empty($_FILES['image']['name'])) {
    $imgName = time() . "_" . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "../images/" . $imgName);
}

$sql = "INSERT INTO accessories (category_id, name, brand, material, size, description, price, stock, image, status, created_at) 
        VALUES ('$category_id', '$name', '$brand', '$material', '$size', '$description', '$price', '$stock', '$imgName', 'ACTIVE', NOW())";

$conn->query($sql);

header("Location: index.php?p=phukien");
exit;
?>
