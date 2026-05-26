<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $email = strtolower(trim($_POST['email']));
    $passwordPlain = $_POST['password'];
    $telefono = trim($_POST['telefono'] ?? '');

    if ($nome === '' || $cognome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($passwordPlain) < 8) {
        $error = "Compila tutti i campi. La password deve avere almeno 8 caratteri.";
    }

    try {
        if (!isset($error)) {
            $password = password_hash($passwordPlain, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utenti (nome, cognome, email, password_hash, telefono) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $cognome, $email, $password, $telefono !== '' ? $telefono : null]);
            header('Location: login.php?registered=1');
            exit;
        }
    } catch(PDOException $e) {
        $error = "Email già registrata!";
    }
}
include 'header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card auth-card shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="bi bi-person-plus display-5 text-primary"></i>
                    <h1 class="h3 mt-3 mb-1">Registrazione</h1>
                    <p class="text-muted mb-0">Crea il tuo account per salvare il carrello e acquistare.</p>
                </div>
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nome" class="form-label">Nome</label>
                            <input id="nome" type="text" name="nome" class="form-control" autocomplete="given-name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cognome" class="form-label">Cognome</label>
                            <input id="cognome" type="text" name="cognome" class="form-control" autocomplete="family-name" required>
                        </div>
                        <div class="col-md-7">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" name="email" class="form-control" autocomplete="email" required>
                        </div>
                        <div class="col-md-5">
                            <label for="telefono" class="form-label">Telefono</label>
                            <input id="telefono" type="text" name="telefono" class="form-control" autocomplete="tel">
                        </div>
                        <div class="col-12">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" name="password" class="form-control" minlength="8" autocomplete="new-password" required>
                            <div class="form-text">Minimo 8 caratteri.</div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-4">Registrati</button>
                </form>
                <p class="text-center text-muted mt-4 mb-0">Hai già un account? <a href="login.php">Accedi</a></p>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
