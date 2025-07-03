<?php
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
$mail->SMTPDebug = SMTP::DEBUG_SERVER; // Show detailed debug output

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'v.gugan16@gmail.com'; // Replace with your email
    $mail->Password   = 'vdlz mwgd repb gqyt'; // Replace with app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('adeepakkumar717@gmail.com', 'Test Sender');
    $mail->addAddress('adeepakkumar26@gmail.com', 'Test Recipient');

    $mail->isHTML(true);
    $mail->Subject = 'SMTP Test Email';
    $mail->Body    = 'This is a <b>test email</b> from PHPMailer.';

    $mail->send();
    echo '✅ Email sent successfully!';
} catch (Exception $e) {
    echo "❌ Error: {$mail->ErrorInfo}";
}