<?php

function generateAndPersistKeys()
{
    $privateKey = new \Bitpay\PrivateKey('/tmp/bitpay.pri');
    $privateKey->generate();
    $publicKey = new \Bitpay\PublicKey('/tmp/bitpay.pub');
    $publicKey->setPrivateKey($privateKey);
    $publicKey->generate();
    $sinKey = new \Bitpay\SinKey('/tmp/sin.key');
    $sinKey->setPublicKey($publicKey);
    $sinKey->generate();

    //Persist Keys
    $storageEngine = new \Bitpay\Storage\EncryptedFilesystemStorage('YourTopSecretPassword');
    $storageEngine->persist($privateKey);
    $storageEngine->persist($publicKey);

    return array($privateKey, $publicKey, $sinKey);
}

function loadKeys()
{
    $storageEngine = new \Bitpay\Storage\EncryptedFilesystemStorage('YourTopSecretPassword');
    $privateKey    = $storageEngine->load('/tmp/bitpay.pri');
    $publicKey     = $storageEngine->load('/tmp/bitpay.pub');
    $token_id      = file_get_contents('/tmp/token.json');

    return array($privateKey, $publicKey, $token_id);
}

function createClient($network, $privateKey = null, $publicKey = null, $curl_options = null)
{
    if(true === is_null($curl_options)) {
        $curl_options = array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        );
    }
    $adapter = new \Bitpay\Client\Adapter\CurlAdapter($curl_options);
    $client = new \Bitpay\Client\Client();

    if(true === !is_null($privateKey)) {
        $client->setPrivateKey($privateKey);
    }
    if(true === !is_null($publicKey)) {
        $client->setPublicKey($publicKey);
    }

    $client->setNetwork($network);
    $client->setAdapter($adapter);

    return $client;
}