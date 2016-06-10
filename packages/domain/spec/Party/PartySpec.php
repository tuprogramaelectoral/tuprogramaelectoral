<?php

namespace spec\TPE\Domain\Party;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TPE\Domain\Election\Election;
use TPE\Domain\Party\Party;

class PartySpec extends ObjectBehavior
{
    function let(Election $election)
    {
        $election->getId()->willReturn(1);

        $this->beConstructedWith($election, 'Partido Ficticio', 'PF', 'http://partido-ficticio.es');
    }

    function it_should_extend_initial_data()
    {
        $this->shouldHaveType('TPE\Domain\Data\InitialData');
    }

    function it_should_have_a_name()
    {
        $this->getName()->shouldReturn('Partido Ficticio');
    }

    function it_should_have_an_id_generated_from_the_party_name_and_election_edition()
    {
        $this->getId()->shouldReturn('1_partido-ficticio');
    }

    function it_should_have_party_id_generated_from_the_party_name()
    {
        $this->getParty()->shouldReturn('partido-ficticio');
    }

    function it_should_have_an_acronym()
    {
        $this->getAcronym()->shouldReturn('PF');
    }

    function it_should_have_a_programme_url()
    {
        $this->getProgrammeUrl()->shouldReturn('http://partido-ficticio.es');
    }

    function it_should_take_part_in_an_election(Election $election)
    {
        $this->getElection()->shouldReturn($election);
    }

    function it_throws_an_exception_if_the_party_name_is_empty(Election $election)
    {
        $this->beConstructedWith($election, '', '');
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_should_throw_an_exception_if_the_party_acronym_is_empty(Election $election)
    {
        $this->beConstructedWith($election, 'name', '');
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_should_throw_an_exception_if_the_programme_url_is_empty_or_not_an_url(Election $election)
    {
        $this->beConstructedWith($election, 'name', 'acronym', 'not-an-url');
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_should_be_possible_to_create_from_JSON(Election $election)
    {
        $this::createFromJson($election, '{"name": "Partido Ficticio", "acronym": "PF", "programmeUrl": "http://partido-ficticio.es"}')
            ->shouldBeLike(new Party($election->getWrappedObject(), "Partido Ficticio", "PF", "http://partido-ficticio.es"));
    }
}
