##  Pairing
To create a pairing code, please open the API Tokens page within your
merchant account. You will next need to click the button labeled "Add
New Token". You will be given a Pairing Code that you will need in the
few steps.

Pairing {#pairing-1}
=======

Create an instance of the Bitpay class.

``` {.sourceCode .php}
$bitpay = new \Bitpay\Bitpay(
    array(
        'bitpay' => array(
            'network'     => 'testnet', // testnet or livenet, default is livenet
            'public_key'  => getenv('HOME').'/.bitpay/api.pub',
            'private_key' => getenv('HOME').'/.bitpay/api.key',
        )
    )
);
```

Next you will need to get the client.

``` {.sourceCode .php}
// @var \Bitpay\Client\Client
$client = $bitpay->get('client');
```

You will next need to create a SIN based on your Public Key.

``` {.sourceCode .php}
// @var \Bitpay\KeyManager
$manager   = $bitpay->get('key_manager');
$publicKey = $manager->load($bitpay->getContainer()->getParameter('bitpay.public_key'));
$sin = new \Bitpay\SinKey();
$sin->setPublicKey($publicKey);
$sin->generate();

// @var \Bitpay\TokenInterface
$token = $client->createToken(
    array(
        'id'          => (string) $sin,
        'pairingCode' => 'Enter the Pairing Code',
        'label'       => 'Any label you want',
    )
);
```

> **note**
>
> Want to know more about SINs? See
> <https://en.bitcoin.it/wiki/Identity_protocol_v1>

The token that you get back will be needed to be sent with future
requests.
