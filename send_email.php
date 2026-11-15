
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // SMTP Settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    $mail->Username = 'yourgmail@gmail.com';
    $mail->Password = 'your_16_digit_app_password';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Sender
    $mail->setFrom('yourgmail@gmail.com', 'Your Company');

    // Customer Email
    $mail->addAddress('customer@gmail.com');

    // Email Content
    $mail->isHTML(true);
    $mail->Subject = 'Customer Notification';

    $mail->Body = '
        <h2>Hello Customer!</h2>
        <p>Thank you for choosing our service.</p>
        <p>We appreciate your business.</p>
    ';

    $mail->send();

    echo "Email sent successfully!";

} catch (Exception $e) {
    echo "Email could not be sent: {$mail->ErrorInfo}";
}
