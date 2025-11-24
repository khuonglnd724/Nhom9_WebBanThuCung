<?php
include "../config.php";

$id = $_GET['id'];

$category_id = $_POST['category_id'];
$name = $_POST['name'];
$brand = $_POST['brand'];
$material = $_POST['material'];
$size = $_POST['size'];
$description = $_POST['description'];
$price = $_POST['price'];
$stock = $_POST['stock'];

$imgSql = "";
if (!empty($_FILES['image']['name'])) {
    $newImg = time() . "_" . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "../images/" . $newImg);

    $imgSql = ", image = '$newImg'";
}

$sql = "UPDATE accessories SET 
        category_id='$category_id',
        name='$name',
        brand='$brand',
        material='$material',
        size='$size',
        description='$description',
        price='$price',
        stock='$stock'
        $imgSql,
        updated_at = NOW()
        WHERE id=$id";

$conn->query($sql);

header("Location: index.php?p=phukien");
exit;
?>
