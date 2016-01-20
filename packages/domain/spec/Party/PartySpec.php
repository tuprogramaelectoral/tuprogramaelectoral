<?php

namespace spec\TPE\Domain\Party;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PartySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Partido Ficticio', 'PF', 'http://partido-ficticio.es');
    }

    function it_should_extend_initial_data()
    {
        $this->shouldHaveType('TPE\Domain\Data\InitialData');
    }

    function it_should_have_a_name()
    {
        $this->getName()->shouldReturn('Partido Ficticio');
    }

    function it_should_have_an_id_generated_from_the_party_name()
    {
        $this->getId()->shouldReturn('partido-ficticio');
    }

    function it_should_have_an_acronym()
    {
        $this->getAcronym()->shouldReturn('PF');
    }

    function it_should_have_a_programme_url()
    {
        $this->getProgrammeUrl()->shouldReturn('http://partido-ficticio.es');
    }

    function it_throws_an_exception_if_the_party_name_is_empty()
    {
        $this->beConstructedWith('', '');
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_should_throw_an_exception_if_the_party_acronym_is_empty()
    {
        $this->beConstructedWith('name', '');
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_should_throw_an_exception_if_the_programme_url_is_empty_or_not_an_url()
    {
        $this->beConstructedWith('name', 'acronym', 'not-an-url');
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_should_be_possible_to_create_from_JSON()
    {
        $this::createFromJson('{"name": "Partido Ficticio", "acronym": "PF", "programmeUrl": "http://partido-ficticio.es"}')->shouldReturnAnInstanceOf('TPE\Domain\Party\Party');
    }
}
