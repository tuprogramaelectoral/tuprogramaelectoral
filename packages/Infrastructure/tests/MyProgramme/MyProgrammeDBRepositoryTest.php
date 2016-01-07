<?php

require_once __DIR__ . "/../DB_TestCase.php";

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Ramsey\Uuid\Uuid;
use TPE\Domain\Field\Field;
use TPE\Infrastructure\Data\Loader;
use TPE\Infrastructure\MyProgramme\MyProgrammeDBRepository;


class MyProgrammeDBRepositoryTest extends DB_TestCase
{
    public function testShouldReturnNullWhenfindOneByIdIsNotAValidUuid()
    {
        $myProgramme = $this->getRepository()->findOneBy(['id' => 'notAValidUuid']);

        $this->assertNull($myProgramme);
    }

    public function testShouldFindNotExpiredByIdWhenMyProgrammeIsPublicAndNotExpired()
    {
        $this->loadFiles([]);

        $uuid = $this->saveNewMyProgramme(
            ['sanidad' => null],
            true
        );

        $this->assertInstanceOf(
            'TPE\Domain\MyProgramme\MyProgramme',
            $this->getRepository()->findNotExpiredById($uuid)
        );
    }

    public function testShouldFindNotExpiredByIdWhenMyProgrammeIsPublicAndExpired()
    {
        $this->loadFiles([]);

        $uuid = $this->saveNewMyProgramme(
            ['sanidad' => null],
            true,
            false,
            new \DateTime(MyProgrammeDBRepository::EXPIRATION_WINDOW)
        );

        $this->assertInstanceOf(
            'TPE\Domain\MyProgramme\MyProgramme',
            $this->getRepository()->findNotExpiredById($uuid)
        );
    }

    public function testShouldFindNotExpiredByIdWhenMyProgrammeIsNotPublicAndNotExpired()
    {
        $this->markTestIncomplete('It fails because of querying sqlite with a boolean = false.');

        $this->loadFiles([]);

        $uuid = $this->saveNewMyProgramme(
            ['sanidad' => null]
        );

        $myProgramme = $this->getRepository()->findNotExpiredById($uuid);

        $this->assertInstanceOf(
            'TPE\Domain\MyProgramme\MyProgramme',
            $myProgramme
        );
    }

    public function testShouldNotFindNotExpiredByIdWhenMyProgrammeIsNotPublicAndExpired()
    {
        $this->markTestIncomplete('It fails because of querying sqlite with a boolean = false.');

        $this->loadFiles([]);

        $uuid = $this->saveNewMyProgramme(
            ['sanidad' => null],
            false,
            false,
            new \DateTime(MyProgrammeDBRepository::EXPIRATION_WINDOW)
        );

        $this->assertNull($this->getRepository()->findNotExpiredById($uuid));
    }

    public function testShouldReturnTrueIfTheListOfInterestsExistDuringInterestsExist()
    {
        $this->loadFiles([
            'field/sanidad/field.json' => '{"name": "Sanidad"}',
            'field/educacion/field.json' => '{"name": "Educacion"}',
        ], true);

        $this->assertTrue($this->getRepository()->interestsExist(['sanidad', 'educacion']));
    }

    public function testShouldReturnFalseIfOneOfTheListOfInterestsDoNotExistDuringInterestsExist()
    {
        $this->loadFiles([
            'field/sanidad/field.json' => '{"name": "Sanidad"}',
            'field/educacion/field.json' => '{"name": "Educacion"}',
        ], true);

        $this->assertFalse($this->getRepository()->interestsExist(['sanidad', 'educacion', 'notExistentInterest']));
    }

    public function testShouldReturnTrueIfTheListOfPoliciesExistDuringPoliciesExist()
    {
        $this->loadFiles([
            'field/sanidad/field.json' => '{"name": "Sanidad"}',
            'field/educacion/field.json' => '{"name": "Educacion"}',
            'party/partido-ficticio/party.json' => '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}',
            'field/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "field": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
            'field/sanidad/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita',
            'field/educacion/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "field": "educacion", "sources": ["http://partido-ficticio.es/programa/"]}',
            'field/educacion/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita'
        ], true);

        $this->assertTrue($this->getRepository()->policiesExist(['partido-ficticio_sanidad', 'partido-ficticio_educacion']));
    }

    public function testShouldReturnFalseIfOneOfTheListOfPoliciesDoNotExistDuringPoliciesExist()
    {
        $this->loadFiles([
            'field/sanidad/field.json' => '{"name": "Sanidad"}',
            'field/educacion/field.json' => '{"name": "Educacion"}',
            'party/partido-ficticio/party.json' => '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}',
            'field/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "field": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
            'field/sanidad/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita',
            'field/educacion/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "field": "educacion", "sources": ["http://partido-ficticio.es/programa/"]}',
            'field/educacion/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita'
        ], true);

        $this->assertFalse($this->getRepository()->policiesExist(['sanidad', 'educacion', 'notExistentInterest']));
    }

    /**
     * @return MyProgrammeDBRepository
     */
    private function getRepository()
    {
        return $this->repos[Loader::CLASS_MY_PROGRAMME];
    }

    private function saveNewMyProgramme(array $policies, $public = false, $completed = false, \DateTime $lastModification = null)
    {
        $uuid = Uuid::uuid4()->toString();
        $lastModification = ($lastModification) ? $lastModification : new \DateTime();

        $metadata = $this->metadata[Loader::CLASS_MY_PROGRAMME];
        $query = $this->em->getConnection()->createQueryBuilder()
            ->insert($metadata->getTableName())
            ->setValue($metadata->getColumnName('id'), ':id')
            ->setValue($metadata->getColumnName('policies'), ':policies')
            ->setValue($metadata->getColumnName('public'), ':public')
            ->setValue($metadata->getColumnName('completed'), ':completed')
            ->setValue($metadata->getColumnName('lastModification'), ':lastModification')
            ->setParameters([
                'id' => $uuid,
                'policies' => json_encode($policies),
                'public' => $public,
                'completed' => $completed,
                'lastModification' => date_format($lastModification, 'Y-m-d H:i:s')
            ]);

        $query->execute();

        return $uuid;
    }
}
