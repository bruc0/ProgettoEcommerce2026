<?php
enum Carburante: string {
    case Benzina = 'Benzina';
    case Diesel = 'Diesel';
    case Elettrica = 'Elettrica';
    case Ibrida = 'Ibrida';
    case GPL = 'GPL';
}

enum TipoVenditore: string {
    case Concessionario = 'Concessionario';
    case Privato = 'Privato';
}

class Auto {
    public function __construct(
        public string $marca,
        public string $modello,
        public int $prezzo,
        public int $chilometraggio,
        public DateTime $immatricolazione,
        public Carburante $carburante,
        public int $potenzaCv,
        public string $colore,
        public bool $garanzia,
        public array $optional = [],
        public int $porte = 5,
        public TipoVenditore $tipoVenditore = TipoVenditore::Concessionario,
        public ?array $immagini = null,
        public ?int $id = null,
        public ?DateTime $createdAt = null,
        public ?DateTime $updatedAt = null
    ) {}

    public static function fromArray(array $data): self
    {
        $optional = $data['optional'] ?? [];
        if (is_string($optional)) {
            $decoded = json_decode($optional, true);
            $optional = is_array($decoded) ? $decoded : [];
        }

        $immagini = self::normalizeImmagini($data);

        return new self(
            marca: (string) ($data['marca'] ?? ''),
            modello: (string) ($data['modello'] ?? ''),
            prezzo: (int) ($data['prezzo'] ?? 0),
            chilometraggio: (int) ($data['chilometraggio'] ?? 0),
            immatricolazione: new DateTime((string) ($data['immatricolazione'] ?? 'now')),
            carburante: Carburante::from((string) ($data['carburante'] ?? Carburante::Benzina->value)),
            potenzaCv: (int) ($data['potenza_cv'] ?? $data['potenzaCv'] ?? 0),
            colore: (string) ($data['colore'] ?? ''),
            garanzia: filter_var($data['garanzia'] ?? false, FILTER_VALIDATE_BOOLEAN),
            optional: $optional,
            porte: (int) ($data['porte'] ?? 5),
            tipoVenditore: TipoVenditore::from((string) ($data['tipo_venditore'] ?? $data['tipoVenditore'] ?? TipoVenditore::Concessionario->value)),
            immagini: $immagini,
            id: isset($data['id']) ? (int) $data['id'] : null,
            createdAt: isset($data['created_at']) ? new DateTime((string) $data['created_at']) : null,
            updatedAt: isset($data['updated_at']) ? new DateTime((string) $data['updated_at']) : null
        );
    }

    public function toArray(bool $includeMetadata = true): array
    {
        $data = [
            'marca' => $this->marca,
            'modello' => $this->modello,
            'prezzo' => $this->prezzo,
            'chilometraggio' => $this->chilometraggio,
            'immatricolazione' => $this->immatricolazione->format('Y-m-d'),
            'carburante' => $this->carburante->value,
            'potenza_cv' => $this->potenzaCv,
            'colore' => $this->colore,
            'immagine_url' => $this->getCoverImageUrl(),
            'immagini' => $this->immagini ?? [],
            'garanzia' => $this->garanzia,
            'optional' => $this->optional,
            'porte' => $this->porte,
            'tipo_venditore' => $this->tipoVenditore->value,
        ];

        if ($includeMetadata) {
            $data['id'] = $this->id;
            $data['created_at'] = $this->createdAt?->format(DateTimeInterface::ATOM);
            $data['updated_at'] = $this->updatedAt?->format(DateTimeInterface::ATOM);
        }

        return $data;
    }

    public function getMarca(): string
    {
        return $this->marca;
    }

    public function setMarca(string $marca): self
    {
        $this->marca = $marca;
        return $this;
    }

    public function getModello(): string
    {
        return $this->modello;
    }

    public function setModello(string $modello): self
    {
        $this->modello = $modello;
        return $this;
    }

    public function getPrezzo(): int
    {
        return $this->prezzo;
    }

    public function setPrezzo(int $prezzo): self
    {
        $this->prezzo = $prezzo;
        return $this;
    }

    public function getChilometraggio(): int
    {
        return $this->chilometraggio;
    }

    public function setChilometraggio(int $chilometraggio): self
    {
        $this->chilometraggio = $chilometraggio;
        return $this;
    }

    public function getImmatricolazione(): DateTime
    {
        return $this->immatricolazione;
    }

    public function setImmatricolazione(DateTime $immatricolazione): self
    {
        $this->immatricolazione = $immatricolazione;
        return $this;
    }

    public function getCarburante(): Carburante
    {
        return $this->carburante;
    }

    public function setCarburante(Carburante $carburante): self
    {
        $this->carburante = $carburante;
        return $this;
    }

    public function getPotenzaCv(): int
    {
        return $this->potenzaCv;
    }

    public function setPotenzaCv(int $potenzaCv): self
    {
        $this->potenzaCv = $potenzaCv;
        return $this;
    }

    public function getColore(): string
    {
        return $this->colore;
    }

    public function setColore(string $colore): self
    {
        $this->colore = $colore;
        return $this;
    }

    public function getGaranzia(): bool
    {
        return $this->garanzia;
    }

    public function setGaranzia(bool $garanzia): self
    {
        $this->garanzia = $garanzia;
        return $this;
    }

    public function getOptional(): array
    {
        return $this->optional;
    }

    public function setOptional(array $optional): self
    {
        $this->optional = $optional;
        return $this;
    }

    public function getPorte(): int
    {
        return $this->porte;
    }

    public function setPorte(int $porte): self
    {
        $this->porte = $porte;
        return $this;
    }

    public function getTipoVenditore(): TipoVenditore
    {
        return $this->tipoVenditore;
    }

    public function setTipoVenditore(TipoVenditore $tipoVenditore): self
    {
        $this->tipoVenditore = $tipoVenditore;
        return $this;
    }

    public function getImmagini(): ?array
    {
        return $this->immagini;
    }

    public function setImmagini(?array $immagini): self
    {
        $this->immagini = $immagini;
        return $this;
    }

    public function getCoverImageUrl(): ?string
    {
        foreach ($this->immagini ?? [] as $immagine) {
            if (!empty($immagine['is_cover'])) {
                return $immagine['image_url'];
            }
        }

        return $this->immagini[0]['image_url'] ?? null;
    }

    private static function normalizeImmagini(array $data): ?array
    {
        if (array_key_exists('immagini', $data)) {
            $immagini = $data['immagini'];

            if (!is_array($immagini)) {
                return [];
            }

            return array_values(array_filter(array_map(
                fn (mixed $immagine): ?array => self::normalizeImmagine($immagine),
                $immagini
            )));
        }

        $imageUrl = $data['immagine_url'] ?? $data['immagineUrl'] ?? null;
        if ($imageUrl !== null && trim((string) $imageUrl) !== '') {
            return [[
                'image_url' => trim((string) $imageUrl),
                'is_cover' => true,
            ]];
        }

        return null;
    }

    private static function normalizeImmagine(mixed $immagine): ?array
    {
        if (is_string($immagine)) {
            $imageUrl = trim($immagine);
            return $imageUrl === '' ? null : [
                'image_url' => $imageUrl,
                'is_cover' => false,
            ];
        }

        if (!is_array($immagine)) {
            return null;
        }

        $imageUrl = trim((string) ($immagine['image_url'] ?? $immagine['immagine_url'] ?? $immagine['url'] ?? ''));
        if ($imageUrl === '') {
            return null;
        }

        $normalized = [
            'image_url' => $imageUrl,
            'is_cover' => filter_var($immagine['is_cover'] ?? $immagine['cover'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];

        if (isset($immagine['id'])) {
            $normalized['id'] = (int) $immagine['id'];
        }

        if (isset($immagine['created_at'])) {
            $normalized['created_at'] = (string) $immagine['created_at'];
        }

        return $normalized;
    }
}
?>
