<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $errors = [];

    // Validare
    if (empty($current_password)) {
        $errors[] = "Parola actuală este obligatorie";
    }
    if (empty($new_password)) {
        $errors[] = "Parola nouă este obligatorie";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "Parola nouă trebuie să aibă cel puțin 6 caractere";
    }
    if ($new_password !== $confirm_password) {
        $errors[] = "Parolele nu coincid";
    }

    if (empty($errors)) {
        // Verifică parola actuală
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (password_verify($current_password, $user['password_hash'])) {
            // Actualizează parola
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            
            if ($stmt->execute([$password_hash, $_SESSION['user_id']])) {
                $_SESSION['success'] = "Parola a fost schimbată cu succes!";
                redirect('profile.php');
            } else {
                $errors[] = "A apărut o eroare la schimbarea parolei";
            }
        } else {
            $errors[] = "Parola actuală este incorectă";
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Schimbă Parola</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p class="mb-0"><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Parola Actuală</label>
                        <input type="password" class="form-control" id="current_password" 
                               name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Parola Nouă</label>
                        <input type="password" class="form-control" id="new_password" 
                               name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmă Parola Nouă</label>
                        <input type="password" class="form-control" id="confirm_password" 
                               name="confirm_password" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn bg-info">Schimbă Parola</button>
                        <a href="profile.php" class="btn btn-secondary">Anulează</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 