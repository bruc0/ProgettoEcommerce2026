<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $sent = false;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Inserisci un indirizzo email valido.';
    } else {
        ensurePasswordResetColumns($pdo);

        $stmt = $pdo->prepare('SELECT id, email FROM utenti WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $expiresAt = (new DateTime('+60 minutes'))->format('Y-m-d H:i:s');

            $stmt = $pdo->prepare('UPDATE utenti SET reset_token_hash = ?, reset_expires_at = ? WHERE id = ?');
            $stmt->execute([$tokenHash, $expiresAt, $user['id']]);

            $resetUrl = appBaseUrl() . '/reset_password.php?token=' . urlencode($token);
            $sent = sendPasswordResetEmail($user['email'], $resetUrl);
        }

        $success = $sent
            ? 'Se l\'email è registrata, riceverai un link per reimpostare la password.'
            : 'Se l\'email è registrata, riceverai un link per reimpostare la password. Controlla anche la posta indesiderata.';
    }
}

include 'header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">
        <div class="card auth-card shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="bi bi-envelope-lock display-5 text-primary"></i>
                    <h1 class="h3 mt-3 mb-1">Recupera password</h1>
                    <p class="text-muted mb-0">Inserisci l'email del tuo account.</p>
                </div>
                <?php if(isset($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" class="form-control" autocomplete="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Invia link di recupero</button>
                </form>
                <p class="text-center text-muted mt-4 mb-0"><a href="login.php">Torna al login</a></p>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
