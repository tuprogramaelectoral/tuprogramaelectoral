<?php

use TPE\Domain\Scope\Scope;
use TPE\Domain\Party\Party;
use TPE\Domain\Party\Policy;
use TPE\Infrastructure\Data\ReaderOfFiles;


class ReaderOfFilesTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldReadScopeFilesAndReturnsItsContent()
    {
        $path = ReaderOfFiles::writeTestFiles([
            'scope/administracion-publica/scope.json' => '{"name": "Administración Pública"}',
            'scope/agricultura/scope.json' => '{"name": "Agricultura"}'
        ]);

        $data = [
            '{"name": "Administración Pública"}',
            '{"name": "Agricultura"}',
        ];

        $reader = new ReaderOfFiles($path);
        $scopes = $reader->read('TPE\Domain\Scope\Scope');

        $this->assertCount(count($data), $scopes);

        for ($i = 0; $i < count($scopes); $i++) {
            $this->assertEquals($data[$i], $scopes[$i]);
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
            'scope/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "scope": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
            'scope/sanidad/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita'
        ]);

        $data = [
            [
                'json' => '{"party": "partido-ficticio", "scope": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
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
