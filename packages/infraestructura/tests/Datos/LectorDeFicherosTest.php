<?php

use TPE\Dominio\Ambito\Ambito;
use TPE\Dominio\Partido\Partido;
use TPE\Dominio\Partido\Politica;
use TPE\Infraestructura\Datos\LectorDeFicheros;


class LectorDeFicherosTest extends \PHPUnit_Framework_TestCase
{
    public function testLeeLosFicherosConAmbitosYDevuelveSuContenido()
    {
        $path = LectorDeFicheros::escribirFicherosDeTest([
            'ambito/administracion-publica/ambito.json' => '{"nombre": "Administración Pública"}',
            'ambito/agricultura/ambito.json' => '{"nombre": "Agricultura"}'
        ]);

        $datos = [
            '{"nombre": "Administración Pública"}',
            '{"nombre": "Agricultura"}',
        ];

        $lector = new LectorDeFicheros($path);
        $ambitos = $lector->leer('TPE\Dominio\Ambito\Ambito');

        $this->assertCount(count($datos), $ambitos);

        for ($i = 0; $i < count($ambitos); $i++) {
            $this->assertEquals($datos[$i], $ambitos[$i]);
        }
    }

    public function testLeeLosFicherosConPartidosYDevuelveSuContenido()
    {
        $path = LectorDeFicheros::escribirFicherosDeTest([
            'partido/partido-ficticio/partido.json' => '{"nombre": "Partido Ficticio", "siglas": "PF", "programa": "http://partido-ficticio.es"}'
        ]);

        $datos = [
            '{"nombre": "Partido Ficticio", "siglas": "PF", "programa": "http://partido-ficticio.es"}'
        ];

        $lector = new LectorDeFicheros($path);
        $partidos = $lector->leer('TPE\Dominio\Partido\Partido');

        $this->assertCount(count($datos), $partidos);

        for ($i = 0; $i < count($partidos); $i++) {
            $this->assertEquals($datos[$i], $partidos[$i]);
        }
    }

    public function testLeeLosFicherosConPoliticasYDevuelveSuContenido()
    {
        $path = LectorDeFicheros::escribirFicherosDeTest([
            'ambito/sanidad/politica/partido-ficticio/politica.json' => '{"partido": "partido-ficticio", "ambito": "sanidad", "fuentes": ["http://partido-ficticio.es/programa/sanidad apartado sobre sanidad en el programa electoral del partido"]}',
            'ambito/sanidad/politica/partido-ficticio/contenido.md' => '## sanidad universal y gratuita'
        ]);

        $datos = [
            [
                'json' => '{"partido": "partido-ficticio", "ambito": "sanidad", "fuentes": ["http://partido-ficticio.es/programa/sanidad apartado sobre sanidad en el programa electoral del partido"]}',
                'contenido' => '## sanidad universal y gratuita'
            ]
        ];

        $lector = new LectorDeFicheros($path);
        $politicas = $lector->leer('TPE\Dominio\Partido\Politica');

        $this->assertCount(count($datos), $politicas);

        for ($i = 0; $i < count($politicas); $i++) {
            $this->assertEquals($datos[$i], $politicas[$i]);
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
}
