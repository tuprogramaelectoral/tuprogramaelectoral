<?php

namespace TPE\Dominio\Partido;

use Parsedown;
use TPE\Dominio\Ambito\Ambito;
use TPE\Dominio\Datos\DatoInicial;


class Politica implements DatoInicial
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Partido
     */
    private $partido;

    /**
     * @var Ambito
     */
    private $ambito;

    /**
     * @var array
     */
    private $fuentes;

    /**
     * @var string
     */
    private $contenido;


    public function __construct(Partido $partido, Ambito $ambito, array $fuentes, $contenido)
    {
        \Assert\lazy()
            ->that($fuentes, 'fuentes')->isArray()->notEmpty()
            ->that($contenido, 'contenido')->string()->notEmpty()
            ->verifyNow();

        $this->id = $partido->getId() . '_' . $ambito->getId();
        $this->partido = $partido;
        $this->ambito = $ambito;
        $this->fuentes = $fuentes;
        $this->contenido = $contenido;
    }

    public function getPartido()
    {
        return $this->partido;
    }

    public function getPartidoId()
    {
        return $this->partido->getId();
    }

    public function getAmbito()
    {
        return $this->ambito;
    }

    public function getAmbitoId()
    {
        return $this->ambito->getId();
    }

    public function getFuentes()
    {
        return $this->fuentes;
    }

    public function getContenidoEnMarkdown()
    {
        return $this->contenido;
    }

    public function getContenidoEnHtml()
    {
        return (new Parsedown())->text($this->contenido);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
