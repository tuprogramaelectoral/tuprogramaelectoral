<?php

namespace TPE\Infraestructura\Datos;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use TPE\Dominio\Ambito\Ambito;
use TPE\Dominio\Datos\DatoInicial;
use TPE\Dominio\Datos\Lector;


class LectorDeFicheros implements Lector
{
    /**
     * @var string
     */
    private $path;


    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @return DatoInicial[]
     */
    public function leer($clase)
    {
        $objetos = [];
        $ficheros = $this->ficheros($clase);
        foreach ($ficheros as $fichero) {
            $objetos[] = $this->cargarDesdeJson($clase, file_get_contents($fichero->getPathname()));
        }

        return $objetos;
    }

    /**
     * @param string $clase
     * @return \SplFileInfo[]
     * @throws \Exception
     */
    private function ficheros($clase)
    {
        switch ($clase) {
            case 'TPE\Dominio\Ambito\Ambito':
                return (new Finder())->files()->in($this->path . '/ambito/*/')->depth(0);
        };

        throw new \BadMethodCallException("la clase {$clase} no está registrada en el lector de ficheros para lectura");
    }


    private function cargarDesdeJson($clase, $contenido)
    {
        switch ($clase) {
            case 'TPE\Dominio\Ambito\Ambito':
                return Ambito::crearUsandoJson($contenido);
        };

        throw new \BadMethodCallException("la clase {$clase} no está registrada en el lector de ficheros para instanciar");
    }

    public static function escribirFicherosDeTest(array $ficheros)
    {
        $fs = new Filesystem();
        $path = sys_get_temp_dir() . '/vsp-tests/';
        $fs->remove($path);

        foreach ($ficheros as $filePath => $contenido) {
            $fs->dumpFile($path . $filePath, $contenido);
        }

        return $path;
    }
}
