<?php

namespace spec\TPE\Infrastructure\MyProgramme;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InterestsExistSpec extends ObjectBehavior
{
    function it_should_extend_constraint()
    {
        $this->shouldHaveType('Symfony\Component\Validator\Constraint');
    }

    function it_should_be_validated_by_interests_exist_validator()
    {
        $this->validatedBy()->shouldReturn('interests_exist_validator');
    }
}
