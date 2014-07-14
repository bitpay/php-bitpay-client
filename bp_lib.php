<?php

/**
 * Copyright (c) 2011-2014 BITPAY, INC.
 *  
 * Bitcoin PHP payment library using the bitpay.com service. You can always 
 * download the latest version at https://github.com/bitpay/php-client
 * 
 * PHP Version 5
 * 
 * License: Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all 
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @category   Bitcoin
 * @package    Bitcoin\BitPay\PHP-Client-Library
 * @author     Rich Morgan <rich@bitpay.com>
 * @copyright  2014 BITPAY, INC.
 * @license    http://opensource.org/licenses/MIT  The MIT License (MIT)
 * @version    Release 1.9
 * @link       https://github.com/bitpay/php-client
 * @since      File available since Release 0.1
 */

require_once 'bp_options.php';

/*
 * BitPay PHP Client Library Version
 */
define('VERSION', '1.9');


/**
 * Writes $contents to the system logger which is usually the webserver's error log 
 * but can be changed depending on your development requirements.
 *
 * @param mixed $contents
 * @return boolean
 * @throws Exception $e 
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
    if(isset($contents)) {
      if(is_resource($contents))
        return error_log(serialize($contents));
      else
        return error_log(var_dump($contents, true));
    } else {
      return false;
    }

  } catch (Exception $e) {
    echo 'Error in bpLog(): ' . $e->getMessage();
  }
}

/**
 * Returns the correct API service endpoint hostname depending on whether the
 * production or test environment is selected.
 *
 * @param none
 * @return string $host
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
 * Handles post/get to BitPay via curl.
 *
 * @param string $url, string $apiKey, boolean $post
 * @return mixed $response
 * @throws Exception $e
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
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);

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
          if(function_exists('curl_strerror'))
            $curl_error_description = curl_strerror($curl_error_number);
       	  else
       	    $curl_error_description = bpCurlStrerror($curl_error_number);

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
 * Creates BitPay invoice via bpCurl.  More information regarding the various options are explained
 * below.  For the official API documentation, see: https://bitpay.com/downloads/bitpayApi.pdf
 *
 * @param string $orderId, string $price, string $posData, array $options
 * @return array $response
 * @throws Exception $e
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
   * $options keys can include any of:
   *	'itemDesc', 'itemCode', 'notificationEmail', 'notificationURL', 'redirectURL', 'apiKey'
   *	'currency', 'physical', 'fullNotifications', 'transactionSpeed', 'buyerName',
   *	'buyerAddress1', 'buyerAddress2', 'buyerCity', 'buyerState', 'buyerZip', 'buyerEmail', 'buyerPhone'
   *
   * If a given option is not provided here, the value of that option will default to what is found in bp_options.php
   * (see api documentation for information on these options).
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
 * Call from your notification handler to convert $_POST data to an object containing invoice data
 *
 * @param boolean $apiKey
 * @return mixed $json
 * @throws Exception $e
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
 * Retrieves an invoice from BitPay.  $options can include 'apiKey'
 *
 * @param string $invoiceId, boolean $apiKey
 * @return mixed $json
 * @throws Exception $e
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
 * Generates a base64 encoded keyed hash using the HMAC method. For more 
 * information, see: http://www.php.net/manual/en/function.hash-hmac.php
 *
 * @param string $data, string $key
 * @return string $hmac
 * @throws Exception $e
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
 * Decodes JSON response and returns an associative array.
 * 
 * @param string $response
 * @return array $arrResponse
 * @throws Exception $e
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
 * Retrieves a list of all supported currencies and returns an associative array.
 * 
 * @param none
 * @return array $currencies
 * @throws Exception $e
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
 * Retrieves the current rate based on $code. The default code us USD, so calling the 
 * function without a parameter will return the current BTC/USD price.
 * 
 * @param string $code
 * @return string $rate
 * @throws Exception $e
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
 * Fallback JSON encoding function in the event you do not have the PHP JSON extension installed and
 * cannot install it.  This function takes data in various forms and returns a JSON encoded string.
 * 
 * @param mixed $data
 * @return string $jsondata
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

/**
 * Fallback cURL error string function in the event you do not have PHP >= 5.5.0 installed.  These cURL 
 * error codes and descriptions were retrieved from the official libcurl error documentation.  See: 
 * http://curl.haxx.se/libcurl/c/libcurl-errors.html
 *
 * @param integer $errorno
 * @return string $error_description
 */
function bpCurlStrerror($errorno) {
	$error_description = '';

	if (!is_int($errorno) || is_null($errorno) || empty($errorno))
		return 'Error in bpCurlStrerror(): No error number integer passed to function. Usage: bpCurlStrerror($errorno)';

	switch($errorno) {
		case 0:
			/*
			 * CURLE_OK (0)
			 */
			$error_description = 'CURLE_OK: All fine. Proceed as usual.';
			break;
		case 1:
			/*
			 * CURLE_UNSUPPORTED_PROTOCOL (1)
			 */
			$error_description = 'CURLE_UNSUPPORTED_PROTOCOL: The URL you passed to libcurl used a protocol that this libcurl does not support. The support might be a compile-time option that you didn\'t use, it can be a misspelled protocol string or just a protocol libcurl has no code for.';
			break;
		case 2:
			/*
			 * CURLE_FAILED_INIT (2)
			 */
			$error_description = 'CURLE_FAILED_INIT: Very early initialization code failed. This is likely to be an internal error or problem, or a resource problem where something fundamental couldn\'t get done at init time.';
			break;
		case 3:
			/*
			 * CURLE_URL_MALFORMAT (3)
			 */
			$error_description = 'CURLE_URL_MALFORMAT: The URL was not properly formatted.';
			break;
		case 4:
			/*
			 * CURLE_NOT_BUILT_IN (4)
			 */
			$error_description = 'CURLE_NOT_BUILT_IN: A requested feature, protocol or option was not found built-in in this libcurl due to a build-time decision. This means that a feature or option was not enabled or explicitly disabled when libcurl was built and in order to get it to function you have to get a rebuilt libcurl.';
			break;
		case 5:
			/*
			 * CURLE_COULDNT_RESOLVE_PROXY (5)
			 */
			$error_description = 'CURLE_COULDNT_RESOLVE_PROXY: Couldn\'t resolve proxy. The given proxy host could not be resolved.';
			break;
		case 6:
			/*
			 * CURLE_COULDNT_RESOLVE_HOST (6)
			 */
			$error_description = 'CURLE_COULDNT_RESOLVE_HOST: Couldn\'t resolve host. The given remote host was not resolved.';
			break;
		case 7:
			/*
			 * CURLE_COULDNT_CONNECT (7)
			 */
			$error_description = 'CURLE_COULDNT_CONNECT: Failed to connect() to host or proxy.';
			break;
		case 8:
			/*
			 * CURLE_FTP_WEIRD_SERVER_REPLY (8)
			 */
			$error_description = 'CURLE_FTP_WEIRD_SERVER_REPLY: After connecting to a FTP server, libcurl expects to get a certain reply back. This error code implies that it got a strange or bad reply. The given remote server is probably not an OK FTP server.';
			break;
		case 9:
			/*
			 * CURLE_REMOTE_ACCESS_DENIED (9)
			 */
			$error_description = 'CURLE_REMOTE_ACCESS_DENIED: We were denied access to the resource given in the URL. For FTP, this occurs while trying to change to the remote directory.';
			break;
		case 10:
			/*
			 * CURLE_FTP_ACCEPT_FAILED (10)
			 */
			$error_description = 'CURLE_FTP_ACCEPT_FAILED: While waiting for the server to connect back when an active FTP session is used, an error code was sent over the control connection or similar.';
			break;
		case 11:
			/*
			 * CURLE_FTP_WEIRD_PASS_REPLY (11)
			 */
			$error_description = 'CURLE_FTP_WEIRD_PASS_REPLY: After having sent the FTP password to the server, libcurl expects a proper reply. This error code indicates that an unexpected code was returned.';
			break;
		case 12:
			/*
			 * CURLE_FTP_ACCEPT_TIMEOUT (12)
			 */
			$error_description = 'CURLE_FTP_ACCEPT_TIMEOUT: During an active FTP session while waiting for the server to connect, the CURLOPT_ACCEPTTIMOUT_MS(3) (or the internal default) timeout expired.';
			break;
		case 13:
			/*
			 * CURLE_FTP_WEIRD_PASV_REPLY (13)
			 */
			$error_description = 'CURLE_FTP_WEIRD_PASV_REPLY: libcurl failed to get a sensible result back from the server as a response to either a PASV or a EPSV command. The server is flawed.';
			break;
		case 14:
			/*
			 * CURLE_FTP_WEIRD_227_FORMAT (14)
			 */
			$error_description = 'CURLE_FTP_WEIRD_227_FORMAT: FTP servers return a 227-line as a response to a PASV command. If libcurl fails to parse that line, this return code is passed back.';
			break;
		case 15:
			/*
			 * CURLE_FTP_CANT_GET_HOST (15)
			 */
			$error_description = 'CURLE_FTP_CANT_GET_HOST: An internal failure to lookup the host used for the new connection.';
			break;
		case 17:
			/*
			 * CURLE_FTP_COULDNT_SET_TYPE (17)
			 */
			$error_description = 'CURLE_FTP_COULDNT_SET_TYPE: Received an error when trying to set the transfer mode to binary or ASCII.';
			break;
		case 18:
			/*
			 * CURLE_PARTIAL_FILE (18)
			 */
			$error_description = 'CURLE_PARTIAL_FILE: A file transfer was shorter or larger than expected. This happens when the server first reports an expected transfer size, and then delivers data that doesn\'t match the previously given size.';
			break;
		case 19:
			/*
			 * CURLE_FTP_COULDNT_RETR_FILE (19)
			 */
			$error_description = 'CURLE_FTP_COULDNT_RETR_FILE: This was either a weird reply to a \'RETR\' command or a zero byte transfer complete.';
			break;
		case 21:
			/*
			 * CURLE_QUOTE_ERROR (21)
			 */
			$error_description = 'CURLE_QUOTE_ERROR: When sending custom "QUOTE" commands to the remote server, one of the commands returned an error code that was 400 or higher (for FTP) or otherwise indicated unsuccessful completion of the command.';
			break;
		case 22:
			/*
			 * CURLE_HTTP_RETURNED_ERROR (22)
			 */
			$error_description = 'CURLE_HTTP_RETURNED_ERROR: This is returned if CURLOPT_FAILONERROR is set TRUE and the HTTP server returns an error code that is >= 400.';
			break;
		case 23:
			/*
			 * CURLE_WRITE_ERROR (23)
			 */
			$error_description = 'CURLE_WRITE_ERROR: An error occurred when writing received data to a local file, or an error was returned to libcurl from a write callback.';
			break;
		case 25:
			/*
			 * CURLE_UPLOAD_FAILED (25)
			 */
			$error_description = 'CURLE_UPLOAD_FAILED: Failed starting the upload. For FTP, the server typically denied the STOR command. The error buffer usually contains the server\'s explanation for this.';
			break;
		case 26:
			/*
			 * CURLE_READ_ERROR (26)
			 */
			$error_description = 'CURLE_READ_ERROR: There was a problem reading a local file or an error returned by the read callback.';
			break;
		case 27:
			/*
			 * CURLE_OUT_OF_MEMORY (27)
			 */
			$error_description = 'CURLE_OUT_OF_MEMORY: A memory allocation request failed. This is serious badness and things are severely screwed up if this ever occurs.';
			break;
		case 28:
			/*
			 * CURLE_OPERATION_TIMEDOUT (28)
			 */
			$error_description = 'CURLE_OPERATION_TIMEDOUT: Operation timeout. The specified time-out period was reached according to the conditions.';
			break;
		case 30:
			/*
			 * CURLE_FTP_PORT_FAILED (30)
			 */
			$error_description = 'CURLE_FTP_PORT_FAILED: The FTP PORT command returned error. This mostly happens when you haven\'t specified a good enough address for libcurl to use. See CURLOPT_FTPPORT.';
			break;
		case 31:
			/*
			 * CURLE_FTP_COULDNT_USE_REST (31)
			 */
			$error_description = 'CURLE_FTP_COULDNT_USE_REST: The FTP REST command returned error. This should never happen if the server is sane.';
			break;
		case 33:
			/*
			 * CURLE_RANGE_ERROR (33)
			 */
			$error_description = 'CURLE_RANGE_ERROR: The server does not support or accept range requests.';
			break;
		case 34:
			/*
			 * CURLE_HTTP_POST_ERROR (34)
			 */
			$error_description = 'CURLE_HTTP_POST_ERROR: This is an odd error that mainly occurs due to internal confusion.';
			break;
		case 35:
			/*
			 * CURLE_SSL_CONNECT_ERROR (35)
			 */
			$error_description = 'CURLE_SSL_CONNECT_ERROR: A problem occurred somewhere in the SSL/TLS handshake. You really want the error buffer and read the message there as it pinpoints the problem slightly more. Could be certificates (file formats, paths, permissions), passwords, and others.';
			break;
		case 36:
			/*
			 * CURLE_BAD_DOWNLOAD_RESUME (36)
			 */
			$error_description = 'CURLE_BAD_DOWNLOAD_RESUME: The download could not be resumed because the specified offset was out of the file boundary.';
			break;
		case 37:
			/*
			 * CURLE_FILE_COULDNT_READ_FILE (37)
			 */
			$error_description = 'CURLE_FILE_COULDNT_READ_FILE: A file given with FILE:// couldn\'t be opened. Most likely because the file path doesn\'t identify an existing file. Did you check file permissions?';
			break;
		case 38:
			/*
			 * CURLE_LDAP_CANNOT_BIND (38)
			 */
			$error_description = 'CURLE_LDAP_CANNOT_BIND: LDAP cannot bind. LDAP bind operation failed.';
			break;
		case 39:
			/*
			 * CURLE_LDAP_SEARCH_FAILED (39)
			 */
			$error_description = 'CURLE_LDAP_SEARCH_FAILED: LDAP search failed.';
			break;
		case 41:
			/*
			 * CURLE_FUNCTION_NOT_FOUND (41)
			 */
			$error_description = 'CURLE_FUNCTION_NOT_FOUND: Function not found. A required zlib function was not found.';
			break;
		case 42:
			/*
			 * CURLE_ABORTED_BY_CALLBACK (42)
			 */
			$error_description = 'CURLE_ABORTED_BY_CALLBACK: Aborted by callback. A callback returned "abort" to libcurl.';
			break;
		case 43:
			/*
			 * CURLE_BAD_FUNCTION_ARGUMENT (43)
			 */
			$error_description = 'CURLE_BAD_FUNCTION_ARGUMENT: Internal error. A function was called with a bad parameter.';
			break;
		case 45:
			/*
			 * CURLE_INTERFACE_FAILED (45)
			 */
			$error_description = 'CURLE_INTERFACE_FAILED: Interface error. A specified outgoing interface could not be used. Set which interface to use for outgoing connections\' source IP address with CURLOPT_INTERFACE.';
			break;
		case 47:
			/*
			 * CURLE_TOO_MANY_REDIRECTS (47)
			 */
			$error_description = 'CURLE_TOO_MANY_REDIRECTS: Too many redirects. When following redirects, libcurl hit the maximum amount. Set your limit with CURLOPT_MAXREDIRS.';
			break;
		case 48:
			/*
			 * CURLE_UNKNOWN_OPTION (48)
			 */
			$error_description = 'CURLE_UNKNOWN_OPTION: An option passed to libcurl is not recognized/known. Refer to the appropriate documentation. This is most likely a problem in the program that uses libcurl. The error buffer might contain more specific information about which exact option it concerns.';
			break;
		case 49:
			/*
			 * CURLE_TELNET_OPTION_SYNTAX (49)
			 */
			$error_description = 'CURLE_TELNET_OPTION_SYNTAX: A telnet option string was Illegally formatted.';
			break;
		case 51:
			/*
			 * CURLE_PEER_FAILED_VERIFICATION (51)
			 */
			$error_description = 'CURLE_PEER_FAILED_VERIFICATION: The remote server\'s SSL certificate or SSH md5 fingerprint was deemed not OK.';
			break;
		case 52:
			/*
			 * CURLE_GOT_NOTHING (52)
			 */
			$error_description = 'CURLE_GOT_NOTHING: Nothing was returned from the server, and under the circumstances, getting nothing is considered an error.';
			break;
		case 53:
			/*
			 * CURLE_SSL_ENGINE_NOTFOUND (53)
			 */
			$error_description = 'CURLE_SSL_ENGINE_NOTFOUND: The specified crypto engine wasn\'t found.';
			break;
		case 54:
			/*
			 * CURLE_SSL_ENGINE_SETFAILED (54)
			 */
			$error_description = 'CURLE_SSL_ENGINE_SETFAILED: Failed setting the selected SSL crypto engine as default!';
			break;
		case 55:
			/*
			 * CURLE_SEND_ERROR (55)
			 */
			$error_description = 'CURLE_SEND_ERROR: Failed sending network data.';
			break;
		case 56:
			/*
			 * CURLE_RECV_ERROR (56)
			 */
			$error_description = 'CURLE_RECV_ERROR: Failure with receiving network data.';
			break;
		case 58:
			/*
			 * CURLE_SSL_CERTPROBLEM (58)
			 */
			$error_description = 'CURLE_SSL_CERTPROBLEM: Problem with the local client certificate.';
			break;
		case 59:
			/*
			 * CURLE_SSL_CIPHER (59)
			 */
			$error_description = 'CURLE_SSL_CIPHER: Couldn\'t use specified cipher.';
			break;
		case 60:
			/*
			 * CURLE_SSL_CACERT (60)
			 */
			$error_description = 'CURLE_SSL_CACERT: Peer certificate cannot be authenticated with known CA certificates.';
			break;
		case 61:
			/*
			 * CURLE_BAD_CONTENT_ENCODING (61)
			 */
			$error_description = 'CURLE_BAD_CONTENT_ENCODING: Unrecognized transfer encoding.';
			break;
		case 62:
			/*
			 * CURLE_LDAP_INVALID_URL (62)
			 */
			$error_description = 'CURLE_LDAP_INVALID_URL: Invalid LDAP URL.';
			break;
		case 63:
			/*
			 * CURLE_FILESIZE_EXCEEDED (63)
			 */
			$error_description = 'CURLE_FILESIZE_EXCEEDED: Maximum file size exceeded.';
			break;
		case 64:
			/*
			 * CURLE_USE_SSL_FAILED (64)
			 */
			$error_description = 'CURLE_USE_SSL_FAILED: Requested FTP SSL level failed.';
			break;
		case 65:
			/*
			 * CURLE_SEND_FAIL_REWIND (65)
			 */
			$error_description = 'CURLE_SEND_FAIL_REWIND: When doing a send operation curl had to rewind the data to retransmit, but the rewinding operation failed.';
			break;
		case 66:
			/*
			 * CURLE_SSL_ENGINE_INITFAILED (66)
			 */
			$error_description = 'CURLE_SSL_ENGINE_INITFAILED: Initiating the SSL Engine failed.';
			break;
		case 67:
			/*
			 * CURLE_LOGIN_DENIED (67)
			 */
			$error_description = 'CURLE_LOGIN_DENIED: The remote server denied curl to login (Added in 7.13.1)';
			break;
		case 68:
			/*
			 * CURLE_TFTP_NOTFOUND (68)
			 */
			$error_description = 'CURLE_TFTP_NOTFOUND: File not found on TFTP server.';
			break;
		case 69:
			/*
			 * CURLE_TFTP_PERM (69)
			 */
			$error_description = 'CURLE_TFTP_PERM: Permission problem on TFTP server.';
			break;
		case 70:
			/*
			 * CURLE_REMOTE_DISK_FULL (70)
			 */
			$error_description = 'CURLE_REMOTE_DISK_FULL: Out of disk space on the server.';
			break;
		case 71:
			/*
			 * CURLE_TFTP_ILLEGAL (71)
			 */
			$error_description = 'CURLE_TFTP_ILLEGAL: Illegal TFTP operation.';
			break;
		case 72:
			/*
			 * CURLE_TFTP_UNKNOWNID (72)
			 */
			$error_description = 'CURLE_TFTP_UNKNOWNID: Unknown TFTP transfer ID.';
			break;
		case 73:
			/*
			 * CURLE_REMOTE_FILE_EXISTS (73)
			 */
			$error_description = 'CURLE_REMOTE_FILE_EXISTS: File already exists and will not be overwritten.';
			break;
		case 74:
			/*
			 * CURLE_TFTP_NOSUCHUSER (74)
			 */
			$error_description = 'CURLE_TFTP_NOSUCHUSER: This error should never be returned by a properly functioning TFTP server.';
			break;
		case 75:
			/*
			 * CURLE_CONV_FAILED (75)
			 */
			$error_description = 'CURLE_CONV_FAILED: Character conversion failed.';
			break;
		case 76:
			/*
			 * CURLE_CONV_REQD (76)
			 */
			$error_description = 'CURLE_CONV_REQD: Caller must register conversion callbacks.';
			break;
		case 77:
			/*
			 * CURLE_SSL_CACERT_BADFILE (77)
			 */
			$error_description = 'CURLE_SSL_CACERT_BADFILE: Problem with reading the SSL CA cert (path? access rights?)';
			break;
		case 78:
			/*
			 * CURLE_REMOTE_FILE_NOT_FOUND (78)
			 */
			$error_description = 'CURLE_REMOTE_FILE_NOT_FOUND: The resource referenced in the URL does not exist.';
			break;
		case 79:
			/*
			 * CURLE_SSH (79)
			 */
			$error_description = 'CURLE_SSH: An unspecified error occurred during the SSH session.';
			break;
		case 80:
			/*
			 * CURLE_SSL_SHUTDOWN_FAILED (80)
			 */
			$error_description = 'CURLE_SSL_SHUTDOWN_FAILED: Failed to shut down the SSL connection.';
			break;
		case 81:
			/*
			 * CURLE_AGAIN (81)
			 */
			$error_description = 'CURLE_AGAIN: Socket is not ready for send/recv wait till it\'s ready and try again. This return code is only returned from curl_easy_recv and curl_easy_send (Added in 7.18.2)';
			break;
		case 82:
			/*
			 * CURLE_SSL_CRL_BADFILE (82)
			 */
			$error_description = 'CURLE_SSL_CRL_BADFILE: Failed to load CRL file (Added in 7.19.0)';
			break;
		case 83:
			/*
			 * CURLE_SSL_ISSUER_ERROR (83)
			 */
			$error_description = 'CURLE_SSL_ISSUER_ERROR: Issuer check failed (Added in 7.19.0)';
			break;
		case 84:
			/*
			 * CURLE_FTP_PRET_FAILED (84)
			 */
			$error_description = 'CURLE_FTP_PRET_FAILED: The FTP server does not understand the PRET command at all or does not support the given argument. Be careful when using CURLOPT_CUSTOMREQUEST, a custom LIST command will be sent with PRET CMD before PASV as well. (Added in 7.20.0)';
			break;
		case 85:
			/*
			 * CURLE_RTSP_CSEQ_ERROR (85)
			 */
			$error_description = 'CURLE_RTSP_CSEQ_ERROR: Mismatch of RTSP CSeq numbers.';
			break;
		case 86:
			/*
			 * CURLE_RTSP_SESSION_ERROR (86)
			 */
			$error_description = 'CURLE_RTSP_SESSION_ERROR: Mismatch of RTSP Session Identifiers.';
			break;
		case 87:
			/*
			 * CURLE_FTP_BAD_FILE_LIST (87)
			 */
			$error_description = 'CURLE_FTP_BAD_FILE_LIST: Unable to parse FTP file list (during FTP wildcard downloading).';
			break;
		case 88:
			/*
			 * CURLE_CHUNK_FAILED (88)
			 */
			$error_description = 'CURLE_CHUNK_FAILED: Chunk callback reported error.';
			break;
		case 89:
			/*
			 * CURLE_NO_CONNECTION_AVAILABLE (89) - Added for completeness.
			 */
			$error_description = 'CURLE_NO_CONNECTION_AVAILABLE: (For internal use only, will never be returned by libcurl) No connection available, the session will be queued. (added in 7.30.0)';
			break;
		default:
			$error_description = 'UNKNOWN CURL ERROR NUMBER: This error code is not mapped to any known error.  Possibly a system error?';
			break;
	}

	return $error_description;
}

/* END bp_lib.php */
