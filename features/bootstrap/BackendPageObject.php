<?php

use Behat\Mink\Mink;


class BackendPageObject
{
    /**
     * @var Mink
     */
    private $mink;

    /**
     * @var array
     */
    private $parameters;


    public function __construct(Mink $mink, array $parameters = null)
    {
        $this->mink = $mink;
        $this->parameters = $parameters;
    }

    public function visit($path, $edition = null)
    {
        if ($edition !== null) {
            $path = 'elections/' . $edition . '/' . $path;
        }

        $session = $this->mink->getSession();

        $session->setRequestHeader('Accept', 'application/json');
        $session->visit($this->locatePath($path));

        return $this->getResponse();
    }

    public function visitScope($scope, $edition)
    {
        return $this->visit("scopes/{$scope}", $edition);
    }

    private function getResponse()
    {
        return json_decode($this->mink->getSession()->getPage()->getContent(), true);
    }

    public function selectInterests($edition, $interests)
    {
        $data = ['edition' => $edition];
        foreach ($interests as $interest) {
            $data['policies'][$interest] = null;
        }

        return $this->request('POST', '/myprogrammes', $data);
    }

    public function selectLinkedPolicy($myProgrammeId, $edition, $scope, $party)
    {
        return $this->updateMyProgramme(
            $myProgrammeId,
            ["policies" => [$scope => $party], "edition" => $edition]
        );
    }

    public function myProgrammeExists($myProgrammeId)
    {
        $response = $this->visit("/myprogrammes/{$myProgrammeId}");
        if (isset($response['error']['code']) && $response['error']['code'] == '404') {
            return false;
        }

        return true;
    }

    public function completeMyProgramme($myProgrammeId, $public)
    {
        return $this->updateMyProgramme(
            $myProgrammeId,
            ["policies" => [], "completed" => 'Yes', 'public' => ($public) ? 'Yes' : 'No']
        );
    }

    private function updateMyProgramme($myProgrammeId, $changes)
    {
        $response = $this->request("POST", "/myprogrammes/{$myProgrammeId}", $changes);
        if (null == $response) {
            $response = $this->visit("myprogrammes/{$myProgrammeId}");
        }

        return $response;
    }

    public function deleteMyProgramme($myProgrammeId)
    {
        $this->request("DELETE", "/myprogrammes/{$myProgrammeId}");
    }

    public function request($verb, $url, $data = [])
    {
        $this->mink
            ->getSession()
            ->getDriver()
            ->getClient()
            ->request(
                $verb,
                $url,
                $data,
                [],
                ['HTTP_ACCEPT' => 'application/json']
            );

        return $this->getResponse();
    }

    private function locatePath($path)
    {
        $startUrl = rtrim($this->parameters['base_url'], '/') . '/';

        return 0 !== strpos($path, 'http') ? $startUrl . ltrim($path, '/') : $path;
    }
}
