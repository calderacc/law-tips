<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Sign;
use App\Vzkat\ImageImporter\ImageImporterInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImageImportCommand extends Command
{
    protected static $defaultName = 'vzkat:import-images';

    protected ManagerRegistry $registry;

    protected ImageImporterInterface $imageImporter;

    public function __construct($name = null, ManagerRegistry $registry, ImageImporterInterface $imageImporter)
    {
        $this->registry = $registry;
        $this->imageImporter = $imageImporter;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import sign files from Wikimedia Commons')
            ->addOption('overwrite', null, InputOption::VALUE_OPTIONAL, 'Overwrite old images', false)
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Number of signs to renew', 25)
            ->addOption('offset', 'o', InputOption::VALUE_REQUIRED, 'Number of signs to start', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $signList = $this->registry->getRepository(Sign::class)->findForImageImport($input->getOption('overwrite') !== null ? true : false, (int) $input->getOption('limit'), (int) $input->getOption('offset'));

        $progressBar = new ProgressBar($output, count($signList));

        $table = new Table($output);
        $table->setHeaders(['Number', 'Image Url']);

        /** @var Sign $sign */
        foreach ($signList as $sign) {
            $sign = $this->imageImporter->importImageForSign($sign);

            $progressBar->advance();
            $table->addRow([$sign->getNumber(), $sign->getDescription()]);
        }

        $this->registry->getManager()->flush();
        $progressBar->finish();
        $table->render();

        return 0;
    }
}
