<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM utenti WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['utente_id'] = $user['id'];
        $_SESSION['utente_nome'] = $user['nome'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Credenziali non valide.";
    }
}
include 'header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">
        <div class="card auth-card shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="bi bi-person-circle display-5 text-primary"></i>
                    <h1 class="h3 mt-3 mb-1">Accesso</h1>
                    <p class="text-muted mb-0">Accedi per acquistare e gestire il carrello.</p>
                </div>
                <?php if(isset($_GET['registered'])): ?>
                    <div class="alert alert-success">Registrazione completata! Accedi.</div>
                <?php endif; ?>
                <?php if(isset($_GET['reset'])): ?>
                    <div class="alert alert-success">Password aggiornata. Accedi con le nuove credenziali.</div>
                <?php endif; ?>
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" class="form-control" autocomplete="email" required>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="password" class="form-label">Password</label>
                            <a href="forgot_password.php" class="small">Password dimenticata?</a>
                        </div>
                        <input id="password" type="password" name="password" class="form-control" autocomplete="current-password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Accedi</button>
                </form>
                <p class="text-center text-muted mt-4 mb-0">Non hai un account? <a href="register.php">Registrati</a></p>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
