<?php

namespace TPE\Dominio\Datos;

abstract class DatoInicial
{
    /** @var string */
    protected $id;


    public function __construct($nombre)
    {
        $this->id = \slugifier\slugify($nombre);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
