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
    <div class="col-md-6">
        <h2>Accesso</h2>
        <?php if(isset($_GET['registered'])) echo "<div class='alert alert-success'>Registrazione completata! Accedi.</div>"; ?>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>" . htmlspecialchars($error) . "</div>"; ?>
        <form method="post">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Accedi</button>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
