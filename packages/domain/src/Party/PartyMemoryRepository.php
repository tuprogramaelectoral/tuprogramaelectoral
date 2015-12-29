<?php

namespace TPE\Domain\Party;

use TPE\Domain\Data\MemoryRepository;


class PartyMemoryRepository extends MemoryRepository
{
    /**
     * @return string
     */
    public function getClassName()
    {
        return 'TPE\Domain\Party\Party';
    }
}
