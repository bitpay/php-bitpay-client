<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client;

/**
 * Generic Response class used to parse a response from a server.
 *
 * @package Bitpay
 */
class Response implements ResponseInterface
{
    /**
     * @var string
     */
    protected $raw;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var integer
     */
    protected $statusCode;

    public function __construct($raw = null)
    {
        $this->headers = array();
        $this->raw     = $raw;
    }

    /**
     * Returns the raw http response
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->raw;
    }

    /**
     * @param Response
     */
    public static function createFromRawResponse($rawResponse)
    {
        $response = new self($rawResponse);
        $lines    = preg_split('/(\\r?\\n)/', $rawResponse);
        $linesLen = count($lines);

        for ($i = 0; $i < $linesLen; $i++) {
            if (0 == $i) {
                preg_match('/^HTTP\/(\d\.\d)\s(\d+)\s(.+)/', $lines[$i], $statusLine);
    
                $response->setStatusCode($statusCode = $statusLine[2]);

                continue;
            }

            if (empty($lines[$i])) {
                $body = array_slice($lines, $i + 1);
                $response->setBody(implode("\n", $body));

                break;
            }

            if (strpos($lines[$i], ':')) {
                $headerParts = explode(':', $lines[$i]);
                $response->setHeader($headerParts[0], $headerParts[1]);
            }
        }

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param integer
     * @return Response
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = (integer)$statusCode;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the body of the response
     *
     * @param string $body
     * @return Response
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $header
     * @param string $value
     * @return Response
     */
    public function setHeader($header, $value)
    {
        $this->headers[$header] = $value;

        return $this;
    }
}
