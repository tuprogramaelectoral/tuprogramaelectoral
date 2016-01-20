<?php

namespace TPE\Domain\Data;


interface Reader
{
    /**
     * @return InitialData[]
     */
    public function read($classname);
}
