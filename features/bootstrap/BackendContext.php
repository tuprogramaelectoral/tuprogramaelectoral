<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Filesystem\Filesystem;
use TPE\Dominio\Ambito\Ambito;
use TPE\Dominio\Datos\DatoInicial;
use TPE\Dominio\Partido\Partido;
use TPE\Dominio\Partido\Politica;
use TPE\Infraestructura\Ambito\AmbitoBaseDeDatosRepositorio;
use TPE\Infraestructura\Datos\BaseDeDatosRepositorio;
use TPE\Infraestructura\Datos\Cargador;
use TPE\Infraestructura\Datos\LectorDeFicheros;

/**
 * Defines application features from the specific context.
 */
class BackendContext implements Context, SnippetAcceptingContext
{
    use \Behat\Symfony2Extension\Context\KernelDictionary;

    const AMBITOS = "ámbitos";
    const PARTIDOS = "partidos";
    const POLITICAS = "políticas";
    const CONTENIDO_POLITICA = "contenido política";

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
        /** @var DatoInicial[] $datos */
        $datos = $this->getRepositorio($tipoDeDato)->findAll();
        $this->actuales[$tipoDeDato] = [];
        foreach ($datos as $dato) {
            $this->actuales[$tipoDeDato][$dato->getId()] = $dato;
        }
    }


    /**
     * @When veo la lista de políticas del ámbito :arg2
     */
    public function veoLaListaDePoliticasDelAmbito($ambitoId)
    {
        /** @var Ambito $ambito */
        $ambito = $this->getRepositorio(self::AMBITOS)->findOneBy(['id' => $ambitoId]);
        $this->actuales[self::POLITICAS] = [];
        foreach ($ambito->getPoliticas() as $politica) {
            $this->actuales[self::POLITICAS][$politica->getId()] = $politica;
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
            $this->compararDatoActualConEsperado($tipoDeDato, $actual, $esperados[$actual->getId()]);
        }
    }

    private function compararDatoActualConEsperado($tipo, DatoInicial $actual, array $esperado)
    {
        switch ($tipo) {
            case self::AMBITOS:
                /** @var Ambito $actual */
                PHPUnit_Framework_Assert::assertEquals($esperado['nombre'], $actual->getNombre());
                break;
            case self::PARTIDOS:
                /** @var Partido $actual */
                PHPUnit_Framework_Assert::assertEquals($esperado['nombre'], $actual->getNombre());
                PHPUnit_Framework_Assert::assertEquals($esperado['siglas'], $actual->getSiglas());
                PHPUnit_Framework_Assert::assertEquals($esperado['programa'], $actual->getPrograma());
                break;
            case self::POLITICAS:
                /** @var Politica $actual */
                PHPUnit_Framework_Assert::assertEquals($esperado['partidoId'], $actual->getPartidoId());
                PHPUnit_Framework_Assert::assertEquals($esperado['ambitoId'], $actual->getAmbitoId());
                PHPUnit_Framework_Assert::assertEquals(json_decode($esperado['fuentes']), $actual->getFuentes());
                PHPUnit_Framework_Assert::assertEquals($esperado['contenido'], $actual->getContenidoEnMarkdown());
                break;
            default:
                throw new \Exception("no existe forma de comparar " . $tipo);
        }
    }

    /**
     * @return Cargador
     */
    private function getCargador()
    {
        return $this->getContainer()->get('cargador_de_datos');
    }

    /**
     * @param string $tipoDeDato
     * @return EntityRepository
     */
    private function getRepositorio($tipoDeDato)
    {
        $repositorios = [
            self::AMBITOS => 'app.repository.ambito',
            self::PARTIDOS => 'app.repository.partido'

        ];

        return $this->getContainer()->get($repositorios[$tipoDeDato]);
    }
}
