<?php

namespace TPE\Dominio\Datos;

abstract class DatoInicial
{
    /** @var string */
    protected $id;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
