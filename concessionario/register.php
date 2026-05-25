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
    <div class="col-md-6">
        <h2>Registrazione</h2>
        <?php if(isset($error)) echo "<div class='alert alert-danger'>" . htmlspecialchars($error) . "</div>"; ?>
        <form method="post">
            <div class="mb-3">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Cognome</label>
                <input type="text" name="cognome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" minlength="8" required>
            </div>
            <div class="mb-3">
                <label>Telefono</label>
                <input type="text" name="telefono" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Registrati</button>
        </form>
        <p class="mt-3">Hai già un account? <a href="login.php">Accedi</a></p>
    </div>
</div>
<?php include 'footer.php'; ?>
