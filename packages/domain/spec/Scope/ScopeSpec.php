<?php

namespace spec\TPE\Domain\Scope;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TPE\Domain\Election\Election;
use TPE\Domain\Party\Policy;
use TPE\Domain\Scope\Scope;

class ScopeSpec extends ObjectBehavior
{
    function let(Election $election)
    {
        $election->getId()->willReturn(1);

        $this->beConstructedWith($election, 'Administración Pública');
    }

    function it_should_extends_initial_data()
    {
        $this->shouldHaveType('TPE\Domain\Data\InitialData');
    }

    function it_should_have_a_name()
    {
        $this->getName()->shouldReturn('Administración Pública');
    }

    function it_should_have_an_id_generated_from_its_name_and_election_edition()
    {
        $this->getId()->shouldReturn('1_administracion-publica');
    }

    function it_should_have_a_scope_id_generated_from_its_name()
    {
        $this->getScope()->shouldReturn('administracion-publica');
    }

    function it_should_have_no_policies_by_default()
    {
        $this->getPolicies()->shouldReturn([]);
    }

    function it_should_be_part_of_an_election(Election $election)
    {
        $this->getElection()->shouldReturn($election);
    }

    function it_should_have_policies(Election $election, Policy $partyPolicy, Policy $rivalPartyPolicy)
    {
        $this->beConstructedWith($election, 'Administración Pública', [$partyPolicy, $rivalPartyPolicy]);

        $this->getPolicies()->shouldReturn([$partyPolicy, $rivalPartyPolicy]);
    }

    function it_should_be_possible_to_create_from_JSON(Election $election)
    {
        $this::createFromJson($election, '{"name": "Administración Pública"}')
            ->shouldBeLike(new Scope($election->getWrappedObject(), "Administración Pública"));
    }

    function it_should_throw_an_exception_if_name_is_empty(Election $election)
    {
        $this->beConstructedWith($election, '');
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_should_throw_an_exception_if_required_attributes_are_missing_from_json(Election $election)
    {
        $this->shouldThrow(new \BadMethodCallException('Missing required attributes while creating Scope from {}'))
            ->duringCreateFromJson($election, "{}");
    }

    function it_should_throw_an_exception_if_created_from_malformed_json(Election $election)
    {
        $this->shouldThrow(new \BadMethodCallException('Detected malformed JSON while creating Scope from {name}'))
            ->duringCreateFromJson($election, '{name}');
    }
}
