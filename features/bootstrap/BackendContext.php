<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Filesystem\Filesystem;
use TPE\Domain\Data\InitialData;
use TPE\Domain\Field\Field;
use TPE\Domain\Party\Party;
use TPE\Domain\Party\Policy;
use TPE\Infrastructure\Data\Loader;
use TPE\Infrastructure\Data\ReaderOfFiles;


/**
 * Defines application features from the specific context.
 */
class BackendContext extends MinkContext implements SnippetAcceptingContext
{
    use \Behat\Symfony2Extension\Context\KernelDictionary;

    const FIELDS = "fields";
    const PARTIES = "parties";
    const POLICIES = "policies";
    const POLICY_CONTENT = "policy content";
    const MYPROGRAMME = "my programme";

    /**
     * @var string
     */
    private $filesPath;

    /**
     * @var array
     */
    private $current;

    /**
     * @var array
     */
    private $myProgramme;


    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given the repository files and content is:
     */
    public function theRepositoryFilesAndContentIs(TableNode $table)
    {
        $files = [];
        foreach ($table as $file) {
            $files[$file['path']] = $file['content'];
        }

        $this->filesPath = ReaderOfFiles::writeTestFiles($files);
    }

    /**
     * @Given the content of the files are loaded into the system
     */
    public function theContentOfTheFilesAreLoadedIntoTheSystem()
    {
        $this->getLoader()->regenerateScheme();
        $this->getLoader()->load(new ReaderOfFiles($this->filesPath));
    }

    /**
     * @When I see the list of available :dataType
     */
    public function iSeeTheListOfAvailableDataType($dataType)
    {
        $this->getSession()->setRequestHeader('Accept', 'application/json');
        $this->visit($dataType);

        $this->current[$dataType] = [];
        foreach ($this->getResponse() as $data) {
            $this->current[$dataType][$data['id']] = $data;
        }
    }

    /**
     * @When I see the list of policies linked to the field :field
     */
    public function iSeeTheListOfPoliciesLinkedToTheField($field)
    {
        $this->getSession()->setRequestHeader('Accept', 'application/json');
        $this->visit("fields/{$field}");

        $this->current[self::POLICIES] = [];
        foreach ($this->getResponse()['policies'] as $data) {
            $this->current[self::POLICIES][$data['id']] = $data;
        }
    }

    /**
     * @Then the list of :dataType contains:
     */
    public function theListOfDataTypeContains($dataType, TableNode $table)
    {
        $expected = [];
        foreach ($table as $data) {
            $expected[$data['id']] = $data;
        }

        PHPUnit_Framework_Assert::assertCount(count($expected), $this->current[$dataType]);
        foreach ($this->current[$dataType] as $current) {
            $this->compareCurrentWithExpected($dataType, $current, $expected[$current['id']]);
        }
    }

    private function compareCurrentWithExpected($dataType, array $current, array $expected)
    {
        switch ($dataType) {
            case self::FIELDS:
                /** @var Field $current */
                PHPUnit_Framework_Assert::assertEquals($expected['name'], $current["name"]);
                break;
            case self::PARTIES:
                /** @var Party $current */
                PHPUnit_Framework_Assert::assertEquals($expected['name'], $current["name"]);
                PHPUnit_Framework_Assert::assertEquals($expected['acronym'], $current["acronym"]);
                PHPUnit_Framework_Assert::assertEquals($expected['programmeUrl'], $current["programme_url"]);
                break;
            case self::POLICIES:
                /** @var Policy $current */
                PHPUnit_Framework_Assert::assertEquals(json_decode($expected['sources']), $current["sources"]);
                PHPUnit_Framework_Assert::assertEquals($expected['content'], $current["content"]);
                PHPUnit_Framework_Assert::assertEquals($expected['partyId'], $current["party"]["id"]);
                break;
            default:
                throw new \Exception("It doesn't exist a way to compare " . $dataType);
        }
    }

    /**
     * @When (that) I select these interests:
     */
    public function iSelectTheseInterests(TableNode $table)
    {
        $policies = [];
        foreach ($table->getColumn(0) as $interest) {
            $policies['policies'][$interest] = null;
        }

        $this->request('POST', 'myprogrammes', $policies);
        $this->myProgramme = $this->getResponse();
    }

    /**
     * @Then my programme contains the interests:
     */
    public function myProgrammeContainsTheInterests(TableNode $table)
    {
        $expected = array_flip($table->getColumn(0));
        foreach ($this->myProgramme['interests'] as $current) {
            PHPUnit_Framework_Assert::assertTrue(isset($expected[$current]));
        }
    }

    /**
     * @Then the next interest is :interest
     */
    public function theNextInterestIs($interest)
    {
        PHPUnit_Framework_Assert::assertEquals($interest, $this->myProgramme["next_interest"]);
    }

    /**
     * @Given there is no next interest
     */
    public function thereIsNoNextInterest()
    {
        PHPUnit_Framework_Assert::assertNotTrue(isset($this->myProgramme["next_interest"]));
    }

    /**
     * @Then the system shows an error
     */
    public function theSystemShowsAnError()
    {
        PHPUnit_Framework_Assert::assertEquals(400, $this->myProgramme['code']);
    }

    /**
     * @When I select the linked policy :policy
     */
    public function iSelectTheLinkedPolicy($policy)
    {
        $this->updateMyProgramme(
            $this->myProgramme['id'],
            ["policies" => [$this->myProgramme['next_interest'] => $policy]]
        );
    }

    private function updateMyProgramme($myProgrammeId, $changes)
    {
        $this->request("POST", "/myprogrammes/{$myProgrammeId}", $changes);

        $response = $this->getResponse();
        if (null == $response) {
            $this->visit("myprogrammes/{$myProgrammeId}");
            $response = $this->getResponse();
        }

        $this->myProgramme = $response;
    }

    /**
     * @When I set my programme as completed and privacy :privacy
     */
    public function iSetMyProgrammeAsCompletedAndPrivacy($privacy)
    {
        $this->updateMyProgramme(
            $this->myProgramme['id'],
            ["policies" => [], "completed" => true, 'public' => ($privacy == 'public') ? true : false]
        );
    }

    /**
     * @Then my programme contains these linked policies:
     */
    public function myProgrammeContainsTheseLinkedPolicies(TableNode $table)
    {
        $expected = [];
        foreach ($table as $data) {
            if (!empty($data['policy'])) {
                $expected[$data['field']] = $data['policy'];
            }
        }

        foreach ($this->myProgramme['policies'] as $field => $policy) {
            PHPUnit_Framework_Assert::assertTrue(isset($expected[$field]));
            PHPUnit_Framework_Assert::assertEquals($expected[$field], $policy);
        }
    }

    /**
     * @Then my programme is completed
     */
    public function myProgrammeIsCompleted()
    {
        PHPUnit_Framework_Assert::assertTrue($this->myProgramme['completed']);
    }

    /**
     * @Then my programme privacy is :privacy
     */
    public function myProgrammePrivacyIs($privacy)
    {
        if ('public' == $privacy) {
            PHPUnit_Framework_Assert::assertTrue($this->myProgramme['public']);
        } else {
            PHPUnit_Framework_Assert::assertFalse($this->myProgramme['public']);
        }
    }

    /**
     * @Then my programme party affinity is:
     */
    public function myProgrammePartyAffinityIs(TableNode $table)
    {
        $expected = [];
        foreach ($table as $data) {
            $expected[$data['party']] = $data['affinity'];
        }

        foreach ($this->myProgramme['party_affinity'] as $party => $affinity) {
            PHPUnit_Framework_Assert::assertTrue(isset($expected[$party]));
            PHPUnit_Framework_Assert::assertEquals($expected[$party], $affinity);
        }
    }

    /**
     * @When :timePassed passes from my last programme modification
     */
    public function passesFromMyLastProgrammeModification($timePassed)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var ClassMetadata $metadata */
        $metadata = $em->getClassMetadata(Loader::CLASS_MY_PROGRAMME);
        $em->getConnection()->createQueryBuilder()
            ->update($metadata->getTableName())
            ->where("{$metadata->getColumnName('id')} = :id")
            ->set($metadata->getColumnName('lastModification'), ':lastModification')
            ->setParameters([
                'id' => $this->myProgramme['id'],
                'lastModification' => date_format(new \DateTime('-' . $timePassed), 'Y-m-d H:i:s')
            ])
            ->execute();
    }

    /**
     * @Then my programme is still accessible
     */
    public function myProgrammeIsStillAccessible()
    {
        $this->visit("myprogrammes/{$this->myProgramme['id']}");
        $actual = $this->getResponse();

        PHPUnit_Framework_Assert::assertEquals($this->myProgramme['id'], $actual['id']);
    }

    /**
     * @Then my programme is not accessible
     */
    public function myProgrammeIsNotAccessible()
    {
        $this->visit("myprogrammes/{$this->myProgramme['id']}");
        $actual = $this->getResponse();

        PHPUnit_Framework_Assert::assertEquals(404, $actual['error']['code']);
    }

    /**
     * @return Loader
     */
    private function getLoader()
    {
        return $this->getContainer()->get('data_loader');
    }

    private function request($verb, $url, $valores)
    {
        $this
            ->getSession()
            ->getDriver()
            ->getClient()
            ->request(
                $verb,
                $url,
                $valores,
                [],
                ['HTTP_ACCEPT' => 'application/json']
            );
    }

    /**
     * @return array
     */
    private function getResponse()
    {
        return json_decode($this->getSession()->getPage()->getContent(), true);
    }
}
