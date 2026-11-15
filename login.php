<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'send-mail/vendor/autoload.php';


require_once 'config/db.php';

$error = "";
$otpStep = isset($_SESSION['pending_user_id']);

// Already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// STEP 1: LOGIN VERIFICATION
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE email = ? LIMIT 1"
    );

    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        // Generate 6 digit OTP
        $otp = (string) random_int(100000, 999999);

        session_regenerate_id(true);

        // Store OTP and temporary user information
        $_SESSION['pending_user_id'] = $user['id'];
        $_SESSION['login_otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + 300;
        $_SESSION['otp_attempts'] = 0;


        // _________________________________________________________

        $mail = new PHPMailer(true);

        try {

            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            // Your Gmail address
            $mail->Username = 'msafran066@gmail.com';

            // Your Google App Password
            $mail->Password = '';

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Sender details
            $mail->setFrom(
                'msafran066@gmail.com',
                'My Website'
            );

            // Receiver email
            $mail->addAddress($email);

            // User reply email
            $mail->addReplyTo('ms5904122@gmail.com', 'My Website');

            // Email content
            $mail->isHTML(true);

            $mail->Subject = 'OTP Verification';

            $mail->Body = "
            <h2>New Website Message</h2>
            <p><strong>Name:</strong> Here is your OTP</p>
            <p><strong>Email:</strong> $otp</p>
            <p><strong>Message:</strong></p>
        ";

            $mail->AltBody = "Name: nasik\nEmail: $otp\nMessage: ";

            // Send email
            $mail->send();

            echo "success";
        } catch (Exception $e) {

            error_log("Email sending failed: " . $mail->ErrorInfo);

            echo "error";
        }
        // _________________________________________________________

        // Move to OTP verification step
        header("Location: login.php");
        exit;
    } else {

        $error = "Invalid email address or password.";
    }
}


// STEP 2: OTP VERIFICATION
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['verify_otp'])) {

    if (!isset($_SESSION['pending_user_id'])) {

        header("Location: login.php");
        exit;
    }

    $enteredOtp = trim($_POST['otp'] ?? '');

    if (time() > $_SESSION['otp_expiry']) {

        $error = "OTP expired. Please login again.";

        unset(
            $_SESSION['pending_user_id'],
            $_SESSION['login_otp'],
            $_SESSION['otp_expiry'],
            $_SESSION['otp_attempts']
        );

        $otpStep = false;
    } elseif ($_SESSION['otp_attempts'] >= 5) {

        $error = "Maximum attempts reached. Please login again.";

        unset(
            $_SESSION['pending_user_id'],
            $_SESSION['login_otp'],
            $_SESSION['otp_expiry'],
            $_SESSION['otp_attempts']
        );

        $otpStep = false;
    } else {

        $_SESSION['otp_attempts']++;

        if (
            preg_match('/^\d{6}$/', $enteredOtp) &&
            hash_equals($_SESSION['login_otp'], $enteredOtp)
        ) {

            // Fetch verified user
            $stmt = $pdo->prepare(
                "SELECT id, name, email FROM users WHERE id = ? LIMIT 1"
            );

            $stmt->execute([
                $_SESSION['pending_user_id']
            ]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {

                session_regenerate_id(true);

                // Complete login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['nasik'] = 'hi';

                // Remove temporary OTP information
                unset(
                    $_SESSION['pending_user_id'],
                    $_SESSION['login_otp'],
                    $_SESSION['otp_expiry'],
                    $_SESSION['otp_attempts']
                );

                header("Location: index.php");
                exit;
            } else {

                $error = "User account not found.";
            }
        } else {

            $error = "Invalid OTP. Please try again.";
        }
    }

    $otpStep = isset($_SESSION['pending_user_id']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ELITE Daily | Login</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #0b0e13;
            color: #f5f5f5;
        }

        .glass {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(214, 179, 106, 0.18);
            backdrop-filter: blur(15px);
        }

        .gold {
            background: linear-gradient(135deg, #e4c47d, #a98238);
            color: #111;
        }

        .gold-text {
            color: #d6b36a;
        }

        .field {
            background: #111720;
            border: 1px solid #303640;
            color: white;
        }

        .field:focus {
            outline: none;
            border-color: #d6b36a;
        }

        .otp-field {
            letter-spacing: 12px;
            text-align: center;
            font-size: 25px;
            font-weight: 700;
        }
    </style>

</head>

<body class="min-h-screen flex items-center justify-center px-5">

    <div class="w-full max-w-md">

        <!-- Logo -->
        <div class="text-center mb-8">

            <h1 class="text-4xl font-extrabold tracking-widest">
                ELITE<span class="gold-text">.</span>
            </h1>

            <p class="text-gray-400 text-sm mt-2 tracking-widest">
                PERSONAL MANAGEMENT
            </p>

        </div>


        <div class="glass rounded-2xl p-8 shadow-2xl">

            <?php if (!$otpStep): ?>

                <!-- LOGIN SECTION -->

                <div class="mb-7">

                    <p class="gold-text text-xs font-semibold tracking-widest mb-2">
                        WELCOME BACK
                    </p>

                    <h2 class="text-2xl font-bold">
                        Sign in to your account
                    </h2>

                    <p class="text-gray-400 text-sm mt-2">
                        Manage your expenses and daily tasks in one place.
                    </p>

                </div>

            <?php else: ?>

                <!-- OTP SECTION -->

                <div class="mb-7 text-center">

                    <p class="gold-text text-xs font-semibold tracking-widest mb-2">
                        SECURITY VERIFICATION
                    </p>

                    <h2 class="text-2xl font-bold">
                        Verify Your Identity
                    </h2>

                    <p class="text-gray-400 text-sm mt-3">
                        Enter your 6-digit verification code to continue.
                    </p>

                </div>

            <?php endif; ?>


            <!-- ERROR MESSAGE -->

            <?php if ($error): ?>

                <div class="bg-red-500/10 border border-red-500/30 text-red-300 rounded-lg p-3 text-sm mb-5">

                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>

                </div>

            <?php endif; ?>


            <?php if (!$otpStep): ?>

                <!-- LOGIN FORM -->

                <form method="POST">

                    <div class="mb-5">

                        <label class="block text-sm text-gray-300 mb-2">
                            Email Address
                        </label>

                        <input
                            type="email"
                            name="email"
                            required
                            autocomplete="email"
                            placeholder="Enter your email"
                            class="field w-full rounded-lg px-4 py-3 text-sm">

                    </div>


                    <div class="mb-6">

                        <label class="block text-sm text-gray-300 mb-2">
                            Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Enter your password"
                            class="field w-full rounded-lg px-4 py-3 text-sm">

                    </div>


                    <button
                        type="submit"
                        name="login"
                        class="gold w-full py-3 rounded-lg font-bold text-sm hover:opacity-90 transition">

                        SIGN IN

                    </button>

                </form>


                <p class="text-center text-gray-400 text-sm mt-7">

                    Don't have an account?

                    <a href="register.php" class="gold-text font-semibold hover:underline">
                        Sign Up
                    </a>

                </p>


            <?php else: ?>

                <!-- OTP FORM -->

                <form method="POST">

                    <div class="mb-6">

                        <label class="block text-sm text-gray-300 mb-3 text-center">
                            Enter 6-Digit OTP
                        </label>

                        <input
                            type="text"
                            name="otp"
                            maxlength="6"
                            minlength="6"
                            pattern="[0-9]{6}"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            placeholder="000000"
                            required
                            autofocus
                            class="field otp-field w-full rounded-lg px-4 py-4">

                        <p class="text-gray-500 text-xs text-center mt-3">
                            Your OTP is valid for 5 minutes.
                        </p>

                    </div>


                    <button
                        type="submit"
                        name="verify_otp"
                        class="gold w-full py-3 rounded-lg font-bold text-sm hover:opacity-90 transition">

                        VERIFY & CONTINUE

                    </button>

                </form>


                <p class="text-center text-gray-400 text-sm mt-7">

                    <a href="login.php?cancel=1" class="gold-text font-semibold hover:underline">
                        Back to Login
                    </a>

                </p>

            <?php endif; ?>

        </div>


        <p class="text-center text-gray-600 text-xs mt-7">

            © 2026 ELITE Daily. All rights reserved.

        </p>

    </div>

</body>

</html>