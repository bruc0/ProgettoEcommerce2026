<?php
require_once __DIR__ . '/../Core/Database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

try {
    $pdo = Database::getConnection();
} catch (PDOException $e) {
    die("Connessione fallita: " . $e->getMessage());
}

function currentUserId(): ?int
{
    return isset($_SESSION['utente_id']) ? (int) $_SESSION['utente_id'] : null;
}

function appBaseUrl(): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/concessionario'));

    return rtrim($scheme . '://' . $host . $scriptDir, '/');
}

function ensurePasswordResetColumns(PDO $pdo): void
{
    $columns = $pdo->query("SHOW COLUMNS FROM utenti")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('reset_token_hash', $columns, true)) {
        $pdo->exec('ALTER TABLE utenti ADD COLUMN reset_token_hash VARCHAR(64) NULL AFTER password_hash');
    }

    if (!in_array('reset_expires_at', $columns, true)) {
        $pdo->exec('ALTER TABLE utenti ADD COLUMN reset_expires_at DATETIME NULL AFTER reset_token_hash');
    }
}

function sendPasswordResetEmail(string $email, string $resetUrl): bool
{
    $subject = 'Recupero password Concessionario Auto';
    $message = "Ciao,\n\nabbiamo ricevuto una richiesta di recupero password.\n" .
        "Apri questo link per impostare una nuova password:\n\n" .
        $resetUrl . "\n\n" .
        "Il link scade tra 60 minuti. Se non hai richiesto tu il recupero, ignora questa email.\n";
    $headers = [
        'From: Concessionario Auto <no-reply@rtbcars.altervista.org>',
        'Reply-To: no-reply@rtbcars.altervista.org',
        'Content-Type: text/plain; charset=UTF-8',
    ];

    return mail($email, $subject, $message, implode("\r\n", $headers));
}
?>
