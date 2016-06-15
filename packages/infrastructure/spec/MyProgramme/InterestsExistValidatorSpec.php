<?php

namespace spec\TPE\Infrastructure\MyProgramme;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use TPE\Domain\MyProgramme\MyProgramme;
use TPE\Domain\MyProgramme\MyProgrammeRepository;
use TPE\Infrastructure\MyProgramme\InterestsExist;

class InterestsExistValidatorSpec extends ObjectBehavior
{
    function let(
        MyProgrammeRepository $repository,
        MyProgramme $value,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $violationBuilder
    ) {
        $repository->interestsExist(1, ['sanidad'])->willReturn(true);
        $repository->interestsExist(1, Argument::any())->willReturn(false);
        $repository->policiesExist(1, ['sanidad' => 'partido-ficticio-sanidad'])->willReturn(true);
        $repository->policiesExist(1, Argument::any())->willReturn(false);
        $context->buildViolation(Argument::any())->willReturn($violationBuilder);
        $value->getEdition()->willReturn(1);
        $value->getInterests()->willReturn(['sanidad']);
        $value->getPolicies()->willReturn(['sanidad' => 'partido-ficticio-sanidad']);

        $this->beConstructedWith($repository);
        $this->initialize($context);
    }

    function it_should_extend_constraint_validator()
    {
        $this->shouldHaveType('Symfony\Component\Validator\ConstraintValidator');
    }

    function it_should_add_a_violation_if_some_of_the_interests_do_not_exist_during_validation(MyProgramme $value, ExecutionContextInterface $context, ConstraintViolationBuilderInterface $violationBuilder)
    {
        $value->getInterests()->willReturn(['nonExistingInterest']);

        $this->validate($value, new InterestsExist());

        $constraint = new InterestsExist();
        $context->buildViolation($constraint->interestsMissing)->shouldHaveBeenCalledTimes(1);
        $violationBuilder->addViolation()->shouldHaveBeenCalledTimes(1);
    }

    function it_should_add_a_violation_if_some_of_the_policies_do_not_exist_during_validation(MyProgramme $value, ExecutionContextInterface $context, ConstraintViolationBuilderInterface $violationBuilder)
    {
        $value->getInterests()->willReturn(['sanidad']);
        $value->getPolicies()->willReturn(['sanidad' => 'nonExistingPolicy']);

        $this->validate($value, new InterestsExist());

        $constraint = new InterestsExist();
        $context->buildViolation($constraint->policiesMissing)->shouldHaveBeenCalledTimes(1);
        $violationBuilder->addViolation()->shouldHaveBeenCalledTimes(1);
    }

    function it_should_add_two_violations_if_some_of_the_policies_and_interests_do_not_exist_during_validation(MyProgramme $value, ExecutionContextInterface $context, ConstraintViolationBuilderInterface $violationBuilder)
    {
        $value->getInterests()->willReturn(['nonExistingInterest']);
        $value->getPolicies()->willReturn(['nonExistingInterest' => 'nonExistingPolicy']);

        $this->validate($value, new InterestsExist());

        $constraint = new InterestsExist();
        $context->buildViolation($constraint->policiesMissing)->shouldHaveBeenCalledTimes(1);
        $context->buildViolation($constraint->interestsMissing)->shouldHaveBeenCalledTimes(1);
        $violationBuilder->addViolation()->shouldHaveBeenCalledTimes(2);
    }

    function it_should_add_no_violation_if_the_policies_and_interests_exist_during_validation(MyProgramme $value, ExecutionContextInterface $context, ConstraintViolationBuilderInterface $violationBuilder)
    {
        $this->validate($value, new InterestsExist());

        $constraint = new InterestsExist();
        $context->buildViolation($constraint->policiesMissing)->shouldNotHaveBeenCalled();
        $context->buildViolation($constraint->interestsMissing)->shouldNotHaveBeenCalled();
        $violationBuilder->addViolation()->shouldNotHaveBeenCalled();
    }
}
