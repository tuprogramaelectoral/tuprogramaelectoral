<?php

namespace TPE\Infraestructura\MiPrograma;

use Doctrine\DBAL\Types\Type;
use Ramsey\Uuid\Uuid;
use TPE\Dominio\Datos\DatoInicial;
use TPE\Dominio\MiPrograma\MiPrograma;
use TPE\Infraestructura\Datos\BaseDeDatosRepositorio;


class MiProgramaBaseDeDatosRepositorio extends BaseDeDatosRepositorio
{
    public function createNew()
    {
        return new MiPrograma([]);
    }

    public function findById($id)
    {
        if (Uuid::isValid($id)) {
            return $this->findOneBy(['id' => $id]);
        }

        return null;
    }

    public function existenLosIntereses(array $intereses)
    {
        return $this->_em
            ->createQueryBuilder()
            ->select('count(a.id)')
            ->from('TPE\Dominio\Ambito\Ambito', 'a')
            ->where('a.id IN (:intereses)')
            ->setParameter('intereses', $intereses)
            ->getQuery()
            ->getSingleScalarResult() == count($intereses);
    }

    public function existenLasPoliticas(array $politicas)
    {
        return $this->_em
            ->createQueryBuilder()
            ->select('count(p.id)')
            ->from('TPE\Dominio\Partido\Politica', 'p')
            ->where('p.id IN (:politicas)')
            ->setParameter('politicas', $politicas)
            ->getQuery()
            ->getSingleScalarResult() == count($politicas);
    }
}
