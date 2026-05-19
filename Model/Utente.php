<?php

class Utente
{
    public function __construct(
        private string $nome,
        private string $cognome,
        private string $email,
        private string $passwordHash,
        private ?string $telefono = null,
        private ?int $id = null,
        private ?DateTime $createdAt = null,
        private ?DateTime $updatedAt = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nome: (string) ($data['nome'] ?? ''),
            cognome: (string) ($data['cognome'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            passwordHash: (string) ($data['password_hash'] ?? $data['passwordHash'] ?? ''),
            telefono: $data['telefono'] ?? null,
            id: isset($data['id']) ? (int) $data['id'] : null,
            createdAt: isset($data['created_at']) ? new DateTime((string) $data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new DateTime((string) $data['updated_at']) : null
        );
    }

    public function toArray(bool $includeMetadata = true): array
    {
        $data = [
            'nome' => $this->nome,
            'cognome' => $this->cognome,
            'email' => $this->email,
            'telefono' => $this->telefono,
        ];

        if ($includeMetadata) {
            $data['id'] = $this->id;
            $data['created_at'] = $this->createdAt?->format(DateTimeInterface::ATOM);
            $data['updated_at'] = $this->updatedAt?->format(DateTimeInterface::ATOM);
        }

        return $data;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    public function getCognome(): string
    {
        return $this->cognome;
    }

    public function setCognome(string $cognome): self
    {
        $this->cognome = $cognome;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;
        return $this;
    }
}
?>
