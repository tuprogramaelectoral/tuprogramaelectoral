<?php

namespace TPE\Infraestructura\Datos;

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use TPE\Dominio\Ambito\Ambito;
use TPE\Dominio\Datos\DatoInicial;
use TPE\Dominio\Datos\Lector;
use TPE\Dominio\Partido\Partido;
use TPE\Dominio\Partido\Politica;


class LectorDeFicheros implements Lector
{
    const CLASE_AMBITO = 'TPE\Dominio\Ambito\Ambito';
    const CLASE_PARTIDO = 'TPE\Dominio\Partido\Partido';
    const CLASE_POLITICA = 'TPE\Dominio\Partido\Politica';
    const CLASE_POLITICA_CONTENIDO = 'ContenidoPolitica';
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
            $objetos[] = $this->cargarDesdeFichero($clase, $fichero);
        }

        return $objetos;
    }

    /**
     * @param string $clase
     * @return Finder|\Iterator
     * @throws \Exception
     */
    private function ficheros($clase)
    {
        try {
            switch ($clase) {
                case self::CLASE_AMBITO:
                    return (new Finder())->files()->in($this->path . '/ambito/*/')->name('ambito.json')->depth(0);
                case self::CLASE_PARTIDO:
                    return (new Finder())->files()->in($this->path . '/partido/*/')->name('partido.json')->depth(0);
                case self::CLASE_POLITICA:
                    return (new Finder())->files()->in($this->path . '/ambito/*/politica/*/')->name('politica.json')->depth(0);
                case self::CLASE_POLITICA_CONTENIDO:
                    return (new Finder())->files()->in($this->path . '/ambito/*/politica/*/')->name('contenido.md')->depth(0);
            };
        } catch (\InvalidArgumentException $exception) {
            return [];
        }

        throw new \BadMethodCallException("la clase {$clase} no está registrada en el lector de ficheros para lectura");
    }

    private function cargarDesdeFichero($clase, SplFileInfo $fichero)
    {
        switch ($clase) {
            case self::CLASE_AMBITO:
                return $fichero->getContents();
            case self::CLASE_PARTIDO:
                return $fichero->getContents();
            case self::CLASE_POLITICA:
                return [
                    'json' => $fichero->getContents(),
                    'contenido' => (new SplFileInfo(
                        $fichero->getPath() . 'contenido.md',
                        $fichero->getRelativePath(),
                        'contenido.md')
                    )->getContents()
                ];
        };

        throw new \BadMethodCallException("la clase {$clase} no está registrada en el lector de ficheros para instanciar");
    }

    public static function escribirFicherosDeTest(array $ficheros)
    {
        $fs = new Filesystem();
        $path = sys_get_temp_dir() . '/tpe-tests';
        $fs->remove($path);

        foreach ($ficheros as $filePath => $contenido) {
            $fs->dumpFile($path . '/' . $filePath, $contenido);
        }

        return $path;
    }
}
