<?php

namespace TPE\Dominio\Ambito;

use TPE\Dominio\Datos\DatoInicial;
use TPE\Dominio\Datos\DatoInicialRepositorio;
use TPE\Dominio\Datos\EnMemoriaRepositorio;


class AmbitoEnMemoriaRepositorio extends EnMemoriaRepositorio
{
    /**
     * @return string
     */
    public function getClassName()
    {
        return 'TPE\Dominio\Ambito\Ambito';
    }

    public function regenerarDatos($argument1)
    {
        // TODO: write logic here
    }
}
