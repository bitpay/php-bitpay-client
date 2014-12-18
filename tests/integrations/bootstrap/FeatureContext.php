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

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    private $params = array();

    protected $mink;

    public $validPairingCode;

    protected $error;

    public $reponse;

    protected $InvoiceId;

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

    /**
     * @Then /^clean up$/
     */
    public function deconstruct()
    {
        unlink('/tmp/token.json');
        unlink('/tmp/bitpay.pri');
        unlink('/tmp/bitpay.pub');
    }

    /**
     * @Given /^the user is authenticated with BitPay$/
     */
    public function theUserIsAuthenticatedWithBitpay()
    {
        if(true == !file_exists('/tmp/token.json') || true == !file_exists('/tmp/bitpay.pri') || true == !file_exists('/tmp/bitpay.pub')){
            $this->theUserPairsWithBitpayWithAValidPairingCode();
            $this->theUserIsPairedWithBitpay();
        }
    }

    /**
     * @When /^the user creates an invoice for "([^"]*)" "([^"]*)"$/
     */
    public function theUserCreatesAnInvoiceFor($price, $currency)
    {
        try {
            $storageEngine = new \Bitpay\Storage\EncryptedFilesystemStorage('YourTopSecretPassword'); // Password may need to be updated if you changed it
            $privateKey    = $storageEngine->load('/tmp/bitpay.pri');
            $publicKey     = $storageEngine->load('/tmp/bitpay.pub');
            $token_id      = file_get_contents('/tmp/token.json');

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

            $token = new \Bitpay\Token();
            $token->setToken($token_id);
            $client->setToken($token);

            $invoice = new \Bitpay\Invoice();

            $item = new \Bitpay\Item();
            $item
                ->setCode('skuNumber')
                ->setDescription('General Description of Item')
                ->setPrice($price);
            $invoice->setItem($item);

            $invoice->setCurrency(new \Bitpay\Currency($currency));

            $client->createInvoice($invoice);
            $this->response = $client->getResponse();
            $body = $this->response->getBody();
            $json = json_decode($body, true);
            $this->InvoiceId = $json['data']['id'];
        } catch (\Exception $e) {
            $this->error = $e;
        } finally {
            return true;
        }
    }

    /**
     * @Then /^they should recieve an invoice in response for "([^"]*)" "([^"]*)"$/
     */
    public function theyShouldRecieveAnInvoiceInResponseFor($price, $currency)
    {
        $body = $this->response->getBody();
        $json = json_decode($body, true);
        $responsePrice = (string) $json['data']['price'];
        $responseCurrency = $json['data']['currency'];
        if ($responsePrice !== $price){
            throw new Exception("Error: Price is different", 1);
        }
        if ($responseCurrency !== $currency){
            throw new Exception("Error: Currency is different", 1);
        }    
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
        list($privateKey, $publicKey, $sinKey) = generateAndPersistKeys();

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
            $token_file = fopen('/tmp/token.json', 'w');
            fwrite($token_file, $token);
            fclose($token_file);
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
            list($privateKey, $publicKey, $sinKey) = generateAndPersistKeys();

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
        if ($this->error->getMessage() !== $errorMessage){
            throw new Exception("Error message incorrect", 1);
        }
        if (get_class($this->error) !== $errorName){
            throw new Exception("Error name incorrect", 1);
        }
    }

    /**
     * @Then /^they will receive a "([^"]*)" matching \'([^\']*)\'$/
     */
    public function theyWillReceiveAnErrorMatching2($errorName, $errorMessage)
    {
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
            list($privateKey, $publicKey, $sinKey) = generateAndPersistKeys();

            //Set Client
            $client = new \Bitpay\Client\Client();
            $network = new \Bitpay\Network\Customnet("alex.bp", 8974, true);
            $curl_options = array(
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_TIMEOUT        => 1,
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
        if(true == !file_exists('/tmp/token.json') || true == !file_exists('/tmp/bitpay.pri') || true == !file_exists('/tmp/bitpay.pub')){
            $this->theUserPairsWithBitpayWithAValidPairingCode();
            $this->theUserIsPairedWithBitpay();
        }
        $this->theUserCreatesAnInvoiceFor(1.99, 'USD');
    }

    /**
     * @Then /^they can retrieve that invoice$/
     */
    public function theyCanRetrieveThatInvoice()
    {
        try
        {
            $client = new \Bitpay\Client\Client();
            $network = new \Bitpay\Network\Customnet("alex.bp", 8088, true);
            $curl_options = array(
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        );
            $adapter = new \Bitpay\Client\Adapter\CurlAdapter($curl_options);
            $client->setNetwork($network);
            $client->setAdapter($adapter);
            $response  = $client->getInvoice($this->InvoiceId);
        } catch (Exception $e){
            var_dump($e->getMessage());
        }
        $responseInvoiceId = $response->getId();
        if($responseInvoiceId !== $this->InvoiceId){
            throw new Exception("Invoice ids don't match");
        } 
    }

}