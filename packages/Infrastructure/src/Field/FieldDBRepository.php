<?php

namespace TPE\Infrastructure\Field;

use TPE\Domain\Field\Field;
use TPE\Infrastructure\Data\DBRepository;


class FieldDBRepository extends DBRepository
{
    /**
     * @param $id
     * @return Field|null
     */
    public function findFieldWithPoliciesById($id)
    {
        return $this->_em->createQueryBuilder()
            ->select('f, p, pf, pp')
            ->from('TPE\Domain\Field\Field', 'f')
            ->leftJoin('f.policies', 'p')
            ->leftJoin('p.field', 'pf')
            ->leftJoin('p.party', 'pp')
            ->where('f.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
