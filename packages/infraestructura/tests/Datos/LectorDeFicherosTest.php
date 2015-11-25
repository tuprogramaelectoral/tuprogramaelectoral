<?php

use TPE\Dominio\Ambito\Ambito;
use TPE\Dominio\Partido\Partido;
use TPE\Infraestructura\Datos\LectorDeFicheros;


class LectorDeFicherosTest extends \PHPUnit_Framework_TestCase
{
    public function testLeeLosFicherosConAmbitosYLosDevuelveInstanciadosEnObjetos()
    {
        $path = LectorDeFicheros::escribirFicherosDeTest([
            'ambito/administracion-publica/ambito.json' => '{"nombre": "Administración Pública"}',
            'ambito/agricultura/ambito.json' => '{"nombre": "Agricultura"}'
        ]);

        $datos = [
            'administracion-publica' => Ambito::crearUsandoJson('{"nombre": "Administración Pública"}'),
            'agricultura' => Ambito::crearUsandoJson('{"nombre": "Agricultura"}'),
        ];

        $lector = new LectorDeFicheros($path);
        $ambitos = $lector->leer('TPE\Dominio\Ambito\Ambito');

        $this->assertCount(count($datos), $ambitos);

        foreach ($ambitos as $ambito) {
            $this->assertEquals($datos[$ambito->getId()], $ambito);
        }
    }

    public function testLeeLosFicherosConPartidosYLosDevuelveInstanciadosEnObjetos()
    {
        $path = LectorDeFicheros::escribirFicherosDeTest([
            'partido/partido-ficticio/partido.json' => '{"nombre": "Partido Ficticio", "siglas": "PF", "programa": "http://partido-ficticio.es"}'
        ]);

        $datos = [
            'partido-ficticio' => Partido::crearUsandoJson('{"nombre": "Partido Ficticio", "siglas": "PF", "programa": "http://partido-ficticio.es"}')
        ];

        $lector = new LectorDeFicheros($path);
        $partidos = $lector->leer('TPE\Dominio\Partido\Partido');

        $this->assertCount(count($datos), $partidos);

        foreach ($partidos as $partido) {
            $this->assertEquals($datos[$partido->getId()], $partido);
        }
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testLanzaExcepcionCuandoLaClaseNoEstaRegistrada()
    {
        $lector = new LectorDeFicheros(LectorDeFicheros::escribirFicherosDeTest([]));

        $lector->leer('Clase\Que\No\Existe');
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testLanzaExcepcionCuandoElContenidoJsonNoEsValido()
    {
        $lector = new LectorDeFicheros(LectorDeFicheros::escribirFicherosDeTest([
            'ambito/administracion-publica/ambito.json' => 'ContenidoNoValido',
        ]));

        $lector->leer('TPE\Dominio\Ambito\Ambito');
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testLanzaExcepcionCuandoElJsonEstaIncompleto()
    {
        $lector = new LectorDeFicheros(LectorDeFicheros::escribirFicherosDeTest([
            'ambito/administracion-publica/ambito.json' => '{}',
        ]));

        $lector->leer('TPE\Dominio\Ambito\Ambito');
    }
}
