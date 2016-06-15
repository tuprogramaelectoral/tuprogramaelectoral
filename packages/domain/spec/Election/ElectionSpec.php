<?php

namespace spec\TPE\Domain\Election;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TPE\Domain\Election\Election;

class ElectionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(12, '2015-12-20');
    }

    function it_should_have_an_edition()
    {
        $this->getEdition()->shouldReturn(12);
    }

    function it_should_have_a_date()
    {
        $this->getDate()->shouldBeLike(new \DateTime('2015-12-20'));
    }

    function it_should_be_possible_to_create_from_JSON()
    {
        $this::createFromJson('{"edition": "1", "date": "1977-06-15"}')
            ->shouldBeLike(new Election(1, '1977-06-15'));
    }
}
