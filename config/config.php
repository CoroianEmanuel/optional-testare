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

$translations = [];

function loadTranslations($lang = 'ro') {
    global $translations;
    if ($lang === 'ro') {
        $translations = include "lang/ro.php";
    } else {
        $translations = include "lang/en.php"; // Fără traduceri suplimentare, folosește conținutul implicit
    }
}

function __($key) {
    global $translations;
    return $translations[$key] ?? $key; // Returnează traducerea sau cheia originală dacă nu există
}

// Setați limba (din sesiune, cookie sau altă sursă)
$lang = $_SESSION['lang'] ?? 'ro'; // Poți schimba 'ro' cu limba dorită
loadTranslations($lang);


if (isset($_GET['lang'])) {
    $lang = $_GET['lang']; // Preia limba din URL
    $_SESSION['lang'] = $lang; // Stochează limba în sesiune
    header("Location: " . $_SERVER['PHP_SELF']); // Redirecționează către pagina curentă
    exit();
}

// Setează limba implicită dacă nu este deja în sesiune
$lang = $_SESSION['lang'] ?? 'ro';
loadTranslations($lang);
?>
