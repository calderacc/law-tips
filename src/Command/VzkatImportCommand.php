<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Sign;
use DOMElement;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Link;

class VzkatImportCommand extends Command
{
    protected static $defaultName = 'vzkat:import';

    protected function configure()
    {
        $this->setDescription('Import sign list from Wikipedia');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
        */
        $signList = $this->import();

        $table = new Table($output);
        $table->setHeaders(['Number', 'Description']);

        /** @var Sign $sign */
        foreach ($signList as $sign) {
            $table->addRow([$sign->getNumber(), $sign->getDescription()]);
        }

        $table->render();
    }

    protected function import(): array
    {
        $client = new Client();
        $response = $client->get('https://de.wikipedia.org/wiki/Bildtafel_der_Verkehrszeichen_in_der_Bundesrepublik_Deutschland_seit_2017');

        $crawler = new Crawler((string) $response->getBody());

        $crawler = $crawler->filter('.gallery .gallerybox');

        $signList = [];

        $crawler->each(function(Crawler $node, int $i) use (&$signList) {
            try {
                $number = $node->filter('b')->text();
                $description = $node->filter('.gallerytext p b')->text();

            } catch (\InvalidArgumentException $exception) {
                return;
            }

            $sign = new Sign();
            $sign
                ->setNumber($number)
                ->setDescription($description);

            $signList[] = $sign;
        });

        return $signList;
    }
}
