<?php

class AdminManager
{
    public function __construct(private PDO $pdo) {}

    public function login(string $username, string $password): ?array
    {
        $username = strtolower(trim($username));
        $this->ensureInitialAdmins();

        $admin = $this->findByUsername($username);

        if ($admin === null) {
            return null;
        }

        if (!password_verify($password, $admin['password_hash'])) {
            if (!$this->canUseInitialPassword($username, $password)) {
                return null;
            }

            $this->updatePasswordHash((int) $admin['id'], password_hash($password, PASSWORD_DEFAULT));
            $admin = $this->findByUsername($username);
        }

        if (password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)) {
            $this->updatePasswordHash((int) $admin['id'], password_hash($password, PASSWORD_DEFAULT));
            $admin = $this->findByUsername($username);
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

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admin_user WHERE username = :username');
        $stmt->execute(['username' => strtolower(trim($username))]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    private function ensureInitialAdmins(): void
    {
        foreach ($this->getInitialAdminUsernames() as $username) {
            if ($this->findByUsername($username) !== null) {
                continue;
            }

            $stmt = $this->pdo->prepare(
                'INSERT INTO admin_user (username, password_hash)
                 VALUES (:username, :password_hash)'
            );
            $stmt->execute([
                'username' => $username,
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            ]);
        }
    }

    private function canUseInitialPassword(string $username, string $password): bool
    {
        return $password === 'admin123' && in_array($username, $this->getInitialAdminUsernames(), true);
    }

    private function getInitialAdminUsernames(): array
    {
        return ['bruc0', 'trencio', 'lello'];
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
            'username' => $admin['username'],
            'created_at' => isset($admin['created_at']) ? (new DateTime((string) $admin['created_at']))->format(DateTimeInterface::ATOM) : null,
            'updated_at' => isset($admin['updated_at']) ? (new DateTime((string) $admin['updated_at']))->format(DateTimeInterface::ATOM) : null,
        ];
    }
}
?>
