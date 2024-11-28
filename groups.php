<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Obține toate grupurile din care face parte utilizatorul
$stmt = $pdo->prepare("
    SELECT g.*, 
           COUNT(DISTINCT gm.user_id) as member_count,
           CASE WHEN g.created_by = ? THEN 1 ELSE 0 END as is_owner
    FROM user_groups g
    LEFT JOIN group_members gm ON g.id = gm.group_id
    WHERE g.id IN (
        SELECT group_id 
        FROM group_members 
        WHERE user_id = ?
    )
    OR g.created_by = ?
    GROUP BY g.id
    ORDER BY g.created_at DESC
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
$groups = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2>Grupurile Mele</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGroupModal">
            Creează Grup Nou
        </button>
    </div>
</div>

<div class="row">
    <?php foreach ($groups as $group): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($group['name']); ?></h5>
                </div>
                <div class="card-body">
                    <p class="card-text"><?php echo htmlspecialchars($group['description']); ?></p>
                    <p class="small text-muted">Membri: <?php echo $group['member_count']; ?></p>
                    <div class="d-flex justify-content-between">
                        <a href="view_group.php?id=<?php echo $group['id']; ?>" class="btn btn-primary">Vezi Detalii</a>
                        <?php if ($group['is_owner']): ?>
                            <button class="btn btn-danger" onclick="deleteGroup(<?php echo $group['id']; ?>)">Șterge</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal pentru crearea unui grup nou -->
<div class="modal fade" id="createGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Creează Grup Nou</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="create_group.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="groupName" class="form-label">Numele Grupului</label>
                        <input type="text" class="form-control" id="groupName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="groupDescription" class="form-label">Descriere</label>
                        <textarea class="form-control" id="groupDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Închide</button>
                    <button type="submit" class="btn btn-primary">Creează Grup</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteGroup(groupId) {
    if (confirm('Ești sigur că vrei să ștergi acest grup?')) {
        fetch('delete_group.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'group_id=' + groupId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('A apărut o eroare la ștergerea grupului.');
            }
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?> 