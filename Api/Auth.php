<?php

session_start();

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/UserManager.php';
require_once __DIR__ . '/../Core/AdminManager.php';

header('Content-Type: application/json; charset=utf-8');

function sendJson(mixed $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function readJsonBody(): array
{
    $rawBody = file_get_contents('php://input');
    $data = json_decode($rawBody ?: '{}', true);

    if (!is_array($data)) {
        sendJson(['error' => 'JSON non valido.'], 400);
    }

    return $data;
}

try {
    $pdo = Database::getConnection();
    $manager = new UserManager($pdo);
    $adminManager = new AdminManager($pdo);
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $action = $_GET['action'] ?? null;

    if ($method === 'POST' && $action === 'register') {
        $utente = $manager->register(readJsonBody());
        $_SESSION['utente_id'] = $utente['id'];

        sendJson([
            'message' => 'Registrazione completata.',
            'utente' => $utente,
        ], 201);
    }

    if ($method === 'POST' && $action === 'login') {
        $data = readJsonBody();
        $email = (string) ($data['email'] ?? '');
        $password = (string) ($data['password'] ?? '');

        if ($email === '' || $password === '') {
            sendJson(['error' => 'Email e password sono obbligatorie.'], 422);
        }

        $utente = $manager->login($email, $password);
        if ($utente === null) {
            sendJson(['error' => 'Credenziali non valide.'], 401);
        }

        $_SESSION['utente_id'] = $utente['id'];

        sendJson([
            'message' => 'Login completato.',
            'utente' => $utente,
        ]);
    }


    if ($method === 'POST' && $action === 'admin_login') {
        $data = readJsonBody();
        $username = (string) ($data['username'] ?? $data['nome_utente'] ?? '');
        $password = (string) ($data['password'] ?? '');

        if (trim($username) === '' || $password === '') {
            sendJson(['error' => 'Nome utente e password sono obbligatori.'], 422);
        }

        $admin = $adminManager->login($username, $password);
        if ($admin === null) {
            sendJson(['error' => 'Credenziali admin non valide.'], 401);
        }

        $_SESSION['admin_id'] = $admin['id'];

        sendJson([
            'message' => 'Login admin completato.',
            'admin' => $admin,
        ]);
    }

    if ($method === 'POST' && $action === 'admin_logout') {
        unset($_SESSION['admin_id']);

        sendJson(['message' => 'Logout admin completato.']);
    }

    if ($method === 'GET' && $action === 'admin_me') {
        if (!isset($_SESSION['admin_id'])) {
            sendJson(['authenticated' => false], 401);
        }

        $admin = $adminManager->getById((int) $_SESSION['admin_id']);
        if ($admin === null) {
            unset($_SESSION['admin_id']);
            sendJson(['authenticated' => false], 401);
        }

        sendJson([
            'authenticated' => true,
            'admin' => $admin,
        ]);
    }

    if ($method === 'POST' && $action === 'logout') {
        $_SESSION = [];
        session_destroy();

        sendJson(['message' => 'Logout completato.']);
    }

    if ($method === 'GET' && $action === 'me') {
        if (!isset($_SESSION['utente_id'])) {
            sendJson(['authenticated' => false], 401);
        }

        $utente = $manager->getById((int) $_SESSION['utente_id']);
        if ($utente === null) {
            $_SESSION = [];
            session_destroy();
            sendJson(['authenticated' => false], 401);
        }

        sendJson([
            'authenticated' => true,
            'utente' => $utente,
        ]);
    }

    header('Allow: GET, POST');
    sendJson(['error' => 'Azione non valida. Usa action=register, login, logout, me, admin_login, admin_logout o admin_me.'], 404);
} catch (InvalidArgumentException $exception) {
    sendJson(['error' => $exception->getMessage()], 422);
} catch (PDOException $exception) {
    sendJson(['error' => 'Errore database.', 'detail' => $exception->getMessage()], 500);
}
?>
