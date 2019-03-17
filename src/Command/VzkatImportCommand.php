<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Sign;
use App\Vzkat\Importer\ImporterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VzkatImportCommand extends Command
{
    /** @var string $defaultName */
    protected static $defaultName = 'vzkat:import';

    /** @var ImporterInterface $importer */
    protected $importer;

    public function __construct($name = null, ImporterInterface $importer)
    {
        $this->importer = $importer;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Import sign list from Wikipedia');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $signList = $this->importer->import()->getSignList();

        $table = new Table($output);
        $table->setHeaders(['Number', 'Description']);

        /** @var Sign $sign */
        foreach ($signList as $sign) {
            $table->addRow([$sign->getNumber(), $sign->getDescription()]);
        }

        $table->render();
    }
}
