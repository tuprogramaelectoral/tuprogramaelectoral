<?php

namespace spec\TPE\Domain\Field;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TPE\Domain\Party\Policy;

class FieldSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Administración Pública');
    }

    function it_should_extends_initial_data()
    {
        $this->shouldHaveType('TPE\Domain\Data\InitialData');
    }

    function it_should_have_a_name()
    {
        $this->getName()->shouldReturn('Administración Pública');
    }

    function it_should_have_an_id_generated_from_its_name()
    {
        $this->getId()->shouldReturn('administracion-publica');
    }

    function it_should_have_no_policies_by_default()
    {
        $this->getPolicies()->shouldReturn([]);
    }

    function it_should_have_policies(Policy $policy)
    {
        $this->beConstructedWith('Administración Pública', [$policy]);

        $this->getPolicies()->shouldReturn([$policy]);
    }

    function it_should_be_possible_to_create_from_JSON()
    {
        $this::createFromJson('{"name": "Administración Pública"}')->shouldReturnAnInstanceOf('TPE\Domain\Field\Field');
    }

    function it_should_throw_an_exception_if_name_is_empty()
    {
        $this->beConstructedWith('');
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_should_throw_an_exception_if_required_attributes_are_missing_from_json()
    {
        $this->shouldThrow(new \BadMethodCallException('Missing required attributes while creating Field from {}'))
            ->duringCreateFromJson("{}");
    }

    function it_should_throw_an_exception_if_created_from_malformed_json()
    {
        $this->shouldThrow(new \BadMethodCallException('Detected malformed JSON while creating Field from {name}'))
            ->duringCreateFromJson('{name}');
    }
}
