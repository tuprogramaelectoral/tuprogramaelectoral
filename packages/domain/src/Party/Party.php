<?php

namespace TPE\Domain\Party;

use Assert\Assertion;
use TPE\Domain\Data\InitialData;


class Party implements InitialData
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $acronym;

    /**
     * @var string
     */
    private $programmeUrl;

    /**
     * @var Policy[]
     */
    private $policies;


    public function __construct($name, $acronym, $programmeUrl = null)
    {
        \Assert\lazy()
            ->that($name, 'name')->string()->notEmpty()
            ->that($acronym, 'acronym')->string()->notEmpty()
            ->verifyNow();
        Assertion::nullOrUrl($programmeUrl, 'programmeUrl is not a valid URL');

        $this->id = \slugifier\slugify($name);
        $this->name = $name;
        $this->acronym = $acronym;
        $this->programmeUrl = $programmeUrl;
    }

    /**
     * @return Policy[]
     */
    public function getPolicies()
    {
        return $this->policies;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAcronym()
    {
        return $this->acronym;
    }

    /**
     * @return string
     */
    public function getProgrammeUrl()
    {
        return $this->programmeUrl;
    }

    /**
     * @param string $json
     * @return Party
     */
    public static function createFromJson($json)
    {
        $data = json_decode($json, true);

        if (null === $data) {
            throw new \BadMethodCallException('Detected malformed JSON while creating Party from ' . $json);
        }

        if (isset($data['name']) && isset($data['acronym'])) {
            return new Party(
                $data['name'],
                $data['acronym'],
                isset($data['programmeUrl']) ? $data['programmeUrl'] : null
            );
        }

        throw new \BadMethodCallException('Missing required attributes while creating Party from ' . $json);
    }
}
