<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($name === "" || $email === "" || $password === "") {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $error = "Password must contain at least 8 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {

        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = "This email is already registered.";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, password)
                VALUES (?, ?, ?)
            ");

            $stmt->execute([$name, $email, $hashedPassword]);

            header("Location: login.php?registered=1");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELITE Daily | Sign Up</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }

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

        .gold-text { color: #d6b36a; }

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

<body class="min-h-screen flex items-center justify-center px-5 py-10">

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
                    CREATE YOUR ACCOUNT
                </p>

                <h2 class="text-2xl font-bold">Get started with ELITE</h2>

                <p class="text-gray-400 text-sm mt-2">
                    Create an account to manage your daily life.
                </p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/30 text-red-300 rounded-lg p-3 text-sm mb-5">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-4">
                    <label class="block text-sm text-gray-300 mb-2">Full Name</label>
                    <input
                        type="text"
                        name="name"
                        required
                        autocomplete="name"
                        placeholder="Enter your name"
                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                        class="field w-full rounded-lg px-4 py-3 text-sm"
                    >
                </div>

                <div class="mb-4">
                    <label class="block text-sm text-gray-300 mb-2">Email Address</label>
                    <input
                        type="email"
                        name="email"
                        required
                        autocomplete="email"
                        placeholder="Enter your email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        class="field w-full rounded-lg px-4 py-3 text-sm"
                    >
                </div>

                <div class="mb-4">
                    <label class="block text-sm text-gray-300 mb-2">Password</label>
                    <input
                        type="password"
                        name="password"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Minimum 8 characters"
                        class="field w-full rounded-lg px-4 py-3 text-sm"
                    >
                </div>

                <div class="mb-6">
                    <label class="block text-sm text-gray-300 mb-2">Confirm Password</label>
                    <input
                        type="password"
                        name="confirm_password"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Re-enter your password"
                        class="field w-full rounded-lg px-4 py-3 text-sm"
                    >
                </div>

                <button
                    type="submit"
                    class="gold w-full py-3 rounded-lg font-bold text-sm hover:opacity-90 transition"
                >
                    CREATE ACCOUNT
                </button>

            </form>

            <p class="text-center text-gray-400 text-sm mt-7">
                Already have an account?
                <a href="login.php" class="gold-text font-semibold hover:underline">
                    Sign In
                </a>
            </p>

        </div>

    </div>

</body>
</html>