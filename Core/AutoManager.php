<?php

require_once __DIR__ . '/../Model/Auto.php';

class AutoManager
{
    public function __construct(private PDO $pdo) {}

    public function getAll(array $filters = []): array
    {
        [$whereSql, $params] = $this->buildFilterQuery($filters);
        $stmt = $this->pdo->prepare('SELECT * FROM autos' . $whereSql . ' ORDER BY created_at DESC, id DESC');
        $stmt->execute($params);
        $rows = $stmt->fetchAll();
        $imagesByAutoId = $this->getImagesByAutoIds(array_map(
            fn (array $row): int => (int) $row['id'],
            $rows
        ));

        return array_map(
            fn (array $row): array => $this->rowToAuto($row, $imagesByAutoId[(int) $row['id']] ?? [])->toArray(),
            $rows
        );
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM autos WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? $this->rowToAuto($row, $this->getImagesByAutoId($id))->toArray() : null;
    }

    public function create(Auto $auto): array
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO autos
                (marca, modello, prezzo, chilometraggio, immatricolazione, carburante, potenza_cv, colore, garanzia, optional, porte, tipo_venditore)
             VALUES
                (:marca, :modello, :prezzo, :chilometraggio, :immatricolazione, :carburante, :potenza_cv, :colore, :garanzia, :optional, :porte, :tipo_venditore)'
        );

        $this->pdo->beginTransaction();

        try {
            $stmt->execute($this->autoToParams($auto));
            $id = (int) $this->pdo->lastInsertId();
            $this->replaceImages($id, $auto->getImmagini() ?? []);
            $this->pdo->commit();

            return $this->getById($id);
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
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
        $this->pdo->beginTransaction();

        try {
            $stmt->execute($params);

            if ($auto->getImmagini() !== null) {
                $this->replaceImages($id, $auto->getImmagini());
            }

            $this->pdo->commit();

            return $this->getById($id);
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM autos WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    private function rowToAuto(array $row, array $images = []): Auto
    {
        $row['immagini'] = $images;
        return Auto::fromArray($row);
    }

    private function buildFilterQuery(array $filters): array
    {
        $conditions = [];
        $params = [];

        $this->addTextFilter($conditions, $params, $filters, 'marca');
        $this->addTextFilter($conditions, $params, $filters, 'modello');
        $this->addTextFilter($conditions, $params, $filters, 'colore');
        $this->addExactFilter($conditions, $params, $filters, 'carburante');
        $this->addExactFilter($conditions, $params, $filters, 'tipo_venditore');
        $this->addExactIntFilter($conditions, $params, $filters, 'porte');
        $this->addBooleanFilter($conditions, $params, $filters, 'garanzia');
        $this->addRangeFilter($conditions, $params, $filters, 'prezzo', 'min_prezzo', 'max_prezzo');
        $this->addRangeFilter($conditions, $params, $filters, 'chilometraggio', 'min_chilometraggio', 'max_chilometraggio');
        $this->addRangeFilter($conditions, $params, $filters, 'potenza_cv', 'min_potenza_cv', 'max_potenza_cv');

        if (isset($filters['q']) && trim((string) $filters['q']) !== '') {
            $conditions[] = '(marca LIKE :q OR modello LIKE :q OR colore LIKE :q)';
            $params['q'] = '%' . trim((string) $filters['q']) . '%';
        }

        return [
            $conditions === [] ? '' : ' WHERE ' . implode(' AND ', $conditions),
            $params,
        ];
    }

    private function addTextFilter(array &$conditions, array &$params, array $filters, string $field): void
    {
        if (!isset($filters[$field]) || trim((string) $filters[$field]) === '') {
            return;
        }

        $conditions[] = $field . ' LIKE :' . $field;
        $params[$field] = '%' . trim((string) $filters[$field]) . '%';
    }

    private function addExactFilter(array &$conditions, array &$params, array $filters, string $field): void
    {
        if (!isset($filters[$field]) || trim((string) $filters[$field]) === '') {
            return;
        }

        $conditions[] = $field . ' = :' . $field;
        $params[$field] = trim((string) $filters[$field]);
    }

    private function addExactIntFilter(array &$conditions, array &$params, array $filters, string $field): void
    {
        if (!isset($filters[$field]) || filter_var($filters[$field], FILTER_VALIDATE_INT) === false) {
            return;
        }

        $conditions[] = $field . ' = :' . $field;
        $params[$field] = (int) $filters[$field];
    }

    private function addBooleanFilter(array &$conditions, array &$params, array $filters, string $field): void
    {
        if (!array_key_exists($field, $filters) || $filters[$field] === '') {
            return;
        }

        $value = filter_var($filters[$field], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($value === null) {
            return;
        }

        $conditions[] = $field . ' = :' . $field;
        $params[$field] = $value ? 1 : 0;
    }

    private function addRangeFilter(array &$conditions, array &$params, array $filters, string $field, string $minKey, string $maxKey): void
    {
        if (isset($filters[$minKey]) && filter_var($filters[$minKey], FILTER_VALIDATE_INT) !== false) {
            $conditions[] = $field . ' >= :' . $minKey;
            $params[$minKey] = (int) $filters[$minKey];
        }

        if (isset($filters[$maxKey]) && filter_var($filters[$maxKey], FILTER_VALIDATE_INT) !== false) {
            $conditions[] = $field . ' <= :' . $maxKey;
            $params[$maxKey] = (int) $filters[$maxKey];
        }
    }

    private function getImagesByAutoId(int $autoId): array
    {
        return $this->getImagesByAutoIds([$autoId])[$autoId] ?? [];
    }

    private function getImagesByAutoIds(array $autoIds): array
    {
        $autoIds = array_values(array_unique(array_map('intval', $autoIds)));
        if ($autoIds === []) {
            return [];
        }

        $placeholders = [];
        $params = [];
        foreach ($autoIds as $index => $autoId) {
            $placeholder = 'auto_id_' . $index;
            $placeholders[] = ':' . $placeholder;
            $params[$placeholder] = $autoId;
        }

        $stmt = $this->pdo->prepare(
            'SELECT id, auto_id, image_url, is_cover, created_at
             FROM auto_images
             WHERE auto_id IN (' . implode(', ', $placeholders) . ')
             ORDER BY auto_id ASC, is_cover DESC, id ASC'
        );
        $stmt->execute($params);

        $imagesByAutoId = [];
        foreach ($stmt->fetchAll() as $row) {
            $autoId = (int) $row['auto_id'];
            $imagesByAutoId[$autoId][] = [
                'id' => (int) $row['id'],
                'image_url' => $row['image_url'],
                'is_cover' => (bool) $row['is_cover'],
                'created_at' => $row['created_at'],
            ];
        }

        return $imagesByAutoId;
    }

    private function replaceImages(int $autoId, array $images): void
    {
        $deleteStmt = $this->pdo->prepare('DELETE FROM auto_images WHERE auto_id = :auto_id');
        $deleteStmt->execute(['auto_id' => $autoId]);

        if ($images === []) {
            return;
        }

        $coverIndex = $this->getCoverImageIndex($images);
        $insertStmt = $this->pdo->prepare(
            'INSERT INTO auto_images (auto_id, image_url, is_cover)
             VALUES (:auto_id, :image_url, :is_cover)'
        );

        foreach ($images as $index => $image) {
            $insertStmt->execute([
                'auto_id' => $autoId,
                'image_url' => $image['image_url'],
                'is_cover' => $index === $coverIndex ? 1 : 0,
            ]);
        }
    }

    private function getCoverImageIndex(array $images): int
    {
        foreach ($images as $index => $image) {
            if (!empty($image['is_cover'])) {
                return (int) $index;
            }
        }

        return 0;
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
