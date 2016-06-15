<?php

namespace TPE\Domain\Party;

use Assert\Assertion;
use TPE\Domain\Data\InitialData;
use TPE\Domain\Election\Election;


class Party implements InitialData
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $party;

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

    /**
     * @var Election
     */
    private $election;


    public function __construct(Election $election, $name, $acronym, $programmeUrl = null)
    {
        \Assert\lazy()
            ->that($name, 'name')->string()->notEmpty()
            ->that($acronym, 'acronym')->string()->notEmpty()
            ->verifyNow();
        Assertion::nullOrUrl($programmeUrl, 'programmeUrl is not a valid URL');

        $this->name = $name;
        $this->party = \slugifier\slugify($name);
        $this->id = $election->getId() . '_' . $this->party;
        $this->acronym = $acronym;
        $this->programmeUrl = $programmeUrl;
        $this->election = $election;
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
    public function getParty()
    {
        return $this->party;
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
     * @return Election
     */
    public function getElection()
    {
        return $this->election;
    }

    /**
     * @param Election $election
     * @param string $json
     * @return Party
     */
    public static function createFromJson(Election $election, $json)
    {
        $data = json_decode($json, true);

        if (null === $data) {
            throw new \BadMethodCallException('Detected malformed JSON while creating Party from ' . $json);
        }

        if (isset($data['name']) && isset($data['acronym'])) {
            return new Party(
                $election,
                $data['name'],
                $data['acronym'],
                isset($data['programmeUrl']) ? $data['programmeUrl'] : null
            );
        }

        throw new \BadMethodCallException('Missing required attributes while creating Party from ' . $json);
    }
}
