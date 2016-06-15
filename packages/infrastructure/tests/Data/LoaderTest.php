<?php

require_once __DIR__ . "/../DB_TestCase.php";

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use TPE\Domain\Election\Election;
use TPE\Domain\Scope\Scope;
use TPE\Domain\Party\Party;
use TPE\Domain\Party\Policy;
use TPE\Infrastructure\Data\Loader;


class LoaderTest extends DB_TestCase
{
    public function testShouldReadDataFilesAndInsertTheDatabaseWithThem()
    {
        $this->loadFiles([
            '1/election.json' => '{"edition": "1", "date": "1977-06-15"}',
            '1/scope/sanidad/scope.json' => '{"name": "Sanidad"}',
            '1/party/partido-ficticio/party.json' => '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}',
            '1/scope/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "scope": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
            '1/scope/sanidad/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita'
        ]);

        $election = Election::createFromJson('{"edition": "1", "date": "1977-06-15"}');
        $scope = Scope::createFromJson($election, '{"name": "Sanidad"}');
        $party = Party::createFromJson($election, '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}');
        $policy = new Policy(
            $party,
            $scope,
            ["http://partido-ficticio.es/programa/"],
            '## sanidad universal y gratuita'
        );

        $elections = $this->repos[Loader::CLASS_ELECTION]->findAll();
        $scopes = $this->repos[Loader::CLASS_SCOPE]->findAll();
        $parties = $this->repos[Loader::CLASS_PARTY]->findAll();
        $policies = $this->repos[Loader::CLASS_SCOPE]->findScopeWithPolicies(1, 'sanidad')->getPolicies();

        $this->assertCount(1, $elections);
        $this->assertEquals($election, $elections[0]);

        $this->assertCount(1, $scopes);
        $this->compareScopes($scope, $scopes[0]);

        $this->assertCount(1, $parties);
        $this->compareParties($party, $parties[0]);

        $this->assertCount(1, $policies);
        $this->comparePolicies($policy, $policies[0]);
    }

    private function compareScopes(Scope $expected, Scope $current)
    {
        $this->assertEquals($expected->getName(), $current->getName());
        $this->assertEquals($expected->getId(), $current->getId());
        $this->assertCount(1, $current->getPolicies());
    }

    private function compareParties(Party $expected, Party $current)
    {
        $this->assertEquals($expected->getName(), $current->getName());
        $this->assertEquals($expected->getId(), $current->getId());
        $this->assertEquals($expected->getAcronym(), $current->getAcronym());
        $this->assertEquals($expected->getProgrammeUrl(), $current->getProgrammeUrl());
        $this->assertCount(1, $current->getPolicies());
    }

    private function comparePolicies(Policy $expected, Policy $current)
    {
        $this->compareScopes($expected->getScope(), $current->getScope());
        $this->compareParties($expected->getParty(), $current->getParty());
        $this->assertEquals($expected->getId(), $current->getId());
        $this->assertEquals($expected->getSources(), $current->getSources());
        $this->assertEquals($expected->getContentInMarkdown(), $current->getContentInMarkdown());
        $this->assertEquals($expected->getContentInHtml(), $current->getContentInHtml());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testShouldThrowAnExceptionWhenContentIsNonValidJson()
    {
        $this->loadFiles([
            '1/election.json' => '{"edition": "1", "date": "1977-06-15"}',
            '1/scope/administracion-publica/scope.json' => 'NonValidJson',
        ]);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testShouldThrowAnExceptionWhenJsonIsIncomplete()
    {
        $this->loadFiles([
            '1/election.json' => '{"edition": "1", "date": "1977-06-15"}',
            '1/scope/administracion-publica/scope.json' => '{}',
        ]);
    }

    public function testShouldReadDataFilesAndUpdateTheDatabaseWithThem()
    {
        // Initial Data
        $this->loadFiles([
            '1/election.json' => '{"edition": "1", "date": "1977-06-15"}',
            '1/scope/sanidad/scope.json' => '{"name": "Sanidad"}',
            '1/party/partido-ficticio/party.json' => '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}',
            '1/scope/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "scope": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
            '1/scope/sanidad/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita'
        ]);

        // Update
        $this->loadFiles([
            '1/scope/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "scope": "sanidad", "sources": ["http://partido-ficticio.es/programa/sanidad nuevo apartado sobre sanidad en el programa electoral del partido"]}',
            '1/scope/sanidad/policy/partido-ficticio/content.md' => '## Nueva política'
        ], false);

        $election = Election::createFromJson('{"edition": "1", "date": "1977-06-15"}');
        $scope = Scope::createFromJson($election, '{"name": "Sanidad"}');
        $party = Party::createFromJson($election, '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}');
        $policy = new Policy(
            $party,
            $scope,
            ["http://partido-ficticio.es/programa/sanidad nuevo apartado sobre sanidad en el programa electoral del partido"],
            '## Nueva política'
        );

        $policies = $this->repos[Loader::CLASS_SCOPE]->findScopeWithPolicies(1, 'sanidad')->getPolicies();

        $this->assertCount(1, $policies);
        $this->comparePolicies($policy, $policies[0]);
    }
}
