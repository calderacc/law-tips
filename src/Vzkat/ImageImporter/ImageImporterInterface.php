<?php declare(strict_types=1);

namespace App\Vzkat\ImageImporter;

use App\Entity\Sign;

interface ImageImporterInterface
{
    public function importImageForSign(Sign $sign): Sign;
}