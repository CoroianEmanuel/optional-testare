<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

try {
    // Query explicit pentru toate coloanele
    $stmt = $pdo->prepare("
        SELECT id, username, email, password_hash, created_at 
        FROM users 
        WHERE id = ?
    ");
    
    // Debug pentru a vedea ID-ul utilizatorului
    echo "<!-- User ID: " . $_SESSION['user_id'] . " -->";
    
    $stmt->execute([$_SESSION['user_id']]);
    
    // Afișăm toate coloanele returnate
    echo "<!-- Available columns: ";
    $columnCount = $stmt->columnCount();
    for ($i = 0; $i < $columnCount; $i++) {
        $meta = $stmt->getColumnMeta($i);
        echo $meta['name'] . ", ";
    }
    echo " -->";
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Debug pentru datele utilizatorului
    echo "<!-- User data: ";
    print_r($user);
    echo " -->";
    
} catch (PDOException $e) {
    echo "<!-- Database error: " . $e->getMessage() . " -->";
    $user = null;
}

// Obține sarcinile recente
$stmt = $pdo->prepare("
    SELECT t.*, u.username as assigned_to_name 
    FROM tasks t
    LEFT JOIN users u ON t.assigned_to = u.id
    WHERE t.created_by = ? OR t.assigned_to = ?
    ORDER BY t.due_date DESC
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$tasks = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Profilul Meu</h5>
            </div>
            <div class="card-body text-center">
                <h4 class="mb-2"><?php echo htmlspecialchars($user['username']); ?></h4>
                <p class="text-muted mb-4">
                    <?php 
                    // Debug pentru email
                    echo "<!-- Email direct from array: " . ($user['email'] ?? 'null') . " -->";
                    
                    if (isset($user['email']) && !empty($user['email'])) {
                        echo htmlspecialchars($user['email']);
                    } else {
                        echo "Nu există email";
                    }
                    ?>
                </p>
                <button class="btn btn-primary w-100 mb-3" onclick="location.href='edit_profile.php'">
                    Editează Profilul
                </button>
                <button class="btn btn-outline-primary w-100" onclick="location.href='change_password.php'">
                    Schimbă Parola
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Sarcini Recente</h5>
            </div>
            <div class="card-body">
                <?php if ($tasks): ?>
                    <div class="list-group">
                        <?php foreach ($tasks as $task): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($task['title']); ?></h6>
                                    <small>
                                        Termen: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?>
                                    </small>
                                </div>
                                <p class="mb-1"><?php echo htmlspecialchars($task['description']); ?></p>
                                <small>
                                    Status: <?php 
                                        if ($task['status'] == 'pending') echo 'În Așteptare';
                                        elseif ($task['status'] == 'in_progress') echo 'În Progres';
                                        else echo 'Finalizat';
                                    ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Nu există sarcini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 