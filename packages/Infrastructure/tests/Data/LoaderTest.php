<?php

require_once __DIR__ . "/../DB_TestCase.php";

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use TPE\Domain\Field\Field;
use TPE\Domain\Party\Party;
use TPE\Domain\Party\Policy;
use TPE\Infrastructure\Data\Loader;


class LoaderTest extends DB_TestCase
{
    public function testShouldReadDataFilesAndInsertTheDatabaseWithThem()
    {
        $this->loadFiles([
            'field/sanidad/field.json' => '{"name": "Sanidad"}',
            'party/partido-ficticio/party.json' => '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}',
            'field/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "field": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
            'field/sanidad/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita'
        ]);

        $field = Field::createFromJson('{"name": "Sanidad"}');
        $party = Party::createFromJson('{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}');
        $policy = new Policy(
            $party,
            $field,
            ["http://partido-ficticio.es/programa/"],
            '## sanidad universal y gratuita'
        );

        $fields = $this->repos[Loader::CLASS_FIELD]->findAll();
        $parties = $this->repos[Loader::CLASS_PARTY]->findAll();
        $policies = $this->repos[Loader::CLASS_FIELD]->findOneBy(['id' => 'sanidad'])->getPolicies();

        $this->assertCount(1, $fields);
        $this->compareFields($field, $fields[0]);

        $this->assertCount(1, $parties);
        $this->compareParties($party, $parties[0]);

        $this->assertCount(1, $policies);
        $this->comparePolicies($policy, $policies[0]);
    }

    private function compareFields(Field $expected, Field $current)
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
        $this->compareFields($expected->getField(), $current->getField());
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
            'field/administracion-publica/field.json' => 'NonValidJson',
        ]);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testShouldThrowAnExceptionWhenJsonIsIncomplete()
    {
        $this->loadFiles([
            'field/administracion-publica/field.json' => '{}',
        ]);
    }

    public function testShouldReadDataFilesAndUpdateTheDatabaseWithThem()
    {
        // Initial Data
        $this->loadFiles([
            'field/sanidad/field.json' => '{"name": "Sanidad"}',
            'party/partido-ficticio/party.json' => '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}',
            'field/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "field": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
            'field/sanidad/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita'
        ]);

        // Update
        $this->loadFiles([
            'field/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "field": "sanidad", "sources": ["http://partido-ficticio.es/programa/sanidad nuevo apartado sobre sanidad en el programa electoral del partido"]}',
            'field/sanidad/policy/partido-ficticio/content.md' => '## Nueva política'
        ], false);

        $field = Field::createFromJson('{"name": "Sanidad"}');
        $party = Party::createFromJson('{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}');
        $policy = new Policy(
            $party,
            $field,
            ["http://partido-ficticio.es/programa/sanidad nuevo apartado sobre sanidad en el programa electoral del partido"],
            '## Nueva política'
        );

        $policies = $this->repos[Loader::CLASS_FIELD]->findOneBy(['id' => 'sanidad'])->getPolicies();

        $this->assertCount(1, $policies);
        $this->comparePolicies($policy, $policies[0]);
    }
}
