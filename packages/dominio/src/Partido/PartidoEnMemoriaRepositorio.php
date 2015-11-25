<?php

namespace TPE\Dominio\Partido;

use TPE\Dominio\Datos\DatoInicial;
use TPE\Dominio\Datos\DatoInicialRepositorio;
use TPE\Dominio\Datos\EnMemoriaRepositorio;


class PartidoEnMemoriaRepositorio extends EnMemoriaRepositorio
{
    /**
     * @return string
     */
    public function getClassName()
    {
        return 'TPE\Dominio\Partido\Partido';
    }
}
