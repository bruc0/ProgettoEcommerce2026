<?php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Core/AutoManager.php';

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

function getRequestId(): ?int
{
    if (isset($_GET['id'])) {
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        return $id === false ? null : $id;
    }

    $pathInfo = $_SERVER['PATH_INFO'] ?? '';
    $id = trim($pathInfo, '/');

    return $id !== '' && ctype_digit($id) ? (int) $id : null;
}


function getAutoFilters(): array
{
    $allowedFilters = [
        'q',
        'marca',
        'modello',
        'colore',
        'carburante',
        'tipo_venditore',
        'porte',
        'garanzia',
        'min_prezzo',
        'max_prezzo',
        'min_chilometraggio',
        'max_chilometraggio',
        'min_potenza_cv',
        'max_potenza_cv',
    ];

    return array_intersect_key($_GET, array_flip($allowedFilters));
}

function autoFromRequest(array $data): Auto
{
    $requiredFields = [
        'marca',
        'modello',
        'prezzo',
        'chilometraggio',
        'immatricolazione',
        'carburante',
        'potenza_cv',
        'colore',
        'garanzia',
        'porte',
        'tipo_venditore',
    ];

    foreach ($requiredFields as $field) {
        if (!array_key_exists($field, $data)) {
            sendJson(['error' => "Campo obbligatorio mancante: {$field}."], 422);
        }
    }

    try {
        return Auto::fromArray($data);
    } catch (ValueError $exception) {
        sendJson(['error' => 'Valore enum non valido per carburante o tipo_venditore.'], 422);
    } catch (Exception $exception) {
        sendJson(['error' => 'Dati auto non validi.'], 422);
    }
}

try {
    $manager = new AutoManager(Database::getConnection());
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $id = getRequestId();

    switch ($method) {
        case 'GET':
            if ($id !== null) {
                $auto = $manager->getById($id);
                $auto === null
                    ? sendJson(['error' => 'Auto non trovata.'], 404)
                    : sendJson($auto);
            }

            sendJson($manager->getAll(getAutoFilters()));

        case 'POST':
            $created = $manager->create(autoFromRequest(readJsonBody()));
            sendJson($created, 201);

        case 'PUT':
            if ($id === null) {
                sendJson(['error' => 'ID auto obbligatorio per aggiornare.'], 400);
            }

            $updated = $manager->update($id, autoFromRequest(readJsonBody()));
            $updated === null
                ? sendJson(['error' => 'Auto non trovata.'], 404)
                : sendJson($updated);

        case 'DELETE':
            if ($id === null) {
                sendJson(['error' => 'ID auto obbligatorio per eliminare.'], 400);
            }

            $manager->delete($id)
                ? sendJson(['message' => 'Auto eliminata correttamente.'])
                : sendJson(['error' => 'Auto non trovata.'], 404);

        default:
            header('Allow: GET, POST, PUT, DELETE');
            sendJson(['error' => 'Metodo non consentito.'], 405);
    }
} catch (PDOException $exception) {
    sendJson(['error' => 'Errore database.', 'detail' => $exception->getMessage()], 500);
}
?>
