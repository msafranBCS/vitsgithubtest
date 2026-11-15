
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // $name = trim($_POST['name'] ?? '');
    // $email = trim($_POST['email'] ?? '');
    // $subject = trim($_POST['subject'] ?? '');
    // $message = trim($_POST['message'] ?? '');

    // Validate user inputs
    // if (
    //     empty($name) ||
    //     empty($email) ||
    //     empty($subject) ||
    //     empty($message) ||
    //     !filter_var($email, FILTER_VALIDATE_EMAIL)
    // ) {
    //     exit("Please enter valid details.");
    // }

    $mail = new PHPMailer(true);

    try {

        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Your Gmail address
        $mail->Username = 'msafran066@gmail.com';

        // Your Google App Password
        $mail->Password = 'umet ojyx kntr fnve';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender details
        $mail->setFrom(
            'msafran066@gmail.com',
            'My Website'
        );

        // Receiver email
        $mail->addAddress('mrnrfriends@gmail.com');

        // User reply email
        $mail->addReplyTo('ms5904122@gmail.com', 'My Website');

        // Email content
        $mail->isHTML(true);

        $mail->Subject = 'sample subject';

        $mail->Body = "
            <h2>New Website Message</h2>
            <p><strong>Name:</strong> nasik</p>
            <p><strong>Email:</strong> @gmail.com</p>
            <p><strong>Message:</strong></p>
        ";

        $mail->AltBody = "Name: nasik\nEmail: @gmail.com\nMessage: ";

        // Send email
        $mail->send();

        echo "success";

    } catch (Exception $e) {

        error_log("Email sending failed: " . $mail->ErrorInfo);

        echo "error";

    }

// } else {

//     echo "Invalid request.";

// }
