<?php
    session_start();
    include "config.php";
    $conn = ketnoi();

    // Kiểm tra xem người dùng có đăng nhập không
    $tendn = $_SESSION['tendn'] ?? null;
    $hoten = "Tài Khoản"; // Mặc định
    $anh = "https://cdn-icons-png.flaticon.com/512/456/456212.png"; // Icon mặc định

    if ($tendn) {
        $sql = "SELECT hoten, anh FROM user WHERE tendn = '$tendn'";
        $result = mysqli_query($conn, $sql);
        if ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['hoten'])) {
                $hoten = $row['hoten'];
            }
            if (!empty($row['anh'])) {
                $anh = $row['anh']; // Lấy ảnh từ database
            }
        }
    }

    // Xử lý đăng xuất (nếu được xác nhận)
    if (isset($_GET['confirm_logout']) && $_GET['confirm_logout'] == "true") {
        session_destroy();  // Xóa tất cả session
        header("Location: trangchu.php"); // Chuyển hướng về trang chủ (trạng thái chưa đăng nhập)
        exit();
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Bán Hàng</title>
    <link rel="icon" type="image/png" href="picture/logoTD.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            margin: 0;
            box-sizing: border-box;
            font-family: 'Inter', Helvetica, Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Đảm bảo chiều cao tối thiểu bằng 100% màn hình */
        }

        /* Thanh header */
        .header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            text-align: right;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
        }

        /* Nút Map */
        .map-button {
            background: transparent;
            border: none;
            font-size: 16px;
            color: #007bff;
            cursor: pointer;
            transition: 0.3s;
        }

        .map-button:hover {
            color: #0056b3;
        }
        /* Thanh nav */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #ffffff;
            padding: 10px 20px;
            height: 55px;;
            border-bottom: 1px solid #ddd;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        .logo img {
            height: 60px; /* Điều chỉnh kích thước logo */
            width: auto;
            object-fit: contain;
            margin-right: 15px;
        }
        .store-name {
            font-size: 38px;
            font-weight: bold;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            background: linear-gradient(45deg, #4facfe, #6a11cb, #34e89e);
            background-clip: text; /* Dùng chuẩn mới */
            -webkit-background-clip: text; /* Dành cho Chrome, Safari */
            -webkit-text-fill-color: transparent; /* Bắt buộc để hiển thị màu gradient */
            text-shadow: 4px 4px 12px rgba(106, 17, 203, 0.5);
            letter-spacing: 2px;
            animation: glow 1.5s infinite alternate;
        }
        @keyframes glow {
            0% { text-shadow: 4px 4px 12px rgba(106, 17, 203, 0.4); }
            100% { text-shadow: 4px 4px 20px rgba(52, 232, 158, 0.8); }
        }
        /* Thiết lập chung cho thanh tìm kiếm */
        .search-bar {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 5px;
            max-width: 550px;
            width: 100%;
            margin: 20px auto;
        }
        /* Ô nhập liệu */
        .search-bar input {
            flex: 1;
            padding: 10px 12px;
            border: none;
            font-size: 16px;
            outline: none;
            background: #ffffff;
        }
        /* Nút Micro */
        .mic-button {
            padding: 8px;
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #007bff;
            margin: 0 8px;
            transition: 0.2s;
        }
        .mic-button:hover {
            color: #0056b3;
        }
        .mic-button.listening {
            color: red;
            animation: shake  1s infinite;
        }
        .mic-button.listening .mic-animation {
            opacity: 1;
            animation: pulse 1s infinite;
        }
        .hidden {
            display: none;
        }
        /* Vòng tròn hiệu ứng */
        .mic-animation {
            position: absolute;
            width: 40px;
            height: 40px;
            background: rgba(255, 0, 0, 0.3);
            border-radius: 50%;
            opacity: 0;
            transform: scale(1);
            transition: all 0.3s ease-in-out;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        /* Hiệu ứng rung nhẹ */
        @keyframes shake {
            0% { transform: rotate(0); }
            25% { transform: rotate(-5deg); }
            50% { transform: rotate(5deg); }
            75% { transform: rotate(-5deg); }
            100% { transform: rotate(0); }
        }
        /* Nút Tìm kiếm */
        .search-button {
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .search-button:hover {
            background: #0056b3;
        }
        .nav-icons {
            display: flex;
            align-items: center;
        }
        .nav-icons a {
            margin-left: 20px;
            text-decoration: none;
            color: #333;
            font-size: 16px;
            display: flex;
            align-items: center;
            transition: 0.3s;
            padding-right: 15px;
        }
        .nav-icons a:hover {
            color: #007bff;
        }
        .nav-icons a i {
            margin-right: 5px;
            font-size: 18px;
        }
        /* Thanh menu */
        .menu-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            background: #e3f2fd; /* Xanh dương nhạt */
            padding: 8px 15px;
            height: 40px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-bottom: 2px solid #64b5f6;
        }

        /* Tài khoản */
        .account {
            display: flex;
            align-items: center;
            padding: 5px 12px;
            background: #90caf9; /* Nền xanh pastel */
            border-radius: 50px;
            cursor: pointer;
            transition: 0.3s;
            border: 2px solid #42a5f5; /* Viền xanh dương */
        }

        .account:hover {
            background: #64b5f6;
        }

        .account img {
            width: 20px; /* Thu nhỏ avatar */
            height: 20px;
            border-radius: 50%;
            margin-right: 8px;
            border: 2px solid white;
            padding: 2px;
            background: white;
        }

        .account span {
            color: white;
            font-weight: bold;
            font-size: 14px;
        }

        /* Nút đăng nhập */
        .login-btn {
            background: #42a5f5;
            color: white;
            padding: 6px 12px; /* Thu nhỏ nút */
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            margin-left: 25px;
            transition: 0.3s;
            font-size: 14px;
            box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.15);
        }

        .login-btn:hover {
            background: #1e88e5;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }

        /* Icon user khi chưa đăng nhập */
        .user-icon {
            width: 100%;
            height: 100%;
        }

         /* Nút */
         .btn {
            display: flex;
            align-items: center;
            background: #42a5f5;
            color: white;
            padding: 6px 14px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            margin-left: 12px;
            transition: 0.3s;
            font-size: 14px;
            box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.15);
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #1e88e5;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }

        .btn i {
            font-size: 16px;
            margin-right: 6px;
        }

        .logout-btn {
            background: #e53935;
        }

        .logout-btn:hover {
            background: #c62828;
        }

        /* Bố cục chính */
        .main-container {
            display: flex;
            flex-grow: 1;
        }
        /* Khung danh mục bên trái */
        .category-container {
            width: 250px;
            background-color: #f8f9fa;
            padding: 15px;
            border-right: 1px solid #ddd;
            height: 470px; /* Đặt chiều cao cố định */
            overflow-y: auto; /* Tự động hiển thị thanh cuộn nếu nội dung quá dài */
        }
        /* Tùy chỉnh thanh cuộn */
        .category-container::-webkit-scrollbar {
            width: 8px;
        }
        .category-container::-webkit-scrollbar-thumb {
            background-color: #007bff;
            border-radius: 10px;
        }
        .category-container::-webkit-scrollbar-track {
            background-color: #f1f1f1;
        }
        .category-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .category-list a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            border-radius: 4px;
        }
        .category-list a:hover {
            background-color: #007bff;
            color: white;
        }
        /* Danh mục con */
        .sub-category {
            display: none; /* Ẩn danh mục con mặc định */
            padding-left: 15px;
        }
        .sub-category a {
            font-size: 14px;
            padding: 8px;
            display: block;
            text-decoration: none;
            color: #555;
        }
        .sub-category a:hover {
            background-color: #cce9f1;
        }
        
        /* Footer */
        .footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 14px;
        }
        /* Nội dung */
        .content-container {
            flex: 1;
            padding: 20px;
        }
        .content-title {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .product-list-container {
                max-height: 400px;
                overflow-y: auto;
                border: 1px solid #ddd;
                padding: 10px;
                border-radius: 5px;
            }
        .product-list {
            display: flex;
                gap: 15px;
                flex-wrap: wrap;
}
/* Mỗi sản phẩm chiếm 20% chiều rộng container (5 sp trên 1 dòng) */
.product-item {
    flex: 0 0 calc(20% - 10px);
    max-width: calc(20% - 10px);
    background: #fff;
    padding: 10px;
    text-align: center;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}
.product-item img {
    width: 100%;
    height: 100px; /* Giảm kích thước ảnh */
    object-fit: cover;
    border-radius: 5px;
}
.product-item h3 {
    font-size: 12px; /* Giảm kích thước tiêu đề */
    margin: 5px 0;
}
        .product-item p {
            display: -webkit-box;
            -webkit-line-clamp: 2; /* Chỉ hiển thị 2 dòng */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-height: 3em; /* Điều chỉnh chiều cao tương ứng với số dòng */
            line-height: 1.5em; /* Căn chỉnh chiều cao dòng */
            color: #666; /* Màu chữ */
            font-size: 11px;
}

        .product-buttons {
            display: flex;
    justify-content: center;
    align-items: center;
    gap: 5px;
        }
        .product-item:hover {
    transform: scale(1.05); /* Hiệu ứng phóng to nhẹ khi hover */
}
        .cart-btn, .wishlist-btn {
            background: none;
            border: none;
            font-size: 12px;
            cursor: pointer;
            padding: 4px;
            transition: transform 0.2s, color 0.2s;
        }

        /* Màu khi hover */
        .cart-btn:hover {
            color: blue;
            transform: scale(1.2);
        }

        .wishlist-btn:hover {
            color: red;
            transform: scale(1.2);
        }

        .divider {
            font-size: 12px;
            color: #aaa;
        }   

        .highlight {
            display: inline-block;
            position: relative;
            font-weight: bold;
            color: red;
            text-transform: uppercase;
        }

        .fa-fire {
            font-size: 30px;
            color: red;
            text-shadow: 0 0 1px rgba(255, 165, 0, 0.8);
            animation: fireEffect 1s infinite alternate;
        }

        /* Hiệu ứng lửa nâng cao */
        @keyframes fireEffect {
            0% { color: red; text-shadow: 0 0 5px rgba(255, 69, 0, 0.8); transform: scale(1); }
            50% { color: orange; text-shadow: 0 0 10px rgba(255, 140, 0, 0.9); transform: scale(1.1); }
            100% { color: yellow; text-shadow: 0 0 15px rgba(255, 215, 0, 1); transform: scale(1); }
        }


    </style>
</head>
<body>
    <!-- Thanh địa chỉ -->
    <div class="header">
        Địa chỉ: <span id="address-text">123 Đường ABC, Quận XYZ, TP.HCM</span>
        <button class="map-button"><i class="fas fa-map-marker-alt"></i></button>
    </div>
    
    <script>
        document.querySelector(".map-button").addEventListener("click", function () {
            window.location.href = "#"; // Thay bằng URL trang nhập địa chỉ của bạn
        });
    </script>
    <!-- Thanh điều hướng -->
    <div class="navbar">
        <div class="logo">
            <img src="picture/logoMK.png" alt="logo">
        </div>
        <div class="store-name">M'K Store</div>
        <div class="search-bar">
            <input id="search-input" type="text" placeholder="Nhập sản phẩm cần tìm...">
            <span id="listening-text" class="hidden">Đang nghe...</span>
            <button class="mic-button" id="mic-button"><i class="fas fa-microphone"></i></button>
            <button class="search-button"><i class="fas fa-search"></i> Tìm kiếm</button>
        </div>        
        <div class="nav-icons"> 
            <a href="#"><i class="fas fa-home"></i>Trang chủ</a>
            <a href="#"><i class="fas fa-shopping-cart"></i>Giỏ hàng</a>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.getElementById("search-input");
            const micButton = document.getElementById("mic-button");
            const listeningText = document.getElementById("listening-text");

            if (!("webkitSpeechRecognition" in window)) {
                alert("Trình duyệt của bạn không hỗ trợ tìm kiếm bằng giọng nói.");
                return;
            }

            const recognition = new webkitSpeechRecognition();
            recognition.lang = "vi-VN"; // Ngôn ngữ tiếng Việt
            recognition.continuous = false;
            recognition.interimResults = false;

            micButton.addEventListener("click", () => {
                recognition.start();
                micButton.classList.add("listening");
                listeningText.classList.remove("hidden");
            });

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                searchInput.value = transcript;
                stopListening();
            };

            recognition.onerror = (event) => {
                console.error("Lỗi nhận diện giọng nói:", event.error);
                stopListening();
            };

            recognition.onend = () => {
                stopListening();
            };

            function stopListening() {
                micButton.classList.remove("listening");
                listeningText.classList.add("hidden");
            }
        });
    </script>
    <script>
        const placeholderTexts = [
            "Nhập sản phẩm cần tìm...",
            "Tìm kiếm laptop...",
            "Tìm kiếm điện thoại...",
            "Tìm kiếm phụ kiện..."
        ];

        let index = 0;
        const searchInput = document.getElementById("search-input");

        function changePlaceholder() {
            searchInput.setAttribute("placeholder", placeholderTexts[index]);
            index = (index + 1) % placeholderTexts.length;
        }
        setInterval(changePlaceholder, 3000); // Thay đổi mỗi 3 giây
    </script>

<div class="menu-bar">
        <!-- Tài khoản -->
        <div class="account">
            <img src="<?php echo $anh; ?>" alt="Avatar">
            <span><?php echo $hoten; ?></span>
        </div>

        <!-- Nếu chưa đăng nhập, hiển thị nút đăng nhập -->
        <?php if (!$tendn) { ?>
            <a href="dangnhap.php" class="btn">
                <i class="fas fa-sign-in-alt"></i> Đăng nhập
            </a>
        <?php } else { ?>
            <!-- Nếu đã đăng nhập, hiển thị nút đăng xuất -->
            <button class="btn logout-btn" onclick="confirmLogout()">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </button>
        <?php } ?>
    </div>

<script>
    function confirmLogout() {
        let confirmAction = confirm("Bạn có chắc chắn muốn đăng xuất không?");
        if (confirmAction) {
            window.location.href = "?confirm_logout=true";
        }
    }
</script>

    <!-- Bố cục chính -->
    <div class="main-container">
        <!-- Khung danh mục bên trái -->
        <div class="category-container">
            <div class="category-title">Danh mục sản phẩm</div>
            <div class="category-list">
                <a href="javascript:void(0);" onclick="loadProductsByCategory(4);">🧸 Gấu bông</a>
                 <!-- Danh mục có xổ xuống -->
                <a href="javascript:void(0);" onclick="toggleSubMenu('submenu-balo')">🎒 Balo, Túi, Ví</a>
                <div class="sub-category" id="submenu-balo">
                    <a href="javascript:void(0);" onclick="loadProductsByCategory(5);">👜 Balo thời trang Nam nữ</a>
                    <a href="javascript:void(0);" onclick="loadProductsByCategory(6);">🎒 Túi Nam Nữ</a>
                    <a href="javascript:void(0);" onclick="loadProductsByCategory(7);">💼 Ví thời trang cao cấp</a>
                </div>
                <a href="javascript:void(0);" onclick="toggleSubMenu('submenu-watch')">⌚ Đồng hồ</a>
                <div class="sub-category" id="submenu-watch">
                    <a href="javascript:void(0);" onclick="loadProductsByCategory(8);">⌚ Đồng hồ Nam Nữ</a>
                    <a href="javascript:void(0);" onclick="loadProductsByCategory(9);">🏆 Đồng hồ Thông minh</a>
                </div>
                <a href="javascript:void(0);" onclick="toggleSubMenu('submenu-makeup')">💅 Trang điểm</a>
                <div class="sub-category" id="submenu-makeup">
                    <a href="javascript:void(0);" onclick="loadProductsByCategory(10);">🧴 Dành cho da</a>
                    <a href="javascript:void(0);" onclick="loadProductsByCategory(11);">👁 Dành cho mắt</a>
                    <a href="javascript:void(0);" onclick="loadProductsByCategory(12);">💄 Dành cho môi</a>
                    <a href="javascript:void(0);" onclick="loadProductsByCategory(13);">🖌 Dụng cụ trang điểm</a>
                </div>
                <a href="javascript:void(0);" onclick="toggleSubMenu('submenu-decoration')">🎀 Trang trí</a>
                <div class="sub-category" id="submenu-decoration">
                    <a href="javascript:void(0);" onclick="loadProductsByCategory(14);">🏵 Vòng/Lắc tay chân</a>
                    <a href="javascript:void(0);" onclick="loadProductsByCategory(15);">📿 Vòng cổ</a>
                    <a href="javascript:void(0);" onclick="loadProductsByCategory(16);">💍 Nhẫn</a>
                    <a href="javascript:void(0);" onclick="loadProductsByCategory(17);">🌸 Bông tai</a>
                </div>
                <a href="#">🚀 Khác</a>
            </div>
        </div>
        <!-- Nội dung chính -->
        <div class="content-container">
            <h2 class="content-title">Danh sách sản phẩm <span class="highlight"><i class="fa-solid fa-fire"></i> NEWS</span></h2>
            <div class="product-list-container">
            <div class="product-list" id="product-list">
                <!-- Dữ liệu sản phẩm sẽ được hiển thị ở đây -->
                
            </div>
        </div>
        </div>
    </div>
    <script>
        // Hàm cập nhật sản phẩm
function updateProduct(productId) {
    let newName = prompt("Nhập tên sản phẩm mới:");
    if (newName) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "update_product.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText);
                location.reload(); // Tải lại trang sau khi cập nhật
            }
        };
        xhr.send("id=" + productId + "&name=" + encodeURIComponent(newName));
    }
}

// Hàm xóa sản phẩm
function deleteProduct(productId, categoryId) {
    // Hiển thị hộp thoại xác nhận
    let confirmDelete = confirm("Bạn có chắc chắn muốn xóa sản phẩm này?");
    
    if (confirmDelete) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_product.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText);
                loadProductsByCategory(categoryId);
            }
        };
        xhr.send("id=" + productId);
        
    } else {
        alert("Hủy xóa sản phẩm!"); // Thông báo nếu người dùng chọn "Hủy"
    }
}

    </script>
    <script>
        function loadProductsByCategory(categoryId) {
            let xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_products.php?category=" + categoryId, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById("product-list").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
    </script>
    <!-- JavaScript để xử lý xổ xuống -->
    <script>
        function toggleSubMenu(menuId) {
            var submenu = document.getElementById(menuId);
            if (submenu.style.display === "block") {
                submenu.style.display = "none";
            } else {
                submenu.style.display = "block";
            }
        }
    </script>
   
    <!-- Footer -->
    <div class="footer">
        &copy; 2025 Tên Cửa Hàng. Mọi quyền được bảo lưu.
    </div>
</body>
</html>
