<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Crypto;

/**
 * @package Bitauth
 */
class McryptExtensionTest extends \PHPUnit_Framework_TestCase
{

    public function testHasSupport()
    {
        $mcrypt = new McryptExtension();
        $this->assertSame(extension_loaded('mcrypt'), $mcrypt->hasSupport());
    }

    public function testEncryptAndDecrypt()
    {

        $mcrypt = new McryptExtension();

        $this->assertNotNull($mcrypt);

        for ($i=1; $i<=20; $i++) {
            $plaintext = $this->generateRandomString($i);
            $key = $this->generateRandomString(8);

            $iv_size = $mcrypt->getIVSize();
            $iv = mcrypt_create_iv($iv_size);

            $encryptedtext = $mcrypt->encrypt($plaintext, $key, $iv);
            $this->assertNotEquals($plaintext, $encryptedtext);

            $decryptedtext = $mcrypt->decrypt($encryptedtext, $key, $iv);
            $this->assertEquals($plaintext, $decryptedtext);
        }
    }

    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

}
