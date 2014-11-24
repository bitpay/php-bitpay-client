<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Crypto;

/**
 * Wrapper around the OpenSSL PHP Extension
 *
 * @see http://php.net/manual/en/book.openssl.php
 */
class OpenSSLExtension implements CryptoInterface
{
    /**
     * @inheritdoc
     */
    public static function hasSupport()
    {
        return function_exists('openssl_open');
    }

    /**
     * @inheritdoc
     */
    public function getAlgos()
    {
    }

    /**
     * Function to generate a new RSA keypair. This is not
     * used for point derivation or for generating signatures.
     * Only used for assymetric data encryption, as needed.
     *
     * @param int
     * @param string
     * @return array|boolean array of keys on success, boolean false on failure
     */
    final public function generateKeypair($keybits = 512, $digest_alg = 'sha512')
    {
        try {
            /* see: http://www.php.net/manual/en/function.openssl-pkey-new.php */
            if (function_exists('openssl_pkey_new')) {
                $keypair = array();

                /* openssl keysize can't be smaller than 384 bits */
                if ((int) $keybits < 384) {
                    $keybits = 384;
                }

                if (!isset($digest_alg) || trim($digest_alg) == '') {
                    $digest_alg = 'sha512';
                }

                /*
                 * RSA is the only supported key type at this time
                 * http://www.php.net/manual/en/function.openssl-csr-new.php
                 */
                $config = array(
                                'digest_alg'       => $digest_alg,
                                'private_key_bits' => (int) $keybits,
                                'private_key_type' => OPENSSL_KEYTYPE_RSA,
                                );

                $resource = openssl_pkey_new($config);

                if (!$resource) {
                    throw new \Exception('Error in generateOpenSSLKeypair: Could not create new OpenSSL resource.');

                    /* with the openssl extension, you also have it's own errors returned */
                    while ($msg = openssl_error_string()) {
                        throw new \Exception('Error in generateOpenSSLKeypair: OpenSSL reported error: '.$msg);
                    }

                    return false;
                }

                if (openssl_pkey_export($resource, $keypair['pri'])) {
                    $publickey      = openssl_pkey_get_details($resource);
                    $keypair['pub'] = $publickey['key'];
                } else {
                    throw new \Exception('Error in generateOpenSSLKeypair: Private key could not be determined from OpenSSL key resource.');

                    while ($msg = openssl_error_string()) {
                        throw new \Exception('Error in generateOpenSSLKeypair: OpenSSL reported error: '.$msg);
                    }

                    return false;
                }

                openssl_pkey_free($resource);

                return $keypair;
            } else {
                throw new \Exception('Error in generateOpenSSLKeypair: OpenSSL PHP extension missing. Cannot continue.');

                return false;
            }
        } catch (Exception $e) {
            while ($msg = openssl_error_string()) {
                throw new \Exception('Error in generateOpenSSLKeypair: OpenSSL reported error: '.$msg);
            }

            throw $e;

            return false;
        }
    }

    /**
     * Generates a high-quality random number suitable for
     * use in cryptographic functions and returns hex value.
     *
     * @param int
     * @return string|bool
     */
    final public function randomNumber($bytes = 32)
    {
        $random_data = openssl_random_pseudo_bytes($bytes, $cstrong);

        if (!$cstrong || !$random_data) {
            return false;
        } else {
            return bin2hex($random_data);
        }
    }

    /**
     * Returns the cipher length on success, or FALSE
     * on failure.  (PHP 5 >= PHP 5.3.3)
     *
     * @param string
     * @return int|bool
     */
    final public function cypherIVLength($cypher = '')
    {
        return openssl_cipher_iv_length($cypher);
    }

    /**
     * Takes the Certificate Signing Request represented
     * by $csr and saves it as ascii-armoured text into
     * the file named by $outfilename.
     * (PHP 4 >= 4.2.0, PHP 5)
     *
     * @param resource
     * @param string
     * @param bool
     * @return bool
     */
    final public function saveCSRtoFile($csr, $outfilename, $notext = true)
    {
        if (!is_resource($csr)) {
            return false;
        }

        return openssl_csr_export_to_file($csr, $outfilename, $notext);
    }

    /**
     * Takes the Certificate Signing Request represented
     * by $csr and stores it as ascii-armoured text into
     * $out, which is passed by reference.
     * (PHP 4 >= 4.2.0, PHP 5)
     *
     * @param resource
     * @param string
     * @param bool
     * @return bool
     */
    final public function saveCSRtoString($csr, $out, $notext = true)
    {
        if (!is_resource($csr)) {
            return false;
        }

        return openssl_csr_export($csr, $out, $notext);
    }
}
