<?php
    session_start();
    include "config.php";
    $conn = ketnoi();
    $error = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tendn = $_POST['tendn'];
        $matkhau = $_POST['matkhau'];
        $sql = "SELECT tendn, matkhau, quyen FROM user WHERE tendn = '$tendn'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_row($result);
            if ($matkhau == $row[1]) {
                $_SESSION['tendn'] = $row[0];
                $_SESSION['matkhau'] = $row[1];
                $_SESSION['quyen'] = $row[2];
                header("Location: trangchu.php"); 
                exit();
            } else {
                $error = "Mật khẩu không chính xác.";
            }
        } else {
            $error = "Tên đăng nhập không tồn tại.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
</head>
<body>
    <form action="" method="post">
        <input type="text" name="tendn" id="tendn" placeholder="Tên đăng nhập!"> <br> <br>
        <input type="text" name="matkhau" id="matkhau" placeholder="Mật khẩu!"> <br> <br>
        <button type="submit">Đăng nhập</button>
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
    </form>
</body>
</html>
