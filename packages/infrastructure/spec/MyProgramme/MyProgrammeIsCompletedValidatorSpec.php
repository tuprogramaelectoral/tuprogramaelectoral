<?php

namespace spec\TPE\Infrastructure\MyProgramme;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use TPE\Domain\MyProgramme\MyProgramme;
use TPE\Infrastructure\MyProgramme\MyProgrammeIsCompleted;


class MyProgrammeIsCompletedValidatorSpec extends ObjectBehavior
{
    function let(
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $violationBuilder
    ) {
        $context->buildViolation(Argument::any())->willReturn($violationBuilder);

        $this->initialize($context);
    }

    function it_should_extend_constraint_validator()
    {
        $this->shouldHaveType('Symfony\Component\Validator\ConstraintValidator');
    }

    function it_should_add_a_violation_if_my_programme_is_completed_and_some_interests_do_not_have_a_linked_policy_during_validation(ExecutionContextInterface $context, ConstraintViolationBuilderInterface $violationBuilder)
    {
        $myProgramme = new MyProgramme(['sanidad' => null, 'educacion' => 'partido-ficticio-educacion'], false, true);

        $this->validate($myProgramme, new MyProgrammeIsCompleted());

        $context->buildViolation((new MyProgrammeIsCompleted())->message)->shouldHaveBeenCalled();
        $violationBuilder->addViolation()->shouldHaveBeenCalled();
    }

    function it_should_add_no_violation_if_my_programme_is_completed_and_every_interest_has_a_linked_policy_during_validation(ExecutionContextInterface $context, ConstraintViolationBuilderInterface $violationBuilder)
    {
        $myProgramme = new MyProgramme(['sanidad' => 'partido-ficticio-sanidad', 'educacion' => 'partido-ficticio-educacion'], false, true);

        $this->validate($myProgramme, new MyProgrammeIsCompleted());

        $context->buildViolation((new MyProgrammeIsCompleted())->message)->shouldNotHaveBeenCalled();
        $violationBuilder->addViolation()->shouldNotHaveBeenCalled();
    }
}
