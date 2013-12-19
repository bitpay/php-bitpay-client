<?php namespace BitPay\Tests;

use BitPay\Hash;
use \PHPUnit_Framework_TestCase;

class HashTest extends PHPUnit_Framework_TestCase
{

    public function testEncryptReturnsString()
    {
        $hash = Hash::encrypt(serialize('data'), 'key');
        $this->assertTrue(
            is_string($hash)
        );
    }

    public function testEncryptArrayReturnsCorrectHash()
    {
        $this->assertEquals(
            Hash::encrypt(serialize('data'), 'key'),
            'TCFzuJLomjQ_nvVWATq8mY6fHzhN_TktdIJchg0Qi4o'
        );
    }

    public function testEncryptStringReturnsCorrectHash()
    {
        $this->assertEquals(
            Hash::encrypt('data', 'key'),
            'UDH-PZicbRU3oBP6bnOdojRj_a7DtwE32Cjjas4iG9A'
        );
    }
}
