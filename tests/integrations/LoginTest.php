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
 * Test to make sure that login page is working
 */
class LoginTest extends \PHPUnit_Framework_TestCase
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

    public function testLogin()
    {
        $this->mink->getSession()->visit('https://test.bitpay.com/merchant-login');
        $this->mink->getSession()->getPage()->fillField('email', getenv('BITPAY_EMAIL'));
        $this->mink->getSession()->getPage()->fillField('password', getenv('BITPAY_PASSWORD'));
        $this->mink->getSession()->getPage()->findById('loginForm')->submit();
        $this->mink->getSession()->wait(2500); // wait 2.5 seconds for page to load
        $assert = $this->mink->assertSession();
        $assert->pageTextContains('Dashboard');
    }
}
