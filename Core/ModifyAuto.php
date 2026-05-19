<?php

require_once __DIR__ . '/AutoManager.php';

class ModifyAuto
{
    public function __construct(private AutoManager $autoManager) {}

    public function update(int $id, Auto $auto): ?array
    {
        return $this->autoManager->update($id, $auto);
    }
}
?>
