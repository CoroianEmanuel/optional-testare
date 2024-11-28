<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $group_id = $_POST['group_id'];
    $email = trim($_POST['email']);
    $errors = [];

    // Validare
    if (empty($group_id) || !is_numeric($group_id)) {
        $errors[] = "ID grup invalid";
    }
    if (empty($email)) {
        $errors[] = "Emailul este obligatoriu";
    }

    if (empty($errors)) {
        try {
            // Verifică dacă utilizatorul are dreptul să invite în acest grup
            $stmt = $pdo->prepare("
                SELECT created_by 
                FROM user_groups 
                WHERE id = ? AND created_by = ?
            ");
            $stmt->execute([$group_id, $_SESSION['user_id']]);
            $group = $stmt->fetch();

            if ($group) {
                // Găsește utilizatorul după email
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user) {
                    // Verifică dacă utilizatorul este deja membru
                    $stmt = $pdo->prepare("
                        SELECT COUNT(*) 
                        FROM group_members 
                        WHERE group_id = ? AND user_id = ?
                    ");
                    $stmt->execute([$group_id, $user['id']]);
                    $isMember = $stmt->fetchColumn() > 0;

                    if (!$isMember) {
                        // Adaugă utilizatorul în grup
                        $stmt = $pdo->prepare("
                            INSERT INTO group_members (group_id, user_id) 
                            VALUES (?, ?)
                        ");
                        $stmt->execute([$group_id, $user['id']]);
                        $_SESSION['success'] = "Utilizatorul a fost adăugat în grup cu succes!";
                    } else {
                        $_SESSION['error'] = "Utilizatorul este deja membru al acestui grup";
                    }
                } else {
                    $_SESSION['error'] = "Nu există niciun utilizator cu acest email";
                }
            } else {
                $_SESSION['error'] = "Nu ai permisiunea să adaugi membri în acest grup";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "A apărut o eroare la adăugarea membrului";
        }
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
    }
}

// Redirecționează înapoi la pagina grupului
if (isset($group_id)) {
    redirect("view_group.php?id=" . $group_id);
} else {
    redirect("groups.php");
} 