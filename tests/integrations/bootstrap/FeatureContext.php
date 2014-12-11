<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;

require __DIR__ . '/../../../vendor/autoload.php';
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
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->params = $parameters;
    }
    /**
     * @Then /^I wait for the suggestion box to appear$/
     */
    public function iWaitForTheSuggestionBoxToAppear()
    {
        $this->getSession()->wait(5000,
            "$('.suggestions-results').children().length > 0"
        );
    }

    /**
     * @Given /^that there is no token saved locally$/
     */
    public function thatThereIsNoTokenSavedLocally()
    {
        $token_path = $this->params['features_path'].'token.txt';
        if (file_exists($token_path))
            unlink($token_path);
    }

    /**
     * @Given /^that there is a local keyfile$/
     */
    public function thatThereIsALocalKeyfile()
    {
        $token_path = $this->params['features_path'].'pri.pem';
    }

    /**
     * @When /^the user pairs with BitPay with a valid pairing code$/
     */
    public function theUserPairsWithBitpayWithAValidPairingCode()
    {
        throw new PendingException();
    }

    /**
     * @Given /^tokens will be saved locally$/
     */
    public function tokensWillBeSavedLocally()
    {
        throw new PendingException();
    }

    /**
     * @Given /^that there is no local keyfile$/
     */
    public function thatThereIsNoLocalKeyfile()
    {
        $key_path = $this->params['features_path'].'pri.pem';
        if (file_exists($key_path))
            unlink($key_path);
    }

    /**
     * @Then /^the keyfile will be saved locally$/
     */
    public function theKeyfileWillBeSavedLocally()
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
     * @Then /^they will receive a BitPay::ArgumentError matching "([^"]*)"$/
     */
    public function theyWillReceiveABitpayArgumenterrorMatching($arg1)
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


}