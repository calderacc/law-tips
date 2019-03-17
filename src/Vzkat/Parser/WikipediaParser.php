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

        $crawler = new Crawler($this->content);

        $crawler = $crawler->filter('.gallery .gallerybox');

        $signList = [];

        $crawler->each(function(Crawler $node, int $i) use (&$signList) {
            try {
                $number = $node->filter('b')->text();
                $description = $node->filter('.gallerytext p b')->text();
            } catch (\InvalidArgumentException $exception) {
                return;
            }

            $signList[] = $this->createSign($number, $description);
        });

        return $signList;
    }

    protected function createSign(string $number, string $description): Sign
    {
        $sign = new Sign();
        $sign
            ->setNumber($number)
            ->setDescription($description);

        return $sign;
    }
}