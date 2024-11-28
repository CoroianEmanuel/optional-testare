<?php
session_start();

// Configurare bază de date
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root'); // Completează cu parola ta pentru root dacă ai setat una
define('DB_NAME', 'task_manager'); // Numele bazei tale de date

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    die("Eroare conexiune: " . $e->getMessage());
}

// Funcții helper
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($page) {
    header("Location: $page");
    exit();
}
?> 