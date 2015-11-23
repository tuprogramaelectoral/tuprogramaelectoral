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
use TPE\Dominio\Datos\Cargador;
use TPE\Infraestructura\Datos\LectorDeFicheros;


class CargaCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('datos:carga')
            ->setDescription('Load data into the database')
            ->addArgument(
                'directorio',
                InputArgument::REQUIRED,
                'Directorio con los datos iniciales'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('directorio');

        $this->getCargador()->cargar(new LectorDeFicheros($path));

    }

    /**
     * @return Cargador
     */
    private function getCargador()
    {
        return $this->getContainer()->get('cargador_de_datos');

    }
}
