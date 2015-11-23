<?php

namespace TPE\Dominio\Ambito;

use slugifier as s;
use TPE\Dominio\Datos\DatoInicial;

/**
 * Ámbito sobre el que actuan una serie de políticas
 *
 * Class Ambito
 * @package TPE\Ambito
 */
class Ambito extends DatoInicial
{
    /**
     * Nombre del Ámbito
     * @var string
     */
    private $nombre;


    /**
     * @param string $json
     * @return Ambito
     */
    public static function crearUsandoJson($json)
    {
        $data = json_decode($json, true);

        if (null === $data) {
            throw new \BadMethodCallException('detectado JSON malformado al crear el Ámbito a partir de ' . $json);
        }

        if (isset($data['nombre'])) {
            return new Ambito($data['nombre']);
        }

        throw new \BadMethodCallException('Faltan parámetros para crear el Ámbito a partir de ' . $json);
    }

    /**
     * @param string $nombre
     */
    public function __construct($nombre)
    {
        $this->nombre = $nombre;
        $this->id = s\slugify($nombre);
    }

    /**
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }
}
