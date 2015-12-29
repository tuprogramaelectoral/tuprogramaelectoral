<?php

namespace spec\TPE\Domain\Party;

use PhpSpec\ObjectBehavior;


class PartyMemoryRepositorySpec extends ObjectBehavior
{
    function it_should_return_the_classname_of_the_managed_entity()
    {
        $this->getClassName()->shouldReturn('TPE\Domain\Party\Party');
    }
}

