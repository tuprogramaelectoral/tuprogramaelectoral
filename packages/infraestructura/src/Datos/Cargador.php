<?php

namespace TPE\Infraestructura\Datos;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use TPE\Dominio\Ambito\Ambito;
use TPE\Dominio\Datos\Lector;
use TPE\Dominio\Partido\Partido;
use TPE\Dominio\Partido\Politica;
use TPE\Dominio\Datos\DatoInicial;

class Cargador
{
    const CLASE_AMBITO = 'TPE\Dominio\Ambito\Ambito';
    const CLASE_PARTIDO = 'TPE\Dominio\Partido\Partido';
    const CLASE_POLITICA = 'TPE\Dominio\Partido\Politica';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var SchemaTool
     */
    private $schemaTool;

    /**
     * @var ClassMetadata[]
     */
    private $metadata;

    /**
     * @var Lector
     */
    private $lector;

    /**
     * @var DatoInicial[]
     */
    private $objetos;


    public function __construct(EntityManager $em)
    {
        $this->em = $em;

        $this->metadata = [
            self::CLASE_AMBITO => $this->em->getClassMetadata(self::CLASE_AMBITO),
            self::CLASE_PARTIDO => $this->em->getClassMetadata(self::CLASE_PARTIDO),
            self::CLASE_POLITICA => $this->em->getClassMetadata(self::CLASE_POLITICA)
        ];

        $this->schemaTool = new SchemaTool($this->em);
    }

    /**
     * @param Lector $lector
     */
    public function cargar(Lector $lector)
    {
        $this->lector = $lector;

        $this->regenerarEsquema();
        $this->cargarDatos(self::CLASE_AMBITO);
        $this->cargarDatos(self::CLASE_PARTIDO);
        $this->cargarDatos(self::CLASE_POLITICA);

        $this->em->flush();
        $this->em->clear();
    }

    private function cargarDatos($tipo)
    {
        foreach ($this->lector->leer($tipo) as $datos) {
            $this->em->persist(
                $this->crearObjeto($tipo, $datos)
            );
        }
    }

    private function crearObjeto($tipo, $datos)
    {
        switch ($tipo) {
            case self::CLASE_AMBITO:
                $ambito = Ambito::crearUsandoJson($datos);
                $this->objetos[self::CLASE_AMBITO][$ambito->getId()] = $ambito;
                return $ambito;
            case self::CLASE_PARTIDO:
                $partido = Partido::crearUsandoJson($datos);
                $this->objetos[self::CLASE_PARTIDO][$partido->getId()] = $partido;
                return $partido;
            case self::CLASE_POLITICA:
                $json = json_decode($datos['json'], true);
                return new Politica(
                    $this->getObjeto(self::CLASE_PARTIDO, $json['partido']),
                    $this->getObjeto(self::CLASE_AMBITO, $json['ambito']),
                    $json['fuentes'],
                    $datos['contenido']
                );
        };

        throw new \BadMethodCallException("la clase {$tipo} no está registrada en el lector de ficheros para instanciar");
    }

    private function getObjeto($tipo, $id)
    {
        if (isset($this->objetos[$tipo][$id])) {
            return $this->objetos[$tipo][$id];
        }

        throw new \Exception("No se ha encontrado el objeto referenciado {$id}");
    }

    private function regenerarEsquema()
    {
        $this->schemaTool->dropSchema([
            $this->metadata[self::CLASE_POLITICA],
            $this->metadata[self::CLASE_AMBITO],
            $this->metadata[self::CLASE_PARTIDO]
        ]);

        $this->schemaTool->createSchema([
            $this->metadata[self::CLASE_POLITICA],
            $this->metadata[self::CLASE_AMBITO],
            $this->metadata[self::CLASE_PARTIDO]
        ]);
    }
}
