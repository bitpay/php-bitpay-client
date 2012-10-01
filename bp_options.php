<?php

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
# example: $bpNotificationUrl = 'http://www.example.com/callback.php';
$bpOptions['notificationURL'] = '';

// url where the customer should be directed to after paying for the order
# example: $bpNotificationUrl = 'http://www.example.com/confirmation.php';
$bpOptions['redirectURL'] = '';

// This is the currency used for the price setting.  A list of other pricing
// currencies supported is found at bitpay.com
$bpOptions['currency'] = 'BTC';

// Indicates whether anything is to be shipped with
// the order (if false, the buyer will be informed that nothing is
// to be shipped)
$bpOptions['physical'] = 'true';

// If set to false, then notificaitions are only
// sent when an invoice is confirmed (according the the
// transactionSpeed setting). If set to true, then a notification
// will be sent on every status change
$bpOptions['fullNotifications'] = 'true';

// transaction speed: low/medium/high.   See API docs for more details.
$bpOptions['transactionSpeed'] = 'low'; 

?>