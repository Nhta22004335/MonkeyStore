<?php
function ketnoi()
{
    $servername = "localhost";
    $username = "root"; 
    $password = "";
    $dbname = "qlbanpk";
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) die("Kết nối thất bại: " .  mysqli_connect_error());
    return $conn;
}
?>