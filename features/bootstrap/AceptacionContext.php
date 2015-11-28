<?php

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Mink;
use Behat\MinkExtension\Context\MinkAwareContext;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use TPE\Infraestructura\Datos\LectorDeFicheros;

/**
 * Defines application features from the specific context.
 */
class AceptacionContext extends MinkContext implements SnippetAcceptingContext
{
    /** @var string */
    private $dataPath;


    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct($dataPath)
    {
        $this->dataPath = $dataPath;
    }

    /**
     * @Given que la aplicación está ejecutándose
     */
    public function queLaAplicacionEstaEjecutandose()
    {
    }

    /**
     * @When visito la página principal
     */
    public function visitoLaPaginaPrincipal()
    {
        $this->iAmOnHomepage();
    }

    /**
     * @Then veo los ámbitos existentes en el repositorio
     */
    public function veoLosAmbitosExistentesEnElRepositorio()
    {
        $ambitos = [];
        $contenidos = (new LectorDeFicheros($this->dataPath))->leer(LectorDeFicheros::CLASE_AMBITO);
        foreach ($contenidos as $contenido) {
            $ambito = json_decode($contenido, true);
            $ambitos[\slugifier\slugify($ambito['nombre'])] = $ambito;
        }

        /** @var NodeElement[] $intereses */
        $intereses = $this->getSession()->getPage()->findAll('css','.interes');

        PHPUnit_Framework_Assert::assertEquals(count($ambitos), count($intereses));
        foreach ($intereses as $interes) {
            PHPUnit_Framework_Assert::assertArrayHasKey($interes->getAttribute('name'), $ambitos);
            PHPUnit_Framework_Assert::assertEquals(
                $interes->getParent()->getText(),
                $ambitos[$interes->getAttribute('name')]["nombre"]
            );
        }
    }
}
