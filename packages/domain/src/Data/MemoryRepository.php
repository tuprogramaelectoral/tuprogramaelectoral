<?php

namespace TPE\Domain\Data;


abstract class MemoryRepository implements InitialDataRepository
{
    /* @var InitialData[] */
    private $data = [];


    /**
     * @param InitialData[] $data
     */
    public function __construct(array $data = null)
    {
        if (null !== $data) {
            foreach ($data as $object) {
                $this->save($object);
            }
        }
    }

    public function findAll()
    {
        return array_values($this->data);
    }

    public function find($id)
    {
        return isset($this->data[$id]) ? $this->data[$id] : null;
    }

    public function save(InitialData $object, $flush = true)
    {
        $this->data[$object->getId()] = $object;
    }
}
