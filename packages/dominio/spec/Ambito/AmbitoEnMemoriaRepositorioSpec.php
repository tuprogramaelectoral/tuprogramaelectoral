<?php

namespace spec\TPE\Dominio\Ambito;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TPE\Dominio\Ambito\Ambito;


class AmbitoEnMemoriaRepositorioSpec extends ObjectBehavior
{
    function let(Ambito $admon, Ambito $empleo, Ambito $educacion)
    {
        $admon->getId()->willReturn("administracion-publica");
        $empleo->getId()->willReturn("empleo");
        $educacion->getId()->willReturn("educacion");

        $this->beConstructedWith([
            $empleo,
            $admon,
        ]);
    }

    function it_encuentra_una_lista_vacia_si_no_hay_ambitos_almacenados()
    {
        $this->beConstructedWith([]);

        $this->findAll()->shouldReturn([]);
    }

    function it_encuentra_un_ambito_por_su_id(Ambito $admon)
    {
        $this->find('administracion-publica')->shouldBeEqualTo($admon);
    }

    function it_guarda_un_ambito(Ambito $educacion)
    {
        $this->save($educacion);

        $this->find('educacion')->shouldReturn($educacion);
    }

    function it_devuelve_el_namespace_del_tipo_de_dato_que_gestiona()
    {
        $this->getClassName()->shouldReturn('TPE\Dominio\Ambito\Ambito');
    }
}

