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
 *
 * Version 1.3, rich@bitpay.com
 * 
 */

require_once 'bp_options.php';

/**
 *
 * Writes $contents to a log file specified in the bp_options file or, if missing,
 * defaults to a standard filename of 'bplog.txt'.
 *
 * @param mixed $contents
 * @return
 * @throws Exception $e 
 *
 */
function bpLog($contents) {
  global $bpOptions;
  
  try {
    if(isset($bpOptions['logFile']) && $bpOptions['logFile'] != '') {
      $file = dirname(__FILE__).$bpOptions['logFile'];
    } else {
      // Fallback to using a default logfile name in case the variable is
      // missing or not set.
      $file = dirname(__FILE__).'/bplog.txt';
    }

    file_put_contents($file, date('m-d H:i:s').": ", FILE_APPEND);

    if (is_array($contents))
      $contents = var_export($contents, true);	
    else if (is_object($contents))
      $contents = json_encode($contents);

    file_put_contents($file, $contents."\n", FILE_APPEND);

  } catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
  }
}

/**
 *
 * Handles post/get to BitPay via curl.
 *
 * @param string $url, string $apiKey, boolean $post
 * @return mixed $response
 * @throws Exception $e
 *
 */
function bpCurl($url, $apiKey, $post = false) {
  global $bpOptions;	

  if((isset($url) && trim($url) != '') && (isset($apiKey) && trim($apiKey) != '')) {
    try {
      $curl = curl_init();
      $length = 0;

      if ($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $length = strlen($post);
      }

      $uname = base64_encode($apiKey);

      if($uname) {
        $header = array(
                  'Content-Type: application/json',
                  'Content-Length: ' . $length,
                  'Authorization: Basic ' . $uname,
        );

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1); // verify certificate
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // check existence of CN and verify that it matches hostname
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);

        $responseString = curl_exec($curl);

        if($responseString == false) {
          $response = array('error' => curl_error($curl));
          if($bpOptions['useLogging'])
            bpLog('Error: ' . curl_error($curl));
        } else {
          $response = json_decode($responseString, true);
          if (!$response) {
            $response = array('error' => 'invalid json: '.$responseString);
            if($bpOptions['useLogging'])
              bpLog('Error - Invalid JSON: ' . $responseString);
          }
        }

        curl_close($curl);
        return $response;
      } else {
        curl_close($curl);
        if($bpOptions['useLogging'])
          bpLog('Invalid data found in apiKey value passed to bpCurl. (Failed: base64_encode(apikey))');
        return array('error' => 'Invalid data found in apiKey value passed to bpCurl. (Failed: base64_encode(apikey))');
      }
    } catch (Exception $e) {
      @curl_close($curl);
      if($bpOptions['useLogging'])
        bpLog('Error: ' . $e->getMessage());
      return array('error' => $e->getMessage());
    }
  } else {
    // Invalid parameter specified
    if($bpOptions['useLogging'])
      bpLog('Error: You must supply non-empty url and apiKey parameters.');
    return array('error' => 'You must supply non-empty url and apiKey parameters.');
  }

}

/**
 *
 * Creates BitPay invoice via bpCurl.
 *
 * @param string $orderId, string $price, string $posData, array $options
 * @return array $response
 * @throws Exception $e
 *
 */
function bpCreateInvoice($orderId, $price, $posData, $options = array()) {
  // $orderId: Used to display an orderID to the buyer. In the account summary view, this value is used to
  // identify a ledger entry if present. Maximum length is 100 characters.
  //
  // $price: by default, $price is expressed in the currency you set in bp_options.php.  The currency can be
  // changed in $options.
  //
  // $posData: this field is included in status updates or requests to get an invoice.  It is intended to be used by
  // the merchant to uniquely identify an order associated with an invoice in their system.  Aside from that, Bit-Pay does
  // not use the data in this field.  The data in this field can be anything that is meaningful to the merchant.
  // Maximum length is 100 characters.
  //
  // Note:  Using the posData hash option will APPEND the hash to the posData field and could push you over the 100
  //        character limit.
  //
  //
  // $options keys can include any of:
  //	'itemDesc', 'itemCode', 'notificationEmail', 'notificationURL', 'redirectURL', 'apiKey'
  //	'currency', 'physical', 'fullNotifications', 'transactionSpeed', 'buyerName',
  //	'buyerAddress1', 'buyerAddress2', 'buyerCity', 'buyerState', 'buyerZip', 'buyerEmail', 'buyerPhone'
  //
  // If a given option is not provided here, the value of that option will default to what is found in bp_options.php
  // (see api documentation for information on these options).

  global $bpOptions;	

  try {
    $options = array_merge($bpOptions, $options);  // $options override any options found in bp_options.php
    $pos = array('posData' => $posData);

    if ($bpOptions['verifyPos']) 
      $pos['hash'] = bpHash(serialize($posData), $options['apiKey']);

    $options['posData'] = json_encode($pos);

    if(strlen($options['posData']) > 100)
      return array('error' => 'posData > 100 character limit. Are you using the posData hash?');

    $options['orderID'] = $orderId;
    $options['price'] = $price;

    $postOptions = array('orderID', 'itemDesc', 'itemCode', 'notificationEmail', 'notificationURL', 'redirectURL', 
                         'posData', 'price', 'currency', 'physical', 'fullNotifications', 'transactionSpeed', 'buyerName', 
                         'buyerAddress1', 'buyerAddress2', 'buyerCity', 'buyerState', 'buyerZip', 'buyerEmail', 'buyerPhone');
                         
    /* $postOptions = array('orderID', 'itemDesc', 'itemCode', 'notificationEmail', 'notificationURL', 'redirectURL', 
                         'posData', 'price', 'currency', 'physical', 'fullNotifications', 'transactionSpeed', 'buyerName', 
                         'buyerAddress1', 'buyerAddress2', 'buyerCity', 'buyerState', 'buyerZip', 'buyerEmail', 'buyerPhone',
                         'pluginName', 'pluginVersion', 'serverInfo', 'serverVersion', 'addPluginInfo');
    */
    // Usage information for support purposes. Do not modify.
    //$postOptions['pluginName']    = 'PHP Library';
    //$postOptions['pluginVersion'] = '1.3';
    //$postOptions['serverInfo']    = htmlentities($_SERVER['SERVER_SIGNATURE'], ENT_QUOTES);
    //$postOptions['serverVersion'] = htmlentities($_SERVER['SERVER_SOFTWARE'], ENT_QUOTES);
    //$postOptions['addPluginInfo'] = htmlentities($_SERVER['SCRIPT_FILENAME'], ENT_QUOTES);

    foreach($postOptions as $o) {
      if (array_key_exists($o, $options))
        $post[$o] = $options[$o];
    }

    $post = json_encode($post);

    $response = bpCurl('https://bitpay.com/api/invoice/', $options['apiKey'], $post);

    if($bpOptions['useLogging']) {
      bpLog('Create Invoice: ');
      bpLog($post);
      bpLog('Response: ');
      bpLog($response);
    }

    return $response;

  } catch (Exception $e) {
    if($bpOptions['useLogging'])
      bpLog('Error: ' . $e->getMessage());
    return array('error' => $e->getMessage());
  }
}

/**
 *
 * Call from your notification handler to convert $_POST data to an object containing invoice data
 *
 * @param boolean $apiKey
 * @return mixed $json
 * @throws Exception $e
 *
 */
function bpVerifyNotification($apiKey = false) {
  global $bpOptions;

  try {
    if (!$apiKey) 
      $apiKey = $bpOptions['apiKey'];		

    $post = file_get_contents("php://input");

    if (!$post)
      return 'No post data';

    $json = json_decode($post, true);

    if (is_string($json))
      return $json; // error

    if (!array_key_exists('posData', $json))
      return 'no posData';

    $posData = json_decode($json['posData'], true);

    if($bpOptions['verifyPos'] and $posData['hash'] != bpHash(serialize($posData['posData']), $apiKey))
      return 'authentication failed (bad hash)';

    $json['posData'] = $posData['posData'];

    return $json;
  } catch (Exception $e) {
    if($bpOptions['useLogging'])
      bpLog('Error: ' . $e->getMessage());
    return array('error' => $e->getMessage());
  }
}

/**
 *
 * Retrieves an invoice from BitPay.  $options can include 'apiKey'
 *
 * @param string $invoiceId, boolean $apiKey
 * @return mixed $json
 * @throws Exception $e
 *
 */
function bpGetInvoice($invoiceId, $apiKey=false) {
  global $bpOptions;

  try {
    if (!$apiKey)
      $apiKey = $bpOptions['apiKey'];

    $response = bpCurl('https://bitpay.com/api/invoice/'.$invoiceId, $apiKey);

    if (is_string($response))
      return $response; // error

    $response['posData'] = json_decode($response['posData'], true);
    $response['posData'] = $response['posData']['posData'];

    return $response;
  } catch (Exception $e) {
    if($bpOptions['useLogging'])
      bpLog('Error: ' . $e->getMessage());
    return 'Error: ' . $e->getMessage();
  }
}

/**
 *
 * Generates a base64 encoded keyed hash.
 *
 * @param string $data, string $key
 * @return string $hmac
 * @throws Exception $e
 *
 */
function bpHash($data, $key) {
  global $bpOptions;
  
  try {
    $hmac = base64_encode(hash_hmac('sha256', $data, $key, TRUE));
    return strtr($hmac, array('+' => '-', '/' => '_', '=' => ''));
  } catch (Exception $e) {
    if($bpOptions['useLogging'])
      bpLog('Error: ' . $e->getMessage());
    return 'Error: ' . $e->getMessage();
  }
}

/**
 * 
 * Decodes JSON response and returns
 * associative array.
 * 
 * @param string $response
 * @return array $arrResponse
 * @throws Exception $e
 * 
 */
function decodeResponse($response) {
  global $bpOptions;
  
  try {
    if (empty($response) || !(is_string($response)))
      return 'Error: decodeResponse expects a string parameter.';

    return json_decode($response, true);
  } catch (Exception $e) {
    if($bpOptions['useLogging'])
      bpLog('Error: ' . $e->getMessage());
    return 'Error: ' . $e->getMessage();
  }
}
