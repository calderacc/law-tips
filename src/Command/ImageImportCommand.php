<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Sign;
use GuzzleHttp\Client;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageImportCommand extends Command
{
    /** @var string $defaultName */
    protected static $defaultName = 'vzkat:import-images';

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct($name = null, RegistryInterface $registry)
    {
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Import sign files from Wikimedia Commons');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $signList = $this->registry->getRepository(Sign::class)->findAll();

        $table = new Table($output);
        $table->setHeaders(['Number', 'Image Url']);

        /** @var Sign $sign */
        foreach ($signList as $sign) {
            $client = new Client();
            $response = $client->get($sign->getImagePageUrl());

            $crawler = new Crawler((string) $response->getBody(), null, 'https://commons.wikimedia.org/');
            $linkElement = $crawler->filter('.fullMedia a')->first();
            $imageUri = $linkElement->link()->getUri();

            $imageResponse = $client->get($imageUri);
            $imageContent = (string) $imageResponse->getBody();

            $filename = sprintf('%s', uniqid());
            $path = sprintf('/tmp/%s', $filename);

            $filesystem = new Filesystem();
            $filesystem->dumpFile($path, $imageContent);

            $file = new UploadedFile($path, $filename, null, null, true);
            $sign->setImageFile($file);

            $table->addRow([$sign->getNumber(), $sign->getDescription()]);

            $this->registry->getManager()->flush();

            break;
        }

        $table->render();
    }
}
