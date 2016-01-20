<?php

namespace spec\TPE\Infrastructure\MyProgramme;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MyProgrammeIsCompletedSpec extends ObjectBehavior
{
    function it_should_extend_constraint()
    {
        $this->shouldHaveType('Symfony\Component\Validator\Constraint');
    }

    function it_should_be_validated_by_my_programme_is_completed_validator()
    {
        $this->validatedBy()->shouldReturn('my_programme_is_completed_validator');
    }
}
