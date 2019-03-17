<?php declare(strict_types=1);

namespace App\Vzkat\Parser;

use App\Entity\Sign;
use Symfony\Component\DomCrawler\Crawler;

class WikipediaParser implements ParserInterface
{
    /** @var string $content */
    protected $content;

    public function setContent(string $content): ParserInterface
    {
        $this->content = $content;

        return $this;
    }

    public function parse(): array
    {
        if (!$this->content) {
            return [];
        }

        $crawler = new Crawler($this->content, null, 'https://de.wikipedia.org/');

        $crawler = $crawler->filter('.gallery .gallerybox');

        $signList = [];

        $crawler->each(function(Crawler $node, int $i) use (&$signList) {
            try {
                $number = $this->parseNumber($node);
                $description = $this->parseDescription($node);
                $imagePageUrl = $this->parseImageUrl($node);

                if ($number && $description) {
                    $signList[] = $this->createSign($number, $description, $imagePageUrl);
                }
            } catch (\InvalidArgumentException $exception) {
            }
        });

        return $signList;
    }

    protected function parseImageUrl(Crawler $crawler): ?string
    {
        $imageElement = $crawler->filter('.thumb a.image');

        if (!$imageElement) {
            return null;
        }

        return $imageElement->link()->getUri();
    }

    protected function parseNumber(Crawler $crawler): ?string
    {
        $number = $crawler->filter('.gallerytext p b')->text();

        return WikipediaNumberParser::parse($number);
    }

    protected function parseDescription(Crawler $crawler): ?string
    {
        $description = $crawler->filter('.gallerytext p')->text();

        return WikipediaDescriptionParser::parse($description);
    }

    protected function createSign(string $number, string $description, string $imagePageUrl): Sign
    {
        $sign = new Sign();
        $sign
            ->setNumber($number)
            ->setDescription($description)
            ->setImagePageUrl($imagePageUrl);

        return $sign;
    }
}