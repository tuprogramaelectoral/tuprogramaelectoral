<?php

namespace TPE\Infrastructure\MyProgramme;

use Symfony\Component\Validator\Constraint;


class InterestsExist extends Constraint
{
    public $interestsMissing = 'Unable to find some of the interests in the system';
    public $policiesMissing = 'Unable to find some of the policies in the system';
    public $notArray = 'Missing list of interests and policies';

    public function validatedBy()
    {
        return 'interests_exist_validator';
    }
}
