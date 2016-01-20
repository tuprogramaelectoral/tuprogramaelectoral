<?php

namespace TPE\Domain\MyProgramme;

use TPE\Domain\Data\InitialDataRepository;


interface MyProgrammeRepository extends InitialDataRepository
{
    public function findNotExpiredById($id);

    public function interestsExist(array $interests);

    public function policiesExist(array $policies);
}
