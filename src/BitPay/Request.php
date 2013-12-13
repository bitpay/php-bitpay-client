<?php namespace BitPay;

abstract class Request
{

    /**
     * BitPay URL Host
     */
    const HOST = "https://bitpay.com/api/";

    /**
     * Amount of time (in seconds) to wait for the BitPay server to respond
     * before timing out. Default 30 seconds.
     *
     * @var int
     */
    protected $timeout = 30;

    /**
     * Response object used by request object
     *
     * @var object
     */
    protected $response;

    /**
     * Host to make request to
     *
     * @var string
     */
    protected $host;

    /**
     * Create a new instance
     *
     */
    public function __construct()
    {
        $this->setHost();
    }

    /**
     * Set the host that the request will be sent too
     *
     */
    public function setHost()
    {
        $this->host = self::HOST;
    }

    /**
     * Set the timeout for the request
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = (int) $timeout;
    }

    /**
     * Get the response object associated with the request
     *
     * @return object
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * GET request method to be implemented by classes that extend the Request class
     *
     * @abstract
     * @return mixed
     */
    abstract public function get($url, $apiKey = null);

    /**
     * POST request method to be implemented by classes that extend the Request class
     *
     * @abstract
     * @return mixed
     */
    abstract public function post($url, $data = null, $apiKey = null);
}
