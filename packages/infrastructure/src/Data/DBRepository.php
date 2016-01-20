<?php

namespace TPE\Infrastructure\Data;

use Doctrine\ORM\Tools\SchemaTool;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use TPE\Domain\Data\InitialData;
use TPE\Domain\Data\InitialDataRepository;


abstract class DBRepository extends EntityRepository implements InitialDataRepository
{
    /**
     * @param InitialData $dato
     * @param bool $flush
     */
    public function save(InitialData $dato, $flush = true)
    {
        $this->_em->persist($dato);

        if ($flush) {
            $this->_em->flush();
        }
    }
}
