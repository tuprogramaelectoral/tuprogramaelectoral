<?php

require_once __DIR__ . "/../DB_TestCase.php";

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Ramsey\Uuid\Uuid;
use TPE\Domain\MyProgramme\MyProgramme;
use TPE\Domain\Scope\Scope;
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
            MyProgramme::class,
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
            MyProgramme::class,
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
            MyProgramme::class,
            $myProgramme
        );
    }

    public function testShouldNotFindNotExpiredByIdWhenMyProgrammeIsNotPublicAndExpired()
    {
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
            '1/election.json' => '{"edition": "1", "date": "1977-06-15"}',
            '1/scope/sanidad/scope.json' => '{"name": "Sanidad"}',
            '1/scope/educacion/scope.json' => '{"name": "Educacion"}',
        ], true);

        $this->assertTrue($this->getRepository()->interestsExist(1, ['sanidad', 'educacion']));
    }

    public function testShouldReturnFalseIfOneOfTheListOfInterestsDoNotExistDuringInterestsExist()
    {
        $this->loadFiles([
            '1/election.json' => '{"edition": "1", "date": "1977-06-15"}',
            '1/scope/sanidad/scope.json' => '{"name": "Sanidad"}',
            '1/scope/educacion/scope.json' => '{"name": "Educacion"}',
        ], true);

        $this->assertFalse($this->getRepository()->interestsExist(1, ['sanidad', 'educacion', 'notExistentInterest']));
    }

    public function testShouldReturnTrueIfTheListOfPoliciesExistDuringPoliciesExist()
    {
        $this->loadFiles([
            '1/election.json' => '{"edition": "1", "date": "1977-06-15"}',
            '1/scope/sanidad/scope.json' => '{"name": "Sanidad"}',
            '1/scope/educacion/scope.json' => '{"name": "Educacion"}',
            '1/party/partido-ficticio/party.json' => '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}',
            '1/scope/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "scope": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
            '1/scope/sanidad/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita',
            '1/scope/educacion/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "scope": "educacion", "sources": ["http://partido-ficticio.es/programa/"]}',
            '1/scope/educacion/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita'
        ], true);

        $this->assertTrue($this->getRepository()->policiesExist(1, ['sanidad' => 'partido-ficticio', 'educacion' => 'partido-ficticio']));
    }

    public function testShouldReturnFalseIfOneOfTheListOfPoliciesDoNotExistDuringPoliciesExist()
    {
        $this->loadFiles([
            '1/election.json' => '{"edition": "1", "date": "1977-06-15"}',
            '1/scope/sanidad/scope.json' => '{"name": "Sanidad"}',
            '1/scope/educacion/scope.json' => '{"name": "Educacion"}',
            '1/party/partido-ficticio/party.json' => '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}',
            '1/scope/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "scope": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
            '1/scope/sanidad/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita',
            '1/scope/educacion/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "scope": "educacion", "sources": ["http://partido-ficticio.es/programa/"]}',
            '1/scope/educacion/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita'
        ], true);

        $this->assertFalse($this->getRepository()->policiesExist(1, ['sanidad' => 'partido-ficticio', 'educacion' => 'notExistentPolicy']));
    }

    /**
     * @return MyProgrammeDBRepository
     */
    private function getRepository()
    {
        return $this->repos[Loader::CLASS_MY_PROGRAMME];
    }

    private function saveNewMyProgramme¢(array $policies, $public = false, $completed = false, \DateTime $lastModification = null)
    {
        $uuid = Uuid::uuid4()->toString();
        $lastModification = ($lastModification) ? $lastModification : new \DateTime();

        $metadata = $this->metadata[Loader::CLASS_MY_PROGRAMME];
        $query = $this->em->getConnection()->createQueryBuilder()
            ->insert($metadata->getTableName())
            ->setValue($metadata->getColumnName('id'), ':id')
            ->setValue($metadata->getColumnName('edition'), 1)
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
