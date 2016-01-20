<?php

namespace TPE\Domain\Data;


interface InitialDataRepository
{
    /**
     * @return InitialData[]
     */
    public function findAll();

    /**
     * @param string $id
     * @return InitialData|null
     */
    public function find($id);

    /**
     * @param InitialData $object
     * @param bool $flush
     */
    public function save(InitialData $object, $flush = true);

    /**
     * @return string
     */
    public function getClassName();
}
