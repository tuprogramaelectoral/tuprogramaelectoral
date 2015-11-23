<?php

namespace TPE\Dominio\Datos;

use TPE\Dominio\Ambito\Ambito;


interface Lector
{
    /**
     * @return DatoInicial[]
     */
    public function leer($clase);
}
