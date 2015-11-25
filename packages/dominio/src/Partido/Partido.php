<?php

namespace TPE\Dominio\Partido;

use Assert\Assertion;
use TPE\Dominio\Datos\DatoInicial;


class Partido extends DatoInicial
{
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


    public function __construct($nombre, $siglas, $programa = null)
    {
        \Assert\lazy()
            ->that($nombre, 'nombre')->notEmpty()
            ->that($siglas, 'siglas')->notEmpty()
            ->verifyNow();
        Assertion::nullOrUrl($programa, 'programa no es una url');

        parent::__construct($nombre);
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
}
