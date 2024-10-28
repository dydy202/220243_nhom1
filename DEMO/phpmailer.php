<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email@gmail.com';
    $mail->Password = 'your_email_password'; // Mật khẩu hoặc mật khẩu ứng dụng
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('your_email@gmail.com', 'Your Name');
    $mail->addAddress($email); // Email của người dùng

    $mail->isHTML(true);
    $mail->Subject = 'Xác nhận đăng ký';
    $mail->Body    = 'Chào ' . htmlspecialchars($username) . ',<br> Cảm ơn bạn đã đăng ký!';

    $mail->send();
    echo 'Email xác nhận đã được gửi';
} catch (Exception $e) {
    echo "Email không thể gửi. Lỗi: {$mail->ErrorInfo}";
}

