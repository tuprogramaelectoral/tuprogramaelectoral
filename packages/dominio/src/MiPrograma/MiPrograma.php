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


    public function __construct(array $politicas)
    {
        \Assert\that($politicas)->isArray();

        $this->id = Uuid::uuid4()->toString();
        $this->politicas = $politicas;
        $this->ordenarPoliticas();
    }

    private function ordenarPoliticas()
    {
        if (!$this->yaOrdenado) {
            ksort($this->politicas);
            $this->yaOrdenado = true;
        }
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

    public function getPoliticas()
    {
        $this->ordenarPoliticas();
        return $this->politicas;
    }

    public function elegirPolitica($interes, $politica) {
        $this->politicas[$interes] = $politica;
    }

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
}
