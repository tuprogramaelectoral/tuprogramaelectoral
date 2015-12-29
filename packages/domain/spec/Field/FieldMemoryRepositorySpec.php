<?php

namespace spec\TPE\Domain\Field;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TPE\Domain\Field\Field;


class FieldMemoryRepositorySpec extends ObjectBehavior
{
    function let(Field $admon, Field $empleo, Field $educacion)
    {
        $admon->getId()->willReturn("administracion-publica");
        $empleo->getId()->willReturn("empleo");
        $educacion->getId()->willReturn("educacion");

        $this->beConstructedWith([
            $empleo,
            $admon,
        ]);
    }

    function it_should_return_empty_when_finds_all_and_there_are_no_fields()
    {
        $this->beConstructedWith([]);

        $this->findAll()->shouldReturn([]);
    }

    function it_should_find_a_field_by_its_id(Field $admon)
    {
        $this->find('administracion-publica')->shouldBeEqualTo($admon);
    }

    function it_should_save_a_field(Field $educacion)
    {
        $this->save($educacion);

        $this->find('educacion')->shouldReturn($educacion);
    }

    function it_should_return_the_classname_of_the_managed_entity()
    {
        $this->getClassName()->shouldReturn('TPE\Domain\Field\Field');
    }
}

