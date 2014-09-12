<?php

namespace Bitpay;

use Bitpay\Util\Base58;
use Bitpay\Util\Gmp;
use Bitpay\Util\Util;

/**
 * @package Bitcore
 */
class SinKey extends Key
{

    const SIN_TYPE    = '02';
    const SIN_VERSION = '0F';

    /**
     * @var string
     */
    protected $value;

    protected $publicKey;

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    public function setPublicKey(PublicKey $publicKey)
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * @return SinKey
     */
    public function generate()
    {
        if (is_null($this->publicKey)) {
            throw new \Exception('Public Key has not been set');
        }

        $compressedValue = $this->publicKey->getX();

        if (empty($compressedValue)) {
            throw new \Exception('The Public Key needs to be generated.');
        }

        $step1 = Util::sha256(Gmp::gmpBinconv($this->publicKey->getX()), true);
        $step2 = Util::ripe160($step1);
        $step3 = sprintf(
            '%s%s%s',
            self::SIN_VERSION,
            self::SIN_TYPE,
            $step2
        );

        $step4 = Util::twoSha256(Gmp::gmpBinconv($step3), true);
        $step5 = substr(bin2hex($step4), 0, 8);
        $step6 = $step3 . $step5;

        $this->value = Base58::encode($step6);

        return $this;
    }

    public function isValid()
    {
        return true;
    }
}
