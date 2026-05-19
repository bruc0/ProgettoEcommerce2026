<?php

require_once __DIR__ . '/../Model/Utente.php';

class UserManager
{
    public function __construct(private PDO $pdo) {}

    public function register(array $data): array
    {
        $this->validateRegistrationData($data);

        if ($this->findByEmail((string) $data['email']) !== null) {
            throw new InvalidArgumentException('Email già registrata.');
        }

        $passwordHash = password_hash((string) $data['password'], PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(
            'INSERT INTO utenti (nome, cognome, email, password_hash, telefono)
             VALUES (:nome, :cognome, :email, :password_hash, :telefono)'
        );
        $stmt->execute([
            'nome' => trim((string) $data['nome']),
            'cognome' => trim((string) $data['cognome']),
            'email' => strtolower(trim((string) $data['email'])),
            'password_hash' => $passwordHash,
            'telefono' => isset($data['telefono']) ? trim((string) $data['telefono']) : null,
        ]);

        $utente = $this->getById((int) $this->pdo->lastInsertId());
        if ($utente === null) {
            throw new RuntimeException('Registrazione completata, ma utente non rileggibile.');
        }

        return $utente;
    }

    public function login(string $email, string $password): ?array
    {
        $utente = $this->findByEmail($email);

        if ($utente === null || !password_verify($password, $utente->getPasswordHash())) {
            return null;
        }

        if (password_needs_rehash($utente->getPasswordHash(), PASSWORD_DEFAULT)) {
            $this->updatePasswordHash($utente->getId(), password_hash($password, PASSWORD_DEFAULT));
        }

        return $utente->toArray();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM utenti WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Utente::fromArray($row)->toArray() : null;
    }

    public function findByEmail(string $email): ?Utente
    {
        $stmt = $this->pdo->prepare('SELECT * FROM utenti WHERE email = :email');
        $stmt->execute(['email' => strtolower(trim($email))]);
        $row = $stmt->fetch();

        return $row ? Utente::fromArray($row) : null;
    }

    private function updatePasswordHash(?int $id, string $passwordHash): void
    {
        if ($id === null) {
            return;
        }

        $stmt = $this->pdo->prepare('UPDATE utenti SET password_hash = :password_hash WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'password_hash' => $passwordHash,
        ]);
    }

    private function validateRegistrationData(array $data): void
    {
        foreach (['nome', 'cognome', 'email', 'password'] as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                throw new InvalidArgumentException("Campo obbligatorio mancante: {$field}.");
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email non valida.');
        }

        if (strlen((string) $data['password']) < 8) {
            throw new InvalidArgumentException('La password deve contenere almeno 8 caratteri.');
        }
    }
}
?>
