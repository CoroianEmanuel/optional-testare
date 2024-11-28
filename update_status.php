<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    redirect('dashboard.php');
}

$task_id = $_GET['id'];
$new_status = $_GET['status'];

// Verifică dacă statusul este valid
$valid_statuses = ['pending', 'in_progress', 'completed'];
if (!in_array($new_status, $valid_statuses)) {
    redirect('dashboard.php');
}

// Verifică dacă sarcina aparține utilizatorului curent
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND (created_by = ? OR assigned_to = ?)");
$stmt->execute([$task_id, $_SESSION['user_id'], $_SESSION['user_id']]);
$task = $stmt->fetch();

if (!$task) {
    redirect('dashboard.php');
}

// Actualizează statusul
$stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
if ($stmt->execute([$new_status, $task_id])) {
    $_SESSION['success'] = "Statusul sarcinii a fost actualizat cu succes!";
} else {
    $_SESSION['error'] = "A apărut o eroare la actualizarea statusului.";
}

redirect('dashboard.php');
?> 