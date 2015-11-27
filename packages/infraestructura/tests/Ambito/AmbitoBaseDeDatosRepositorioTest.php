<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use TPE\Dominio\Ambito\Ambito;
use TPE\Infraestructura\Ambito\AmbitoBaseDeDatosRepositorio;
use TPE\Infraestructura\Datos\LectorDeFicheros;


class AmbitoBaseDeDatosRepositorioTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var AmbitoBaseDeDatosRepositorio
     */
    private $ambitoRepositorio;


    public function setUp()
    {
        $namespaces = array(
            'src/Ambito' => 'TPE\Dominio\Ambito',
            'src/Partido' => 'TPE\Dominio\Partido'
        );

        $dbParams = array(
            'driver'   => 'pdo_sqlite',
            'path'     => '/tmp/sqlite.db'
        );

        $config = Setup::createConfiguration(true);
        $config->setMetadataDriverImpl(new SimplifiedYamlDriver($namespaces));

        $this->em = EntityManager::create($dbParams, $config);

        $schemaTool = new SchemaTool($this->em);
        $metadata = $this->em->getClassMetadata('TPE\Dominio\Ambito\Ambito');
        $schemaTool->dropSchema([$metadata]);
        $schemaTool->createSchema([$metadata]);

        $this->ambitoRepositorio = New AmbitoBaseDeDatosRepositorio(
            $this->em,
            $metadata
        );
    }

    public function testGuardaUnAmbito()
    {
        $esperado = New Ambito('Ámbito de pueba');

        $this->ambitoRepositorio->save($esperado);

        $actual = $this->ambitoRepositorio->findOneBy(['nombre' => $esperado->getNombre()]);
        $this->assertEquals($esperado, $actual);
    }
}
