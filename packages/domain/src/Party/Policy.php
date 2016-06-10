<?php

namespace TPE\Domain\Party;

use Parsedown;
use TPE\Domain\Scope\Scope;
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
     * @var Scope
     */
    private $scope;

    /**
     * @var array
     */
    private $sources;

    /**
     * @var string
     */
    private $content;


    public function __construct(Party $party, Scope $scope, array $sources, $content)
    {
        \Assert\lazy()
            ->that($sources, 'sources')->isArray()
            ->that($content, 'content')->string()->notEmpty()
            ->verifyNow();

        $this->id = $party->getElection()->getId() . '_' . $party->getParty() . '_' . $scope->getScope();
        $this->party = $party;
        $this->scope = $scope;
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
        return $this->party->getParty();
    }

    public function getPartyName()
    {
        return $this->party->getName();
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function getScopeId()
    {
        return $this->scope->getScope();
    }

    public function getScopeName()
    {
        return $this->scope->getName();
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
