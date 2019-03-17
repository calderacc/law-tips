<?php declare(strict_types=1);

namespace App\Vzkat\WebLoader;

interface WebLoaderInterface
{
    public const WIKIPEDIA_PAGE = 'https://de.wikipedia.org/wiki/Bildtafel_der_Verkehrszeichen_in_der_Bundesrepublik_Deutschland_seit_2017';

    public function load(): string;
}