<?php

namespace TPE\Infrastructure\Data;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\SchemaTool;
use TPE\Domain\Scope\Scope;
use TPE\Domain\Data\Reader;
use TPE\Domain\Party\Party;
use TPE\Domain\Party\Policy;
use TPE\Domain\Data\InitialData;

class Loader
{
    const CLASS_FIELD = 'TPE\Domain\Scope\Scope';
    const CLASS_PARTY = 'TPE\Domain\Party\Party';
    const CLASS_POLICY = 'TPE\Domain\Party\Policy';
    const CLASS_MY_PROGRAMME = 'TPE\Domain\MyProgramme\MyProgramme';

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
     * @var Reader
     */
    private $reader;

    /**
     * @var InitialDAta[]
     */
    private $objects;


    public function __construct(EntityManager $em)
    {
        $this->em = $em;

        $this->metadata = [
            self::CLASS_FIELD => $this->em->getClassMetadata(self::CLASS_FIELD),
            self::CLASS_PARTY => $this->em->getClassMetadata(self::CLASS_PARTY),
            self::CLASS_POLICY => $this->em->getClassMetadata(self::CLASS_POLICY),
            self::CLASS_MY_PROGRAMME => $this->em->getClassMetadata(self::CLASS_MY_PROGRAMME),
        ];

        $this->schemaTool = new SchemaTool($this->em);
    }

    /**
     * @param Reader $reader
     */
    public function load(Reader $reader)
    {
        $this->reader = $reader;

        $this->loadDataFor(self::CLASS_FIELD);
        $this->loadDataFor(self::CLASS_PARTY);
        $this->loadDataFor(self::CLASS_POLICY);
    }

    private function loadDataFor($class)
    {
        foreach ($this->reader->read($class) as $data) {
            $object = $this->createObject($class, $data);
            if ($this->exists($object)) {
                $this->update($object);
            } else {
                $this->insert($object);
            }
        }
    }

    private function exists(InitialData $object)
    {
        return $this->em->getConnection()->createQueryBuilder()
            ->select('count(*)')
            ->from($this->getMetadata($object)->getTableName())
            ->where('id = :id')
            ->setParameter('id', $object->getId())
            ->execute()
            ->fetchColumn() == 1;
    }

    private function update(InitialData $object)
    {
        $metadata = $this->getMetadata($object);
        $idColumn = $metadata->getSingleIdentifierColumnName();
        $query = $this->em->getConnection()->createQueryBuilder()
            ->update($metadata->getTableName())
            ->where("{$idColumn} = :{$idColumn}")
            ->setParameter($idColumn, $object->getId());

        foreach ($metadata->getFieldNames() as $scope) {
            $query
                ->set($metadata->getColumnName($scope), ':' . $scope)
                ->setParameter($scope, $this->getFieldValue($object, $scope));
        }

        $query->execute();
    }

    private function insert(InitialData $object)
    {
        $metadata = $this->getMetadata($object);
        $query = $this->em->getConnection()->createQueryBuilder()
            ->insert($metadata->getTableName());

        foreach ($metadata->getFieldNames() as $scope) {
            $query
                ->setValue($metadata->getColumnName($scope), ':' . $scope)
                ->setParameter($scope, $this->getFieldValue($object, $scope));
        }

        foreach ($metadata->getAssociationMappings() as $scope => $mapping) {
            if ($mapping['type'] == ClassMetadataInfo::MANY_TO_ONE && $mapping['isOwningSide']) {
                $query
                    ->setValue($metadata->getColumnName($scope), ':' . $scope)
                    ->setParameter($scope, $metadata->getFieldValue($object, $scope)->getId());
            }
        }

        $query->execute();
    }

    private function getFieldValue(InitialData $object, $scope)
    {
        $metadata = $this->getMetadata($object);
        $type = Type::getType($metadata->getTypeOfField($scope));

        return $type->convertToDatabaseValue(
            $metadata->getFieldValue($object, $scope),
            $this->em->getConnection()->getDatabasePlatform()
        );
    }

    /**
     * @param InitialData $object
     * @return ClassMetadataInfo
     */
    private function getMetadata(InitialData $object)
    {
        return $this->metadata[get_class($object)];
    }

    /**
     * @param string $data
     * @param string $data
     * @return InitialData
     * @throws \Exception
     */
    private function createObject($class, $data)
    {
        switch ($class) {
            case self::CLASS_FIELD:
                $scope = Scope::createFromJson($data);
                $this->objects[self::CLASS_FIELD][$scope->getId()] = $scope;
                return $scope;
            case self::CLASS_PARTY:
                $party = Party::createFromJson($data);
                $this->objects[self::CLASS_PARTY][$party->getId()] = $party;
                return $party;
            case self::CLASS_POLICY:
                $json = json_decode($data['json'], true);
                return new Policy(
                    $this->getObject(self::CLASS_PARTY, $json['party']),
                    $this->getObject(self::CLASS_FIELD, $json['scope']),
                    $json['sources'],
                    $data['content']
                );
        };

        throw new \BadMethodCallException("the class {$class} is not registered in the Loader object creation");
    }

    private function getObject($class, $id)
    {
        if (isset($this->objects[$class][$id])) {
            return $this->objects[$class][$id];
        }

        throw new \Exception("Unable to locate the referred object {$id}");
    }

    public function regenerateScheme()
    {
        $this->schemaTool->dropSchema($this->metadata);
        $this->schemaTool->createSchema($this->metadata);
    }
}
