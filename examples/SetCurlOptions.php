<?php

require __DIR__ . '/../vendor/autoload.php';

//Customize the curl options
$curl_options = array(
            CURLOPT_PORT           => 443,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FORBID_REUSE   => 1,
            CURLOPT_FRESH_CONNECT  => 1,
            CURLOPT_HEADER         => true,
            );

// If nothing is passed into the CurlAdapter 
// then default values are used
$adapter = new Bitpay\Client\Adapter\CurlAdapter($curl_options);
