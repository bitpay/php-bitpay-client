<?php

namespace Bitpay;


interface SupportRequestInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     *
     * @return SupportRequestInterface
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getRequestDate();

    /**
     * @param string $date
     *
     * @return SupportRequestInterface
     */
    public function setRequestDate($date);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     *
     * @return SupportRequestInterface
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param string $token
     *
     * @return SupportRequestInterface
     */
    public function setToken($token);
}
