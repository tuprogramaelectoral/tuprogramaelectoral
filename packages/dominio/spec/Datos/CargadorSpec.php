<?php

namespace spec\TPE\Dominio\Datos;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TPE\Dominio\Ambito\Ambito;
use TPE\Dominio\Datos\DatoInicialRepositorio;
use TPE\Dominio\Datos\Lector;


class CargadorSpec extends ObjectBehavior
{
    const AMBITO = 'TPE\Dominio\Ambito\Ambito';


    function let(DatoInicialRepositorio $ambitoRepositorio, Lector $lector, Ambito $educacion)
    {
        $ambitoRepositorio->getClassName()->willReturn(self::AMBITO);
        $lector->leer(self::AMBITO)->willReturn([$educacion]);

        $this->beConstructedWith([$ambitoRepositorio]);
    }


    function it_carga_datos_procedientes_de_un_lector(DatoInicialRepositorio $ambitoRepositorio, Lector $lector, Ambito $educacion)
    {
        $lector->leer(self::AMBITO)->shouldBeCalled();
        $ambitoRepositorio->getClassName()->shouldBeCalled();
        $ambitoRepositorio->regenerarDatos([$educacion])->shouldBeCalled();

        $this->cargar($lector);
    }
}
