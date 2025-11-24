<?php
include "../config.php";

$id = $_GET['id'];

// Xóa ảnh cũ
$img = $conn->query("SELECT image FROM accessories WHERE id=$id")->fetch_assoc();
if ($img['image'] != "") {
    unlink("../images/" . $img['image']);
}

$conn->query("DELETE FROM accessories WHERE id=$id");

header("Location: index.php?p=phukien");
exit;
?>
