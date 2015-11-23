<?php

namespace spec\TPE\Dominio\Ambito;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AmbitoSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Administración Pública');
    }

    function it_tiene_un_nombre()
    {
        $this->getNombre()->shouldReturn('Administración Pública');
    }

    function it_tiene_un_id_generado_a_partir_del_nombre()
    {
        $this->getId()->shouldReturn('administracion-publica');
    }

    function it_puede_ser_creado_usando_JSON()
    {
        $this::crearUsandoJson('{"nombre": "Administración Pública"}')->shouldReturnAnInstanceOf('TPE\Dominio\Ambito\Ambito');
    }

    function it_lanza_excepcion_si_faltan_campos_al_ser_creado_usando_JSON()
    {
        $this->shouldThrow(new \BadMethodCallException('Faltan parámetros para crear el Ámbito a partir de {}'))
            ->duringCrearUsandoJson("{}");
    }

    function it_lanza_excepcion_al_ser_creado_usando_un_JSON_malformado()
    {
        $this->shouldThrow(new \BadMethodCallException('detectado JSON malformado al crear el Ámbito a partir de {nombre}'))
            ->duringCrearUsandoJson('{nombre}');
    }
}
