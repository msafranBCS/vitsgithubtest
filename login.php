<?php
session_start();
require_once 'config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        header("Location: index.php");
        exit;

    } else {
        $error = "Invalid email address or password.";
    }
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
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(214,179,106,0.18);
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
    </style>
</head>

<body class="min-h-screen flex items-center justify-center px-5">

    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold tracking-widest">
                ELITE<span class="gold-text">.</span>
            </h1>
            <p class="text-gray-400 text-sm mt-2 tracking-widest">
                PERSONAL MANAGEMENT
            </p>
        </div>

        <div class="glass rounded-2xl p-8 shadow-2xl">

            <div class="mb-7">
                <p class="gold-text text-xs font-semibold tracking-widest mb-2">
                    WELCOME BACK
                </p>

                <h2 class="text-2xl font-bold">Sign in to your account</h2>

                <p class="text-gray-400 text-sm mt-2">
                    Manage your expenses and daily tasks in one place.
                </p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/30 text-red-300 rounded-lg p-3 text-sm mb-5">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">

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
                        class="field w-full rounded-lg px-4 py-3 text-sm"
                    >
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
                        class="field w-full rounded-lg px-4 py-3 text-sm"
                    >
                </div>

                <button
                    type="submit"
                    class="gold w-full py-3 rounded-lg font-bold text-sm hover:opacity-90 transition"
                >
                    SIGN IN
                </button>

            </form>

            <p class="text-center text-gray-400 text-sm mt-7">
                Don't have an account?
                <a href="register.php" class="gold-text font-semibold hover:underline">
                    Sign Up
                </a>
            </p>

        </div>

        <p class="text-center text-gray-600 text-xs mt-7">
            © 2026 ELITE Daily. All rights reserved.
        </p>

    </div>

</body>
</html>