<?php declare(strict_types=1);

namespace App\Vzkat\Importer;

use App\Entity\Sign;
use App\Vzkat\Parser\ParserInterface;
use App\Vzkat\WebLoader\WebLoaderInterface;
use Doctrine\ORM\EntityManagerInterface;

class Importer implements ImporterInterface
{
    /** @var array<Sign> $signList */
    protected $signList;

    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var WebLoaderInterface $webLoader */
    protected $webLoader;

    /** @var ParserInterface $parser */
    protected $parser;

    public function __construct(EntityManagerInterface $entityManager, WebLoaderInterface $webLoader, ParserInterface $parser)
    {
        $this->entityManager = $entityManager;
        $this->webLoader = $webLoader;
        $this->parser = $parser;
    }

    public function import(): ImporterInterface
    {
        $webHtml = $this->webLoader->load();
        $this->signList = $this->parser->setContent($webHtml)->parse();

        /** @var Sign $sign */
        foreach ($this->signList as $sign) {
            $this->entityManager->persist($sign);
        }

        $this->entityManager->flush();

        return $this;
    }

    public function getSignList(): array
    {
        return $this->signList;
    }
}