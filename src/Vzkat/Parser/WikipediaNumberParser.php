<?php declare(strict_types=1);

namespace App\Vzkat\Parser;

class WikipediaNumberParser
{
    public static function parse(string $number): ?string
    {
        preg_match('/^(\d{3,4})((\-\d{1,2}\,\d{1,2})|([\.\-]?\d{0,4})([\-]?\d{0,3}))$/', $number, $matches);

        if (array_key_exists(2, $matches)) {
            return $matches[2];
        }

        return null;
    }
}