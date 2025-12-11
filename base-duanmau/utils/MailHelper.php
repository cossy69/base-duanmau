<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../libraries/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../libraries/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libraries/PHPMailer/src/SMTP.php';

class MailHelper
{
    private static function getMailer()
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'quanganhlast@gmail.com';
        $mail->Password   = 'grpw jviu kpuc mygk';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->setFrom('no-reply@techhub.com', 'Tech Hub Support');
        $mail->isHTML(true);

        return $mail;
    }

    public static function sendVerificationEmail($toEmail, $toName, $token)
    {
        try {
            $mail = self::getMailer();
            $mail->addAddress($toEmail, $toName);

            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $basePath = rtrim(dirname($_SERVER['PHP_SELF'] ?? '/'), '/\\');
            $link = $scheme . '://' . $host . $basePath . "/index.php?class=login&act=verify_account&token=$token";

            $mail->Subject = "Kích hoạt tài khoản Tech Hub";
            $mail->Body    = "
                <h3>Xin chào $toName,</h3>
                <p>Cảm ơn bạn đã đăng ký. Vui lòng click vào link dưới đây để kích hoạt tài khoản:</p>
                <a href='$link' style='padding:10px 20px; background:#0d6efd; color:white; text-decoration:none; border-radius:5px'>Kích hoạt ngay</a>
            ";
            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Lỗi gửi mail: " . $mail->ErrorInfo;
            die();
        }
    }

    public static function sendResetPasswordEmail($toEmail, $token)
    {
        try {
            $mail = self::getMailer();
            $mail->addAddress($toEmail);

            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $basePath = rtrim(dirname($_SERVER['PHP_SELF'] ?? '/'), '/\\');
            $link = $scheme . '://' . $host . $basePath . "/index.php?class=login&act=reset_password&token=$token";

            $mail->Subject = "Yêu cầu đặt lại mật khẩu";
            $mail->Body    = "
                <h3>Yêu cầu đổi mật khẩu</h3>
                <p>Ai đó (có thể là bạn) đã yêu cầu đổi mật khẩu. Click link dưới đây để tạo mật khẩu mới:</p>
                <a href='$link' style='padding:10px 20px; background:#dc3545; color:white; text-decoration:none; border-radius:5px'>Đặt lại mật khẩu</a>
                <p>Link này sẽ hết hạn sau 1 giờ.</p>
            ";
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function sendConfirmationEmail($toEmail, $toName, $orderId, $token)
    {
        try {
            $mail = self::getMailer();
            $mail->addAddress($toEmail, $toName);

            $mail->setFrom('no-reply@techhub.com', 'Tech Hub Admin');

            $confirmLink = "https://techhubstore.io.vn/index.php?class=order&act=confirm_receipt&id=$orderId&token=$token";

            $mail->Subject = "📦 Xác nhận đã nhận đơn hàng #$orderId";
            $mail->Body    = "
                <h3>Xin chào $toName,</h3>
                <p>Đơn hàng <b>#$orderId</b> của bạn đã được giao thành công.</p>
                <p>Vui lòng nhấn vào nút bên dưới để xác nhận đã nhận hàng và đánh giá sản phẩm nhé:</p>
                <p>
                    <a href='$confirmLink' style='background-color: #0066cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>
                        ✅ Đã nhận hàng & Đánh giá
                    </a>
                </p>
                <p>Hoặc click vào link: <a href='$confirmLink'>$confirmLink</a></p>
                <p>Cảm ơn bạn đã mua sắm tại Tech Hub!</p>
            ";

            $mail->send();
            return ['success' => true, 'message' => 'Đã gửi mail thành công'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi gửi mail.'];
        }
    }
}
