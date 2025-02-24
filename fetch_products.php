<?php
include 'config.php'; // File kết nối CSDL
$conn = ketnoi();
if (isset($_GET['category'])) {
    $categoryId = intval($_GET['category']); // Lấy id danh mục

    $sql = "SELECT idsp, tensp, mota, giaban, soluong, anh, iddm FROM sanpham WHERE iddm = $categoryId ORDER BY thoigianthemsp DESC";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="product-item">';
            echo '<img src="' . $row['anh'] . '">';
            echo '<h3>' . $row['tensp'] . '</h3>';
            echo '<p class="product-description">' . mb_strimwidth($row['mota'], 0, 100, "...") . '</p>';
            echo '<p>' . number_format($row['giaban'], 0, ",", ".") . 'đ</p>';
            echo '<div class="product-buttons">';
            echo '    <button class="update-btn" onclick="updateProduct(' . $row['idsp'] . ')"><i class="fa-solid fa-pen"></i></button>';
            echo '    <span class="divider">|</span>';
            echo '    <button class="delete-btn" onclick="deleteProduct('  . $row['idsp'] . ', ' . $row['iddm'] . ')"><i class="fa-solid fa-trash"></i></button>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "<p>Không có sản phẩm nào trong danh mục này.</p>";
    }
}
?>
