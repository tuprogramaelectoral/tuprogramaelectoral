<?php

namespace TPE\Infrastructure\MyProgramme;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TPE\Domain\MyProgramme\MyProgramme;
use TPE\Domain\MyProgramme\MyProgrammeRepository;
use TPE\Infrastructure\Election\ElectionDBRepository;


class InterestsExistValidator extends ConstraintValidator
{
    /**
     * @var MyProgrammeRepository
     */
    private $myProgrammeRepository;


    public function __construct(MyProgrammeRepository $myProgrammeRepository)
    {
        $this->myProgrammeRepository = $myProgrammeRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /** @var MyProgramme $value */

        if (!$this->myProgrammeRepository->interestsExist($value->getEdition(), $value->getInterests())) {
            $this->context
                ->buildViolation($constraint->interestsMissing)
                ->addViolation();
        }

        if (!$this->myProgrammeRepository->policiesExist($value->getEdition(), $value->getPolicies())) {
            $this->context
                ->buildViolation($constraint->policiesMissing)
                ->addViolation();
        }
    }
}
