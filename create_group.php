<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    if (empty($name)) {
        $_SESSION['error'] = "Numele grupului este obligatoriu";
        redirect('groups.php');
    }
    
    try {
        $pdo->beginTransaction();
        
        // Creează grupul
        $stmt = $pdo->prepare("INSERT INTO user_groups (name, description, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $_SESSION['user_id']]);
        $groupId = $pdo->lastInsertId();
        
        // Adaugă creatorul ca membru
        $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
        $stmt->execute([$groupId, $_SESSION['user_id']]);
        
        $pdo->commit();
        $_SESSION['success'] = "Grupul a fost creat cu succes!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "A apărut o eroare la crearea grupului.";
    }
}

redirect('groups.php'); 