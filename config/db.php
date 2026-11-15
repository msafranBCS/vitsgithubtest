
<?php

$host = "localhost";
$user = "root";
$password = "root123";
$database = "elite_daily";

try {

    // Connect to MySQL server
    $pdo = new PDO(
        "mysql:host=$host;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Create database automatically
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");

    // Select database
    $pdo->exec("USE `$database`");

    // Create tasks table automatically
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            due_date DATE NOT NULL,
            priority ENUM('Low','Medium','High') DEFAULT 'Medium',
            status ENUM('Pending','Completed') DEFAULT 'Pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

} catch (PDOException $e) {

    die("Database Connection Failed: " . htmlspecialchars($e->getMessage()));

}

?>
