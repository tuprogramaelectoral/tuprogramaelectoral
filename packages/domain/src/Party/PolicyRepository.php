<?php

namespace TPE\Domain\Party;

use TPE\Domain\Data\InitialDataRepository;


interface PolicyRepository extends InitialDataRepository
{
    public function findPolicyByEditionScopeAndParty($edition, $scope, $party);
}
