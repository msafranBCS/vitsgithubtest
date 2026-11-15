<?php 
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'daily_expenses_db';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die('Database connection failed: ' . htmlspecialchars($conn->connect_error));
}
$conn->set_charset('utf8mb4');

$conn->query("
    CREATE TABLE IF NOT EXISTS daily_expenses (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        amount DECIMAL(12,2) NOT NULL,
        category VARCHAR(50) NOT NULL,
        expense_date DATE NOT NULL,
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

?>