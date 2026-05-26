<?php
require_once 'config.php';

ensurePasswordResetColumns($pdo);
$token = (string) ($_GET['token'] ?? $_POST['token'] ?? '');
$tokenHash = $token !== '' ? hash('sha256', $token) : '';
$user = null;

if ($tokenHash !== '') {
    $stmt = $pdo->prepare('SELECT id FROM utenti WHERE reset_token_hash = ? AND reset_expires_at > NOW()');
    $stmt->execute([$tokenHash]);
    $user = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

    if (!$user) {
        $error = 'Link non valido o scaduto.';
    } elseif (strlen($password) < 8) {
        $error = 'La nuova password deve avere almeno 8 caratteri.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Le password non coincidono.';
    } else {
        $stmt = $pdo->prepare('UPDATE utenti SET password_hash = ?, reset_token_hash = NULL, reset_expires_at = NULL WHERE id = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
        header('Location: login.php?reset=1');
        exit;
    }
}

include 'header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">
        <div class="card auth-card shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="bi bi-key display-5 text-primary"></i>
                    <h1 class="h3 mt-3 mb-1">Nuova password</h1>
                    <p class="text-muted mb-0">Imposta una nuova password per il tuo account.</p>
                </div>
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if(!$user && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
                    <div class="alert alert-danger">Link non valido o scaduto.</div>
                    <a href="forgot_password.php" class="btn btn-primary w-100">Richiedi un nuovo link</a>
                <?php else: ?>
                    <form method="post">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        <div class="mb-3">
                            <label for="password" class="form-label">Nuova password</label>
                            <input id="password" type="password" name="password" class="form-control" minlength="8" autocomplete="new-password" required>
                            <div class="form-text">Minimo 8 caratteri.</div>
                        </div>
                        <div class="mb-4">
                            <label for="password_confirm" class="form-label">Conferma password</label>
                            <input id="password_confirm" type="password" name="password_confirm" class="form-control" minlength="8" autocomplete="new-password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Salva nuova password</button>
                    </form>
                <?php endif; ?>
                <p class="text-center text-muted mt-4 mb-0"><a href="login.php">Torna al login</a></p>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
