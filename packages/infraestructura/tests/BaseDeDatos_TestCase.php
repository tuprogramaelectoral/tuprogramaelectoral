<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use TPE\Dominio\Ambito\Ambito;
use TPE\Dominio\Partido\Partido;
use TPE\Dominio\Partido\Politica;
use TPE\Infraestructura\Ambito\AmbitoBaseDeDatosRepositorio;
use TPE\Infraestructura\Datos\BaseDeDatosRepositorio;
use TPE\Infraestructura\Datos\Cargador;
use TPE\Infraestructura\Datos\LectorDeFicheros;
use TPE\Infraestructura\MiPrograma\MiProgramaBaseDeDatosRepositorio;
use TPE\Infraestructura\Partido\PartidoBaseDeDatosRepositorio;
use VSP\Dominio\Datos\DatoInicialRepositorio;


class BaseDeDatos_TestCase extends \PHPUnit_Framework_TestCase
{
    const CLASE_AMBITO = Cargador::CLASE_AMBITO;
    const CLASE_PARTIDO = Cargador::CLASE_PARTIDO;
    const CLASE_POLITICA = Cargador::CLASE_POLITICA;
    const CLASE_MIPROGRAMA = Cargador::CLASE_MIPROGRAMA;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var BaseDeDatosRepositorio[]
     */
    protected $repos;

    /**
     * @var Cargador
     */
    protected $cargador;


    public function setUp()
    {
        $namespaces = array(
            'src/Ambito' => 'TPE\Dominio\Ambito',
            'src/Partido' => 'TPE\Dominio\Partido',
            'src/MiPrograma' => 'TPE\Dominio\MiPrograma'
        );

        $dbParams = array(
            'driver' => 'pdo_sqlite',
            'path' => '/tmp/sqlite.db'
        );

        if (file_exists('/tmp/sqlite.db')) {
            unlink('/tmp/sqlite.db');
        }

        $config = Setup::createConfiguration(true);
        $config->setMetadataDriverImpl(new SimplifiedYamlDriver($namespaces));

        $this->em = EntityManager::create($dbParams, $config);
        $this->cargador = new Cargador($this->em);

        $metadata = [
            self::CLASE_AMBITO => $this->em->getClassMetadata(self::CLASE_AMBITO),
            self::CLASE_PARTIDO => $this->em->getClassMetadata(self::CLASE_PARTIDO),
            self::CLASE_POLITICA =>$this->em->getClassMetadata(self::CLASE_POLITICA),
            self::CLASE_MIPROGRAMA =>$this->em->getClassMetadata(self::CLASE_MIPROGRAMA)
        ];

        $this->repos[self::CLASE_AMBITO] = New AmbitoBaseDeDatosRepositorio($this->em, $metadata[self::CLASE_AMBITO]);
        $this->repos[self::CLASE_PARTIDO] = New PartidoBaseDeDatosRepositorio($this->em, $metadata[self::CLASE_PARTIDO]);
        $this->repos[self::CLASE_MIPROGRAMA] = New MiProgramaBaseDeDatosRepositorio($this->em, $metadata[self::CLASE_MIPROGRAMA]);
    }

    protected function cargarFicheros(array $ficheros, $force = true)
    {
        $path = LectorDeFicheros::escribirFicherosDeTest($ficheros);

        $this->cargarDesdePath($path, $force);
    }

    protected function cargarDesdePath($path, $force = true)
    {
        if ($force) {
            $this->cargador->regenerarEsquema();
        }

        $this->cargador->cargar(new LectorDeFicheros($path));
    }
}
