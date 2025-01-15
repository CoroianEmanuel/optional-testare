<?php
require_once './config/config.php';

if (isset($_POST['toggle_language'])) {
    $_SESSION['lang'] = isset($_SESSION['lang']) && $_SESSION['lang'] === 'en' ? 'ro' : 'en';
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ro';

// Include fișierul de limbă corespunzător
if ($lang === 'en') {
    include 'lang/en.php';
} else {
    include 'lang/ro.php';
}

if (!isLoggedIn()) {
    redirect('login.php');
}

// Obține sarcinile utilizatorului curent
$stmt = $pdo->prepare("\n    SELECT t.*, u.username as assigned_to_name \n    FROM tasks t \n    LEFT JOIN users u ON t.assigned_to = u.id \n    WHERE t.created_by = ? OR t.assigned_to = ?\n    ORDER BY t.due_date ASC\n");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$tasks = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row">
    <div class="col-md-12 mb-4">
        <h2><?php echo $lang['dashboard']; ?></h2>
        <div class="d-flex justify-content-end">
            <a href="add_task.php" class="btn bg-info"><?php echo $lang['add_new_task']; ?></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning">
                <h5 class="card-title mb-0"><?php echo $lang['pending']; ?></h5>
            </div>
            <div class="card-body">
                <?php foreach ($tasks as $task): ?>
                    <?php if ($task['status'] == 'pending'): ?>
                        <div class="task-card mb-2 p-2 border rounded">
                            <h6><?php echo htmlspecialchars($task['title']); ?></h6>
                            <p class="small mb-1"><?php echo $lang['due_date']; ?>: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?></p>
                            <p class="small mb-1"><?php echo $lang['priority']; ?>: <?php echo ucfirst($task['priority']); ?></p>
                            <div class="mt-2">
                                <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-primary"><?php echo $lang['edit']; ?></a>
                                <a href="update_status.php?id=<?php echo $task['id']; ?>&status=in_progress" class="btn btn-sm btn-success"><?php echo $lang['start']; ?></a>
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
                <h5 class="card-title mb-0"><?php echo $lang['in_progress']; ?></h5>
            </div>
            <div class="card-body">
                <?php foreach ($tasks as $task): ?>
                    <?php if ($task['status'] == 'in_progress'): ?>
                        <div class="task-card mb-2 p-2 border rounded">
                            <h6><?php echo htmlspecialchars($task['title']); ?></h6>
                            <p class="small mb-1"><?php echo $lang['due_date']; ?>: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?></p>
                            <p class="small mb-1"><?php echo $lang['priority']; ?>: <?php echo ucfirst($task['priority']); ?></p>
                            <div class="mt-2">
                                <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn btn-sm bg-info"><?php echo $lang['edit']; ?></a>
                                <a href="update_status.php?id=<?php echo $task['id']; ?>&status=completed" class="btn btn-sm btn-success"><?php echo $lang['finalize']; ?></a>
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
                <h5 class="card-title mb-0"><?php echo $lang['completed']; ?></h5>
            </div>
            <div class="card-body">
                <?php foreach ($tasks as $task): ?>
                    <?php if ($task['status'] == 'completed'): ?>
                        <div class="task-card mb-2 p-2 border rounded">
                            <h6><?php echo htmlspecialchars($task['title']); ?></h6>
                            <p class="small mb-1"><?php echo $lang['due_date']; ?>: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?></p>
                            <p class="small mb-1"><?php echo $lang['priority']; ?>: <?php echo ucfirst($task['priority']); ?></p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="">
    <button type="submit" name="toggle_language" class="btn btn-secondary">
        <?php echo isset($_SESSION['lang']) && $_SESSION['lang'] === 'en' ? 'Change to Romanian' : 'Change to English'; ?>
    </button>
</form>

<?php include 'includes/footer.php'; ?>
