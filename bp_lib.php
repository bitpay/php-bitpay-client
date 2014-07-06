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

require_once 'bp_options.php';

/*
 * BitPay PHP Client Library Version
 */
define('VERSION', '1.8');


/**
 *
 * Writes $contents to the system logger which is usually the webserver's error log 
 * but can be changed depending on your development requirements.
 *
 * @param mixed $contents
 * @return boolean
 * @throws Exception $e 
 *
 */
function bpLog($contents) {
  global $bpOptions;
  
  if (!isset($contents) || trim($contents) != '' || is_null($contents) || empty($contents))
    return 'Error in bpLog(): Nothing to log was supplied. Usage: bpLog($contents)';

  try {

    /*
     * System Error Logging: bool error_log ( string $message )
     * $message is sent to PHP's system logger, using the Operating System's system
     * logging mechanism or a file, depending on what the error_log configuration
     * directive is set to. This is the default option.
     * See: http://www.php.net/manual/en/function.error-log.php
     */
    return error_log(var_export($contents, true));

  } catch (Exception $e) {
    echo 'Error in bpLog(): ' . $e->getMessage();
  }
}

/**
 *
 * Returns the correct API service endpoint hostname depending on whether the
 * production or test environment is selected.
 *
 * @param none
 * @return string $host
 *
 */
function bpHost() {
  global $bpOptions;

  /*
   * Safety check in case an older version of the option file is being used or the test option is
   * empty or not set at all.  Defaults to the live site.
   */
  if (!isset($bpOptions['testnet']) || trim($bpOptions['testnet']) != '' || is_null($bpOptions['testnet']) || empty($bpOptions['testnet']))
    $bpOptions['testnet'] == false;

  if ($bpOptions['testnet'] == true)
    return 'test.bitpay.com';

  return 'bitpay.com';
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

  /*
   * Container for our curl response or any error messages to return
   * to the calling function.
   */
  $response = null;


  if ((isset($url) && trim($url) != '') && (isset($apiKey) && trim($apiKey) != '')) {

    try {
      $curl = curl_init();

      if (!$curl)
        return 'Error in bpCurl(): Could not initialize a cURL handle!';

      $content_length = 0;

      if ($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

        $content_length = strlen($post);
      }

      $uname = base64_encode($apiKey);

      if ($uname) {
        $header = array(
                  'Content-Type: application/json',
                  'Content-Length: ' . $content_length,
                  'Authorization: Basic ' . $uname,
                  'X-BitPay-Plugin-Info: phplib' . VERSION,
                  );

        /*
         * If you are having SSL certificate errors due to an outdated CA cert on your webserver
         * ask your webhosting provider to update your webserver.  The curl SSL checks are used 
         * to ensure you are actually communicating with the real BitPay network.
         */
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);

        /*
         * Returns the error message or '' (the empty string) if no error occurred.
         */
        $responseString = curl_exec($curl);

        /*
         * For a complete list and description of all curl error codes
         * see: http://curl.haxx.se/libcurl/c/libcurl-errors.html
         */
        $curl_error_number = curl_errno($curl);

        if ($responseString === false || $curl_error_number != 0) {
          $curl_error_description = curl_strerror($curl_error_number);

          $response = array('error' => curl_error($curl), 'error_code' => $curl_error_number, 'error_code_description' => $curl_error_description);

          if ($bpOptions['useLogging'])
            bpLog('Error in bpCurl(): ' . $response);

        } else {

          if (function_exists('json_decode'))
            $response = json_decode($responseString, true);
          else
            $response = bpJSONdecode($responseString);

          if (!$response) {
            $response = array('error' => 'invalid json');

            if ($bpOptions['useLogging'])
              bpLog('Error in bpCurl(): Invalid JSON.');
          }

        }

        curl_close($curl);

        return $response;

      } else {

        curl_close($curl);

        if ($bpOptions['useLogging'])
          bpLog('Error in bpCurl(): Invalid data found in apiKey value passed to bpCurl. (Failed: base64_encode(apikey))');

        return array('error' => 'Invalid data found in apiKey value passed to bpCurl. (Failed: base64_encode(apikey))');
      }

    } catch (Exception $e) {
      
      /*
       * It's possible that an error could occur before curl is initialized.  In that case
       * it is safe to suppress the warning message from calling curl_close without an
       * initialized curl session.
       */
      @curl_close($curl);

      if ($bpOptions['useLogging'])
        bpLog('Error in bpCurl(): ' . $e->getMessage());

      return array('error' => $e->getMessage());
    }

  } else {

    /*
     * Invalid URL or API Key parameter specified
     */
    if ($bpOptions['useLogging'])
      bpLog('Error in bpCurl(): You must supply non-empty url and apiKey parameters.');

    return array('error' => 'You must supply non-empty url and apiKey parameters to bpCurl().');
  }

}

/**
 *
 * Creates BitPay invoice via bpCurl.  More information regarding the various options are explained
 * below.  For the official API documentation, see: https://bitpay.com/downloads/bitpayApi.pdf
 *
 * @param string $orderId, string $price, string $posData, array $options
 * @return array $response
 * @throws Exception $e
 *
 */
function bpCreateInvoice($orderId, $price, $posData = '', $options = array()) {

  /* 
   * $orderId: Used to display an orderID to the buyer. In the account summary view, this value is used to
   * identify a ledger entry if present. Maximum length is 100 characters.
   *
   * $price: by default, $price is expressed in the currency you set in bp_options.php.  The currency can be
   * changed in $options.
   *
   * $posData: this field is included in status updates or requests to get an invoice.  It is intended to be used by
   * the merchant to uniquely identify an order associated with an invoice in their system.  Aside from that, BitPay does
   * not use the data in this field.  The data in this field can be anything that is meaningful to the merchant.
   * Maximum length is 100 characters.
   *
   * Note:  Using the posData hash option will APPEND the hash to the posData field and could push you over the 100
   *        character limit.
   *
   *
   * $options keys can include any of:
   *	'itemDesc', 'itemCode', 'notificationEmail', 'notificationURL', 'redirectURL', 'apiKey'
   *	'currency', 'physical', 'fullNotifications', 'transactionSpeed', 'buyerName',
   *	'buyerAddress1', 'buyerAddress2', 'buyerCity', 'buyerState', 'buyerZip', 'buyerEmail', 'buyerPhone'
   *
   * If a given option is not provided here, the value of that option will default to what is found in bp_options.php
   * (see api documentation for information on these options).
   *
   */

  global $bpOptions;	

  if (!isset($orderId) || is_null($orderId) || trim($orderId) == '' || empty($orderId))
    return 'Error in bpCreateInvoice(): No orderId supplied to function. Usage: bpCreateInvoice($orderId, $price, $posData, $options)';

  if (!isset($price) || is_null($price) || trim($price) == '' || empty($price))
    return 'Error in bpCreateInvoice(): No price supplied to function.  Usage: bpCreateInvoice($orderId, $price, $posData, $options)';

  try {
    $options = array_merge($bpOptions, $options);
    $pos = array('posData' => $posData);

    if ($bpOptions['verifyPos']) 
      $pos['hash'] = bpHash(serialize($posData), $options['apiKey']);

    if (function_exists('json_encode'))
      $options['posData'] = json_encode($pos);
    else
      $options['posData'] = bpJSONencode($pos);

    if (strlen($options['posData']) > 100)
      return array('error' => 'The posData exceeds the 100 character limit. Are you using the posData hash? The hash is APPENDED to the posData string and can cause overflow.');

    $options['orderID'] = $orderId;
    $options['price'] = $price;

    $postOptions = array('orderID', 'itemDesc', 'itemCode', 'notificationEmail', 'notificationURL', 'redirectURL', 
                         'posData', 'price', 'currency', 'physical', 'fullNotifications', 'transactionSpeed', 'buyerName', 
                         'buyerAddress1', 'buyerAddress2', 'buyerCity', 'buyerState', 'buyerZip', 'buyerEmail', 'buyerPhone');

    foreach($postOptions as $o) {
      if (array_key_exists($o, $options))
        $post[$o] = $options[$o];
    }

    if (function_exists('json_encode'))
      $post = json_encode($post);
    else
      $post = bpJSONencode($post);

    $response = bpCurl('https://' . bpHost() . '/api/invoice/', $options['apiKey'], $post);

    return $response;

  } catch (Exception $e) {
    if ($bpOptions['useLogging'])
      bpLog('Error in bpCreateInvoice(): ' . $e->getMessage());

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

    /*
     * There's a PHP quirk when reading a pure JSON POST response.  The $_POST global will be empty
     * so you must read the raw POST input using file_get_contents().  For more information, see:
     * https://support.bitpay.com/hc/en-us/articles/202596678-Blank-IPN-post-response-from-BitPay-when-using-PHP
     */
    $post = file_get_contents("php://input");

    if (!$post)
      return 'Error in bpVerifyNotification(): No POST data returned.';

    if (function_exists('json_decode'))
      $json = json_decode($post, true);
    else
      $json = bpJSONdecode($post);

    if (is_string($json) || (is_array($json) && array_key_exists('error', $json)))
      return $json;

    if (!array_key_exists('posData', $json))
      return 'Error in bpVerifyNotification(): No posData found.';

    if (function_exists('json_decode'))
      $posData = json_decode($json['posData'], true);
    else
      $posData = bpJSONdecode($json['posData']);

    if ($bpOptions['verifyPos'] and $posData['hash'] != bpHash(serialize($posData['posData']), $apiKey))
      return 'Error in bpVerifyNotification(): Authentication failed (bad hash)';

    $json['posData'] = $posData['posData'];

    return $json;

  } catch (Exception $e) {
    if ($bpOptions['useLogging'])
      bpLog('Error in bpVerifyNotification(): ' . $e->getMessage());

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

  if (!isset($invoiceId) || is_null($invoiceId) || trim($invoiceId) == '' || empty($invoiceId))
    return 'Error in bpGetInvoice(): No invoiceId supplied to function. Usage: bpGetInvoice($invoiceId)';

  try {
    if (!$apiKey)
      $apiKey = $bpOptions['apiKey'];

    $response = bpCurl('https://' . bpHost() . '/api/invoice/' . $invoiceId, $apiKey);

    if (is_string($response) || (is_array($response) && array_key_exists('error', $response)))
      return $response;

    if (function_exists('json_decode'))
      $response['posData'] = json_decode($response['posData'], true);
    else
      $response['posData'] = bpJSONdecode($response['posData']);

    $response['posData'] = $response['posData']['posData'];

    return $response;

  } catch (Exception $e) {
    if ($bpOptions['useLogging'])
      bpLog('Error in bpGetInvoice(): ' . $e->getMessage());

    return 'Error in bpGetInvoice(): ' . $e->getMessage();
  }
}

/**
 *
 * Generates a base64 encoded keyed hash using the HMAC method. For more 
 * information, see: http://www.php.net/manual/en/function.hash-hmac.php
 *
 * @param string $data, string $key
 * @return string $hmac
 * @throws Exception $e
 *
 */
function bpHash($data, $key) {
  global $bpOptions;

  if (!isset($key) || is_null($key) || trim($key) == '' || empty($key))
    return 'Error in bpHash(): No key supplied to function. Usage: bpHash($data, $key)';

  if (!isset($data) || is_null($data) || trim($data) == '' || empty($data))
    return 'Error in bpHash(): No data supplied to function. Usage: bpHash($data, $key)';

  try {
    $hmac = base64_encode(hash_hmac('sha256', $data, $key, TRUE));

    return strtr($hmac, array('+' => '-', '/' => '_', '=' => ''));

  } catch (Exception $e) {
    if ($bpOptions['useLogging'])
      bpLog('Error in bpHash(): ' . $e->getMessage());

    return 'Error in bpHash(): ' . $e->getMessage();
  }
}

/**
 * 
 * Decodes JSON response and returns an associative array.
 * 
 * @param string $response
 * @return array $arrResponse
 * @throws Exception $e
 * 
 */
function bpDecodeResponse($response) {
  global $bpOptions;
  
  try {

    if (!is_string($response) || is_null($response) || trim($response) == '' || empty($response))
      return 'Error in bpDecodeResponse(): Missing response string parameter. Usage: bpDecodeResponse($response)';

    if (function_exists('json_decode'))
      return json_decode($response, true);
    else
      return bpJSONdecode($response);

  } catch (Exception $e) {
    if ($bpOptions['useLogging'])
      bpLog('Error in bpDecodeResponse(): ' . $e->getMessage());

    return 'Error in bpDecodeResponse(): ' . $e->getMessage();
  }
}

/**
 *
 * Retrieves a list of all supported currencies and returns an associative array.
 * 
 * @param none
 * @return array $currencies
 * @throws Exception $e
 * 
 */
function bpCurrencyList() {
  global $bpOptions;

  $currencies = array();
  $rate_url = 'https://' . bpHost() . '/api/rates';

  try {

    if (function_exists('json_decode'))
      $clist = json_decode(file_get_contents($rate_url),true);
    else
      $clist = bpJSONdecode(file_get_contents($rate_url));

    foreach($clist as $key => $value)
      $currencies[$value['code']] = $value['name'];

    return $currencies;

  } catch (Exception $e) {
    if ($bpOptions['useLogging'])
      bpLog('Error in bpCurrencyList(): ' . $e->getMessage());

    return 'Error in bpCurrencyList(): ' . $e->getMessage();
  }
}

/**
 * 
 * Retrieves the current rate based on $code. The default code us USD, so calling the 
 * function without a parameter will return the current BTC/USD price.
 * 
 * @param string $code
 * @return string $rate
 * @throws Exception $e
 * 
 */
function bpGetRate($code = 'USD') {
  global $bpOptions;

  $rate = '';
  $clist = '';
  $rate_url = 'https://' . bpHost() . '/api/rates';

  try {

    if (function_exists('json_decode'))
      $clist = json_decode(file_get_contents($rate_url), true);
    else
      $clist = bpJSONdecode(file_get_contents($rate_url));

    foreach($clist as $key => $value) {
      if ($value['code'] == $code)
        $rate = number_format($value['rate'], 2, '.', '');
    }

    return $rate;

  } catch (Exception $e) {
    if ($bpOptions['useLogging'])
      bpLog('Error in bpGetRate(): ' . $e->getMessage());

    return 'Error in bpGetRate(): ' . $e->getMessage();
  }
}

/**
 * 
 * Fallback JSON decoding function in the event you do not have the PHP JSON extension installed and
 * cannot install it.  This function takes an encoded string and returns an associative array.
 * 
 * @param string $jsondata
 * @return array $jsonarray
 */
function bpJSONdecode($jsondata) {
  $jsondata = trim(stripcslashes(str_ireplace('"', '', str_ireplace('\'', '', $jsondata))));
  $jsonarray = array();
  $level = 0;

  if (!is_string($jsondata) || is_null($jsondata) || trim($jsondata) == '' || empty($jsondata))
    return false;

  if ($jsondata[0] == '[')
    $jsondata = trim(substr($jsondata, 1, strlen($jsondata)));

  if ($jsondata[0] == '{')
    $jsondata = trim(substr($jsondata, 1, strlen($jsondata)));

  if (substr($jsondata, strlen($jsondata) - 1, 1) == ']')
    $jsondata = trim(substr($jsondata, 0, strlen($jsondata) - 1));

  if (substr($jsondata, strlen($jsondata) - 1, 1) == '}')
    $jsondata = trim(substr($jsondata, 0, strlen($jsondata) - 1));

  $break = false;

  while(!$break) {
    if (stripos($jsondata,"\t") !== false)
      $jsondata = str_ireplace("\t", ' ', $jsondata);

    if (stripos($jsondata,"\r") !== false)
      $jsondata = str_ireplace("\r", '', $jsondata);

    if (stripos($jsondata,"\n") !== false)
      $jsondata = str_ireplace("\n", '', $jsondata);

    if (stripos($jsondata,' ') !== false)
      $jsondata = str_ireplace(' ', ' ', $jsondata);
    else
      $break = true;
  }

  $level = 0;
  $x = 0;
  $array = false;
  $object = false;

  while($x < strlen($jsondata)) {
    $var = '';
    $val = '';

    while($x < strlen($jsondata) && $jsondata[$x] == ' ')
      $x++;
  
    switch($jsondata[$x]) {
      case '[':
        $level++;
        break;
      case '{':
        $level++;
        break;
    }

    if ($level <= 0) {
      while($x < strlen($jsondata) && $jsondata[$x] != ':') {
        if ($jsondata[$x] != ' ') $var .= $jsondata[$x];
        $x++;
      }

      $var = trim(stripcslashes(str_ireplace('"', '', $var)));

      while($x < strlen($jsondata) && ($jsondata[$x] == ' ' || $jsondata[$x] == ':'))
        $x++;

      switch($jsondata[$x]) {
        case '[':
          $level++;
          break;
        case '{':
         $level++;
         break;
      }
   }

    if ($level > 0) {
 
      while($x< strlen($jsondata) && $level > 0) {
        $val .= $jsondata[$x];
        $x++;

        switch($jsondata[$x]) {
          case '[':
            $level++;
            break;
          case '{':
            $level++;
            break;
          case ']':
            $level--;
            break;
          case '}':
            $level--;
            break;
        }
      }

      if ($jsondata[$x] == ']' || $jsondata[$x] == '}')
        $val .= $jsondata[$x];

      $val = trim(stripcslashes(str_ireplace('"', '', $val)));

      while($x < strlen($jsondata) && ($jsondata[$x] == ' ' || $jsondata[$x] == ',' || $jsondata[$x] == ']' || $jsondata[$x] == '}'))
        $x++;
  
    } else {

      while($x < strlen($jsondata) && $jsondata[$x] != ',') {
        $val .= $jsondata[$x];
        $x++;
      }

      $val = trim(stripcslashes(str_ireplace('"', '', $val)));

      while($x < strlen($jsondata) && ($jsondata[$x] == ' ' || $jsondata[$x] == ','))
        $x++;
    }

    $jsonarray[$var] = $val;

    if ($level < 0) $level = 0;
  }

  return $jsonarray;

}

/**
 * 
 * Fallback JSON encoding function in the event you do not have the PHP JSON extension installed and
 * cannot install it.  This function takes data in various forms and returns a JSON encoded string.
 * 
 * @param mixed $data
 * @return string $jsondata
 * 
 */
function bpJSONencode($data) {
  if (is_array($data)) {
    $jsondata = '{';

    foreach($data as $key => $value) {
      $jsondata .= '"' . $key . '": ';

      if (is_array($value))
        $jsondata .= bpJSONencode($value) . ', ';

      if (is_numeric($value))
        $jsondata .= $value . ', ';

      if (is_string($value))
        $jsondata .= '"' . $value . '", ';

      if (is_bool($value)) {
        if ($value)
          $jsondata .= 'true, ';
        else
          $jsondata .= 'false, ';
      }

      if (is_null($value))
        $jsondata .= 'null, ';
    }

    $jsondata = substr($jsondata, 0, strlen($jsondata) - 2);
    $jsondata .= '}';

  } else {
    $jsondata = '{"' . $data . '"}';
  }

  return $jsondata;
}

/* END bp_lib.php */
