<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\Driver\Selenium2Driver;


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

    protected $invoiceId;

    protected $user;

    protected $password;

    protected $base_url;

    protected $port;

    protected $port_required_in_url;

    protected $network;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->params = $parameters;

        if (null == getenv('BITPAY_EMAIL')) {
            $this->email = $this->params['user'];
        } else {
            $this->email = getenv('BITPAY_EMAIL');
        }
        if (null == getenv('BITPAY_PASSWORD')) {
            $this->password = $this->params['password'];
        } else {
            $this->password = getenv('BITPAY_PASSWORD');
        }

        if (null == getenv('BITPAY_URL')) {
            $this->base_url = $this->params['base_url'];
        } else {
            $this->base_url = getenv('BITPAY_URL');
        }

        $port = parse_url($this->base_url, PHP_URL_PORT);
        if (true == is_null(parse_url($this->base_url, PHP_URL_PORT))) {
            $this->port = 443;
            $this->port_required_in_url = false;
        } else {
            $this->port = parse_url($this->base_url, PHP_URL_PORT);
            $this->port_required_in_url = true;
        }

        if (parse_url($this->base_url, PHP_URL_HOST) == 'test.bitpay.com') {
            $this->network = new \Bitpay\Network\Testnet();
        } else {
            $url = parse_url($this->base_url, PHP_URL_HOST);
            $this->network = new \Bitpay\Network\Customnet($url, $this->port, $this->port_required_in_url);
        }

        if ((null == $this->email) || (null == $this->password)) {
            throw new Exception("Your email or password are not configured.");
            return;
        }

        if (null == $this->base_url) {
            throw new Exception("Your url is not configured.");
            return;
        }

        $phantomjsDriver = new \Behat\Mink\Driver\Selenium2Driver('phantomJS', null, 'http://127.0.0.1:8643');
        $selenium2Driver = new \Behat\Mink\Driver\Selenium2Driver('firefox');

        $this->mink      = new \Behat\Mink\Mink(
            array(
                'phantomjs' => new \Behat\Mink\Session($phantomjsDriver),
                'selenium2' => new \Behat\Mink\Session($selenium2Driver),
            )
        );

        $this->mink->setDefaultSessionName($this->params['driver']);
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
            // Load keys
            list($privateKey, $publicKey, $token_id) = loadKeys();

            $network = $this->network;
            $client = createClient($network, $privateKey, $publicKey);

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
            $this->invoiceId = $json['data']['id'];
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

        $this->mink->getSession()->visit($this->base_url.'/merchant-login');

        $this->mink->getSession()->wait(1500);
        $this->mink->getSession()->getPage()->fillField('email', $this->email);
        $this->mink->getSession()->getPage()->fillField('password', $this->password);

        $value = $this->mink->getSession()->getPage()->pressButton('loginButton');

        $this->mink->getSession()->wait(2500);

        $assert = $this->mink->assertSession();
        $assert->pageTextContains('Dashboard');

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
        $network = $this->network;
        $client = createClient($network, $privateKey, $publicKey);

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
            $network = $this->network;
            $client = createClient($network, $privateKey, $publicKey);

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
     * @Then /^they will receive a "([^"]*)" matching \'([^\']*)\'$/
     */
    public function theyWillReceiveAnErrorMatching($expectedErrorName, $expectedErrorMessage)
    {
        $curlError = $this->error;
        $curlErrorMessage = $this->error->getMessage();
        if (strpos($curlErrorMessage, $expectedErrorMessage) === false) {
            throw new Exception("Error message incorrect: ".$curlErrorMessage, 1);
        }
        if (strpos(get_class($curlError), $expectedErrorName) === false) {
            throw new Exception("Error name incorrect: ".get_class($curlError), 1);
        }
    }

    /**
     * @When /^the client fails to pair with BitPay because (open|closed) port ([0-9]+) is an incorrect port$/
     */
    public function theClientFailsToPairWithBitpayBecauseOfAnIncorrectPort($status, $port)
    {
        try {
            // Stupid rate limiters
            $this->mink->getSession()->wait(1500);

            // Create Keys
            list($privateKey, $publicKey, $sinKey) = generateAndPersistKeys();

            //Set Client
            $url = parse_url($this->base_url, PHP_URL_HOST);
            $network = new \Bitpay\Network\Customnet($url, $port, true);
            $curl_options = array(
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_TIMEOUT        => 5,
                        );
            $client = createClient($network, $privateKey, $publicKey, $curl_options);

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
            $network = $this->network;
            $client = createClient($network);
            $response  = $client->getInvoice($this->invoiceId);
        } catch (Exception $e){
            var_dump($e->getMessage());
        }
        $responseInvoiceId = $response->getId();
        if($responseInvoiceId !== $this->invoiceId){
            throw new Exception("Invoice ids don't match");
        } 
    }
}