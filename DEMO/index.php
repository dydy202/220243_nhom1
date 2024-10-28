<?php
session_start(); // Bắt đầu phiên làm việc

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once("connect.php"); // Kết nối đến cơ sở dữ liệu

    // Lấy thông tin đăng nhập từ biểu mẫu
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra xem tên đăng nhập có bắt đầu bằng "ad_", "gv_", hoặc "sv_"
    if (strpos($username, 'ad_') === 0) {
        // Nếu username bắt đầu bằng "ad_", xác định là admin
        $role = 'admin';
        $sql = "SELECT u.id, u.username, u.email, u.password 
                FROM users u
                WHERE u.username = ? AND u.role_id = (SELECT role_id FROM roles WHERE role_name = 'admin')";
    } elseif (strpos($username, 'gv_') === 0) {
        // Nếu username bắt đầu bằng "gv_", xác định là giảng viên
        $role = 'giangvien';
        $sql = "SELECT u.id, u.username, u.email, u.password 
                FROM users u
                WHERE u.username = ? AND u.role_id = (SELECT role_id FROM roles WHERE role_name = 'Giảng viên')";
    } elseif (strpos($username, 'sv_') === 0) {
        // Nếu username bắt đầu bằng "sv_", xác định là sinh viên
        $role = 'sinhvien';
        $sql = "SELECT u.id, u.username, u.email, u.password 
                FROM users u
                WHERE u.username = ? AND u.role_id = (SELECT role_id FROM roles WHERE role_name = 'Sinh viên')";
    } else {
        // Lưu thông báo lỗi vào session
        $_SESSION['error_message'] = "Tên đăng nhập không hợp lệ!";
        header("Location: index.php"); // Chuyển hướng trở lại index.php
        exit();
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Lấy thông tin người dùng
        $user = $result->fetch_assoc();

        // So sánh mật khẩu
        if (password_verify($password, $user['password'])) {
            // Đăng nhập thành công
            $_SESSION['user'] = $user; // Lưu thông tin người dùng vào session
            $_SESSION['username'] = $username; // Lưu tên người dùng vào session
            $_SESSION['success_message'] = "Đăng nhập thành công!"; // Lưu thông báo thành công vào session

            // Chuyển hướng dựa trên vai trò
            if (strpos($username, 'ad_') === 0) {
                // Nếu là Admin
                $redirect_url = 'trangchu.php'; // Trang chủ cho Admin
            } elseif (strpos($username, 'gv_') === 0) {
                // Nếu là Giảng viên
                $redirect_url = '/DEMO/giangvien/trangchu.php'; // Trang chủ cho Giảng viên
            } elseif (strpos($username, 'sv_') === 0) {
                // Nếu là Sinh viên
                $redirect_url = '/DEMO/sinhvien/trangchu.php'; // Trang chủ cho Sinh viên
            }

            // Hiển thị thông báo thành công và tự động chuyển hướng
            echo "<div id='successMessage' class='alert alert-success'
                    style='text-align: center; border: 2px solid #b5e0b5; background-color: #eaf7ea; padding: 20px; border-radius: 10px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);'>
                    <p>" . htmlspecialchars($_SESSION['success_message']) . "</p>
                </div>
                <script>
                    setTimeout(function() {
                        document.getElementById('successMessage').style.display = 'none';
                        window.location.href = '$redirect_url'; // Chuyển hướng đến trang chủ
                    }, 1000); // Chờ 1 giây trước khi chuyển hướng
                </script>";
            exit();
        } else {
            $_SESSION['error_message'] = "Sai mật khẩu!";
            header("Location: index.php"); // Chu yển hướng trở lại index.php
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Tên đăng nhập không tồn tại!";
        header("Location: index.php"); // Chuyển hướng trở lại index.php
        exit();
    }
}
?>

<!-- Hiển thị thông báo lỗi hoặc thành công nếu có -->
<?php if (isset($_SESSION['error_message'])): ?>
<div id="accessDeniedMessage" class="alert alert-danger"
    style="text-align: center; border: 2px solid #f3c6cb; background-color: #f9d6d8; padding: 20px; border-radius: 10px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
    <p><?php echo htmlspecialchars($_SESSION['error_message']); ?></p>
</div>
<script>
// Ẩn thông báo sau 1 giây
setTimeout(function() {
    document.getElementById("accessDeniedMessage").style.display = "none";
}, 1000); // 1 giây trước khi ẩn
</script>
<?php unset($_SESSION['error_message']); // Xóa thông báo sau khi hiển thị 
    ?>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Quản Lý Lớp Học và Sinh Viên</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 400px;
        margin: 100px auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h2 {
        text-align: center;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    input {
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    button {
        padding: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3;
    }

    a {
        color: #007bff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Đăng Nhập</h2>
        <form id="loginForm" action="index.php" method="POST">
            <label for="login-username">Tên đăng nhập:</label>
            <input type="text" id="login-username" name="username" required>
            <label for="login-password">Mật khẩu:</label>
            <input type="password" id="login-password" name="password" required>
            <p><a href="forgot_password.php">Quên mật khẩu?</a></p>
            <button type="submit">Đăng Nhập</button>
            <p id="login-error-message"></p>
            <p>Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
        </form>
    </div>
</body>

</html>