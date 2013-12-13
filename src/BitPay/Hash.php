<?php namespace BitPay;

class Hash
{
    /**
     * Hash the data to send using sha256
     * @param array $data
     * @param $key API Key
     * @return string Hashed data
     */
    public static function encrypt($data, $key)
    {
        $hmac = base64_encode(hash_hmac('sha256', $data, $key, true));
        return strtr($hmac, array('+' => '-', '/' => '_', '=' => ''));
    }
}
