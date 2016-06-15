<?php

require_once __DIR__ . "/../DB_TestCase.php";

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use TPE\Domain\Election\Election;
use TPE\Domain\Scope\Scope;
use TPE\Infrastructure\Data\Loader;
use TPE\Infrastructure\Scope\ScopeDBRepository;


class ScopeDBRepositoryTest extends DB_TestCase
{
    public function testShouldSaveAScope()
    {
        $this->loadFiles([]);

        $expected = New Scope(
            new Election(1, "1977-06-15"),
            'Test Scope'
        );

        $this->getRepository()->save($expected);

        $current = $this->getRepository()->findOneBy(['name' => $expected->getName()]);
        $this->assertEquals($expected, $current);
    }

    public function testShouldFindScopeWithPoliciesById()
    {
        $this->loadFiles([
            '1/election.json' => '{"edition": "1", "date": "1977-06-15"}',
            '1/scope/sanidad/scope.json' => '{"name": "Sanidad"}',
            '1/party/partido-ficticio/party.json' => '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}',
            '1/scope/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "scope": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
            '1/scope/sanidad/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita'
        ]);

        $scope = $this->getRepository()->findScopeWithPolicies(1, 'sanidad');

        $this->assertCount(1, $scope->getPolicies());
        $this->assertEquals('partido-ficticio', $scope->getPolicies()[0]->getParty()->getParty());
        $this->assertEquals('sanidad', $scope->getPolicies()[0]->getScope()->getScope());
    }

    /**
     * @return ScopeDBRepository
     */
    private function getRepository()
    {
        return $this->repos[Loader::CLASS_SCOPE];
    }
}
