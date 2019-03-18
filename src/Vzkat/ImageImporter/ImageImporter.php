<?php declare(strict_types=1);

namespace App\Vzkat\ImageImporter;

use App\Entity\Sign;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageImporter implements ImageImporterInterface
{
    public function importImageForSign(Sign $sign): Sign
    {
        $imagePageContent = $this->loadImagePageContent($sign);
        $imageUri = $this->fetchFullsizeImageUri($imagePageContent);

        $imageContent = $this->loadImageContent($imageUri);

        $sign = $this->fakeUpload($sign, $imageContent);

        return $sign;
    }

    protected function fakeUpload(Sign $sign, string $imageContent): Sign
    {
        $filename = sprintf('%s', uniqid('', true));
        $path = sprintf('/tmp/%s', $filename);

        $filesystem = new Filesystem();
        $filesystem->dumpFile($path, $imageContent);

        $file = new UploadedFile($path, $filename, null, null, true);
        $sign->setImageFile($file);

        return $sign;
    }

    protected function loadImagePageContent(Sign $sign): string
    {
        $client = new Client();
        $response = $client->get($sign->getImagePageUrl());

        return (string) $response->getBody();
    }

    public function loadImageContent(string $imageUri): string
    {
        $client = new Client();
        $imageResponse = $client->get($imageUri);

        return (string) $imageResponse->getBody();
    }

    protected function fetchFullsizeImageUri(string $pageContent): string
    {
        $crawler = new Crawler($pageContent, null, 'https://commons.wikimedia.org/');
        $linkElement = $crawler->filter('.fullMedia a')->first();

        return $linkElement->link()->getUri();
    }
}