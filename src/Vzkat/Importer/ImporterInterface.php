<?php declare(strict_types=1);

namespace App\Vzkat\Importer;

interface ImporterInterface
{
    public function import(): ImporterInterface;
    public function getSignList(): array;
}