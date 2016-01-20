<?php

require_once __DIR__ . "/../DB_TestCase.php";

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use TPE\Domain\Field\Field;
use TPE\Infrastructure\Data\Loader;
use TPE\Infrastructure\Field\FieldDBRepository;


class FieldDBRepositoryTest extends DB_TestCase
{
    public function testShouldSaveAField()
    {
        $this->loadFiles([]);

        $expected = New Field('Test Field');

        $this->getRepository()->save($expected);

        $current = $this->getRepository()->findOneBy(['name' => $expected->getName()]);
        $this->assertEquals($expected, $current);
    }

    public function testShouldFindFieldWithPoliciesById()
    {
        $this->loadFiles([
            'field/sanidad/field.json' => '{"name": "Sanidad"}',
            'party/partido-ficticio/party.json' => '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}',
            'field/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "field": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
            'field/sanidad/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita'
        ]);

        $field = $this->getRepository()->findFieldWithPoliciesById('sanidad');

        $this->assertCount(1, $field->getPolicies());
        $this->assertEquals('partido-ficticio', $field->getPolicies()[0]->getParty()->getId());
        $this->assertEquals('sanidad', $field->getPolicies()[0]->getField()->getId());
    }

    /**
     * @return FieldDBRepository
     */
    private function getRepository()
    {
        return $this->repos[Loader::CLASS_FIELD];
    }
}
