<?php

namespace TPE\Dominio\Partido;

use Assert\Assertion;
use TPE\Dominio\Datos\DatoInicial;


class Partido implements DatoInicial
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $nombre;

    /**
     * @var string
     */
    private $siglas;

    /**
     * @var string
     */
    private $programa;

    /**
     * @var Politica[]
     */
    private $politicas;


    public function __construct($nombre, $siglas, $programa = null)
    {
        \Assert\lazy()
            ->that($nombre, 'nombre')->string()->notEmpty()
            ->that($siglas, 'siglas')->string()->notEmpty()
            ->verifyNow();
        Assertion::nullOrUrl($programa, 'programa no es una url');

        $this->id = \slugifier\slugify($nombre);
        $this->nombre = $nombre;
        $this->siglas = $siglas;
        $this->programa = $programa;
    }

    /**
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @return string
     */
    public function getSiglas()
    {
        return $this->siglas;
    }

    /**
     * @return string
     */
    public function getPrograma()
    {
        return $this->programa;
    }

    /**
     * @param string $json
     * @return Partido
     */
    public static function crearUsandoJson($json)
    {
        $data = json_decode($json, true);

        if (null === $data) {
            throw new \BadMethodCallException('detectado JSON malformado al crear el Partido a partir de ' . $json);
        }

        if (isset($data['nombre']) && isset($data['siglas'])) {
            return new Partido(
                $data['nombre'],
                $data['siglas'],
                isset($data['programa']) ? $data['programa'] : null
            );
        }

        throw new \BadMethodCallException('Faltan parámetros para crear el Partido a partir de ' . $json);
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
