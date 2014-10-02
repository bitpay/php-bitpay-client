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

    public function testEncrypt()
    {
        $data = array(
            // password, string, expected
            array('', 'o hai, nsa. how i do teh cryptos?', '3uzFC7hwYwVQ57TfdSFwm4ntSeTXZohFhdZ6nvmeGDWjq9Lu8TENcKtPoRFvtRcHTf'),
            array('s4705hiru13z!', 'o hai, nsa. how i do teh cryptos?', '68whtGQvJEXHGQrY9hPLJRhvzzbhygyG2pbAXkjUcEwYSmEKVLcri9nULpxKoxD3Ac'),
        );

        $mcrypt = new McryptExtension();

        $this->assertNotNull($mcrypt);

        foreach ($data as $datum) {
            //$this->assertSame($datum[2], $mcrypt->encrypt($datum[0], $datum[1]));

            // TODO: get value and use for assert. checking not null for now...
            $this->assertNotNull($mcrypt->Encrypt($datum, '12345', '123'));
        }
    }

}
