<?php namespace BitPay\Request;

use BitPay\Request;
use BitPay\Exception\CurlRequestException;

class Curl extends Request
{

    /**
    * Create a new instance
    *
    * @throws CurlRequestException
    */
    public function __construct()
    {
        if (!function_exists('curl_version')) {
            throw new CurlRequestException('Curl extension is either not enabled or not installed');
        }

        parent::__construct();
    }

    public function get($endpoint, $apiKey = null)
    {
        $curl = curl_init($this->host . $endpoint);

        $uname = base64_encode($apiKey);

        $header = array(
            'Content-Type: application/json',
            "Content-Length: 0",
            "Authorization: Basic $uname"
        );

        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);

        try {
            $responseString = curl_exec($curl);
        } catch (Exception $e) {
            throw new CurlRequestException(curl_error($curl), curl_errno($curl));
        }

        $this->response = json_decode($responseString, true);
        if (!$this->response) {
            throw new CurlRequestException('Invalid JSON');
        }

        curl_close($curl);

        return $this->response;
    }

    public function post($endpoint, $data = null, $apiKey = null)
    {
        $curl = curl_init($this->host . $endpoint);

        $uname = base64_encode($apiKey);

        $header = array(
            'Content-Type: application/json',
            "Content-Length: " . strlen($data),
            "Authorization: Basic $uname"
        );

        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $responseString = curl_exec($curl);

        if ($responseString == false) {
            $response = array('error' => curl_error($curl));
        } else {
            $response = json_decode($responseString, true);
            if (!$response) {
                $response = array('error' => 'invalid json: '.$responseString);
            }
        }
        curl_close($curl);
        return $response;
    }
}
