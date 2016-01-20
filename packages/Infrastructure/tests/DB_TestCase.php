<?php

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use TPE\Domain\Field\Field;
use TPE\Domain\Party\Party;
use TPE\Domain\Party\Policy;
use TPE\Infrastructure\Data\DBRepository;
use TPE\Infrastructure\Data\ReaderOfFiles;
use TPE\Infrastructure\Field\FieldDBRepository;
use TPE\Infrastructure\Data\Loader;
use TPE\Infrastructure\MyProgramme\MyProgrammeDBRepository;
use TPE\Infrastructure\Party\PartyDBRepository;


class DB_TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var DBRepository[]
     */
    protected $repos;

    /**
     * @var ClassMetadata[]
     */
    protected $metadata;

    /**
     * @var Loader
     */
    protected $loader;


    public function setUp()
    {
        $namespaces = array(
            'src/Field' => 'TPE\Domain\Field',
            'src/Party' => 'TPE\Domain\Party',
            'src/MyProgramme' => 'TPE\Domain\MyProgramme'
        );

        $dbParams = array(
            'driver' => 'pdo_sqlite',
            'path' => '/tmp/sqlite.db'
        );

        if (file_exists('/tmp/sqlite.db')) {
            unlink('/tmp/sqlite.db');
        }

        $config = Setup::createConfiguration(true);
        $config->setMetadataDriverImpl(new SimplifiedYamlDriver($namespaces));

        $this->em = EntityManager::create($dbParams, $config);
        $this->loader = new Loader($this->em);

        $this->metadata = [
            Loader::CLASS_FIELD => $this->em->getClassMetadata(Loader::CLASS_FIELD),
            Loader::CLASS_PARTY => $this->em->getClassMetadata(Loader::CLASS_PARTY),
            Loader::CLASS_POLICY => $this->em->getClassMetadata(Loader::CLASS_POLICY),
            Loader::CLASS_MY_PROGRAMME => $this->em->getClassMetadata(Loader::CLASS_MY_PROGRAMME)
        ];

        $this->repos[Loader::CLASS_FIELD] = New FieldDBRepository($this->em, $this->metadata[Loader::CLASS_FIELD]);
        $this->repos[Loader::CLASS_PARTY] = New PartyDBRepository($this->em, $this->metadata[Loader::CLASS_PARTY]);
        $this->repos[Loader::CLASS_MY_PROGRAMME] = New MyProgrammeDBRepository($this->em, $this->metadata[Loader::CLASS_MY_PROGRAMME]);
    }

    protected function loadFiles(array $files, $force = true)
    {
        $path = ReaderOfFiles::writeTestFiles($files);

        $this->loadFromPaths($path, $force);
    }

    protected function loadFromPaths($path, $force = true)
    {
        if ($force) {
            $this->loader->regenerateScheme();
        }

        $this->loader->load(new ReaderOfFiles($path));
    }
}
