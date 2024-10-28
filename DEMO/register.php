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
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        #success-message {
            color: green;
            text-align: center;
            margin-top: 10px;
        }

        #error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
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
        <h2>Đăng Ký</h2>
        <form id="registerForm" action="register.php" method="POST">
            <label for="register-username">Tên đăng nhập:</label>
            <input type="text" id="register-username" name="username" required>
            <label for="register-email">Email:</label>
            <input type="email" id="register-email" name="email" required>
            <label for="register-password">Mật khẩu:</label>
            <input type="password" id="register-password" name="password" required>
            <button type="submit">Đăng Ký</button>
            <p id="success-message"></p>
            <p id="error-message"></p>
            <p>Đã có tài khoản? <a href="index.php">Đăng nhập</a></p>
        </form>
    </div>

    <?php
    session_start(); // Bắt đầu session nếu chưa có

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        include_once("connect.php");

        if ($conn->connect_error) {
            die("Kết nối không thành công: " . $conn->connect_error);
        }

        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role_id = null;

        if (strpos($username, 'ad_') === 0) {
            $role_id = 1;
        } elseif (strpos($username, 'gv_') === 0) {
            $role_id = 2;
        } elseif (strpos($username, 'sv_') === 0) {
            $role_id = 3;
        } else {
            $_SESSION['error_message'] = "Tên đăng nhập không hợp lệ.";
            header("Location: register.php");
            exit();
        }

        $checkEmail = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $result = $checkEmail->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['error_message'] = "Email đã được đăng ký!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $username, $email, $password, $role_id);

            if ($stmt->execute()) {
                if (strpos($username, 'ad_') === 0) {
                    $redirect_url = 'trangchu.php';
                } elseif (strpos($username, 'gv_') === 0) {
                    $redirect_url = '/DEMO/giangvien/trangchu.php';
                } elseif (strpos($username, 'sv_') === 0) {
                    $redirect_url = '/DEMO/sinhvien/trangchu.php';
                }

                $_SESSION['success_message'] = "Đăng ký thành công!";
                echo "<div id='successMessage' class='alert alert-success' style='text-align: center; border: 2px solid #b5e0b5; background-color: #eaf7ea; padding: 20px; border-radius: 10px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);'>
                <p>" . htmlspecialchars($_SESSION['success_message']) . "</p>
            </div>
            <script>
                setTimeout(function() {
                    document.getElementById('successMessage').style.display = 'none';
                    window.location.href = '$redirect_url';
                }, 1000);
            </script>";
                exit();
            } else {
                if ($stmt->errno == 1062) {
                    $_SESSION['error_message'] = "Email đã được đăng ký. Vui lòng chọn email khác.";
                    header("Location: register.php");
                    exit();
                } else {
                    echo "<script>alert('Đăng ký thất bại: " . $stmt->error . "');</script>";
                }
            }

            $stmt->close();
        }

        $checkEmail->close();
        $conn->close();
    }

    if (isset($_SESSION['error_message'])) {
        echo "<div id='errorMessage' class='alert alert-danger' style='text-align: center; border: 2px solid pink; background-color: #f8d7da; padding: 20px; border-radius: 10px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);'>
        <p>" . htmlspecialchars($_SESSION['error_message']) . "</p>
    </div>
    <script>
        setTimeout(function() {
            document.getElementById('errorMessage').style.display = 'none';
        }, 1000);
    </script>";
        unset($_SESSION['error_message']);
    }
    ?>
</body>

</html>