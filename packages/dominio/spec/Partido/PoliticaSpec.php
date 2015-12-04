<?php

namespace spec\TPE\Dominio\Partido;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TPE\Dominio\Ambito\Ambito;
use TPE\Dominio\Partido\Partido;

class PoliticaSpec extends ObjectBehavior
{
    function let(Partido $partido, Ambito $ambito)
    {
        $partido->getId()->willReturn('partido-ficticio');
        $ambito->getId()->willReturn('sanidad');

        $this->beConstructedWith(
            $partido,
            $ambito,
            ['http://partido-ficticio.es/programa/sanidad apartado sobre sanidad en el programa electoral del partido'],
            '## sanidad universal y gratuita'
        );
    }

    function it_extiende_dato_inicial()
    {
        $this->shouldHaveType('TPE\Dominio\Datos\DatoInicial');
    }

    function it_esta_asociada_a_un_partido()
    {
        $this->getPartidoId()->shouldReturn('partido-ficticio');
    }

    function it_esta_asociada_a_un_ambito()
    {
        $this->getAmbitoId()->shouldReturn('sanidad');
    }

    function it_tiene_un_id_generado_a_partir_del_partido_y_ambito_asociado()
    {
        $this->getId()->shouldReturn('partido-ficticio_sanidad');
    }

    function it_tiene_una_lista_de_fuentes()
    {
        $this->getFuentes()->shouldReturn(['http://partido-ficticio.es/programa/sanidad apartado sobre sanidad en el programa electoral del partido']);
    }

    function it_tiene_el_contenido_de_la_politica_en_markdown()
    {
        $this->getContenidoEnMarkdown()->shouldReturn('## sanidad universal y gratuita');
    }

    function it_tiene_el_contenido_de_la_politica_en_html()
    {
        $this->getContenidoEnHtml()->shouldReturn('<h2>sanidad universal y gratuita</h2>');
    }

    function it_lanza_una_excepcion_si_no_existen_fuentes(Partido $partido, Ambito $ambito)
    {
        $this->beConstructedWith(
            $partido,
            $ambito,
            [],
            '## sanidad universal y gratuita'
        );

        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_lanza_una_excepcion_si_no_existe_contenido(Partido $partido, Ambito $ambito)
    {
        $this->beConstructedWith(
            $partido,
            $ambito,
            ['http://partido-ficticio.es/programa/sanidad apartado sobre sanidad en el programa electoral del partido'],
            ''
        );

        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }
}
