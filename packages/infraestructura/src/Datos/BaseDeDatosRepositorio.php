<?php

namespace TPE\Infraestructura\Datos;

use Doctrine\ORM\Tools\SchemaTool;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use TPE\Dominio\Ambito\Ambito;
use TPE\Dominio\Datos\DatoInicial;
use TPE\Dominio\Datos\DatoInicialRepositorio;


abstract class BaseDeDatosRepositorio extends EntityRepository implements DatoInicialRepositorio
{
    /**
     * @param DatoInicial $ambito
     * @param bool $flush
     */
    public function save(DatoInicial $ambito, $flush = true)
    {
        $this->_em->persist($ambito);

        if ($flush) {
            $this->_em->flush();
        }
    }
}
