<?php

namespace spec\TPE\Infrastructure\MyProgramme;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use TPE\Domain\MyProgramme\MyProgrammeRepository;
use TPE\Infrastructure\MyProgramme\InterestsExist;

class InterestsExistValidatorSpec extends ObjectBehavior
{
    function let(
        MyProgrammeRepository $repository,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $violationBuilder
    ) {
        $repository->interestsExist(['sanidad'])->willReturn(true);
        $repository->interestsExist(Argument::any())->willReturn(false);
        $repository->policiesExist(['partido-ficticio-sanidad'])->willReturn(true);
        $repository->policiesExist(Argument::any())->willReturn(false);
        $context->buildViolation(Argument::any())->willReturn($violationBuilder);

        $this->beConstructedWith($repository);
        $this->initialize($context);
    }

    function it_should_extend_constraint_validator()
    {
        $this->shouldHaveType('Symfony\Component\Validator\ConstraintValidator');
    }

    function it_should_add_a_violation_if_values_are_not_an_array_during_validation(ExecutionContextInterface $context, ConstraintViolationBuilderInterface $violationBuilder)
    {
        $this->validate(null, new InterestsExist());

        $context->buildViolation((new InterestsExist())->notArray)->shouldHaveBeenCalled();
        $violationBuilder->addViolation()->shouldHaveBeenCalled();
    }

    function it_should_add_a_violation_if_some_of_the_interests_do_not_exist_during_validation(ExecutionContextInterface $context, ConstraintViolationBuilderInterface $violationBuilder)
    {
        $this->validate(['nonExistingInterest' => 'partido-ficticio-sanidad'], new InterestsExist());

        $constraint = new InterestsExist();
        $context->buildViolation($constraint->interestsMissing)->shouldHaveBeenCalledTimes(1);
        $violationBuilder->addViolation()->shouldHaveBeenCalledTimes(1);
    }

    function it_should_add_a_violation_if_some_of_the_policies_do_not_exist_during_validation(ExecutionContextInterface $context, ConstraintViolationBuilderInterface $violationBuilder)
    {
        $this->validate(['sanidad' => 'nonExistingPolicy'], new InterestsExist());

        $constraint = new InterestsExist();
        $context->buildViolation($constraint->policiesMissing)->shouldHaveBeenCalledTimes(1);
        $violationBuilder->addViolation()->shouldHaveBeenCalledTimes(1);
    }

    function it_should_add_two_violations_if_some_of_the_policies_and_interests_do_not_exist_during_validation(ExecutionContextInterface $context, ConstraintViolationBuilderInterface $violationBuilder)
    {
        $this->validate(['nonExistingInterest' => 'nonExistingPolicy'], new InterestsExist());

        $constraint = new InterestsExist();
        $context->buildViolation($constraint->policiesMissing)->shouldHaveBeenCalledTimes(1);
        $context->buildViolation($constraint->interestsMissing)->shouldHaveBeenCalledTimes(1);
        $violationBuilder->addViolation()->shouldHaveBeenCalledTimes(2);
    }

    function it_should_add_no_violation_if_the_policies_and_interests_exist_during_validation(ExecutionContextInterface $context, ConstraintViolationBuilderInterface $violationBuilder)
    {
        $this->validate(['sanidad' => 'partido-ficticio-sanidad'], new InterestsExist());

        $constraint = new InterestsExist();
        $context->buildViolation($constraint->policiesMissing)->shouldNotHaveBeenCalled();
        $context->buildViolation($constraint->interestsMissing)->shouldNotHaveBeenCalled();
        $violationBuilder->addViolation()->shouldNotHaveBeenCalled();
    }
}
