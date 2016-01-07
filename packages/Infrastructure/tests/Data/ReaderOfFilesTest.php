<?php

use TPE\Domain\Field\Field;
use TPE\Domain\Party\Party;
use TPE\Domain\Party\Policy;
use TPE\Infrastructure\Data\ReaderOfFiles;


class ReaderOfFilesTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldReadFieldFilesAndReturnsItsContent()
    {
        $path = ReaderOfFiles::writeTestFiles([
            'field/administracion-publica/field.json' => '{"name": "Administración Pública"}',
            'field/agricultura/field.json' => '{"name": "Agricultura"}'
        ]);

        $data = [
            '{"name": "Administración Pública"}',
            '{"name": "Agricultura"}',
        ];

        $reader = new ReaderOfFiles($path);
        $fields = $reader->read('TPE\Domain\Field\Field');

        $this->assertCount(count($data), $fields);

        for ($i = 0; $i < count($fields); $i++) {
            $this->assertEquals($data[$i], $fields[$i]);
        }
    }

    public function testShouldReadPartyFilesAndReturnsItsContent()
    {
        $path = ReaderOfFiles::writeTestFiles([
            'party/partido-ficticio/party.json' => '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}'
        ]);

        $data = [
            '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}'
        ];

        $reader = new ReaderOfFiles($path);
        $parties = $reader->read('TPE\Domain\Party\Party');

        $this->assertCount(count($data), $parties);

        for ($i = 0; $i < count($parties); $i++) {
            $this->assertEquals($data[$i], $parties[$i]);
        }
    }

    public function testShouldReadPolicyFilesAndReturnsItsContent()
    {
        $path = ReaderOfFiles::writeTestFiles([
            'field/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "field": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
            'field/sanidad/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita'
        ]);

        $data = [
            [
                'json' => '{"party": "partido-ficticio", "field": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
                'content' => '## sanidad universal y gratuita'
            ]
        ];

        $reader = new ReaderOfFiles($path);
        $policies = $reader->read('TPE\Domain\Party\Policy');

        $this->assertCount(count($data), $policies);

        for ($i = 0; $i < count($policies); $i++) {
            $this->assertEquals($data[$i], $policies[$i]);
        }
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testShouldThrowAnExceptionWhenTheClassIsNotRegistered()
    {
        $reader = new ReaderOfFiles(ReaderOfFiles::writeTestFiles([]));

        $reader->read('Unregistered\Class');
    }
}
