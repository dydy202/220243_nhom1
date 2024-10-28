<?php
session_start(); // Bắt đầu phiên làm việc

$_SESSION = array(); // Xóa tất cả các biến phiên
session_destroy(); // Hủy phiên
header("Location: index.php?message=Đăng xuất thành công"); // Chuyển hướng về trang đăng nhập
exit; // Dừng thực thi mã tiếp theo
?>