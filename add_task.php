<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Obține lista utilizatorilor pentru atribuirea sarcinilor
$stmt = $pdo->query("SELECT id, username FROM users ORDER BY username");
$users = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];
    $assigned_to = $_POST['assigned_to'];
    
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Titlul este obligatoriu";
    }
    
    if (empty($due_date)) {
        $errors[] = "Data limită este obligatorie";
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO tasks (title, description, due_date, priority, created_by, assigned_to, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ");
        
        if ($stmt->execute([$title, $description, $due_date, $priority, $_SESSION['user_id'], $assigned_to])) {
            $_SESSION['success'] = "Sarcina a fost adăugată cu succes!";
            redirect('dashboard.php');
        } else {
            $errors[] = "A apărut o eroare. Încercați din nou.";
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Adaugă Sarcină Nouă</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p class="mb-0"><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="mb-3">
                        <label for="title" class="form-label">Titlu</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descriere</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Data limită</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="priority" class="form-label">Prioritate</label>
                        <select class="form-control" id="priority" name="priority">
                            <option value="low">Scăzută</option>
                            <option value="medium" selected>Medie</option>
                            <option value="high">Ridicată</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Atribuie către</label>
                        <select class="form-control" id="assigned_to" name="assigned_to">
                            <option value="">Selectează utilizator</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Adaugă Sarcina</button>
                    <a href="dashboard.php" class="btn btn-secondary">Anulează</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 