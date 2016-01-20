<?php

namespace TPE\Infrastructure\MyProgramme;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TPE\Domain\MyProgramme\MyProgramme;


class MyProgrammeIsCompletedValidator extends ConstraintValidator
{
    public function validate($myProgramme, Constraint $constraint)
    {
        /** @var MyProgramme $myProgramme */
        if ($myProgramme->isCompleted()) {
            $missing = false;
            foreach ($myProgramme->getInterests() as $interest) {
                if (null === $myProgramme->getPolicy($interest)) {
                    $missing = true;
                }
            }

            if ($missing) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
