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

    public function findOneBy(array $criteria)
    {
        if (isset($criteria['id']) && Uuid::isValid($criteria['id'])) {
            return parent::findOneBy($criteria);
        }

        return null;
    }

    public function findNoExpiradoById($id)
    {
        if (Uuid::isValid($id)) {
            return $this->_em->createQueryBuilder()
                ->select('mp')
                ->from('TPE\Dominio\MiPrograma\MiPrograma', 'mp')
                ->where('mp.id = :id')
                ->andWhere('mp.publico = true OR (mp.publico = false AND mp.ultimaModificacion > :fechaCaducidad)')
                ->setParameter('id', $id)
                ->setParameter('fechaCaducidad', new \DateTime('-48 hours'))
                ->getQuery()
                ->getOneOrNullResult();
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
