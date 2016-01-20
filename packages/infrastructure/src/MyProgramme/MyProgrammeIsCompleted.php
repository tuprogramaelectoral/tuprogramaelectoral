<?php

namespace TPE\Infrastructure\MyProgramme;

use Symfony\Component\Validator\Constraint;


class MyProgrammeIsCompleted extends Constraint
{
    public $message = 'A completed programme can not contain policies unassigned';

    public function validatedBy()
    {
        return 'my_programme_is_completed_validator';
    }
}
