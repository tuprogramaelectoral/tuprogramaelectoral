<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Prophecy\Prophecy\ObjectProphecy;
use TPE\Dominio\Ambito\Ambito;
use TPE\Dominio\Ambito\AmbitoEnMemoriaRepositorio;
use TPE\Dominio\Datos\Cargador;
use TPE\Dominio\Datos\DatoInicial;
use TPE\Dominio\Datos\DatoInicialRepositorio;
use TPE\Dominio\Datos\Lector;


/**
 * Defines application features from the specific context.
 */
class DominioContext implements Context, SnippetAcceptingContext
{
    /**
     * @var DatoInicialRepositorio[]
     */
    private $repositorios;

    /**
     * @var DatoInicial[][]
     */
    private $actuales;

    /**
     * @var array
     */
    private $datosIniciales = [];

    /**
     * @var Lector|ObjectProphecy
     */
    private $lector;


    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given que no existen :tipoDeDato en el sistema
     */
    public function queNoExistenDatosDelTipoEnElSistema($tipoDeDato)
    {
        $this->cargarRepositorio($tipoDeDato);
    }

    private function cargarRepositorio($tipoDeDato, $datos = null)
    {
        switch ($tipoDeDato) {
            case "ámbitos":
                $this->repositorios[$tipoDeDato] = new AmbitoEnMemoriaRepositorio($datos);
                break;
        }
    }

    /**
     * @When veo la lista de :tipoDeDato disponibles
     */
    public function veoLaListaDelTipoDeDatoDisponibles($tipoDeDato)
    {
        $datos = $this->repositorios[$tipoDeDato]->findAll();
        $this->actuales[$tipoDeDato] = [];
        foreach ($datos as $dato) {
            $this->actuales[$tipoDeDato][$dato->getId()] = $dato;
        }
    }

    /**
     * @Then la lista de :tipoDeDato debería estar vacía
     */
    public function laListaDelTipoDeberiaEstarVacia($tipoDeDato)
    {
        PHPUnit_Framework_Assert::assertEmpty($this->actuales[$tipoDeDato]);
    }

    /**
 * @Given que existen los siguientes :tipoDeDato:
 */
    public function queExistenLosSiguientesTiposDeDato($tipoDeDato, TableNode $table)
    {
        $datos = [];
        foreach ($table->getColumn(0) as $dato) {
            $datos[] = self::crearDatoUsandoJson($tipoDeDato, $dato);
        }

        $this->cargarRepositorio($tipoDeDato, $datos);
    }

    /**
     * @Then la lista de :tipoDeDato debería contener
     */
    public function laListaDelTipoDeberiaContener($tipoDeDato, TableNode $table)
    {
        $esperados = [];
        foreach ($table as $dato) {
            $esperados[$dato['id']] = $dato;
        }

        PHPUnit_Framework_Assert::assertCount(count($esperados), $this->actuales[$tipoDeDato]);

        foreach ($this->actuales[$tipoDeDato] as $actual) {
            switch ($tipoDeDato) {
                case "ámbitos":
                    /** @var Ambito $actual */
                    PHPUnit_Framework_Assert::assertEquals(
                        $esperados[$actual->getId()]['nombre'],
                        $actual->getNombre()
                    );
                    break;
            }
        }
    }

    /**
     * @Given que los ficheros y su contenido son los siguientes:
     */
    public function queLosFicherosYSuContenidoSonLosSiguientes(TableNode $table)
    {
        $this->datosIniciales = [];
        foreach ($table as $dato) {
            $this->datosIniciales[$dato['tipo']][] = self::crearDatoUsandoJson($dato['tipo'], $dato['contenido']);
        }

        foreach (array_keys($this->datosIniciales) as $tipo) {
            $this->cargarRepositorio($tipo);
        }
    }

    public static function crearDatoUsandoJson($tipoDeDato, $json)
    {
        switch ($tipoDeDato) {
            case "ámbitos":
                return Ambito::crearUsandoJson($json);
        }
    }

    /**
     * @When cargo los ficheros en el sistema
     */
    public function cargoLosFicherosEnElSistema()
    {
        $this->lector = (new \Prophecy\Prophet())->prophesize('TPE\Dominio\Datos\Lector');

        $repositorios = [];
        foreach ($this->repositorios as $tipoDeDato => $repositorio) {
            $repositorios[$repositorio->getClassName()] = $repositorio;
            $this->lector->leer($repositorio->getClassName())->willReturn($this->datosIniciales[$tipoDeDato]);
        }

        (new Cargador($repositorios))
            ->cargar($this->lector->reveal());
    }

    /**
     * @Then el sistema contiene los siguientes :tipoDeDato
     */
    public function elSistemaContieneLosSiguientes($tipoDeDato, TableNode $table)
    {
        $this->veoLaListaDelTipoDeDatoDisponibles($tipoDeDato);
        $this->laListaDelTipoDeberiaContener($tipoDeDato, $table);
    }
}
