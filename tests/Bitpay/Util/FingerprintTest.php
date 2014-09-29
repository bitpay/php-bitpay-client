<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Util;

class FingerprintTest extends \PHPUnit_Framework_TestCase
{

    public function testGenerate()
    {
        $finger = Fingerprint::generate();
        $this->assertNotNull($finger);

        // Make sure it generates the same value
        $this->assertSame($finger, Fingerprint::generate());
    }
}
