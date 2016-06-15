<?php

namespace TPE\Domain\Election;

use TPE\Domain\Data\InitialDataRepository;


interface ElectionRepository extends InitialDataRepository
{
    public function latestEdition();
}
