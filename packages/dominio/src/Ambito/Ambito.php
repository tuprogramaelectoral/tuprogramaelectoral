<?php

namespace TPE\Dominio\Ambito;

use Assert\Assertion;
use TPE\Dominio\Datos\DatoInicial;
use TPE\Dominio\Partido\Politica;


/**
 * Ámbito sobre el que actuan una serie de políticas
 *
 * Class Ambito
 * @package TPE\Ambito
 */
class Ambito implements DatoInicial
{
    /**
     * @var string
     */
    private $id;

    /**
     * Nombre del Ámbito
     * @var string
     */
    private $nombre;

    /**
     * @var Politica[]
     */
    private $politicas = [];


    /**
     * @param string $nombre
     * @param Politica[] $politicas
     */
    public function __construct($nombre, $politicas = null)
    {
        \Assert\that($nombre)->string()->notEmpty();

        $this->id = \slugifier\slugify($nombre);
        $this->nombre = $nombre;
        $this->politicas = is_array($politicas) ? $politicas : [];
    }

    /**
     * @param string $json
     * @param array $politicas
     * @return Ambito
     */
    public static function crearUsandoJson($json, array $politicas = null)
    {
        $data = json_decode($json, true);

        if (null === $data) {
            throw new \BadMethodCallException('detectado JSON malformado al crear el Ámbito a partir de ' . $json);
        }

        if (isset($data['nombre'])) {
            return new Ambito($data['nombre'], $politicas);
        }

        throw new \BadMethodCallException('Faltan parámetros para crear el Ámbito a partir de ' . $json);
    }

    /**
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @return Politica[]
     */
    public function getPoliticas()
    {
        return $this->politicas;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
