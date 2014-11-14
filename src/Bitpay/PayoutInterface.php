<?php

namespace Bitpay;

/**
 * Interface PayoutInterface
 * @package Bitpay
 */
interface PayoutInterface
{
    /**
     * The payroll flow begins with the payroll provider creating a payout request,
     * using the payroll facade.
     */
    const STATUS_NEW = 'new';

    /**
     * Once Bitpay receives a corresponding ACH transfer from the payroll provider,
     * the finance group marks the payout as 'funded'.
     */
    const STATUS_FUNDED = 'funded';

    /**
     * A payout is marked as processing when it has begun to be processed, but
     * is not yet complete.
     */
    const STATUS_PROCESSING = 'processing';

    /**
     * A batch is marked as complete when all payments have been delivered to the
     * destinations.
     */
    const STATUS_COMPLETE = 'complete';

    /**
     * A payout can be cancelled by making a request on the API with a payout specific
     * token (returned when payout was requested)
     */
    const STATUS_CANCELLED = 'cancelled';

    /**
     * The batch ID of the payout assigned by bitpay.com
     *
     * @return string
     */
    public function getId();

    /**
     * Return the account parameter given by bitpay.
     *
     * @return string
     */
    public function getAccountId();

    /**
     * Get total amount of payout based on instructions.
     *
     * @return string
     */
    public function getAmount();

    /**
     * Get the current total of the Payout. This can only change by adding instructions.
     *
     * @return \Bitpay\CurrencyInterface
     */
    public function getCurrency();

    /**
     * Get the effective date for this payout request - ie, when employees are paid.
     *
     * @return string
     */
    public function getEffectiveDate();

    /**
     * Get the timestamp for when this request was created.
     *
     * @return string
     */
    public function getRequestDate();

    /**
     * Get Instructions for payout.
     *
     * @return array
     */
    public function getInstructions();

    /**
     * Get Notification URL, where updates are POSTED.
     *
     * @return string
     */
    public function getNotificationEmail();

    /**
     * Get Notification URL, where updates are POSTED.
     *
     * @return string
     */
    public function getNotificationUrl();

    /**
     * Return the pricing method when converting Instruction amount to bitcoin.
     *
     * @return string
     */
    public function getPricingMethod();

    /**
     * Get the payee supplied reference for this payout request
     *
     * @return string
     */
    public function getReference();

    /**
     * Get the status for this payout
     *
     * @return $string;
     */
    public function getStatus();

    /**
     * Get the payroll token
     *
     * @return mixed
     */
    public function getToken();

    /**
     * Get the response token, for cancelling payout requests later
     *
     * @return string
     */
    public function getResponseToken();
}
