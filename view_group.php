<?php
require_once 'config/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Verifică dacă ID-ul grupului este furnizat
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID grup invalid";
    redirect('groups.php');
}

$groupId = $_GET['id'];

// Obține detaliile grupului și verifică dacă utilizatorul are acces
$stmt = $pdo->prepare("
    SELECT g.*, 
           u.username as creator_name,
           COUNT(DISTINCT gm.user_id) as member_count,
           CASE WHEN g.created_by = ? THEN 1 ELSE 0 END as is_owner
    FROM user_groups g
    LEFT JOIN users u ON g.created_by = u.id
    LEFT JOIN group_members gm ON g.id = gm.group_id
    WHERE g.id = ? AND (
        g.id IN (SELECT group_id FROM group_members WHERE user_id = ?)
        OR g.created_by = ?
    )
    GROUP BY g.id
");

$stmt->execute([$_SESSION['user_id'], $groupId, $_SESSION['user_id'], $_SESSION['user_id']]);
$group = $stmt->fetch();

if (!$group) {
    $_SESSION['error'] = "Grup negăsit sau acces interzis";
    redirect('groups.php');
}

// Obține membrii grupului
$stmt = $pdo->prepare("
    SELECT u.id, u.username, u.email, gm.joined_at
    FROM group_members gm
    JOIN users u ON gm.user_id = u.id
    WHERE gm.group_id = ?
    ORDER BY gm.joined_at ASC
");
$stmt->execute([$groupId]);
$members = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><?php echo htmlspecialchars($group['name']); ?></h2>
                <a href="groups.php" class="btn btn-secondary">Înapoi la Grupuri</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detalii Grup</h5>
                </div>
                <div class="card-body">
                    <p><strong>Descriere:</strong> <?php echo nl2br(htmlspecialchars($group['description'])); ?></p>
                    <p><strong>Creat de:</strong> <?php echo htmlspecialchars($group['creator_name']); ?></p>
                    <p><strong>Data creării:</strong> <?php echo date('d/m/Y', strtotime($group['created_at'])); ?></p>
                    <p><strong>Număr membri:</strong> <?php echo $group['member_count']; ?></p>
                </div>
            </div>

            <?php if ($group['is_owner']): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Administrare Grup</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inviteModal">
                        Invită Membri
                    </button>
                    <button type="button" class="btn btn-danger" onclick="deleteGroup(<?php echo $group['id']; ?>)">
                        Șterge Grup
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Membri</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($members as $member): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($member['username']); ?>
                                <?php if ($group['is_owner'] && $member['id'] != $_SESSION['user_id']): ?>
                                    <button class="btn btn-sm btn-danger" 
                                            onclick="removeMember(<?php echo $group['id']; ?>, <?php echo $member['id']; ?>)">
                                        Elimină
                                    </button>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($group['is_owner']): ?>
<!-- Modal pentru invitarea membrilor -->
<div class="modal fade" id="inviteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invită Membri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="invite_member.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="group_id" value="<?php echo $group['id']; ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Utilizator</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Închide</button>
                    <button type="submit" class="btn btn-primary">Invită</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function removeMember(groupId, userId) {
    if (confirm('Ești sigur că vrei să elimini acest membru?')) {
        fetch('remove_member.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `group_id=${groupId}&user_id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('A apărut o eroare la eliminarea membrului.');
            }
        });
    }
}

function deleteGroup(groupId) {
    if (confirm('Ești sigur că vrei să ștergi acest grup? Această acțiune este ireversibilă.')) {
        fetch('delete_group.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `group_id=${groupId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'groups.php';
            } else {
                alert('A apărut o eroare la ștergerea grupului.');
            }
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?> 