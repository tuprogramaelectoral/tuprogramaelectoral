<?php

namespace TPE\Domain\Field;

use TPE\Domain\Data\MemoryRepository;


class FieldMemoryRepository extends MemoryRepository
{
    /**
     * @return string
     */
    public function getClassName()
    {
        return 'TPE\Domain\Field\Field';
    }
}
