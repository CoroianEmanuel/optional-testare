<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Obține sarcinile utilizatorului curent
$stmt = $pdo->prepare("
    SELECT t.*, u.username as assigned_to_name 
    FROM tasks t 
    LEFT JOIN users u ON t.assigned_to = u.id 
    WHERE t.created_by = ? OR t.assigned_to = ?
    ORDER BY t.due_date ASC
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$tasks = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-md-12 mb-4">
        <h2>Dashboard</h2>
        <div class="d-flex justify-content-end">
            <a href="add_task.php" class="btn btn-primary">Adaugă Sarcină Nouă</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="card-title mb-0">În Așteptare</h5>
            </div>
            <div class="card-body">
                <?php foreach ($tasks as $task): ?>
                    <?php if ($task['status'] == 'pending'): ?>
                        <div class="task-card mb-2 p-2 border rounded">
                            <h6><?php echo htmlspecialchars($task['title']); ?></h6>
                            <p class="small mb-1">Termen: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?></p>
                            <p class="small mb-1">Prioritate: <?php echo ucfirst($task['priority']); ?></p>
                            <div class="mt-2">
                                <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-primary">Editează</a>
                                <a href="update_status.php?id=<?php echo $task['id']; ?>&status=in_progress" class="btn btn-sm btn-success">Start</a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info">
                <h5 class="card-title mb-0">În Progres</h5>
            </div>
            <div class="card-body">
                <?php foreach ($tasks as $task): ?>
                    <?php if ($task['status'] == 'in_progress'): ?>
                        <div class="task-card mb-2 p-2 border rounded">
                            <h6><?php echo htmlspecialchars($task['title']); ?></h6>
                            <p class="small mb-1">Termen: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?></p>
                            <p class="small mb-1">Prioritate: <?php echo ucfirst($task['priority']); ?></p>
                            <div class="mt-2">
                                <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-primary">Editează</a>
                                <a href="update_status.php?id=<?php echo $task['id']; ?>&status=completed" class="btn btn-sm btn-success">Finalizează</a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success">
                <h5 class="card-title mb-0">Finalizate</h5>
            </div>
            <div class="card-body">
                <?php foreach ($tasks as $task): ?>
                    <?php if ($task['status'] == 'completed'): ?>
                        <div class="task-card mb-2 p-2 border rounded">
                            <h6><?php echo htmlspecialchars($task['title']); ?></h6>
                            <p class="small mb-1">Termen: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?></p>
                            <p class="small mb-1">Prioritate: <?php echo ucfirst($task['priority']); ?></p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 