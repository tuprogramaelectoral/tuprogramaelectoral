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
            'administracion-publica' => null,
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
            'administracion-publica' => null,
            'agricultura' => null
        ]);
    }

    function it_permite_asociar_una_politica_a_un_interes()
    {
        $this->elegirPolitica('agricultura', 'partido-ficticio-agricultura');

        $this->getPoliticas()->shouldReturn([
            'administracion-publica' => null,
            'agricultura' => 'partido-ficticio-agricultura'
        ]);
    }

    function it_tiene_un_id_generado_automaticamente()
    {
        $this->getId()->shouldBeAValidUUID();
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
