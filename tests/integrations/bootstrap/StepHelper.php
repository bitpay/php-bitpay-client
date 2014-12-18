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