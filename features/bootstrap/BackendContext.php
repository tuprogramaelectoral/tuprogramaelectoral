<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\Filesystem\Filesystem;
use TPE\Dominio\Ambito\Ambito;
use TPE\Dominio\Datos\Cargador;
use TPE\Infraestructura\Ambito\AmbitoBaseDeDatosRepositorio;
use TPE\Infraestructura\Datos\BaseDeDatosRepositorio;
use TPE\Infraestructura\Datos\LectorDeFicheros;

/**
 * Defines application features from the specific context.
 */
class BackendContext extends MinkContext implements SnippetAcceptingContext
{
    use \Behat\Symfony2Extension\Context\KernelDictionary;

    /**
     * @var string
     */
    private $contenidoPath;

    /**
     * @var array
     */
    private $actuales;


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
     * @Given que existen los siguientes :tipoDeDato:
     */
    public function queExistenLosSiguientesTiposDeDato($tipoDeDato, TableNode $table)
    {
        $actual = [];
        foreach ($table->getColumn(0) as $dato) {
            $actual[] = DominioContext::crearDatoUsandoJson($tipoDeDato, $dato);
        }

        $this->getRepositorio($tipoDeDato)->regenerarDatos($actual);
    }

    /**
     * @param string $tipoDeDato
     * @return BaseDeDatosRepositorio
     */
    private function getRepositorio($tipoDeDato)
    {
        switch ($tipoDeDato) {
            case "ámbitos":
                return $this->getContainer()->get('app.repository.ambito');
        }
    }

    /**
     * @When veo la lista de :tipoDeDato disponibles
     */
    public function veoLaListaDelTipoDeDatoDisponibles($tipoDeDato)
    {
        $paths = [
            'ámbitos' => 'ambitos'
        ];

        $this->getSession()->setRequestHeader('Accept', 'application/json');
        $this->visit($paths[$tipoDeDato]);

        $this->actuales[$tipoDeDato] = json_decode($this->getSession()->getPage()->getContent(), true);
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
            PHPUnit_Framework_Assert::assertEquals(
                $esperados[$actual['id']]['nombre'],
                $actual['nombre']);
        }
    }

    /**
     * @Given que los ficheros y su contenido son los siguientes:
     */
    public function queLosFicherosYSuContenidoSonLosSiguientes(TableNode $table)
    {
        $ficheros = [];
        foreach ($table as $fichero) {
            $ficheros[$fichero['path']] = $fichero['contenido'];
        }

        $this->contenidoPath = LectorDeFicheros::escribirFicherosDeTest($ficheros);
    }

    /**
     * @When cargo los ficheros en el sistema
     */
    public function cargoLosFicherosEnElSistema()
    {
        $this->getCargador()->cargar(new LectorDeFicheros($this->contenidoPath));
    }

    /**
     * @Then el sistema contiene los siguientes :tipoDeDato
     */
    public function elSistemaContieneLosSiguientes($tipoDeDato, TableNode $table)
    {
        $this->veoLaListaDelTipoDeDatoDisponibles($tipoDeDato);
        $this->laListaDelTipoDeberiaContener($tipoDeDato, $table);
    }

    /**
     * @return Cargador
     */
    private function getCargador()
    {
        return $this->getContainer()->get('cargador_de_datos');
    }

}
