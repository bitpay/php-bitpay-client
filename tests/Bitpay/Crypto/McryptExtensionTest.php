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

    public function testEncrypt()
    {

        $mcrypt = new McryptExtension();
        $key = 'testEncrypt';
        $data = array(
            'lsdkjflaslkfslj' => 'kz2DG4D3vkA6bkDbhrvD+Q==',
            'a340932084093280' => 'gNaNgXFc7ecle8SaAJAJOw==',
            '*&($*@*%&*$*#*&@(*#*(' => 'cVGF2lnyH6OHLYWHa+8XxHbFzVNK5IYL',
            '___asdfa234($*(#__' => 'I8OFg5parn9b0Qk8mJnQH0+SgQWwYER5'
        );

        foreach($data as $unencrypted => $encrypted )
        {
            $encryptedtext = $mcrypt->encrypt($unencrypted, $key, '00000000');
            $this->assertEquals($encrypted, $encryptedtext);
        }
    }

    public function testDecrypt()
    {

        $mcrypt = new McryptExtension();
        $key = 'testEncrypt';
        $data = array(
            'lsdkjflaslkfslj' => 'kz2DG4D3vkA6bkDbhrvD+Q==',
            'a340932084093280' => 'gNaNgXFc7ecle8SaAJAJOw==',
            '*&($*@*%&*$*#*&@(*#*(' => 'cVGF2lnyH6OHLYWHa+8XxHbFzVNK5IYL',
            '___asdfa234($*(#__' => 'I8OFg5parn9b0Qk8mJnQH0+SgQWwYER5'
        );

        foreach($data as $unencrypted => $encrypted )
        {
            $plaintext = $mcrypt->decrypt($encrypted, $key, '00000000');
            $this->assertEquals($unencrypted, $plaintext);
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
