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
class FeatureContext extends BehatContext
{
    private $params = array();

    protected $mink;

    public $validPairingCode;

    protected $error;

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
        $selenium2Driver = new \Behat\Mink\Driver\Selenium2Driver('firefox');
        $zombiejsDriver = new \Behat\Mink\Driver\ZombieDriver(
            new \Behat\Mink\Driver\NodeJS\Server\ZombieServer()
        );

        $this->mink      = new \Behat\Mink\Mink(
            array(
                'phantomjs' => new \Behat\Mink\Session($phantomjsDriver),
                'selenium2' => new \Behat\Mink\Session($selenium2Driver),
                'zombiejs'  => new \Behat\Mink\Session($zombiejsDriver),
            )
        );

        $this->mink->setDefaultSessionName($this->params['driver']);
    }

    protected function tearDown()
    {
        $this->mink->getSession()->reset();
        unload('/tmp/bitpay.pri');
        unload('/tmp/bitpay.pub');
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
     * @Given /^the user pairs with BitPay with a valid pairing code$/
     */
    public function theUserPairsWithBitpayWithAValidPairingCode()
    {
        // Login
        $this->mink->getSession()->visit('https://alex.bp:8088/merchant-login');

        $this->mink->getSession()->wait(1500);
        $this->mink->getSession()->getPage()->fillField('email', $this->params['user']);
        $this->mink->getSession()->getPage()->fillField('password', $this->params['password']);
        $this->mink->getSession()->getPage()->findById('loginButton')->click();

        // Navigate to tokens
        $this->mink->getSession()->wait(1500);
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
        $this->validPairingCode = $this->mink->getSession()->getPage()->find('xpath', '//*[@id="my-token-access-wrapper"]/div[1]/div[2]/div/div')->getText();
    }

    /**
     * @Then /^the user is paired with BitPay$/
     */
    public function theUserIsPairedWithBitpay()
    {
        // Create Keys
        $privateKey = new \Bitpay\PrivateKey('/tmp/bitpay.pri');
        $privateKey->generate();
        $publicKey = new \Bitpay\PublicKey('/tmp/bitpay.pub');
        $publicKey->setPrivateKey($privateKey);
        $publicKey->generate();
        $sinKey = new \Bitpay\SinKey('/tmp/sin.key');
        $sinKey->setPublicKey($publicKey);
        $sinKey->generate();

        //Persist Keys
        $storageEngine = new \Bitpay\Storage\EncryptedFilesystemStorage('YourTopSecretPassword');
        $storageEngine->persist($privateKey);
        $storageEngine->persist($publicKey);

        //Set Client
        $client = new \Bitpay\Client\Client();
        $network = new \Bitpay\Network\Customnet("alex.bp", 8088, true);
        $curl_options = array(
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    );
        $adapter = new \Bitpay\Client\Adapter\CurlAdapter($curl_options);
        $client->setPrivateKey($privateKey);
        $client->setPublicKey($publicKey);
        $client->setNetwork($network);
        $client->setAdapter($adapter);
        $pairingCode = $this->validPairingCode;

        // Pair
        try {
            $token = $client->createToken(
                array(
                    'pairingCode' => $pairingCode,
                    'label'       => 'Integrations Testing',
                    'id'          => (string) $sinKey,
                )
            );
        } catch (\Exception $e) {
            $request  = $client->getRequest();
            $response = $client->getResponse();
            echo (string) $request.PHP_EOL.PHP_EOL.PHP_EOL;
            echo (string) $response.PHP_EOL.PHP_EOL;
            exit(1);
        }

    }

    /**
     * @Given /^the user fails to pair with a semantically (?:in|)valid code "([^"]*)"$/
     */
    public function theUserFailsToPairWithASemanticallyValidCode($pairingCode)
    {
        try {
            // Stupid rate limiters
            $this->mink->getSession()->wait(1500);

            // Create Keys
            $privateKey = new \Bitpay\PrivateKey('/tmp/bitpay.pri');
            $privateKey->generate();
            $publicKey = new \Bitpay\PublicKey('/tmp/bitpay.pub');
            $publicKey->setPrivateKey($privateKey);
            $publicKey->generate();
            $sinKey = new \Bitpay\SinKey('/tmp/sin.key');
            $sinKey->setPublicKey($publicKey);
            $sinKey->generate();

            //Persist Keys
            $storageEngine = new \Bitpay\Storage\EncryptedFilesystemStorage('YourTopSecretPassword');
            $storageEngine->persist($privateKey);
            $storageEngine->persist($publicKey);

            //Set Client
            $client = new \Bitpay\Client\Client();
            $network = new \Bitpay\Network\Customnet("alex.bp", 8088, true);
            $curl_options = array(
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        );
            $adapter = new \Bitpay\Client\Adapter\CurlAdapter($curl_options);
            $client->setPrivateKey($privateKey);
            $client->setPublicKey($publicKey);
            $client->setNetwork($network);
            $client->setAdapter($adapter);

            // Pair
            $token = $client->createToken(
                array(
                    'pairingCode' => $pairingCode,
                    'label'       => 'Integrations Testing',
                    'id'          => (string) $sinKey,
                )
            );
        } catch (\Exception $e) {
            $this->error = $e;
        } finally {
            return true;
        }

    }
    /**
     * @Then /^they will receive a "([^"]*)" matching "([^"]*)"$/
     */
    public function theyWillReceiveAnErrorMatching($errorName, $errorMessage)
    {   
        var_dump(get_class($this->error));
        if ($this->error->getMessage() !== $errorMessage){
            throw new Exception("Error message incorrect", 1);
        }
        if (get_class($this->error) !== $errorName){
            throw new Exception("Error name incorrect", 1);
        }
        
    }

    /**
     * @When /^the fails to pair with BitPay because of an incorrect port$/
     */
    public function theFailsToPairWithBitpayBecauseOfAnIncorrectPort()
    {
        try {
            // Stupid rate limiters
            $this->mink->getSession()->wait(1500);

            // Create Keys
            $privateKey = new \Bitpay\PrivateKey('/tmp/bitpay.pri');
            $privateKey->generate();
            $publicKey = new \Bitpay\PublicKey('/tmp/bitpay.pub');
            $publicKey->setPrivateKey($privateKey);
            $publicKey->generate();
            $sinKey = new \Bitpay\SinKey('/tmp/sin.key');
            $sinKey->setPublicKey($publicKey);
            $sinKey->generate();

            //Persist Keys
            $storageEngine = new \Bitpay\Storage\EncryptedFilesystemStorage('YourTopSecretPassword');
            $storageEngine->persist($privateKey);
            $storageEngine->persist($publicKey);

            //Set Client
            $client = new \Bitpay\Client\Client();
            $network = new \Bitpay\Network\Customnet("alex.bp", 8974, true);
            $curl_options = array(
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        );
            $adapter = new \Bitpay\Client\Adapter\CurlAdapter($curl_options);
            $client->setPrivateKey($privateKey);
            $client->setPublicKey($publicKey);
            $client->setNetwork($network);
            $client->setAdapter($adapter);
            $pairingCode = $this->validPairingCode;

            // Pair
            $token = $client->createToken(
                array(
                    'pairingCode' => 'aaaaaaa',
                    'label'       => 'Integrations Testing',
                    'id'          => (string) $sinKey,
                )
            );

        } catch (\Exception $e) {
            $this->error = $e;
        } finally {
            return true;
        }
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