<?php

require_once __DIR__ . '/AutoManager.php';

class ViewAuto
{
    public function __construct(private AutoManager $autoManager) {}

    public function getAll(): array
    {
        return $this->autoManager->getAll();
    }

    public function getById(int $id): ?array
    {
        return $this->autoManager->getById($id);
    }
}
?>
