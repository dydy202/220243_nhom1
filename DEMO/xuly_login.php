<?php
session_start(); // Khởi động session

include_once("connect.php"); // Kết nối tới cơ sở dữ liệu

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username']; // Lấy tên đăng nhập từ form
    $password = $_POST['password']; // Lấy mật khẩu từ form

    // Truy vấn để kiểm tra tên đăng nhập và mật khẩu
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Đăng nhập thành công
        $user = $result->fetch_assoc();
        $userId = $user['id']; // Lấy ID người dùng từ kết quả truy vấn

        // Lưu thông tin vào session
        $_SESSION['user_id'] = $userId; // Lưu ID người dùng
        $_SESSION['is_logged_in'] = true; // Đánh dấu người dùng đã đăng nhập

        header("Location: dashboard.php"); // Chuyển hướng tới trang quản lý
        exit;
    } else {
        // Đăng nhập thất bại
        $_SESSION['error'] = "Sai tên đăng nhập hoặc mật khẩu!";
        header("Location: index.php"); // Quay lại trang đăng nhập
        exit;
    }
}

$stmt->close();
$conn->close(); // Đóng kết nối
?>