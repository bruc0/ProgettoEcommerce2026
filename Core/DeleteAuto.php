<?php

require_once __DIR__ . '/AutoManager.php';

class DeleteAuto
{
    public function __construct(private AutoManager $autoManager) {}

    public function delete(int $id): bool
    {
        return $this->autoManager->delete($id);
    }
}
?>
