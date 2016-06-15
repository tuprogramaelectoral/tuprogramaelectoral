<?php

namespace TPE\Infrastructure\Election;

use TPE\Domain\Election\Election;
use TPE\Domain\Election\ElectionRepository;
use TPE\Domain\Scope\Scope;
use TPE\Infrastructure\Data\DBRepository;


class ElectionDBRepository extends DBRepository implements ElectionRepository
{
    /**
     * @var integer
     */
    private $edition;


    public function latestEdition()
    {
        if ($this->edition === null) {
            $this->edition = $this->_em
                ->createQueryBuilder()
                ->select('max(e.id)')
                ->from(Election::class, 'e')
                ->getQuery()
                ->getSingleScalarResult();
        }

        return $this->edition;
    }
}
