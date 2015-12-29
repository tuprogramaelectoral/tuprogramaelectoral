<?php

namespace TPE\Domain\Party;

use Parsedown;
use TPE\Domain\Field\Field;
use TPE\Domain\Data\InitialData;


class Policy implements InitialData
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Party
     */
    private $party;

    /**
     * @var Field
     */
    private $field;

    /**
     * @var array
     */
    private $sources;

    /**
     * @var string
     */
    private $content;


    public function __construct(Party $party, Field $field, array $sources, $content)
    {
        \Assert\lazy()
            ->that($sources, 'fuentes')->isArray()->notEmpty()
            ->that($content, 'contenido')->string()->notEmpty()
            ->verifyNow();

        $this->id = $party->getId() . '_' . $field->getId();
        $this->party = $party;
        $this->field = $field;
        $this->sources = $sources;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function getParty()
    {
        return $this->party;
    }

    public function getPartyId()
    {
        return $this->party->getId();
    }

    public function getField()
    {
        return $this->field;
    }

    public function getFieldId()
    {
        return $this->field->getId();
    }

    public function getSources()
    {
        return $this->sources;
    }

    public function getContentInMarkdown()
    {
        return $this->content;
    }

    public function getContentInHtml()
    {
        return (new Parsedown())->text($this->content);
    }
}
