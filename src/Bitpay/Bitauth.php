<?php

namespace Bitpay;

use Bitpay\Util\Util;
use Bitpay\Util\Base58;

/**
 * This provides an easy interface for implementing Bitauth
 *
 * @package Bitauth
 */
class Bitauth
{

    /**
     * Initialization Vector
     */
    const IV = '0000000000000000';

    /**
     * @return array
     */
    public function generateSin()
    {
        $priKey = PrivateKey::create()->generate();
        $pubKey = PublicKey::create()->setPrivateKey($priKey)->generate();
        $sinKey = SinKey::create()->setPublicKey($pubKey)->generate();

        return array(
            'public'  => $pubKey,
            'private' => $priKey,
            'sin'     => $sinKey,
        );
    }

    /**
     * @return PublicKey
     */
    public function getPublicKeyFromPrivateKey(\Bitpay\PrivateKey $privateKey)
    {
        return PublicKey::create()->generate($privateKey);
    }

    /**
     * @return Sin
     */
    public function getSinFromPublicKey(\Bitpay\PublicKey $publicKey)
    {
        //return Sin::fromPubKey($publicKey);
    }

    /**
     * @return string
     */
    public function sign($data, $privateKey)
    {
        $hash = Util::sha256($data);

        $signature = $hash;

        return $signature;
    }

    /**
     * @return boolean
     */
    public function verifySignature($contract, $publicKey, $signature)
    {
        return true;
    }

    /**
     * @return boolean
     */
    public function validateSin($sin)
    {
        return true;
    }

    /**
     * @return string
     */
    public function encrypt($password, $data)
    {
        return Base58::encode(bin2hex(openssl_encrypt($data, 'AES-128-CBC', $password, OPENSSL_RAW_DATA, self::IV)));
    }

    /**
     * @return string
     */
    public function decrypt($password, $enc)
    {
    }

    /**
     * Returns the inititialization size used for a particular cypher
     * type.  Returns an integer IV size on success or boolean false
     * on failure.  If no IV is needed for the cypher type and mode,
     * a zero is returned.
     *
     * @param string $cypher_type
     * @return int|bool
     */
    public function rmGetIVSize($cypher_type = 'MCRYPT_TRIPLEDES')
    {

        $block_mode = 'cbc';

        return mcrypt_get_iv_size($cypher_type, $block_mode);

    }

    /**
     * Returns the maximum key size that can be used with a particular
     * cypher type. Any key size equal to or less than the returned
     * value are legal key sizes.  Depending on if the local mycrypt
     * extension is linked against 2.2 or 2.3/2.4 the block mode could
     * be required, hence the if/else statement.
     * 
     * @param string $cypher_type
     * @return int
     */
    public function rmGetKeySize($cypher_type = 'MCRYPT_TRIPLEDES')
    {

        $block_mode = 'cbc';

        $max_key_size = mcrypt_get_key_size($cipher_type);
        
        if ($max_key_size !== false) {
            return $max_key_size;
        } else {
            return mcrypt_get_key_size($cipher_type, $block_mode);
        }

    }

    /**
     * Returns a list of all supported mcrypt algorithms on the local system.
     * 
     * @param none
     * @return array
     */
    public function rmGetAlgos()
    {

        return mcrypt_list_algorithms();

    }

    /**
     * Performs an internal self-test on the specified mcrypt algorithm and
     * returns either boolean true/false depending on if the self-test passed
     * or failed.
     * 
     * @param string $cypher_type
     * @return boolean
     */
    public function rmAlgoSelfTest($cypher_type = 'MCRYPT_TRIPLEDES')
    {

        return mcrypt_module_self_test($cypher_type);

    }

    /**
     *
     * Encrypts $text based on your $key and $iv.  The returned text is
     * base-64 encoded to make it easier to work with in various scenarios.
     * Default cypher is MCRYPT_TRIPLEDES but you can substitute depending
     * on your specific encryption needs.
     *
     * @param string $text
     * @param string $key
     * @param string $iv
     * @param int $bit_check
     * @param string $cypher_type
     * @return string $text
     * @throws Exception $e
     * 
     */
    public function rmEncrypt($text, $key = '', $iv = '', $bit_check = 8, $cypher_type = 'MCRYPT_TRIPLEDES')
    {

        try {

            /* Ensure the key & IV is the same for both encrypt & decrypt. */
            if (!empty($text) && is_string($text)) {
                $text_num = str_split($text, $bit_check);
                $text_num = $bit_check - strlen($text_num[count($text_num) - 1]);

                for ($i=0; $i<$text_num; $i++) {
                    $text = $text . chr($text_num);
                }

                $cipher = mcrypt_module_open($cypher_type, '', 'cbc', '');
                mcrypt_generic_init($cipher, $key, $iv);

                $encrypted = mcrypt_generic($cipher, $text);
                mcrypt_generic_deinit($cipher);

                mcrypt_module_close($cipher);

                return base64_encode($encrypted);
            } else {
                return $text;
            }

        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }

    }


    /**
     *
     * Decrypts $text based on your $key and $iv.  Make sure you use the same key
     * and initialization vector that you used when encrypting the $text. Default
     * cypher is MCRYPT_TRIPLEDES but you can substitute depending on the cypher
     * used for encrypting the text - very important.
     *
     * @param string $encrypted_text
     * @param string $key
     * @param string $iv
     * @param int $bit_check
     * @param string $cypher_type
     * @return string $text
     * @throws Exception $e
     * 
     */
    public function rmEecrypt($encrypted_text, $key = '', $iv = '', $bit_check = 8, $cypher_type = 'MCRYPT_TRIPLEDES')
    {

        try {

            /* Ensure the key & IV is the same for both encrypt & decrypt. */
            if (!empty($encrypted_text)) {
                $cipher = mcrypt_module_open($cypher_type, '', 'cbc', '');

                mcrypt_generic_init($cipher, $key, $iv);
                $decrypted = mdecrypt_generic($cipher, base64_decode($encrypted_text));

                mcrypt_generic_deinit($cipher);
                $last_char = substr($decrypted, -1);
  
                for ($i = 0; $i < $bit_check - 1; $i++) {
                    if (chr($i) == $last_char) {
                        $decrypted = substr($decrypted, 0, strlen($decrypted) - $i);
                        break;
                    }
                }

                mcrypt_module_close($cipher);

                return $decrypted;
            } else {
                return $encrypted_text;
            }

        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }

    }

}
