<?php
include_once("connect.php"); // Kết nối cơ sở dữ liệu

// Thực hiện câu lệnh cập nhật vai trò
$sqlTeacher = "UPDATE users SET role = 'teacher' WHERE username = 'giaovien1'";
$sqlStudent = "UPDATE users SET role = 'student' WHERE username = 'sinhvien1'";

// Cập nhật role cho giáo viên
if ($conn->query($sqlTeacher) === TRUE) {
    echo "Cập nhật vai trò cho giáo viên thành công.";
} else {
    echo "Lỗi cập nhật giáo viên: " . $conn->error;
}

// Cập nhật role cho sinh viên
if ($conn->query($sqlStudent) === TRUE) {
    echo "Cập nhật vai trò cho sinh viên thành công.";
} else {
    echo "Lỗi cập nhật sinh viên: " . $conn->error;
}

$conn->close(); // Đóng kết nối
?>