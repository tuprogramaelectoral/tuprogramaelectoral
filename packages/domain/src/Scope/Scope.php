<?php

namespace TPE\Domain\Scope;

use Assert\Assertion;
use TPE\Domain\Data\InitialData;
use TPE\Domain\Election\Election;
use TPE\Domain\Party\Policy;


/**
 * Scope where policies are linked to
 *
 * Class Scope
 * @package TPE\Scope
 */
class Scope implements InitialData
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $scope;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Policy[]
     */
    private $policies = [];

    /**
     * @var Election
     */
    private $election;


    /**
     * @param Election $election
     * @param string $name
     * @param Policy[] $policies
     */
    public function __construct(Election $election, $name, $policies = null)
    {
        \Assert\that($name)->string()->notEmpty();

        $this->name = $name;
        $this->scope = \slugifier\slugify($name);
        $this->id = $election->getId() . '_' . $this->scope;
        $this->policies = is_array($policies) ? $policies : [];
        $this->election = $election;
    }

    /**
     * @param Election $election
     * @param string $json
     * @param array $policies
     * @return Scope
     */
    public static function createFromJson(Election $election, $json, array $policies = null)
    {
        $data = json_decode($json, true);

        if (null === $data) {
            throw new \BadMethodCallException('Detected malformed JSON while creating Scope from ' . $json);
        }

        if (isset($data['name'])) {
            return new Scope($election, $data['name'], $policies);
        }

        throw new \BadMethodCallException('Missing required attributes while creating Scope from ' . $json);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return Election
     */
    public function getElection()
    {
        return $this->election;
    }
}
