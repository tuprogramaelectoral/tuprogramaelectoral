<?php

namespace spec\TPE\Domain\MyProgramme;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ramsey\Uuid\Uuid;

class MyProgrammeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'administracion-publica' => 'partido-ficticio_administracion-publica',
            'agricultura' => null
        ], 1);
    }

    function it_should_extend_initial_data()
    {
        $this->shouldHaveType('TPE\Domain\Data\InitialData');
    }

    function it_should_have_a_list_of_interests()
    {
        $this->getInterests()->shouldReturn([
            'administracion-publica',
            'agricultura'
        ]);
    }

    function it_should_have_a_list_of_policies()
    {
        $this->getPolicies()->shouldReturn([
            'administracion-publica' => 'partido-ficticio_administracion-publica',
            'agricultura' => null
        ]);
    }

    function it_should_return_the_policy_linked_to_an_interest()
    {
        $this->getPolicy('administracion-publica')->shouldReturn('partido-ficticio_administracion-publica');
    }

    function it_should_return_null_when_an_interest_has_no_linked_policy()
    {
        $this->getPolicy('interes-no-existente')->shouldReturn(null);
    }

    function it_should_select_the_policy_linked_to_an_interest()
    {
        $this->selectPolicy('agricultura', 'partido-ficticio_agricultura');

        $this->getPolicy('agricultura')->shouldReturn('partido-ficticio_agricultura');
    }

    function it_should_return_the_party_affinity_calculated_from_the_selected_policies()
    {
        $this->beConstructedWith([
            'administracion-publica' => 'partido-popular_administracion-publica',
            'agricultura' => 'partido-socialista_agricultura',
            'sanidad' => 'podemos_sanidad',
            'educacion' => 'ciudadanos_educacion',
            'empleo' => 'podemos_empleo',
            'economia' => 'ciudadanos_economia',
        ], 1);

        $this->getPartyAffinity()->shouldReturn(['partido-popular' => 1, 'partido-socialista' => 1, 'ciudadanos' => 2, 'podemos' => 2]);
    }

    function it_should_have_an_id_generated_automatically()
    {
        $this->getId()->shouldBeAValidUUID();
    }

    function it_should_have_an_election_edition()
    {
        return $this->getEdition()->shouldReturn(1);
    }

    function it_should_set_the_election_edition()
    {
        $this->setEdition(2);

        return $this->getEdition()->shouldReturn(2);
    }

    function it_should_be_private_by_default()
    {
        return $this->isPublic()->shouldReturn(false);
    }

    function it_should_be_public_if_it_has_been_created_as_public()
    {
        $this->beConstructedWith([
            'administracion-publica' => 'partido-ficticio_administracion-publica',
            'agricultura' => null
        ], 1, true);

        return $this->isPublic()->shouldReturn(true);
    }

    function it_should_be_set_public_or_private()
    {
        $this->setPublic(true);

        return $this->isPublic()->shouldReturn(true);
    }

    function it_should_be_not_completed_by_default()
    {
        return $this->isCompleted()->shouldReturn(false);
    }

    function it_should_be_completed_if_it_has_been_created_as_completed()
    {
        $this->beConstructedWith([
            'administracion-publica' => 'partido-ficticio_administracion-publica',
            'agricultura' => null
        ], 1, true, true);

        return $this->isCompleted()->shouldReturn(true);
    }

    function it_should_be_set_completed_or_not_completed()
    {
        $this->setCompleted(true);

        return $this->isCompleted()->shouldReturn(true);
    }

    function it_should_return_the_next_interest_to_be_selected()
    {
        $this->beConstructedWith([
            'administracion-publica' => null,
            'agricultura' => null
        ], 1);

        $this->nextInterest()->shouldReturn('administracion-publica');
    }

    function it_should_return_the_next_interest_to_be_selected_after_selecting_a_policy()
    {
        $this->beConstructedWith([
            'administracion-publica' => null,
            'agricultura' => null
        ], 1);

        $this->selectPolicy('administracion-publica', 'partido-ficticio_administracion-publica');

        $this->nextInterest()->shouldReturn('agricultura');
    }

    function it_should_return_null_if_there_is_no_next_interest_to_be_selected()
    {
        $this->beConstructedWith([
            'administracion-publica' => 'partido-ficticio_administracion-publica'
        ], 1);

        $this->nextInterest()->shouldReturn(null);
    }

    public function getMatchers()
    {
        return [
            'beAValidUUID' => function ($subject) {
                return Uuid::isValid($subject);
            }
        ];
    }
}
