<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concessionario Auto - Usato garantito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <style>
        .car-card img { height: 200px; object-fit: cover; }
        .filter-sidebar { background: #f8f9fa; padding: 1rem; border-radius: 10px; }
        .price-badge { font-size: 1.2rem; font-weight: bold; color: #28a745; }
        .sold-badge { background: #dc3545; color: white; padding: 0.2rem 0.5rem; border-radius: 5px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">Concessionario Auto</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <?php if(currentUserId() !== null): ?>
                    <li class="nav-item"><a class="nav-link" href="cart.php"><i class="bi bi-cart"></i> Carrello</a></li>
                    <li class="nav-item"><a class="nav-link" href="order_history.php">I miei ordini</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout (<?= htmlspecialchars($_SESSION['utente_nome'] ?? 'utente') ?>)</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Registrati</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-3">
