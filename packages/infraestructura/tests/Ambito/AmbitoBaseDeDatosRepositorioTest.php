<?php

require_once __DIR__ . "/../BaseDeDatos_TestCase.php";

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use TPE\Dominio\Ambito\Ambito;
use TPE\Infraestructura\Ambito\AmbitoBaseDeDatosRepositorio;
use TPE\Infraestructura\Datos\LectorDeFicheros;


class AmbitoBaseDeDatosRepositorioTest extends BaseDeDatos_TestCase
{
    public function testGuardaUnAmbito()
    {
        $this->cargarFicheros([]);

        $esperado = New Ambito('Ámbito de pueba');

        $this->repos[self::CLASE_AMBITO]->save($esperado);

        $actual = $this->repos[self::CLASE_AMBITO]->findOneBy(['nombre' => $esperado->getNombre()]);
        $this->assertEquals($esperado, $actual);
    }
}
