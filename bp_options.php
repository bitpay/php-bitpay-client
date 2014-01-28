<?php

/**
 * ©2011,2012,2013,2014 BITPAY, INC.
 * 
 * Permission is hereby granted to any person obtaining a copy of this software
 * and associated documentation for use and/or modification in association with
 * the bitpay.com service.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * Bitcoin PHP payment library using the bitpay.com service.
 * Version 1.1
 * 
 */

global $bpOptions;

// Please look carefully through these options and adjust according to your installation.  
// Alternatively, most of these options can be dynamically set upon calling the functions in bp_lib.

// REQUIRED Api key you created at bitpay.com
// example: $bpOptions['apiKey'] = 'L21K5IIUG3IN2J3';
$bpOptions['apiKey'] = '';

// whether to verify POS data by hashing above api key.  If set to false, you should
// have some way of verifying that callback data comes from bitpay.com
// note: this option can only be changed here.  It cannot be set dynamically. 
$bpOptions['verifyPos'] = true;

// email where invoice update notifications should be sent
$bpOptions['notificationEmail'] = '';

// url where bit-pay server should send update notifications.  See API doc for more details.
// example: $bpNotificationUrl = 'http://www.example.com/callback.php';
$bpOptions['notificationURL'] = '';

// url where the customer should be directed to after paying for the order
// example: $bpNotificationUrl = 'http://www.example.com/confirmation.php';
$bpOptions['redirectURL'] = '';

// This is the currency used for the price setting.  A list of other pricing
// currencies supported is found at bitpay.com
$bpOptions['currency'] = 'BTC';

// Indicates whether anything is to be shipped with
// the order (if false, the buyer will be informed that nothing is
// to be shipped)
$bpOptions['physical'] = true;

// If set to false, then notificaitions are only
// sent when an invoice is confirmed (according the the
// transactionSpeed setting). If set to true, then a notification
// will be sent on every status change
$bpOptions['fullNotifications'] = true;

// transaction speed: low/medium/high.   See API docs for more details.
$bpOptions['transactionSpeed'] = 'low'; 

// Logfile for use by the bpLog function.  Note: ensure the web server process has write access
// to this file and/or directory!
$bpOptions['logFile'] = '/bplog.txt';

// Change to 'true' if you would like automatic logging of invoices and errors.
// Otherwise you will have to call the bpLog function manually to log any information.
$bpOptions['useLogging'] = false;

?>
