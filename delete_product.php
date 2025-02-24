<?php
include 'config.php'; // Kết nối CSDL
$conn = ketnoi();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);

    $sql = "DELETE FROM sanpham WHERE idsp = $id";
    if (mysqli_query($conn, $sql)) {
        echo "Xóa sản phẩm thành công!";
        
    } else {
        echo "Lỗi khi xóa: " . mysqli_error($conn);
    }
}
?>
