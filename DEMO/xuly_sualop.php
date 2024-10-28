<?php
session_start(); // Bắt đầu session
include_once("connect.php");

// Kiểm tra nếu dữ liệu từ form tồn tại
if (isset($_POST['ma']) && isset($_POST['txtTen']) && isset($_POST['txtGhiChu'])) {
    // Nhận dữ liệu từ form
    $maLop = $_POST['ma'];
    $tenLop = $_POST['txtTen'];
    $ghiChu = $_POST['txtGhiChu'];

    // Sử dụng prepared statement để tránh SQL Injection
    $sql = "UPDATE lophoc SET tenLop = ?, ghiChu = ? WHERE maLop = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $tenLop, $ghiChu, $maLop);

    // Thực thi truy vấn và xử lý kết quả
    if ($stmt->execute()) {
        // Lưu thông báo và mã lớp vào session
        $_SESSION['message'] = "Cập nhật lớp thành công.";
        $_SESSION['msg_type'] = 'success';
        $_SESSION['updated_class'] = $maLop; // Lưu mã lớp đã cập nhật
    } else {
        $_SESSION['message'] = "Lỗi khi cập nhật lớp: " . $stmt->error;
        $_SESSION['msg_type'] = 'danger';
    }

    // Đóng statement
    $stmt->close();
} else {
    $_SESSION['message'] = "Thiếu dữ liệu cần thiết để cập nhật lớp.";
    $_SESSION['msg_type'] = 'danger';
}

// Đóng kết nối và chuyển hướng về trang lophoc.php
$conn->close();
header("Location: lophoc.php");
exit();
