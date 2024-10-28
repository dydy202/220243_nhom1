<?php if (isset($_SESSION['message'])): ?>
<div id="messageBox" class="alert alert-danger"
    style="text-align: center; border: 2px solid pink; background-color: #f8d7da; padding: 20px; border-radius: 10px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
    <p><?php echo htmlspecialchars($_SESSION['message']); ?></p>
</div>
<script>
// Ẩn thông báo sau 1 giây
setTimeout(function() {
    document.getElementById("messageBox").style.display = "none";
}, 1000); // 1 giây trước khi ẩn
</script>
<?php unset($_SESSION['message']); // Xóa thông báo sau khi hiển thị 
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

    .dashboard-container {
        max-width: 600px;
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
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #218838;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    #error-message {
        color: red;
        text-align: center;
    }

    /* CSS cho nút Đăng nhập */
    #loginForm button {
        background-color: #007bff;
        /* Màu nền xanh dương */
        color: white;
        /* Màu chữ */
        border: none;
        border-radius: 4px;
        padding: 10px;
        cursor: pointer;
    }

    #loginForm button:hover {
        background-color: #0056b3;
        /* Màu nền khi hover (xanh đậm hơn) */
    }

    a {
        color: #007bff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    .back-button {
        margin-top: 10px;
        text-align: center;
        /* Căn giữa cho nút trở về */
    }
    </style>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container" id="login-container">
            <h2>Quên mật khẩu</h2>
            <form action="xuly_checkemail.php" method="POST">
                <label for="email">Nhập email của bạn:</label>
                <input type="email" id="email" name="email" required>
                <button type="submit">Gửi liên kết đặt lại mật khẩu</button>
            </form>
        </div>
        <div class="back-button">
            <a href="index.php">Trở về trang đăng nhập</a>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>