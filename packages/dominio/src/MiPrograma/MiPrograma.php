<?php

namespace TPE\Dominio\MiPrograma;

use Ramsey\Uuid\Uuid;
use TPE\Dominio\Datos\DatoInicial;


class MiPrograma implements DatoInicial
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string[]
     */
    private $politicas = [];

    /**
     * @var bool
     */
    private $yaOrdenado = false;

    /**
     * @var bool
     */
    private $publico;

    /**
     * @var bool
     */
    private $terminado;

    /**
     * @var \DateTime
     */
    private $ultimaModificacion;


    public function __construct(array $politicas, $publico = false, $terminado = false)
    {
        \Assert\that($politicas)->isArray();
        \Assert\that($publico)->boolean();

        $this->id = Uuid::uuid4()->toString();
        $this->publico = $publico;
        $this->terminado = $terminado;
        foreach ($politicas as $interes => $politica) {
            $this->elegirPolitica($interes, $politica);
        }
        $this->ordenarPoliticas();
        $this->actualizarFechaDeModificacion();
    }

    private function ordenarPoliticas()
    {
        if (!$this->yaOrdenado) {
            ksort($this->politicas);
            $this->yaOrdenado = true;
        }
    }

    private function actualizarFechaDeModificacion()
    {
        $this->ultimaModificacion = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getIntereses()
    {
        $this->ordenarPoliticas();
        return array_keys($this->politicas);
    }

    /**
     * @return string[]
     */
    public function getPoliticas()
    {
        $this->ordenarPoliticas();
        return $this->politicas;
    }

    /**
     * @param string $interes
     * @return null|string
     */
    public function getPolitica($interes)
    {
        return (isset($this->politicas[$interes])) ? $this->politicas[$interes] : null;
    }

    /**
     * @return int[]
     */
    public function getAfinidad()
    {
        $afinidad = [];
        foreach ($this->politicas as $politica) {
            if (null !== $politica) {
                $partido = explode('_', $politica, 2)[0];
                $afinidad[$partido] = (isset($afinidad[$partido])) ? $afinidad[$partido] + 1 : 1;
            }
        }

        return $afinidad;
    }

    /**
     * @param string $interes
     * @param string $politica
     */
    public function elegirPolitica($interes, $politica) {
        $this->actualizarFechaDeModificacion();
        $this->politicas[$interes] = $politica;
    }

    /**
     * @return null|string
     */
    public function proximoInteres()
    {
        $this->ordenarPoliticas();
        foreach ($this->politicas as $interes => $politica) {
            if (empty($politica)) {
                return $interes;
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isPublico()
    {
        return $this->publico;
    }

    /**
     * @param bool $publico
     */
    public function setPublico($publico)
    {
        $this->actualizarFechaDeModificacion();
        $this->publico = $publico;
    }

    /**
     * @return bool
     */
    public function isTerminado()
    {
        return $this->terminado;
    }

    /**
     * @param bool $terminado
     */
    public function setTerminado($terminado)
    {
        $this->actualizarFechaDeModificacion();
        $this->terminado = $terminado;
    }
}
