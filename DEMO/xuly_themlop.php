<?php
session_start(); // Khởi động phiên

include_once("connect.php"); // Kết nối đến cơ sở dữ liệu

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $maLop = $_POST['maLop'];
    $tenLop = $_POST['tenLop'];
    $ghiChu = $_POST['ghiChu'];

    // Kiểm tra xem lớp đã tồn tại chưa
    $checkSql = "SELECT * FROM lophoc WHERE maLop = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("s", $maLop);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['message'] = "Mã lớp đã tồn tại. Vui lòng nhập mã khác.";
        $_SESSION['msg_type'] = "danger"; // Thiết lập kiểu thông báo là lỗi
    } else {
        // Thêm lớp vào cơ sở dữ liệu
        $sql = "INSERT INTO lophoc (maLop, tenLop, ghiChu, is_deleted) VALUES (?, ?, ?, 0)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $maLop, $tenLop, $ghiChu);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Thêm lớp thành công.";
            $_SESSION['msg_type'] = "success"; // Thiết lập kiểu thông báo là thành công
        } else {
            $_SESSION['message'] = "Có lỗi xảy ra trong quá trình thêm lớp.";
            $_SESSION['msg_type'] = "danger";
        }
    }
    $stmt->close();
    $conn->close();

    // Chuyển hướng về trang danh sách lớp sau khi thêm lớp
    header("Location: lophoc.php");
    exit();
}
?>
