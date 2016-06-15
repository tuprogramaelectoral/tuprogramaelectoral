<?php

namespace TPE\Infrastructure\Data;

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use TPE\Domain\Scope\Scope;
use TPE\Domain\Data\InitialData;
use TPE\Domain\Data\Reader;
use TPE\Domain\Party\Party;
use TPE\Domain\Party\Policy;


class ReaderOfFiles implements Reader
{
    const CLASS_ELECTION = 'TPE\Domain\Election\Election';
    const CLASS_SCOPE = 'TPE\Domain\Scope\Scope';
    const CLASS_PARTY = 'TPE\Domain\Party\Party';
    const CLASS_POLICY = 'TPE\Domain\Party\Policy';
    const CLASS_POLICY_CONTENT = 'PolicyContent';

    /**
     * @var string
     */
    private $path;


    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @return []
     */
    public function read($class)
    {
        $objects = [];
        $files = $this->files($class);
        foreach ($files as $file) {
            $objects[] = $this->load($class, $file);
        }

        return $objects;
    }

    /**
     * @param string $class
     * @return Finder|\Iterator
     * @throws \Exception
     */
    private function files($class)
    {
        try {
            switch ($class) {
                case self::CLASS_ELECTION:
                    return (new Finder())->files()->in($this->path . '/*/')->name('election.json')->depth(0);
                case self::CLASS_SCOPE:
                    return (new Finder())->files()->in($this->path . '/*/scope/*/')->name('scope.json')->depth(0);
                case self::CLASS_PARTY:
                    return (new Finder())->files()->in($this->path . '/*/party/*/')->name('party.json')->depth(0);
                case self::CLASS_POLICY:
                    return (new Finder())->files()->in($this->path . '/*/scope/*/policy/*/')->name('policy.json')->depth(0);
                case self::CLASS_POLICY_CONTENT:
                    return (new Finder())->files()->in($this->path . '/*/scope/*/policy/*/')->name('content.md')->depth(0);
            };
        } catch (\InvalidArgumentException $exception) {
            return [];
        }

        throw new \BadMethodCallException("Class {$class} it's not registered in the reader of files during reading");
    }

    private function load($class, SplFileInfo $file)
    {
        switch ($class) {
            case self::CLASS_ELECTION:
                return $file->getContents();
            case self::CLASS_PARTY:
            case self::CLASS_SCOPE:
                return [
                    'edition' => $this->extractElectionEditionFromFilePath($file),
                    'json' => $file->getContents()
                ];
            case self::CLASS_POLICY:
                return [
                    'edition' => $this->extractElectionEditionFromFilePath($file),
                    'json' => $file->getContents(),
                    'content' => (new SplFileInfo(
                        $file->getPath() . 'content.md',
                        $file->getRelativePath(),
                        'content.md')
                    )->getContents()
                ];
        };

        throw new \BadMethodCallException("Class {$class} it's not registered in the reader of files during instantiation");
    }

    private function extractElectionEditionFromFilePath(SplFileInfo $file)
    {
        $output = [];
        preg_match('/\/(\d+)\//', $file->getPath(), $output);

        return $output[1];
    }

    public static function writeTestFiles(array $files)
    {
        $fs = new Filesystem();
        $path = sys_get_temp_dir() . '/tpe-tests';
        $fs->remove($path);

        foreach ($files as $filePath => $content) {
            $fs->dumpFile($path . '/' . $filePath, $content);
        }

        return $path;
    }
}
