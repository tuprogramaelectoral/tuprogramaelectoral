<?php

namespace AppBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use TPE\Infrastructure\Data\Loader;
use TPE\Infrastructure\Data\ReaderOfFiles;


class LoaderCommand extends ContainerAwareCommand
{
    const ARG_DIRECTORY = 'directory';
    const OPT_REGENERATE = 'regenerate';

    protected function configure()
    {
        $this
            ->setName('data:load')
            ->setDescription('Load data into the database')
            ->addArgument(
                self::ARG_DIRECTORY,
                InputArgument::REQUIRED,
                'Directory containing the initial data'
            )
            ->addOption(
                self::OPT_REGENERATE,
                null,
                InputOption::VALUE_NONE,
                'regenerate DB schema'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument(self::ARG_DIRECTORY);

        if ($input->getOption(self::OPT_REGENERATE)) {
            $this->getLoader()->regenerateScheme();
        }

        $this->getLoader()->load(new ReaderOfFiles($path));
    }

    /**
     * @return Loader
     */
    private function getLoader()
    {
        return $this->getContainer()->get('data_loader');

    }
}
