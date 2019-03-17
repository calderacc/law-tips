<?php declare(strict_types=1);

namespace App\Vzkat\WebLoader;

use GuzzleHttp\Client;

class WebLoader implements WebLoaderInterface
{
    public function load(): string
    {
        $client = new Client();
        $response = $client->get(self::WIKIPEDIA_PAGE);

        return (string) $response->getBody();
    }
}