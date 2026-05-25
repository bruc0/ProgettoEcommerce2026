<?php

session_start();

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/UserManager.php';
require_once __DIR__ . '/../Core/AdminManager.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: http://rtbcars.altervista.org');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function sendJson($data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function readRequestBody(): array
{
    $rawBody = file_get_contents('php://input');
    $contentType = strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? ''));

    if (strpos($contentType, 'application/json') !== false) {
        $data = json_decode($rawBody ?: '{}', true);

        return is_array($data) ? $data : [];
    }

    if ($_POST !== []) {
        return $_POST;
    }

    parse_str($rawBody, $data);

    return is_array($data) ? $data : [];
}

function readRequestData(): array
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
        return $_GET;
    }

    return readRequestBody();
}

try {
    $pdo = Database::getConnection();
    $manager = new UserManager($pdo);
    $adminManager = new AdminManager($pdo);
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $action = $_GET['action'] ?? null;

    if ($method === 'POST' && $action === 'register') {
        $utente = $manager->register(readRequestBody());
        $_SESSION['utente_id'] = $utente['id'];

        sendJson([
            'message' => 'Registrazione completata.',
            'utente' => $utente,
        ], 201);
    }

    if ($method === 'POST' && $action === 'login') {
        $data = readRequestBody();
        $email = (string) ($data['email'] ?? '');
        $password = (string) ($data['password'] ?? '');

        if ($email === '' || $password === '') {
            sendJson(['authenticated' => false], 422);
        }

        $utente = $manager->login($email, $password);
        if ($utente === null) {
            sendJson(['authenticated' => false], 401);
        }

        $_SESSION['utente_id'] = $utente['id'];

        sendJson(['authenticated' => true]);
    }


    if (
        ($method === 'POST' && ($action === 'admin_login' || $action === null))
        || ($method === 'GET' && $action === null && isset($_GET['username'], $_GET['password']))
    ) {
        $data = readRequestData();
        $username = (string) ($data['username'] ?? $data['nome_utente'] ?? '');
        $password = (string) ($data['password'] ?? '');

        if (trim($username) === '' || $password === '') {
            sendJson(['authenticated' => false], 422);
        }

        $admin = $adminManager->login($username, $password);
        if ($admin === null) {
            sendJson(['authenticated' => false], 401);
        }

        $_SESSION['admin_id'] = $admin['id'];

        sendJson(['authenticated' => true]);
    }

    if ($method === 'POST' && $action === 'admin_logout') {
        unset($_SESSION['admin_id']);

        sendJson(['authenticated' => false]);
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

        sendJson(['authenticated' => true]);
    }

    if ($method === 'POST' && $action === 'logout') {
        $_SESSION = [];
        session_destroy();

        sendJson(['authenticated' => false]);
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

        sendJson(['authenticated' => true]);
    }

    header('Allow: GET, POST');
    sendJson(['authenticated' => false], 404);
} catch (InvalidArgumentException $exception) {
    sendJson(['authenticated' => false], 422);
} catch (PDOException $exception) {
    sendJson(['authenticated' => false], 500);
}
?>
