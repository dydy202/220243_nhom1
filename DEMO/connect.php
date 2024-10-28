<?php
$servername = "localhost"; // Thay đổi nếu cần
$username = "root"; // Tên đăng nhập
$password = ""; // Mật khẩu
$dbname = "ql_sinhvien"; // Tên cơ sở dữ liệu

// Kết nối đến cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>