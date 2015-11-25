<?php

namespace spec\TPE\Dominio\Partido;

use PhpSpec\ObjectBehavior;


class PartidoEnMemoriaRepositorioSpec extends ObjectBehavior
{
    function it_devuelve_el_namespace_del_tipo_de_dato_que_gestiona()
    {
        $this->getClassName()->shouldReturn('TPE\Dominio\Partido\Partido');
    }
}

