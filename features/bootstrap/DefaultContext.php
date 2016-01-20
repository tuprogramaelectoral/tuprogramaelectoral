<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Testwork\Hook\Scope\SuiteScope;
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
class DefaultContext extends MinkContext implements SnippetAcceptingContext
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
     * @var BackendPageObject
     */
    private $pageObject;

    /**
     * @var string
     */
    private $environment;


    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     * @param $environment
     */
    public function __construct($environment)
    {
        $this->environment = $environment;
    }

    /** @BeforeSuite */
    public static function setFrontendToUseTestConfiguration(BeforeSuiteScope $scope)
    {
        if ('frontend' == self::getEnvironment($scope)) {
            (new Filesystem())->copy(
                'apps/frontend/app/scripts/config.test.js',
                'apps/frontend/app/scripts/config.js',
                true
            );
        }
    }

    /** @AfterSuite */
    public static function setFrontendToUseDevConfiguration(AfterSuiteScope $scope)
    {
        if ('frontend' == self::getEnvironment($scope)) {
            (new Filesystem())->copy(
                'apps/frontend/app/scripts/config.dev.js',
                'apps/frontend/app/scripts/config.js',
                true
            );
        }
    }

    private static function getEnvironment(SuiteScope $scope)
    {
        return $scope->getSuite()->getSetting('contexts')[0]['DefaultContext'][0];
    }

    /** @BeforeScenario */
    public function loadPageObject()
    {
        switch ($this->environment) {
            case 'backend':
                $this->pageObject = new BackendPageObject($this->getMink(), $this->getMinkParameters());
                break;
            case 'frontend':
                $this->pageObject = new FrontendPageObject($this->getMink(), $this->getMinkParameters());
                break;
            default:
                throw new \Exception('Undefined environment');
        }
    }

    /**
     * @Given the repository files and content is:
     */
    public function theRepositoryFilesAndContentIs(TableNode $table)
    {
        $this->filesPath = ReaderOfFiles::writeTestFiles(array_column($table->getHash(), 'content', 'path'));
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
        $response = $this->pageObject->visit($dataType);

        $this->current[$dataType] = [];
        foreach ($response as $data) {
            $this->current[$dataType][$data['id']] = $data;
        }
    }

    /**
     * @When I see these policies linked to the field :field
     */
    public function iSeeTheListOfPoliciesLinkedToTheField($field, TableNode $table)
    {
        $response = $this->pageObject->visitField($field);

        $this->current[self::POLICIES] = [];
        foreach ($response['policies'] as $data) {
            $this->current[self::POLICIES][$data['id']] = $data;
        }

        foreach ($table as $policy) {
            PHPUnit_Framework_Assert::assertTrue(isset($this->current[self::POLICIES][$policy['id']]));
            PHPUnit_Framework_Assert::assertEquals(
                $this->current[self::POLICIES][$policy['id']]['content'],
                (new Parsedown())->text($policy['content'])
            );
        }
    }

    /**
     * @Then the list of :dataType contains:
     */
    public function theListOfDataTypeContains($dataType, TableNode $table)
    {
        $expected = $this->transformTableIntoArrayIndexedBy('id', $table);

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
        $this->myProgramme = $this->pageObject->selectInterests($table->getColumn(0));
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
        $this->myProgramme = $this->pageObject->selectLinkedPolicy(
            $this->myProgramme['id'],
            $this->myProgramme['next_interest'],
            $policy
        );
    }

    /**
     * @When I set my programme as completed and privacy :privacy
     */
    public function iSetMyProgrammeAsCompletedAndPrivacy($privacy)
    {
        $this->myProgramme = $this->pageObject->completeMyProgramme(
            $this->myProgramme['id'],
            ($privacy == 'public') ? true : false
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
        $expected = $this->transformTableIntoArrayIndexedBy('party', $table);

        foreach ($this->myProgramme['party_affinity'] as $party => $affinity) {
            PHPUnit_Framework_Assert::assertTrue(isset($expected[$party]));
            PHPUnit_Framework_Assert::assertEquals($expected[$party]['affinity'], $affinity);
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
        $actual = $this->pageObject->visit("myprogrammes/{$this->myProgramme['id']}");

        PHPUnit_Framework_Assert::assertEquals($this->myProgramme['id'], $actual['id']);
    }

    /**
     * @Then my programme is not accessible
     */
    public function myProgrammeIsNotAccessible()
    {
        $actual = $this->pageObject->visitMyProgramme($this->myProgramme['id']);

        PHPUnit_Framework_Assert::assertNull($actual);
    }


    /**
     * @When I delete my programme
     */
    public function iDeleteMyProgramme()
    {
        $this->pageObject->deleteMyProgramme($this->myProgramme['id']);
    }

    /**
     * @return Loader
     */
    private function getLoader()
    {
        return $this->getContainer()->get('data_loader');
    }

    private function transformTableIntoArrayIndexedBy($index, TableNode $table)
    {
        $array = [];
        foreach ($table as $data) {
            $array[$data[$index]] = $data;
        }

        return $array;
    }
}
