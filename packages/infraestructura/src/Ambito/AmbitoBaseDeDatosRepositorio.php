<?php

namespace TPE\Infraestructura\Ambito;

use TPE\Infraestructura\Datos\BaseDeDatosRepositorio;


class AmbitoBaseDeDatosRepositorio extends BaseDeDatosRepositorio
{
    public function findAmbitoYPoliticasById($id)
    {
        return $this->_em->createQueryBuilder()
            ->select('a, p')
            ->from('TPE\Dominio\Ambito\Ambito', 'a')
            ->leftJoin('a.politicas', 'p')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
