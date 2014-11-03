<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Integration;

use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\Driver\Selenium2Driver;

/**
 * Test to make sure that pairing codes can be created
 */
class CreatePairingCodeTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $isEmailConfigured    = getenv('BITPAY_EMAIL');
        $isPasswordConfigured = getenv('BITPAY_PASSWORD');

        if (!$isEmailConfigured || !$isPasswordConfigured) {
            $this->markTestSkipped('Environment Variables need to be set.');
            return;
        }

        $selenium2driver = new Selenium2Driver('firefox', null, 'http://127.0.0.1:4444');
        $this->mink      = new Mink(
            array(
                'selenium2' => new Session($selenium2driver),
            )
        );
        $this->mink->setDefaultSessionName('selenium2');
    }

    protected function tearDown()
    {
        $this->mink->getSession()->reset();
    }

    public function testPairingCode()
    {
        /**
         * Login and create a pairing code
         */
        $this->login();
        $pairingCode = $this->createPairingCode();

        /**
         * Generate some new keys
         */
        list($pri, $pub, $sin) = $this->generateKeys();

        /**
         * Make request to to pair keys and obtain a token
         */
        $this->client = new \Bitpay\Client\Client();
        $this->client->setNetwork(new \Bitpay\Network\Testnet());
        $this->client->setPublicKey($pub);
        $this->client->setPrivateKey($pri);
        $this->client->setAdapter(new \Bitpay\Client\Adapter\CurlAdapter());
        $token = $this->client->createToken(
            array(
                'id'          => (string) $sin,
                'pairingCode' => $pairingCode,
                'label'       => 'Integration Test',
            )
        );
        /**
         * The token is the what will be needed for all requests in the future
         * and so to inject it into the client, you would add the code
         * <code>
         *   $client->setToken($token);
         * </code>
         */
        $this->assertInstanceOf('Bitpay\TokenInterface', $token);
    }

    private function login()
    {
        $this->mink->getSession()->visit('https://test.bitpay.com/merchant-login');
        $this->mink->getSession()->wait(2500);
        $this->mink->getSession()->getPage()->fillField('email', getenv('BITPAY_EMAIL'));
        $this->mink->getSession()->getPage()->fillField('password', getenv('BITPAY_PASSWORD'));
        $this->mink->getSession()->getPage()->findById('loginForm')->submit();
        $this->mink->getSession()->wait(2500);
        $assert = $this->mink->assertSession();
        $assert->pageTextContains('Dashboard');
    }

    private function createPairingCode()
    {
        $this->mink->getSession()->visit('https://test.bitpay.com/api-tokens');
        $session = $this->mink->getSession();
        $page    = $session->getPage();
        // Click the `Add New Token` button
        $page->find('xpath', '//*[@id="apiTokensList"]/div/div[3]/div[2]/div')->click();
        $this->mink->getSession()->wait(500);
        $assert = $this->mink->assertSession();
        $assert->pageTextContains('Add Token');
        // Click `Add Token` button
        $page->find('xpath', '//*[@id="token-new-form"]/button')->click();
        $this->mink->getSession()->wait(2500);
        $pairingCode = $page->find('xpath', '//*[@id="my-token-access-wrapper"]/div[1]/div[2]/div/div')->getText();
        $this->assertNotNull($pairingCode);

        return $pairingCode;
    }

    private function generateKeys()
    {
        $privateKey = new \Bitpay\PrivateKey();
        $privateKey->generate();
        $publicKey = new \Bitpay\PublicKey();
        $publicKey
            ->setPrivateKey($privateKey)
            ->generate();
        $sin = new \Bitpay\SinKey();
        $sin
            ->setPublicKey($publicKey)
            ->generate();

        return array(
            $privateKey,
            $publicKey,
            $sin,
        );
    }
}
