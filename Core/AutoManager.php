<?php

require_once __DIR__ . '/../Model/Auto.php';

class AutoManager
{
    public function __construct(private PDO $pdo) {}

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM autos ORDER BY created_at DESC, id DESC');

        return array_map(
            fn (array $row): array => $this->rowToAuto($row)->toArray(),
            $stmt->fetchAll()
        );
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM autos WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->rowToAuto($row)->toArray() : null;
    }

    public function create(Auto $auto): array
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO autos
                (marca, modello, prezzo, chilometraggio, immatricolazione, carburante, potenza_cv, colore, garanzia, optional, porte, tipo_venditore)
             VALUES
                (:marca, :modello, :prezzo, :chilometraggio, :immatricolazione, :carburante, :potenza_cv, :colore, :garanzia, :optional, :porte, :tipo_venditore)'
        );

        $stmt->execute($this->autoToParams($auto));

        return $this->getById((int) $this->pdo->lastInsertId());
    }

    public function update(int $id, Auto $auto): ?array
    {
        if ($this->getById($id) === null) {
            return null;
        }

        $params = $this->autoToParams($auto);
        $params['id'] = $id;

        $stmt = $this->pdo->prepare(
            'UPDATE autos SET
                marca = :marca,
                modello = :modello,
                prezzo = :prezzo,
                chilometraggio = :chilometraggio,
                immatricolazione = :immatricolazione,
                carburante = :carburante,
                potenza_cv = :potenza_cv,
                colore = :colore,
                garanzia = :garanzia,
                optional = :optional,
                porte = :porte,
                tipo_venditore = :tipo_venditore
             WHERE id = :id'
        );
        $stmt->execute($params);

        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM autos WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    private function rowToAuto(array $row): Auto
    {
        return Auto::fromArray($row);
    }

    private function autoToParams(Auto $auto): array
    {
        return [
            'marca' => $auto->getMarca(),
            'modello' => $auto->getModello(),
            'prezzo' => $auto->getPrezzo(),
            'chilometraggio' => $auto->getChilometraggio(),
            'immatricolazione' => $auto->getImmatricolazione()->format('Y-m-d'),
            'carburante' => $auto->getCarburante()->value,
            'potenza_cv' => $auto->getPotenzaCv(),
            'colore' => $auto->getColore(),
            'garanzia' => $auto->getGaranzia() ? 1 : 0,
            'optional' => json_encode($auto->getOptional(), JSON_UNESCAPED_UNICODE),
            'porte' => $auto->getPorte(),
            'tipo_venditore' => $auto->getTipoVenditore()->value,
        ];
    }
}
?>
