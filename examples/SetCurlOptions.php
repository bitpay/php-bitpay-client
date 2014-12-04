<?php

require __DIR__ . '/../vendor/autoload.php';

//When running bitpay on your local server
$network = new Bitpay\Network\Customnet("alex.bp", 8088, true);

//Customize the curl options
$curl_options = array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            );

// If nothing is passed into the CurlAdapter 
// then default values are used
$adapter = new Bitpay\Client\Adapter\CurlAdapter($curl_options);