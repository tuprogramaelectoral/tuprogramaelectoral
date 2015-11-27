<?php

namespace TPE\Dominio\Datos;

use TPE\Dominio\Datos\DatoInicial;
use TPE\Dominio\Datos\DatoInicialRepositorio;


abstract class EnMemoriaRepositorio implements DatoInicialRepositorio
{
    /* @var DatoInicial[] */
    private $datos = [];


    /**
     * @param DatoInicial[] $datos
     */
    public function __construct(array $datos = null)
    {
        if (null !== $datos) {
            foreach ($datos as $dato) {
                $this->save($dato);
            }
        }
    }

    public function findAll()
    {
        return array_values($this->datos);
    }

    public function find($id)
    {
        return isset($this->datos[$id]) ? $this->datos[$id] : null;
    }

    public function save(DatoInicial $dato, $flush = true)
    {
        $this->datos[$dato->getId()] = $dato;
    }
}
