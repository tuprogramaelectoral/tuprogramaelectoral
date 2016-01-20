<?php

namespace TPE\Infrastructure\Data;

use Doctrine\DBAL\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use TPE\Domain\Field\Field;
use TPE\Domain\Data\InitialData;
use TPE\Domain\Data\Reader;
use TPE\Domain\Party\Party;
use TPE\Domain\Party\Policy;


class ReaderOfFiles implements Reader
{
    const CLASS_FIELD = 'TPE\Domain\Field\Field';
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
     * @return InitialData[]
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
                case self::CLASS_FIELD:
                    return (new Finder())->files()->in($this->path . '/field/*/')->name('field.json')->depth(0);
                case self::CLASS_PARTY:
                    return (new Finder())->files()->in($this->path . '/party/*/')->name('party.json')->depth(0);
                case self::CLASS_POLICY:
                    return (new Finder())->files()->in($this->path . '/field/*/policy/*/')->name('policy.json')->depth(0);
                case self::CLASS_POLICY_CONTENT:
                    return (new Finder())->files()->in($this->path . '/field/*/policy/*/')->name('content.md')->depth(0);
            };
        } catch (\InvalidArgumentException $exception) {
            return [];
        }

        throw new \BadMethodCallException("Class {$class} it's not registered in the reader of files during reading");
    }

    private function load($class, SplFileInfo $file)
    {
        switch ($class) {
            case self::CLASS_FIELD:
                return $file->getContents();
            case self::CLASS_PARTY:
                return $file->getContents();
            case self::CLASS_POLICY:
                return [
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
