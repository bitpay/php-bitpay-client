<?php

namespace Bitpay\Client\Adapter;

use Bitpay\Client\RequestInterface;
use Bitpay\Client\Response;

/**
 * Used to test requests and return a specific response back
 *
 * @codeCoverageIgnore
 * @package Bitpay
 */
class MockAdapter implements AdapterInterface
{

    /**
     * @inheritdoc
     */
    public function sendRequest(RequestInterface $request)
    {
        $path = $request->getPath();
        switch ($path) {
            case ('api/invoice'):
                $response = $this->createMockInvoice($request);
                break;
            default:
                $response = new Response();
                break;
        }

        return $response;
    }

    private function createMockInvoice(RequestInterface $request)
    {
        $faker    = \Faker\Factory::create();
        $response = new Response();
        $id       = $faker->randomNumber;
        $response->setBody(
            json_encode(
                array(
                    'id'              => $id,
                    'url'             => sprintf('https://test.bitpay.com/invoices?id=%s', $id),
                    'status'          => 'new',
                    'btcPrice'        => $faker->randomDigit,
                    'price'           => $faker->randomDigit,
                    'invoiceTime'     => $faker->unixTime,
                    'expirationTime'  => $faker->unixTime,
                    'currentTime'     => $faker->unixTime,
                    'btcPaid'         => $faker->randomDigit,
                    'rate'            => $faker->randomFloat(8, 1, 100),
                    'exceptionStatus' => false,
                )
            )
        );

        return $response;
    }
}
