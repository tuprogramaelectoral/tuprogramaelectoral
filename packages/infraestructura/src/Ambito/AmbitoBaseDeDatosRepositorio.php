<?php

namespace TPE\Infraestructura\Ambito;

use TPE\Infraestructura\Datos\BaseDeDatosRepositorio;


class AmbitoBaseDeDatosRepositorio extends BaseDeDatosRepositorio
{
    public function findAmbitoYPoliticasById($id)
    {
        return $this->_em->createQueryBuilder()
            ->select('a, p, pa, pp')
            ->from('TPE\Dominio\Ambito\Ambito', 'a')
            ->leftJoin('a.politicas', 'p')
            ->leftJoin('p.ambito', 'pa')
            ->leftJoin('p.partido', 'pp')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
