<?php

namespace TPE\Domain\Election;

use TPE\Domain\Data\InitialData;

class Election implements InitialData
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $date;


    public function __construct($edition, $date)
    {
        $this->id = (int) $edition;
        $this->date = new \DateTime($date);
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getEdition()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $json
     * @return Election
     */
    public static function createFromJson($json)
    {
        $data = json_decode($json, true);

        if (null === $data) {
            throw new \BadMethodCallException('Detected malformed JSON while creating Election from ' . $json);
        }

        if (isset($data['edition']) && isset($data['date'])) {
            return new Election($data['edition'], $data['date']);
        }

        throw new \BadMethodCallException('Missing required attributes while creating Party from ' . $json);
    }
}
