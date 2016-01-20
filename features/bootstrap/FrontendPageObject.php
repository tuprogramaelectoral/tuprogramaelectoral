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

    public function visit($path)
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
                        'id' => $element->getAttribute('data-interest-id'),
                        'name' => $element->getText()
                    ];
                }
                break;
        }

        return $response;
    }

    public function visitScope($scope)
    {
        $this->session->wait(20000, "$('#panel-interest-{$scope} .policy-content:visible').length");
        $scopeNode = $this->session->getPage()->find('css', "#panel-interest-{$scope}");

        $response = [];
        foreach ($scopeNode->findAll('xpath', "//div[@data-scope='{$scope}']") as $policyNode) {
            /** @var NodeElement $policyNode */
            $response[] = [
                'id' => $policyNode->getAttribute('data-policy'),
                'content' => $policyNode->find('css', '.policy-content')->getHtml()
            ];
        }

        return ['policies' => $response];
    }

    public function selectInterests($interests)
    {
        foreach ($interests as $interest) {
            $this->session->getPage()
                ->find('xpath', "//input[@data-interest-id='{$interest}']")
                ->check();
        }

        $this->session->getPage()
            ->find('css', '#select-interests')
            ->click();

        $this->session->wait(20000, "$('.panel-interest:visible').length");

        $myProgramme = [
            'id' => $this->session->getCookie('myProgrammeId'),
            'interests' => [],
            'next_interest' => null
        ];

        foreach ($this->findAll('css', '.interest') as $interest) {
            if ($interest->isChecked()) {
                $myProgramme['interests'][] = $interest->getAttribute('data-interest-id');
            }
        }

        return $myProgramme;
    }

    public function selectLinkedPolicy($myProgrammeId, $scope, $policy)
    {
        $this->session->wait(20000, "$('#show-policy-{$policy}:visible').length");
        $this->session->getPage()->find('css', "#show-policy-{$policy}")->click();
        $this->session->wait(20000, "$('#select-policy-{$policy}:visible').length");
        $this->session->getPage()->find('css', "#select-policy-{$policy}")->click();

        return [
            'id' => $this->session->getCookie('myProgrammeId'),
            'interests' => [],
            'next_interest' => null
        ];
    }

    public function myProgrammeExists($myProgrammeId)
    {
        $path = $this->parameters['base_url'] . '/#/' . $myProgrammeId;

        $this->session->restart();
        $this->session->visit($path);

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
            $policies[$policyNode->getAttribute('data-scope')] = $policyNode->getAttribute('data-policy');
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
            'next_interest' => null,
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
