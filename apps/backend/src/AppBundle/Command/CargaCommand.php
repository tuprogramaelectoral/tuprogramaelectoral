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
use TPE\Infraestructura\Datos\Cargador;
use TPE\Infraestructura\Datos\LectorDeFicheros;


class CargaCommand extends ContainerAwareCommand
{
    const ARG_DIRECTORIO = 'directorio';
    const OPT_REGENERAR = 'regenerar';

    protected function configure()
    {
        $this
            ->setName('datos:carga')
            ->setDescription('Load data into the database')
            ->addArgument(
                self::ARG_DIRECTORIO,
                InputArgument::REQUIRED,
                'Directorio con los datos iniciales'
            )
            ->addOption(
                self::OPT_REGENERAR,
                null,
                InputOption::VALUE_NONE,
                'regenera esquema de base de datos'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument(self::ARG_DIRECTORIO);

        if ($input->getOption(self::OPT_REGENERAR)) {
            $this->getCargador()->regenerarEsquema();
        }

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
