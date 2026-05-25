<?php

class AdminManager
{
    public function __construct(private PDO $pdo) {}

    public function login(string $nome_utente, string $password): ?array
    {
        $admin = $this->findByUsername($nome_utente);

        if ($admin === null || !password_verify($password, $admin['password_hash'])) {
            return null;
        }

        if (password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)) {
            $this->updatePasswordHash((int) $admin['id'], password_hash($password, PASSWORD_DEFAULT));
        }

        return $this->adminToArray($admin);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admin_user WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->adminToArray($row) : null;
    }

    public function findByUsername(string $nome_utente): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admin_user WHERE nome_utente = :nome_utente');
        $stmt->execute(['nome_utente' => strtolower(trim($nome_utente))]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    private function updatePasswordHash(int $id, string $passwordHash): void
    {
        $stmt = $this->pdo->prepare('UPDATE admin_user SET password_hash = :password_hash WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'password_hash' => $passwordHash,
        ]);
    }

    private function adminToArray(array $admin): array
    {
        return [
            'id' => (int) $admin['id'],
            'nome_utente' => $admin['nome_utente'],
            'username' => $admin['nome_utente'],
            'created_at' => isset($admin['created_at']) ? (new DateTime((string) $admin['created_at']))->format(DateTimeInterface::ATOM) : null,
            'updated_at' => isset($admin['updated_at']) ? (new DateTime((string) $admin['updated_at']))->format(DateTimeInterface::ATOM) : null,
        ];
    }
}
?>
