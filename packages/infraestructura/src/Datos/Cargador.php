<?php

namespace TPE\Infraestructura\Datos;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
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
    const CLASE_MIPROGRAMA = 'TPE\Dominio\MiPrograma\MiPrograma';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var SchemaTool
     */
    private $schemaTool;

    /**
     * @var ClassMetadataInfo[]
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
            self::CLASE_POLITICA => $this->em->getClassMetadata(self::CLASE_POLITICA),
            self::CLASE_MIPROGRAMA => $this->em->getClassMetadata(self::CLASE_MIPROGRAMA),
        ];

        $this->schemaTool = new SchemaTool($this->em);
    }

    /**
     * @param Lector $lector
     */
    public function cargar(Lector $lector)
    {
        $this->lector = $lector;

        $this->cargarDatos(self::CLASE_AMBITO);
        $this->cargarDatos(self::CLASE_PARTIDO);
        $this->cargarDatos(self::CLASE_POLITICA);
    }

    private function cargarDatos($tipo)
    {
        foreach ($this->lector->leer($tipo) as $datos) {
            $dato = $this->crearDato($tipo, $datos);
            if ($this->existe($dato)) {
                $this->update($dato);
            } else {
                $this->insert($dato);
            }
        }
    }

    private function existe(DatoInicial $dato)
    {
        return $this->em->getConnection()->createQueryBuilder()
            ->select('count(*)')
            ->from($this->getMetadata($dato)->getTableName())
            ->where('id = :id')
            ->setParameter('id', $dato->getId())
            ->execute()
            ->fetchColumn() == 1;
    }

    private function update(DatoInicial $dato)
    {
        $metadata = $this->getMetadata($dato);
        $idColumn = $metadata->getSingleIdentifierColumnName();
        $query = $this->em->getConnection()->createQueryBuilder()
            ->update($metadata->getTableName())
            ->where("{$idColumn} = :{$idColumn}")
            ->setParameter($idColumn, $dato->getId());

        foreach ($metadata->getFieldNames() as $campo) {
            $query
                ->set($metadata->getColumnName($campo), ':' . $campo)
                ->setParameter($campo, $this->getFieldValue($dato, $campo));
        }

        $query->execute();
    }

    private function insert(DatoInicial $dato)
    {
        $metadata = $this->getMetadata($dato);
        $query = $this->em->getConnection()->createQueryBuilder()
            ->insert($metadata->getTableName());

        foreach ($metadata->getFieldNames() as $campo) {
            $query
                ->setValue($metadata->getColumnName($campo), ':' . $campo)
                ->setParameter($campo, $this->getFieldValue($dato, $campo));
        }

        foreach ($metadata->getAssociationMappings() as $campo => $mapping) {
            if ($mapping['type'] == ClassMetadataInfo::MANY_TO_ONE && $mapping['isOwningSide']) {
                $query
                    ->setValue($metadata->getColumnName($campo), ':' . $campo)
                    ->setParameter($campo, $metadata->getFieldValue($dato, $campo)->getId());
            }
        }

        $query->execute();
    }

    private function getFieldValue(DatoInicial $dato, $campo)
    {
        $metadata = $this->getMetadata($dato);
        $type = Type::getType($metadata->getTypeOfField($campo));

        return $type->convertToDatabaseValue(
            $metadata->getFieldValue($dato, $campo),
            $this->em->getConnection()->getDatabasePlatform()
        );
    }

    /**
     * @param DatoInicial $dato
     * @return ClassMetadataInfo
     */
    private function getMetadata(DatoInicial $dato)
    {
        return $this->metadata[get_class($dato)];
    }

    /**
     * @param string $tipo
     * @param string $datos
     * @return DatoInicial
     * @throws \Exception
     */
    private function crearDato($tipo, $datos)
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
                    $this->getDato(self::CLASE_PARTIDO, $json['partido']),
                    $this->getDato(self::CLASE_AMBITO, $json['ambito']),
                    $json['fuentes'],
                    $datos['contenido']
                );
        };

        throw new \BadMethodCallException("la clase {$tipo} no está registrada en el lector de ficheros para instanciar");
    }

    private function getDato($tipo, $id)
    {
        if (isset($this->objetos[$tipo][$id])) {
            return $this->objetos[$tipo][$id];
        }

        throw new \Exception("No se ha encontrado el objeto referenciado {$id}");
    }

    public function regenerarEsquema()
    {
        $this->schemaTool->dropSchema($this->metadata);
        $this->schemaTool->createSchema($this->metadata);
    }
}
