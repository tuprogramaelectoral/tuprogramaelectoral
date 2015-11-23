<?php

namespace TPE\Dominio\Datos;

class Cargador
{
    /**
     * @var DatoInicialRepositorio[]
     */
    private $repositorios = [];


    public function __construct($repositorios)
    {
        $this->repositorios = $repositorios;
    }

    /**
     * @param Lector $lector
     */
    public function cargar(Lector $lector)
    {
        foreach ($this->repositorios as $repositorio) {
            $objetos = $lector->leer($repositorio->getClassName());
            $repositorio->regenerarDatos($objetos);
        }
    }
}
