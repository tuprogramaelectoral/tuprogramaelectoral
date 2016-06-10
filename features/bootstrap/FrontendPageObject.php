<?php

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Mink;
use Behat\Mink\Session;


class FrontendPageObject
{
    /**
     * @var Mink
     */
    private $mink;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var Session
     */
    private $session;


    public function __construct(Mink $mink, array $parameters = null)
    {
        $this->mink = $mink;
        $this->parameters = $parameters;
        $this->session = $mink->getSession();
    }

    public function visit($path, $edition)
    {
        $response = null;

        switch ($path) {
            case 'scopes':
                $this->session->visit($this->parameters['base_url']);
                $this->session->wait(20000, "$('.interest:visible').length");

                /** @var NodeElement[] $elements */
                $elements = $this->session->getPage()->findAll('css', '.interest');

                foreach ($elements as $element) {
                    $response[] = [
                        'scope' => $element->getAttribute('data-interest-id'),
                        'name' => $element->getText()
                    ];
                }
                break;
        }

        return $response;
    }

    public function visitScope($scope, $edition)
    {
        $this->session->wait(20000, "$('#panel-interest-{$scope} .policy-content:visible').length");
        $scopeNode = $this->session->getPage()->find('css', "#panel-interest-{$scope}");

        $response = [];
        foreach ($scopeNode->findAll('xpath', "//div[@data-scope='{$scope}']") as $policyNode) {
            /** @var NodeElement $policyNode */
            $response[] = [
                'scope_id' => $policyNode->getAttribute('data-scope'),
                'party_id' => $policyNode->getAttribute('data-party'),
                'content' => $policyNode->find('css', '.policy-content')->getHtml()
            ];
        }

        return ['policies' => $response];
    }

    public function selectInterests($edition, $interests)
    {
        $page = $this->session->getPage();
        foreach ($interests as $interest) {
            $page
                ->find('xpath', "//input[@data-interest-id='{$interest}']")
                ->check();
        }

        $page
            ->find('css', '#select-interests')
            ->click();

        $this->session->wait(20000, "$('.panel-interest:visible').length");

        $myProgramme = [
            'id' => $this->session->getCookie('myProgrammeId'),
            'edition' => $page->find('css', "#sections")->getAttribute('data-my-programme-edition'),
            'next_interest' => $page->find('css', "#sections")->getAttribute('data-my-programme-next-interest'),
            'interests' => []
        ];

        foreach ($this->findAll('css', '.interest') as $interest) {
            if ($interest->isChecked()) {
                $myProgramme['interests'][] = $interest->getAttribute('data-interest-id');
            }
        }

        return $myProgramme;
    }

    public function selectLinkedPolicy($myProgrammeId, $edition, $scope, $party)
    {
        $page = $this->session->getPage();
        $this->session->wait(20000, "$('#show-policy-{$scope}-{$party}:visible').length");
        $page->find('css', "#show-policy-{$scope}-{$party}")->click();
        $this->session->wait(20000, "$('#select-policy-{$scope}-{$party}:visible').length");
        $page->find('css', "#select-policy-{$scope}-{$party}")->click();
        $this->session->wait(10000);

        $nextInterest = $page->find('css', "#sections")->getAttribute('data-my-programme-next-interest');

        return [
            'id' => $this->session->getCookie('myProgrammeId'),
            'edition' => $page->find('css', "#sections")->getAttribute('data-my-programme-edition'),
            'next_interest' => (empty($nextInterest)) ? null : $nextInterest,
            'interests' => []
        ];
    }

    public function myProgrammeExists($myProgrammeId)
    {
        $path = $this->parameters['base_url'] . '/#/' . $myProgrammeId;

        $this->session->restart();
        $this->session->visit($path);
        $this->session->wait(10000);

        return $path === $this->session->getCurrentUrl();
    }

    public function completeMyProgramme($myProgrammeId, $public)
    {
        $page = $this->session->getPage();
        $this->session->wait(20000, "$('#select-public-privacy:visible').length");
        if ($public) {
            $page->find('css', '#select-public-privacy')->click();
        } else {
            $page->find('css', '#select-private-privacy')->click();
        }

        $this->session->wait(20000, "$('#panel-results:visible').length");

        return $this->getMyProgrammeFromPage();
    }

    private function getMyProgrammeFromPage()
    {
        $page = $this->session->getPage();

        $policies = [];
        foreach ($page->findAll('css', '.panel-completed-programme-policy') as $policyNode) {
            /** @var NodeElement $policyNode */
            $policies[$policyNode->getAttribute('data-scope')] = $policyNode->getAttribute('data-party');
        }

        $isPublic = null;
        if ($page->find('css', '#programme-privacy-private-options')) {
            $isPublic = false;
        } elseif ($page->find('css', '#programme-privacy-public-options')) {
            $isPublic = true;
        }

        $this->session->wait(20000, "$('#graphic:visible').length");
        $graphicData = $this->session->evaluateScript("$('#graphic').scope()['graphic']['options']['data']['content'];");

        return [
            'id' => $this->session->getCookie('myProgrammeId'),
            'interests' => [],
            'edition' => $page->find('css', "#sections")->getAttribute('data-my-programme-edition'),
            'next_interest' => $page->find('css', "#sections")->getAttribute('data-my-programme-next-interest'),
            'policies' => $policies,
            'party_affinity' => array_column($graphicData, 'value', 'label'),
            'completed' => $page->find('css', '#panel-results')->isVisible(),
            'public' => $isPublic
        ];
    }

    public function deleteMyProgramme($myProgrammeId)
    {
        $this->session->getPage()->find('css', '#delete-my-programme')->click();
    }

    /**
     * @param string $selector
     * @param string $locator
     * @return NodeElement[]
     */
    private function findAll($selector, $locator)
    {
        return $this->session->getPage()->findAll($selector, $locator);
    }

    private function makeScreenshot()
    {
        file_put_contents('/var/www/screenshot.png', $this->session->getScreenshot());
    }
}
