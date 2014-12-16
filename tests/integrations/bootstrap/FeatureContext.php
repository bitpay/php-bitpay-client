<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/StepHelper.php';
//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    private $params = array();

    protected $mink;

    public $pairingCode;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->params = $parameters;

        $isEmailConfigured    = $this->params['user'];
        $isPasswordConfigured = $this->params['password'];

        if (!$isEmailConfigured || !$isPasswordConfigured) {
            throw new Exception("Your email or password are not configured.");
            return;
        }

        $phantomjsDriver = new \Behat\Mink\Driver\Selenium2Driver('phantomJS', null, 'http://127.0.0.1:8643');
        $selenium2driver = new \Behat\Mink\Driver\Selenium2Driver('firefox');
        $this->mink      = new \Behat\Mink\Mink(
            array(
                'phantomjs' => new \Behat\Mink\Session($phantomjsDriver),
                'selenium2' => new \Behat\Mink\Session($selenium2driver),
            )
        );

        $this->mink->setDefaultSessionName($this->params['driver']);

        //$this->mink->setDefaultSessionName('phantomjs');
    }

    protected function tearDown()
    {
        $this->mink->getSession()->restart();
    }

    /**
     * @Given /^the user is authenticated with BitPay$/
     */
    public function theUserIsAuthenticatedWithBitpay()
    {
        throw new PendingException();
    }

    /**
     * @When /^the user creates an invoice for "([^"]*)" "([^"]*)"$/
     */
    public function theUserCreatesAnInvoiceFor($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then /^they should recieve an invoice in response for "([^"]*)" "([^"]*)"$/
     */
    public function theyShouldRecieveAnInvoiceInResponseFor($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Then /^they will receive a BitPay::ArgumentError matching "([^"]*)"$/
     */
    public function theyWillReceiveABitpayArgumenterrorMatching($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^the user pairs with BitPay with a valid pairing code$/
     */
    public function theUserPairsWithBitpayWithAValidPairingCode()
    {
        // Login
        $this->mink->getSession()->visit('https://alex.bp:8088/merchant-login');
        var_dump($this->mink->getSession()->getPage()->getHtml());
        $this->mink->getSession()->wait(1500);
        $this->mink->getSession()->getPage()->fillField('email', $this->params['user']);
        $this->mink->getSession()->getPage()->fillField('password', $this->params['password']);
        $this->mink->getSession()->getPage()->findById('loginButton')->click();
        $this->mink->getSession()->wait(1500);

        // Navigate to tokens
        $this->mink->getSession()->getPage()->clickLink('My Account');
        $this->mink->getSession()->wait(1500);
        $this->mink->getSession()->getPage()->clickLink('API Tokens');
        $this->mink->getSession()->wait(1500);

        // Create and set pairing code
        $cssSelector = ".icon-plus";
        $element = $this->mink->getSession()->getPage()->find(
            'xpath',
            $this->mink->getSession()->getSelectorsHandler()->selectorToXpath('css', $cssSelector) // just changed xpath to css
        );
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate CSS Selector: "%s"', $cssSelector));
        }
        $element->press();
        $this->mink->getSession()->wait(1500);
        $this->mink->getSession()->getPage()->pressButton("Add Token");
        $this->mink->getSession()->wait(5000);
        $this->pairingCode = $this->mink->getSession()->getPage()->find('xpath', '//*[@id="my-token-access-wrapper"]/div[1]/div[2]/div/div')->getText();
        $this->tearDown();
    }

    /**
     * @Then /^the user is paired with BitPay$/
     */
    public function theUserIsPairedWithBitpay()
    {
        throw new PendingException();
    }

    /**
     * @Given /^the user fails to pair with a semantically valid code "([^"]*)"$/
     */
    public function theUserFailsToPairWithASemanticallyValidCode($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Then /^they will receive a BitPay::BitPayError matching "([^"]*)"$/
     */
    public function theyWillReceiveABitpayBitpayerrorMatching($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^the user fails to pair with a semantically invalid code "([^"]*)"$/
     */
    public function theUserFailsToPairWithASemanticallyInvalidCode($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When /^the fails to pair with BitPay because of an incorrect port$/
     */
    public function theFailsToPairWithBitpayBecauseOfAnIncorrectPort()
    {
        throw new PendingException();
    }

    /**
     * @Then /^they will receive a BitPay::ConnectionError matching "([^"]*)"$/
     */
    public function theyWillReceiveABitpayConnectionerrorMatching($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given /^that a user knows an invoice id$/
     */
    public function thatAUserKnowsAnInvoiceId()
    {
        throw new PendingException();
    }

    /**
     * @Then /^they can retrieve that invoice$/
     */
    public function theyCanRetrieveThatInvoice()
    {
        throw new PendingException();
    }

}