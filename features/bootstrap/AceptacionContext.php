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
        /** @var \SplFileInfo[] $directorios */
        $directorios = (new Finder())->directories()->in($this->dataPath . '/ambito')->depth(0);
        $ambitos = [];
        foreach ($directorios as $directorio) {
            $ambitos[$directorio->getFilename()] = $directorio->getFilename();
        }

        /** @var NodeElement[] $intereses */
        $intereses = $this->getSession()->getPage()->findAll('css','.interes');

        PHPUnit_Framework_Assert::assertEquals(count($ambitos), count($intereses));
        foreach ($intereses as $interes) {
            PHPUnit_Framework_Assert::assertArrayHasKey(basename($interes->getAttribute('name')), $ambitos);
        }
    }
}
