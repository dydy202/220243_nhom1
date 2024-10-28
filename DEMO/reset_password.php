<?php
include_once("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['token'])) {
    $token = $_GET['token'];
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra nếu mật khẩu và xác nhận mật khẩu khớp
    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Cập nhật mật khẩu trong cơ sở dữ liệu và xóa token
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
        $stmt->bind_param("ss", $hashed_password, $token);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo "Mật khẩu đã được đặt lại thành công!";
        } else {
            echo "Token không hợp lệ hoặc đã hết hạn.";
        }

        $stmt->close();
        $conn->close();
        exit();
    } else {
        echo "Mật khẩu không khớp. Vui lòng nhập lại.";
        exit();
    }
} else {
    echo "Yêu cầu không hợp lệ.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Đặt lại mật khẩu</title>
</head>

<body>
    <h2>Đặt lại mật khẩu</h2>
    <form action="reset_password.php" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label for="new_password">Mật khẩu mới:</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Xác nhận mật khẩu:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Đặt lại mật khẩu</button>
    </form>
</body>

</html>