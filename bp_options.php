<?php

/**
 * Copyright (c) 2011-2014 BITPAY, INC.
 *
 *
 * The MIT License (MIT)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 *
 * Bitcoin PHP payment library using the bitpay.com service. You can always 
 * download the latest version at https://github.com/bitpay/php-client
 */


global $bpOptions;


/* 
 * Please look carefully through these options and adjust according to your installation.  
 * Alternatively, most of these options can be dynamically set upon calling the functions in bp_lib.
 */
 
 
/* 
 * REQUIRED!  This is the API key you created in your merchant account at bitpay.com
 * Example: $bpOptions['apiKey'] = 'L21K5IIUG3IN2J3';
 */
$bpOptions['apiKey'] = '';


/*
 * Boolean value.  Whether to verify POS data by hashing above api key.  If set to false, you should
 * have some way of verifying that callback data comes from bitpay.com
 * Note: this option can only be changed here.  It cannot be set dynamically.
 */
$bpOptions['verifyPos'] = true;


/*
 * Optional - email where you want invoice update notifications sent
 */
$bpOptions['notificationEmail'] = '';


/*
 * Optional - url where bit-pay server should send payment notification updates.  See API doc for more details.
 * Example: $bpNotificationUrl = 'http://www.example.com/callback.php';
 */
$bpOptions['notificationURL'] = '';


/* 
 * Optional - url where the customer should be directed to after paying for the order
 * example: $bpNotificationUrl = 'http://www.example.com/confirmation.php';
 */
$bpOptions['redirectURL'] = '';


/*
 * REQUIRED!  This is the currency used for the price setting.  A list of other pricing
 * currencies supported is found at bitpay.com
 */
$bpOptions['currency'] = 'BTC';


/* 
 * Boolean value.  Indicates whether anything is to be shipped with
 * the order (if false, the buyer will be informed that nothing is
 * to be shipped)
 */
$bpOptions['physical'] = true;


/*
 * If set to false, then notificaitions are only
 * sent when an invoice is confirmed (according the the
 * transactionSpeed setting). If set to true, then a notification
 * will be sent on every status change
 */
$bpOptions['fullNotifications'] = true;


/* 
 * REQUIRED! Transaction speed: low/medium/high.  See API docs for more details.
*/
$bpOptions['transactionSpeed'] = 'low'; 


/* 
 * Boolean value. Change to 'true' if you would like automatic logging of errors.
 * Otherwise you will have to call the bpLog function manually to log any information.
 */
$bpOptions['useLogging'] = false;


/* 
 * Boolean value. Change to 'true' if you want to use the testnet development environment at
 * test.bitpay.com. See: http://blog.bitpay.com/2014/05/13/introducing-the-bitpay-test-environment.html
 * for more information on using testnet.
 */
$bpOptions['testnet'] = false;

?>
