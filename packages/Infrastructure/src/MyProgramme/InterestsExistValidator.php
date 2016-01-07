<?php

namespace TPE\Infrastructure\MyProgramme;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use TPE\Domain\MyProgramme\MyProgrammeRepository;


class InterestsExistValidator extends ConstraintValidator
{
    /**
     * @var MyProgrammeRepository
     */
    private $repository;


    public function __construct(MyProgrammeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function validate($values, Constraint $constraint)
    {
        /** @var InterestsExist $constraint */
        if (is_array($values)) {
            $interests = [];
            $policies = [];
            foreach ($values as $interest => $policy) {
                if (!empty($interest)) {
                    $interests[] = $interest;
                }
                if (!empty($policy)) {
                    $policies[] = $policy;
                }
            }

            if (is_array($interests) && !$this->repository->interestsExist($interests)) {
                $this->context
                    ->buildViolation($constraint->interestsMissing)
                    ->addViolation();
            }

            if (is_array($policies) && !$this->repository->policiesExist($policies)) {
                $this->context
                    ->buildViolation($constraint->policiesMissing)
                    ->addViolation();
            }
        } else {
            $this->context
                ->buildViolation($constraint->notArray)
                ->addViolation();
        }
    }
}
