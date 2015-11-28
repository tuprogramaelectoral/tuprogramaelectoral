<?php

namespace TPE\Dominio\Datos;


interface DatoInicialRepositorio
{
    /**
     * @return DatoInicial[]
     */
    public function findAll();

    /**
     * @param string $id
     * @return DatoInicial|null
     */
    public function find($id);

    /**
     * @param DatoInicial $ambito
     * @param bool $flush
     */
    public function save(DatoInicial $ambito, $flush = true);

    /**
     * @return string
     */
    public function getClassName();
}
