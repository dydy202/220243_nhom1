<?php
include_once("connect.php"); // Kết nối cơ sở dữ liệu

// Kiểm tra nếu có dữ liệu được gửi từ biểu mẫu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nhận thông tin từ biểu mẫu
    $maSV = $_POST["txtMa"];
    $hoLot = $_POST["txtHoLot"];
    $tenSV = $_POST["txtTenSV"];
    $ngaySinh = $_POST["txtNgaySinh"];
    $gioiTinh = $_POST["txtGioiTinh"];
    $maLop = $_POST["txtMaLop"];

    // Cập nhật thông tin sinh viên
    $sql = "UPDATE sinhvien SET hoLot = ?, tenSV = ?, ngaySinh = ?, gioiTinh = ?, maLop = ? WHERE maSV = ?";
    $stmt = $conn->prepare($sql);

    // Kiểm tra nếu chuẩn bị truy vấn thất bại
    if ($stmt === false) {
        die("Lỗi chuẩn bị câu lệnh: " . $conn->error);
    }

    $stmt->bind_param("ssssss", $hoLot, $tenSV, $ngaySinh, $gioiTinh, $maLop, $maSV);

    // Thực thi câu lệnh và kiểm tra lỗi
    if ($stmt->execute()) {
        // Nếu cập nhật thành công, lưu thông báo thành công vào session
        session_start();
        $_SESSION['success_message'] = "Thông tin sinh viên đã được cập nhật thành công!";
        $_SESSION['highlighted_student'] = $tenSV;

        // Chuyển hướng đến trang danh sách sinh viên với thông báo thành công
        header("Location: sinhvien.php");
        exit();
    } else {
        // Nếu có lỗi, thông báo lỗi
        die("Cập nhật thông tin sinh viên không thành công: " . $stmt->error);
    }

    $stmt->close();
}

$conn->close(); // Đóng kết nối