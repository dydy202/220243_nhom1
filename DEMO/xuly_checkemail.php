<?php
session_start();
include_once("connect.php");
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $email = $user['email'];
        $token = bin2hex(random_bytes(50));
        $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@gmail.com';
            $mail->Password = 'your_app_password';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your_email@gmail.com', 'Quản Lý Lớp Học');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Đặt lại mật khẩu';
            $mail->Body = 'Nhấp vào liên kết để đặt lại mật khẩu: <a href="http://your_website.com/reset_password.php?token=' . $token . '">Đặt lại mật khẩu</a>';

            $mail->send();
            $_SESSION['message'] = "Email đặt lại mật khẩu đã được gửi.";
        } catch (Exception $e) {
            $_SESSION['message'] = "Không thể gửi email: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['message'] = "Email không tồn tại.";
    }

    $stmt->close();
    $conn->close();

    header("Location: forgot_password.php");
    exit();
}
