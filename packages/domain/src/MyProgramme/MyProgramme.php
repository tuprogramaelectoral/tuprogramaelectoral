<?php

namespace TPE\Domain\MyProgramme;

use Ramsey\Uuid\Uuid;
use TPE\Domain\Data\InitialData;


class MyProgramme implements InitialData
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string[]
     */
    private $policies = [];

    /**
     * @var bool
     */
    private $sorted = false;

    /**
     * @var bool
     */
    private $public;

    /**
     * @var bool
     */
    private $completed;

    /**
     * @var \DateTime
     */
    private $lastModification;


    public function __construct(array $policies, $public = false, $completed = false)
    {
        \Assert\that($policies)->isArray();
        \Assert\that($public)->boolean();

        $this->id = Uuid::uuid4()->toString();
        $this->public = $public;
        $this->completed = $completed;
        foreach ($policies as $interest => $policy) {
            $this->selectPolicy($interest, $policy);
        }
        $this->sortPolicies();
        $this->updateLastModification();
    }

    private function sortPolicies()
    {
        if (!$this->sorted) {
            ksort($this->policies);
            $this->sorted = true;
        }
    }

    private function updateLastModification()
    {
        $this->lastModification = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getInterests()
    {
        $this->sortPolicies();
        return array_keys($this->policies);
    }

    /**
     * @return string[]
     */
    public function getpolicies()
    {
        $this->sortPolicies();
        return $this->policies;
    }

    /**
     * @param string $interest
     * @return null|string
     */
    public function getPolicy($interest)
    {
        return (isset($this->policies[$interest])) ? $this->policies[$interest] : null;
    }

    /**
     * @return int[]
     */
    public function getPartyAffinity()
    {
        $affinity = [];
        foreach ($this->policies as $policy) {
            if (null !== $policy) {
                $party = explode('_', $policy, 2)[0];
                $affinity[$party] = (isset($affinity[$party])) ? $affinity[$party] + 1 : 1;
            }
        }

        return $affinity;
    }

    /**
     * @param string $interest
     * @param string $policy
     */
    public function selectPolicy($interest, $policy) {
        $this->updateLastModification();
        $this->policies[$interest] = $policy;
        $this->sorted = false;
    }

    /**
     * @return null|string
     */
    public function nextInterest()
    {
        $this->sortPolicies();
        foreach ($this->policies as $scope => $policy) {
            if (empty($policy)) {
                return $scope;
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * @param bool $public
     */
    public function setPublic($public)
    {
        $this->updateLastModification();
        $this->public = $public;
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->completed;
    }

    /**
     * @param bool $completed
     */
    public function setCompleted($completed)
    {
        $this->updateLastModification();
        $this->completed = $completed;
    }
}
