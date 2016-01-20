<?php

namespace spec\TPE\Domain\Party;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TPE\Domain\Scope\Scope;
use TPE\Domain\Party\Party;

class PolicySpec extends ObjectBehavior
{
    function let(Party $party, Scope $scope)
    {
        $party->getId()->willReturn('partido-ficticio');
        $scope->getId()->willReturn('sanidad');

        $this->beConstructedWith(
            $party,
            $scope,
            ['http://partido-ficticio.es/programa/'],
            '## sanidad universal y gratuita'
        );
    }

    function it_should_extend_initial_data()
    {
        $this->shouldHaveType('TPE\Domain\Data\InitialData');
    }

    function it_should_be_linked_to_a_party(Party $party)
    {
        $this->getParty()->shouldReturn($party);
    }

    function it_should_return_the_party_id()
    {
        $this->getPartyId()->shouldReturn('partido-ficticio');
    }

    function it_should_be_linked_to_a_scope(Scope $scope)
    {
        $this->getScope()->shouldReturn($scope);
    }

    function it_should_return_the_scope_id()
    {
        $this->getScopeId()->shouldReturn('sanidad');
    }

    function it_should_have_an_id_generated_from_the_party_name_and_scope_name()
    {
        $this->getId()->shouldReturn('partido-ficticio_sanidad');
    }

    function it_should_have_a_list_of_sources()
    {
        $this->getSources()->shouldReturn(['http://partido-ficticio.es/programa/']);
    }

    function it_should_have_the_content_of_the_policy_in_markdown()
    {
        $this->getContentInMarkdown()->shouldReturn('## sanidad universal y gratuita');
    }

    function it_should_have_the_content_of_the_policy_in_html()
    {
        $this->getContentInHtml()->shouldReturn('<h2>sanidad universal y gratuita</h2>');
    }

    function it_should_throw_an_exception_if_there_are_no_sources(Party $party, Scope $scope)
    {
        $this->beConstructedWith(
            $party,
            $scope,
            [],
            '## sanidad universal y gratuita'
        );

        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_should_throw_an_exception_if_there_is_no_content(Party $party, Scope $scope)
    {
        $this->beConstructedWith(
            $party,
            $scope,
            ['http://partido-ficticio.es/programa/'],
            ''
        );

        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }
}
