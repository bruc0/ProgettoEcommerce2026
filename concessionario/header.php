<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concessionario Auto - Usato garantito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="index.php">
            <i class="bi bi-speedometer2 me-2"></i>Concessionario Auto
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Apri navigazione">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i>Home</a></li>
                <?php if(currentUserId() !== null): ?>
                    <li class="nav-item"><a class="nav-link" href="cart.php"><i class="bi bi-cart3 me-1"></i>Carrello</a></li>
                    <li class="nav-item"><a class="nav-link" href="order_history.php"><i class="bi bi-receipt me-1"></i>I miei ordini</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i>Logout (<?= htmlspecialchars($_SESSION['utente_nome'] ?? 'utente') ?>)</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a></li>
                    <li class="nav-item"><a class="btn btn-light btn-sm ms-lg-2 mt-2 mt-lg-0" href="register.php">Registrati</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4">
