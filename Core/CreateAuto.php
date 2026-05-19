<?php

require_once __DIR__ . '/AutoManager.php';

class CreateAuto
{
    public function __construct(private AutoManager $autoManager) {}

    public function create(Auto $auto): array
    {
        return $this->autoManager->create($auto);
    }
}
?>
