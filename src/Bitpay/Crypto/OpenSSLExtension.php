<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 BitPay, Inc.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
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
     * @return array
     */
    final public function generateKeypair($keybits = 512, $digest_alg = 'sha512')
    {
        try {
            /* see: http://www.php.net/manual/en/function.openssl-pkey-new.php */
            if (function_exists('openssl_pkey_new')) {
                $keypair = array();

                /* openssl keysize can't be smaller than 384 bits */
                if ((int) $keybits < 384) {
                    $this->addNotice('generateOpenSSLKeypair: Keybits param of "'.$keybits.'" is invalid. Setting to the minimum value of 384.');
                    $keybits = 384;
                }

                if (!isset($digest_alg) || trim($digest_alg) == '') {
                    $this->addNotice('generateOpenSSLKeypair: Digest algorithm missing. Using sha512.');
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
                    $this->addError('Error in generateOpenSSLKeypair: Could not create new OpenSSL resource.');

                    /* with the openssl extension, you also have it's own errors returned */
                    while ($msg = openssl_error_string()) {
                        $this->addError('Error in generateOpenSSLKeypair: OpenSSL reported error: '.$msg);
                    }

                    return false;
                }

                if (openssl_pkey_export($resource, $keypair['pri'])) {
                    $publickey      = openssl_pkey_get_details($resource);
                    $keypair['pub'] = $publickey['key'];
                } else {
                    $this->addError('Error in generateOpenSSLKeypair: Private key could not be determined from OpenSSL key resource.');

                    while ($msg = openssl_error_string()) {
                        $this->addError('Error in generateOpenSSLKeypair: OpenSSL reported error: '.$msg);
                    }

                    return false;
                }

                openssl_pkey_free($resource);

                return $keypair;
            } else {
                $this->addError('Error in generateOpenSSLKeypair: OpenSSL PHP extension missing. Cannot continue.');

                return false;
            }
        } catch (Exception $e) {
            while ($msg = openssl_error_string()) {
                $this->addError('Error in generateOpenSSLKeypair: OpenSSL reported error: '.$msg);
            }

            $this->addError('Error in generateOpenSSLKeypair(): '.$e->getMessage());

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

        if (!$cstrong || !$private_key) {
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
    final public function saveCSRtoString($csr, &$out, $notext = true)
    {
        if (!is_resource($csr)) {
            return false;
        }

        return openssl_csr_export($csr, string &$out, $notext);
    }
}
