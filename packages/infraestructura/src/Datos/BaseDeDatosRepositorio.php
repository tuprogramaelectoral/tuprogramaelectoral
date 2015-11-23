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

    /**
     * @param DatoInicial[] $datos = null
     */
    public function regenerarDatos(array $datos = null)
    {
        $this->recrearEsquema();
        if (is_array($datos)) {
            foreach ($datos as $dato) {
                $this->save($dato, false);
            }
        }

        $this->_em->flush();
        $this->clear();
    }

    private function recrearEsquema()
    {
        $metadata = $this->_em->getClassMetadata($this->getClassName());
        $this->_em->getConnection()->executeQuery('DROP TABLE IF EXISTS ' . $metadata->getTableName());
        (new SchemaTool($this->_em))->createSchema([$metadata]);
    }
}
