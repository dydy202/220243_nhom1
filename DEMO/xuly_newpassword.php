<?php
// Kết nối với cơ sở dữ liệu
include_once("connect.php");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy token từ URL
$token = $_GET['token'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy mật khẩu mới từ form
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Băm mật khẩu mới

    // Cập nhật mật khẩu trong cơ sở dữ liệu dựa vào token
    $sql = "UPDATE users SET password='$new_password', reset_token=NULL, reset_token_expire=NULL WHERE reset_token='$token'";
    if ($conn->query($sql) === TRUE) {
        echo "Mật khẩu của bạn đã được đặt lại thành công.";
    } else {
        echo "Có lỗi xảy ra khi đặt lại mật khẩu.";
    }

    $conn->close();
    exit();
}

// Kiểm tra xem token có hợp lệ không
$sql = "SELECT * FROM users WHERE reset_token='$token' AND reset_token_expire > NOW()";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Hiển thị form đặt lại mật khẩu
    echo '
        <h2>Đặt lại mật khẩu</h2>
        <form method="POST">
            <label for="password">Nhập mật khẩu mới:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Đặt lại mật khẩu</button>
        </form>
    ';
} else {
    echo "Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.";
}

$conn->close();
?>
