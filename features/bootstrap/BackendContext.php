<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Doctrine\ORM\EntityManager;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Filesystem\Filesystem;
use TPE\Dominio\Ambito\Ambito;
use TPE\Dominio\Datos\DatoInicial;
use TPE\Dominio\Datos\DatoInicialRepositorio;
use TPE\Dominio\MiPrograma\MiPrograma;
use TPE\Dominio\Partido\Partido;
use TPE\Dominio\Partido\Politica;
use TPE\Infraestructura\Ambito\AmbitoBaseDeDatosRepositorio;
use TPE\Infraestructura\Datos\BaseDeDatosRepositorio;
use TPE\Infraestructura\Datos\Cargador;
use TPE\Infraestructura\Datos\LectorDeFicheros;

/**
 * Defines application features from the specific context.
 */
class BackendContext extends MinkContext implements SnippetAcceptingContext
{
    use \Behat\Symfony2Extension\Context\KernelDictionary;

    const AMBITOS = "ámbitos";
    const PARTIDOS = "partidos";
    const POLITICAS = "políticas";
    const CONTENIDO_POLITICA = "contenido política";
    const MIPROGRAMA = "mi programa";

    /**
     * @var string
     */
    private $contenidoPath;

    /**
     * @var DatoInicial[][]
     */
    private $actuales;

    /**
     * @var array
     */
    private $miPrograma;


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
     * @Given cargo los ficheros en el sistema
     */
    public function cargoLosFicherosEnElSistema()
    {
        $this->getCargador()->cargar(new LectorDeFicheros($this->contenidoPath));
    }

    /**
     * @When veo la lista de :tipoDeDato disponibles
     */
    public function veoLaListaDelTipoDeDatoDisponibles($tipoDeDato)
    {
        $paths = [
            self::AMBITOS => 'ambitos',
            self::PARTIDOS => 'partidos'
        ];

        $this->getSession()->setRequestHeader('Accept', 'application/json');
        $this->visit($paths[$tipoDeDato]);

        $this->actuales[$tipoDeDato] = [];
        foreach ($this->getRespuesta() as $dato) {
            $this->actuales[$tipoDeDato][$dato['id']] = $dato;
        }
    }

    /**
     * @When veo la lista de políticas del ámbito :arg2
     */
    public function veoLaListaDePoliticasDelAmbito($ambito)
    {
        $this->getSession()->setRequestHeader('Accept', 'application/json');
        $this->visit("ambitos/{$ambito}");

        $this->actuales[self::POLITICAS] = [];
        foreach ($this->getRespuesta()['politicas'] as $dato) {
            $this->actuales[self::POLITICAS][$dato['id']] = $dato;
        }
    }

    /**
     * @Then la lista de :tipoDeDato debería contener:
     */
    public function laListaDelTipoDeberiaContener($tipoDeDato, TableNode $table)
    {
        $esperados = [];
        foreach ($table as $dato) {
            $esperados[$dato['id']] = $dato;
        }

        PHPUnit_Framework_Assert::assertCount(count($esperados), $this->actuales[$tipoDeDato]);
        foreach ($this->actuales[$tipoDeDato] as $actual) {
            $this->compararDatoActualConEsperado($tipoDeDato, $actual, $esperados[$actual['id']]);
        }
    }

    private function compararDatoActualConEsperado($tipo, $actual, array $esperado)
    {
        switch ($tipo) {
            case self::AMBITOS:
                /** @var Ambito $actual */
                PHPUnit_Framework_Assert::assertEquals($esperado['nombre'], $actual["nombre"]);
                break;
            case self::PARTIDOS:
                /** @var Partido $actual */
                PHPUnit_Framework_Assert::assertEquals($esperado['nombre'], $actual["nombre"]);
                PHPUnit_Framework_Assert::assertEquals($esperado['siglas'], $actual["siglas"]);
                PHPUnit_Framework_Assert::assertEquals($esperado['programa'], $actual["programa"]);
                break;
            case self::POLITICAS:
                /** @var Politica $actual */
                PHPUnit_Framework_Assert::assertEquals($esperado['partidoId'], $actual["partido_id"]);
                PHPUnit_Framework_Assert::assertEquals(json_decode($esperado['fuentes']), $actual["fuentes"]);
                PHPUnit_Framework_Assert::assertEquals($esperado['contenido'], $actual["contenido"]);
                break;
            default:
                throw new \Exception("no existe forma de comparar " . $tipo);
        }
    }

    /**
     * @When (que) selecciono los siguientes intereses:
     */
    public function seleccionoLosSiguientesIntereses(TableNode $table)
    {
        $politicas = [];
        foreach ($table->getColumn(0) as $interes) {
            $politicas['politicas'][$interes] = null;
        }

        $this->request('POST', 'misprogramas', $politicas);

        $this->miPrograma = $this->getRespuesta();
    }

    /**
     * @Then mi programa debería contener los siguientes intereses:
     */
    public function miProgramaDeberiaContenerLosSiguientesIntereses(TableNode $table)
    {
        $this->visit("misprogramas/{$this->miPrograma['id']}");
        $this->miPrograma = $this->getRespuesta();

        $esperado = array_flip($table->getColumn(0));
        foreach ($this->miPrograma['intereses'] as $actual) {
            PHPUnit_Framework_Assert::assertTrue(isset($esperado[$actual]));
        }
    }

    /**
     * @Then el próximo interés es :arg1
     */
    public function elProximoInteresEs($ambito)
    {
        PHPUnit_Framework_Assert::assertEquals($ambito, $this->miPrograma["proximo_interes"]);
    }

    /**
     * @Then el sistema debería mostrar un error
     */
    public function elSistemaDeberiaMostrarUnError()
    {
        PHPUnit_Framework_Assert::assertEquals(400, $this->getRespuesta()['code']);
    }

    /**
     * @When selecciono la política :politica
     */
    public function seleccionoLaPolitica($politica)
    {
        $this->request(
            "POST",
            "/misprogramas/{$this->miPrograma['id']}",
            ["politicas" => [$this->miPrograma['proximo_interes'] => $politica]]
        );
    }

    /**
     * @Then mi programa debería contener las siguientes políticas:
     */
    public function miProgramaDeberiaContenerLasSiguientesPoliticas(TableNode $table)
    {
        $this->visit("misprogramas/{$this->miPrograma['id']}");
        $this->miPrograma = $this->getRespuesta();
    }

    /**
     * @return Cargador
     */
    private function getCargador()
    {
        return $this->getContainer()->get('cargador_de_datos');
    }

    private function request($verb, $url, $valores)
    {
        $this
            ->getSession()
            ->getDriver()
            ->getClient()
            ->request(
                $verb,
                $url,
                $valores,
                [],
                ['HTTP_ACCEPT' => 'application/json']
            );
    }

    /**
     * @return array
     */
    private function getRespuesta()
    {
        return json_decode($this->getSession()->getPage()->getContent(), true);
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    /**
     * @param string $tipoDeDato
     * @return DatoInicialRepositorio
     */
    private function getRepositorio($tipoDeDato)
    {
        $repositorios = [
            self::AMBITOS => 'app.repository.ambito',
            self::PARTIDOS => 'app.repository.partido',
            self::MIPROGRAMA => 'app.repository.miprograma'
        ];

        return $this->getContainer()->get($repositorios[$tipoDeDato]);
    }
}
