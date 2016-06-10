<?php

use TPE\Domain\Election\Election;
use TPE\Domain\Scope\Scope;
use TPE\Domain\Party\Party;
use TPE\Domain\Party\Policy;
use TPE\Infrastructure\Data\ReaderOfFiles;


class ReaderOfFilesTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldReadElectionFilesAndReturnsItsContent()
    {
        $path = ReaderOfFiles::writeTestFiles([
            '1/election.json' => '{"edition": "1", "date": "1977-06-15"}',
            '2/election.json' => '{"edition": "2", "date": "1979-03-01"}'
        ]);

        $data = [
            '{"edition": "1", "date": "1977-06-15"}',
            '{"edition": "2", "date": "1979-03-01"}',
        ];

        $reader = new ReaderOfFiles($path);
        $elections = $reader->read(Election::class);

        $this->assertCount(count($data), $elections);

        for ($i = 0; $i < count($elections); $i++) {
            $this->assertEquals($data[$i], $elections[$i]);
        }
    }

    public function testShouldReadScopeFilesAndReturnsItsContent()
    {
        $path = ReaderOfFiles::writeTestFiles([
            '1/election.json' => '{"edition": "1", "date": "1977-06-15"}',
            '1/scope/administracion-publica/scope.json' => '{"name": "Administración Pública"}',
            '1/scope/agricultura/scope.json' => '{"name": "Agricultura"}'
        ]);

        $data = [
            ['edition' => 1, 'json' => '{"name": "Administración Pública"}'],
            ['edition' => 1, 'json' => '{"name": "Agricultura"}'],
        ];

        $reader = new ReaderOfFiles($path);
        $scopes = $reader->read(Scope::class);

        $this->assertCount(count($data), $scopes);

        for ($i = 0; $i < count($scopes); $i++) {
            $this->assertEquals($data[$i], $scopes[$i]);
        }
    }

    public function testShouldReadPartyFilesAndReturnsItsContent()
    {
        $path = ReaderOfFiles::writeTestFiles([
            '1/election.json' => '{"edition": "1", "date": "1977-06-15"}',
            '1/party/partido-ficticio/party.json' => '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}'
        ]);

        $data = [
            ['edition' => 1, 'json' => '{"name": "Partido Ficticio", "acronym": "PF", "programme": "http://partido-ficticio.es"}']
        ];

        $reader = new ReaderOfFiles($path);
        $parties = $reader->read(Party::class);

        $this->assertCount(count($data), $parties);

        for ($i = 0; $i < count($parties); $i++) {
            $this->assertEquals($data[$i], $parties[$i]);
        }
    }

    public function testShouldReadPolicyFilesAndReturnsItsContent()
    {
        $path = ReaderOfFiles::writeTestFiles([
            '1/election.json' => '{"edition": "1", "date": "1977-06-15"}',
            '1/scope/sanidad/policy/partido-ficticio/policy.json' => '{"party": "partido-ficticio", "scope": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
            '1/scope/sanidad/policy/partido-ficticio/content.md' => '## sanidad universal y gratuita'
        ]);

        $data = [
            [
                'json' => '{"party": "partido-ficticio", "scope": "sanidad", "sources": ["http://partido-ficticio.es/programa/"]}',
                'content' => '## sanidad universal y gratuita',
                'edition' => '1'
            ]
        ];

        $reader = new ReaderOfFiles($path);
        $policies = $reader->read(Policy::class);

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
