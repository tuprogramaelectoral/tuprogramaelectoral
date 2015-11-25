<?php

namespace spec\TPE\Dominio\Partido;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PartidoSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Partido Ficticio', 'PF', 'http://partido-ficticio.es');
    }

    function it_extiende_dato_inicial()
    {
        $this->shouldHaveType('TPE\Dominio\Datos\DatoInicial');
    }

    function it_tiene_un_nombre()
    {
        $this->getNombre()->shouldReturn('Partido Ficticio');
    }

    function it_tiene_un_id_generado_a_partir_del_nombre()
    {
        $this->getId()->shouldReturn('partido-ficticio');
    }

    function it_tiene_unas_siglas()
    {
        $this->getSiglas()->shouldReturn('PF');
    }

    function it_tiene_un_programa()
    {
        $this->getPrograma()->shouldReturn('http://partido-ficticio.es');
    }

    function it_lanza_una_excepcion_si_el_nombre_esta_vacio()
    {
        $this->beConstructedWith('', '');
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_lanza_una_excepcion_si_las_siglas_estan_vacias()
    {
        $this->beConstructedWith('nombre', '');
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_lanza_una_excepcion_si_programa_no_esta_vacio_y_no_es_una_url()
    {
        $this->beConstructedWith('nombre', 'siglas', 'no-es-una-url');
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_puede_ser_creado_usando_JSON()
    {
        $this::crearUsandoJson('{"nombre": "Partido Ficticio", "siglas": "PF", "programa": "http://partido-ficticio.es"}')->shouldReturnAnInstanceOf('TPE\Dominio\Partido\Partido');
    }
//
//    function it_lanza_excepcion_si_faltan_campos_al_ser_creado_usando_JSON()
//    {
//        $this->shouldThrow(new \BadMethodCallException('Faltan parámetros para crear el Ámbito a partir de {}'))
//            ->duringCrearUsandoJson("{}");
//    }
//
//    function it_lanza_excepcion_al_ser_creado_usando_un_JSON_malformado()
//    {
//        $this->shouldThrow(new \BadMethodCallException('detectado JSON malformado al crear el Ámbito a partir de {nombre}'))
//            ->duringCrearUsandoJson('{nombre}');
//    }
}
