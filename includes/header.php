<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Task Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add_task.php">Sarcini</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="groups.php">Grupuri</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Autentificare</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Înregistrare</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <?php if (isLoggedIn()): ?>
                    <ul class="navbar-nav d-flex align-items-center">
                        <li class="nav-item">
                            <a href="profile.php" class="nav-link text-white me-3">
                                <?php 
                                    $navStmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
                                    $navStmt->execute([$_SESSION['user_id']]);
                                    $navUser = $navStmt->fetch();
                                    echo htmlspecialchars($navUser['username']); 
                                ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Deconectare</a>
                        </li>
                    </ul>
                <?php endif; ?>
                <!-- Butonul pentru schimbarea limbii -->
            </div>
        </div>
    </nav>
    <div class="container mt-4">
