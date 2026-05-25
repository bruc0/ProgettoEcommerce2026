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
?>
