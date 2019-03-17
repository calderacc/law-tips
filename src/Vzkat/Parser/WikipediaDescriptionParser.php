<?php declare(strict_types=1);

namespace App\Vzkat\Parser;

class WikipediaDescriptionParser
{
    public static function parse(string $description): ?string
    {
        return trim($description);
    }
}