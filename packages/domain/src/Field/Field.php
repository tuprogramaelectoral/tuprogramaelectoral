<?php

namespace TPE\Domain\Field;

use Assert\Assertion;
use TPE\Domain\Data\InitialData;
use TPE\Domain\Party\Policy;


/**
 * Ámbito sobre el que actuan una serie de políticas
 *
 * Class Ambito
 * @package TPE\Ambito
 */
class Field implements InitialData
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
     * @var Politica[]
     */
    private $policies = [];


    /**
     * @param string $name
     * @param Politica[] $policies
     */
    public function __construct($name, $policies = null)
    {
        \Assert\that($name)->string()->notEmpty();

        $this->id = \slugifier\slugify($name);
        $this->name = $name;
        $this->policies = is_array($policies) ? $policies : [];
    }

    /**
     * @param string $json
     * @param array $politicas
     * @return Field
     */
    public static function createFromJson($json, array $politicas = null)
    {
        $data = json_decode($json, true);

        if (null === $data) {
            throw new \BadMethodCallException('Detected malformed JSON while creating Field from ' . $json);
        }

        if (isset($data['name'])) {
            return new Field($data['name'], $politicas);
        }

        throw new \BadMethodCallException('Missing required attributes while creating Field from ' . $json);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Politica[]
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
}
