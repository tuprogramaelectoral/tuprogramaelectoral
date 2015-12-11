<?php

namespace spec\TPE\Dominio\MiPrograma;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ramsey\Uuid\Uuid;

class MiProgramaSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'administracion-publica' => 'partido-ficticio-administracion-publica',
            'agricultura' => null
        ]);
    }

    function it_extiende_dato_inicial()
    {
        $this->shouldHaveType('TPE\Dominio\Datos\DatoInicial');
    }

    function it_tiene_una_lista_de_intereses()
    {
        $this->getIntereses()->shouldReturn([
            'administracion-publica',
            'agricultura'
        ]);
    }

    function it_tiene_una_lista_de_politicas()
    {
        $this->getPoliticas()->shouldReturn([
            'administracion-publica' => 'partido-ficticio-administracion-publica',
            'agricultura' => null
        ]);
    }

    function it_devuelve_la_politica_seleccionada_asociada_a_un_interes()
    {
        $this->getPolitica('administracion-publica')->shouldReturn('partido-ficticio-administracion-publica');
    }

    function it_devuelve_nulo_si_el_interes_no_tiene_asociado_una_politica()
    {
        $this->getPolitica('interes-no-existente')->shouldReturn(null);
    }

    function it_calcula_la_afinidad_con_cada_partido_a_partir_de_las_politicas_seleccionadas()
    {
        $this->beConstructedWith([
            'administracion-publica' => 'partido-popular_administracion-publica',
            'agricultura' => 'partido-socialista_agricultura',
            'sanidad' => 'podemos_sanidad',
            'educacion' => 'ciudadanos_educacion',
            'empleo' => 'podemos_empleo',
            'economia' => 'ciudadanos_economia',
        ]);

        $this->getAfinidad()->shouldReturn(['partido-popular' => 1, 'partido-socialista' => 1, 'ciudadanos' => 2, 'podemos' => 2]);
    }

    function it_tiene_un_id_generado_automaticamente()
    {
        $this->getId()->shouldBeAValidUUID();
    }

    function it_es_privado_por_defecto()
    {
        return $this->isPublico()->shouldReturn(false);
    }

    function it_comprueba_si_mi_programa_es_publico()
    {
        $this->beConstructedWith([
            'administracion-publica' => 'partido-ficticio-administracion-publica',
            'agricultura' => null
        ], true);

        return $this->isPublico()->shouldReturn(true);
    }

    function it_establece_si_mi_programa_es_publico()
    {
        $this->setPublico(true);

        return $this->isPublico()->shouldReturn(true);
    }

    function it_no_este_terminado_por_defecto()
    {
        return $this->isTerminado()->shouldReturn(false);
    }

    function it_comprueba_si_mi_programa_esta_terminado()
    {
        $this->beConstructedWith([
            'administracion-publica' => 'partido-ficticio-administracion-publica',
            'agricultura' => null
        ], true, true);

        return $this->isTerminado()->shouldReturn(true);
    }

    function it_establece_si_mi_programa_esta_terminado()
    {
        $this->setTerminado(true);

        return $this->isTerminado()->shouldReturn(true);
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
