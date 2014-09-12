<?php

namespace BitPay;

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */


/**
 * This is a self-contained PHP implementation of BitPay's new cryptographically
 * secure API: https://test.bitpay.com/api.
 *
 * NOTE: This API is currently only available in our test environment and is not
 * yet ready for production. Some or  all of the API calls may change before the
 * official release.
 *
 * PHP version 5
 *
 * LICENSE: This file is subject to the MIT License (MIT) which is available
 * at the following URI: http://opensource.org/licenses/MIT. Permission is hereby
 * granted, free of charge, to any person obtaining a copy of this software and
 * associated documentation files ( the "Software" ), to deal in the Software
 * without restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies of the
 * Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions: The above copyright notice and this
 * permission notice shall be included in all copies or substantial portions of
 * the Software. THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
 * IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category   Bitcoin
 * @package    Bitcoin\BitPay\PHP-APIv2-Client
 * @author     Rich Morgan <rich@bitpay.com>
 * @copyright  2014 BITPAY, INC.
 * @license    http://opensource.org/licenses/MIT  The MIT License (MIT)
 * @version    Release 0.1
 * @link       https://github.com/ionux/<<TODO: Name>>
 * @since      File available since Release 0.1
 */


/*
 * Date functions require the timezone be set. This call
 * will set the timezone used by this script and not
 * cause any spurious warning messages in the web server
 * log file.
 */
date_default_timezone_set( @date_default_timezone_get() );


/*
 * For debugging purposes, this is set to a high value.
 */
set_time_limit( 7200 );


/*
 * Clears the file status cache to prevent cached
 * information from being used in the event the file
 * being referenced has been changed.
 */
clearstatcache();


/*
 * The following three lines control error message
 * display during develpment. In production environments
 * all error reporting should be via the system logger
 * and not displayed to the end-user for security
 * reasons.
 */
error_reporting( E_ALL );
ini_set( 'display_errors'        , TRUE );
ini_set( 'display_startup_errors', TRUE );


/*
 * Constants used by Bitcoin address and SIN (key) creation
 */
define( 'ADDRESSVERSION', '00' );
define( 'SINTYPE'       , '02' );
define( 'SINVERSION'    , '0F' );


/*
 * The GMP PHP extension is required for the elliptic curve math functions,
 * see: http://www.php.net/manual/en/book.gmp.php. Future versions of this
 * library may add support for the BC Math extension.
 */
if ( function_exists( 'gmp_cmp' ) ) {
	define( 'MATH_TYPE', 'GMP' );
} else {
	die ( 'FATAL: This class requires the GMP math extension to be installed. Please contact your web hosting support.' );
}


/*
 * This function is used to generate the cryptographically-strong
 * random number value. see: http://us3.php.net/openssl_random_pseudo_bytes
 */
if ( !function_exists( 'openssl_random_pseudo_bytes' ) ) {
	die ( 'FATAL: OpenSSL PHP extension missing. Please install this extension to use the ECCkeygen class.' );
}


/**
 * This is a self-contained PHP implementation of BitPay's new cryptographically
 * secure API.
 *
 * @category   Bitcoin
 * @package    Bitcoin\BitPay\PHP-APIv2-Client
 * @author     Rich Morgan <rich@bitpay.com>
 * @copyright  2014 BITPAY, INC.
 * @license    http://opensource.org/licenses/MIT  The MIT License (MIT)
 * @version    Release 0.1
 * @link       https://github.com/ionux/<<TODO: Name>>
 * @since      Class available since Release 0.1
 */
class BitPay {

	/*
	 * Elliptic curve parameters for secp256k1, for more information see:
	 * http://www.secg.org/collateral/sec2_final.pdf
	 *
	 * The elliptic curve domain parameters over Fp associated with a Koblitz
	 * curve secp256k1are speciﬁed by the sextuple T = ( p; a; b; G; n; h )
	 * where the ﬁnite ﬁeld F p is deﬁned by:
	 */

	/**
	 * @var string Special point on the curve
	 */
	private $Inf					= 'infinity';

	/**
	 * @var string Base point in uncompressed, hexadecimal format
	 */
	private $G						= '0479BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8';

	/**
	 * @var string Field prime in hexadecimal format
	 */
	private $p						= '0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEFFFFFC2F';

	/**
	 * @var string Order of G in hexadecimal format
	 */
	private $n						= '0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141';

	/**
	 * @var string Coefficient of x in hexadecimal format
	 */
	private $a						= '0x0';

	/**
	 * @var string Intercept in hexadecimal format
	 */
	private $b						= '0x7';

	/**
	 * @var string Cofactor of G in hexadecimal format
	 */
	private $h						= '0x1';

	/**
	 * @var boolean Used to store the tokens returned from the BitPay network
	 */
	private $getTokens				= false;

	/**
	 * @var boolean Determines whether or not to crytpographically sign requests
	 */
	private $signRequests			= false;

	/**
	 * @var boolean Flag to hold OpenSSL extension check
	 */
	private $opensslAvailable		= false;

	/**
	 * @var boolean Flag to hold CURL extension check
	 */
	private $curlAvailable			= false;

	/**
	 * @var boolean Flag to hold https check
	 */
	private $secureComms			= false;

	/**
	 * @var boolean Is this being used for testing?
	 */
	private $_testing				= true;

	/**
	 * @var string Private elliptic curve key in hexadecimal format
	 */
	private $privECKey				= '';

	/**
	 * @var string Public elliptic curve key in hexadecimal format
	 */
	private $pubECKey				= '';

	/**
	 * @var string BitPay API Host parameter
	 */
	private $apiHost				= '';

	/**
	 * @var string BitPay API Post parameter
	 */
	private $apiPort				= '';

	/**
	 * @var string Holds the GUID value used for idempotence in POST requests
	 */
	private $GUID					= '';

	/**
	 * @var string An ever-incrementing value used for API requests
	 */
	private $nonce					= '';

	/**
	 * @var string Service Identification Number, also referred to as a "client" or "key"
	 */
	private $SIN					= '';

	/**
	 * @var string ECDSA signature value in a DER-encoded format
	 */
	private $signature				= '';

	/**
	 * @var string Filename holding the EC private key, do not make this web-accessible
	 */
	private $privKeyFilename		= '';

	/**
	 * @var string Filename holding the EC public key, do not make this web-accessible
	 */
	private $pubKeyFilename			= '';

	/**
	 * @var string Directory holding the EC keyfiles, do not make this web-accessible
	 */
	private $keyfile_dir			= '';

	/**
	 * @var string Version of this library class
	 */
	private $_version				= '0.1';

	/**
	 * @var string Variable holding the facade used for certain resouces in API requests
	 */
	private $facade					= 'public';

	/**
	 * @var string Save file name for generated EC keypair resources - do not make this web-accessible
	 */
	private $defaultSaveFile		= 'bparc.rm';

	/**
	 * @var string Save file name for generated SIN (client) resource - do not make this web-accessible
	 */
	private $defaultSINFile			= 'bpsrc.rm';

	/**
	 * @var string URI for the BitPay payment API resource
	 */
	private $bitpayURL				= 'https://test.bitpay.com';

	/**
	 * @var array Container for the tokens retrieved from the gateway
	 */
	private $tokens					= array();

	/**
	 * @var array Holds error messages for application reference
	 */
	private $errors					= array();

	/**
	 * @var array Holds notice messages for application reference
	 */
	private $notices				= array();

	/**
	 * @var array Container of supported OpenSSL digest methods
	 */
	private $opensslDigestMethods	= array();

	/**
	 * @var array Container of supported OpenSSL hash algorithms
	 */
	private $hashAlgos				= array();


	/**
	 * Class constructor - initializes the environment and prepares
	 * the BitPay payment gateway class to be used.
	 *
	 * @param  string  $keyfiledir   Directory holding the EC keyfiles - do not make this web-accessible
	 * @param  string  $pubfilename  Filename holding the EC public key - do not make this web-accessible
	 * @param  string  $privfilename Filename holding the EC private key - do not make this web-accessible
	 * @param  string  $privECKey    Private elliptic curve key in hexadecimal format
	 * @param  string  $pubECKey     Public elliptic curve key in hexadecimal format
	 * @param  string  $prevSIN      Service Identification Number, also referred to as a "client" or "key"
	 * @param  string  $SINfilename  Filename for a previously generated SIN
	 * @param  array   $options      Array of option class overrides
	 * @param  boolean $testing      Testing flag used for development
     * @return void
     * @access public
     * @since  Method available since Release 0.1
	 */
	public function __construct ( $keyfiledir   = '', $pubfilename = '',
								  $privfilename = '', $privECKey   = '',
								  $pubECKey     = '', $prevSIN     = '',
								  $SINfilename  = '', $options     = array(),
								  $testing      = false                      ) {

		/*
		 * TODO: do we need an autoloader? this class is self-contained
		 */
		if ( !spl_autoload_register( array( $this, 'autoloader' ) ) ) {
			die ( 'FATAL: Cannot register class autoloader.' );
		}


		/*
		 * private & public keys are mandatory. you can pass in your own
		 * pre-generated keypair or the class will attempt to read them from
		 * disk if the keyfile_dir and filenames are valid. lastly, it will
		 * generate a pair if that fails.
		 */
		if ( !isset( $privECKey ) || trim( $privECKey ) == '' || is_null( $privECKey ) ||
			 !isset( $pubECKey  ) || trim( $pubECKey  ) == '' || is_null( $pubECKey  )   ) {

				/*
				 * key data not directly provided, check for directory and filename
				 * values are provided.
				 */
				clearstatcache();

				if ( substr( $keyfiledir,-1 ) != '/' ) {
					$keyfiledir .= '/';
				}

				if ( substr( $privfilename,0,1 ) == '/' ) {
					$privfilename = substr( $privfilename, 1 );
				}

				if ( substr( $pubfilename,0,1 ) == '/' ) {
					$pubfilename  = substr( $pubfilename, 1 );
				}

				if ( is_dir( $keyfiledir )                  &&
					 is_file( $keyfiledir . $privfilename ) &&
					 is_file( $keyfiledir . $pubfilename  )   ) {

						/*
						 * directory and both filenames provided.
						 * attempt to read them
						 */
						if ( !is_readable( $keyfiledir . $pubfilename ) ) {
							die ( 'FATAL: The public key file "' . $keyfiledir . $pubfilename . '" is not readable. Check the file permissions.' );
						} else {
							$pubfile_contents = file_get_contents( $keyfiledir . $pubfilename );
						}

						if ( !is_readable( $keyfiledir . $privfilename ) ) {
							die ( 'FATAL: The private key file "' . $keyfiledir . $privfilename . '" is not readable. Check the file permissions.' );
						} else {
							$privfile_contents = file_get_contents( $keyfiledir . $privfilename );
						}

						/*
						 * read them successfully, now check that they are actually
						 * valid keys
						 */
						if ( $this->checkKeyfile( $pubfile_contents ) &&
							 $this->checkKeyfile( $privfile_contents )  ) {

								if ( $pubfile_contents  !== false &&
									 $privfile_contents !== false   ) {

										$this->set( 'privECkey', $this->parseKeyfile( $privfile_contents ) );
										$this->set( 'pubECkey' , $this->parseKeyfile( $pubfile_contents  ) );

										if ( $this->get( 'privECkey' ) == false ||
											 $this->get( 'pubECkey'  ) == false   ) {

												die ( 'FATAL: The format of the supplied keyfiles is unknown. Only PEM, DER and plain hex formatted keys are supported.' );

											}

										$this->set( 'keyfile_dir',     $keyfiledir   );
										$this->set( 'privKeyFilename', $privfilename );
										$this->set( 'privKeyFilename', $privfilename );

										$this->addNotice( 'Loaded keyfile data from "' . $privfilename . '" and "' . $pubfilename . '" successfully.' );

								} else {

									die ( 'FATAL: The format of the supplied keyfiles is unknown. Only PEM, DER and plain hex formatted keys are supported.' );
								}

						} else {

							die ( 'FATAL: The format of the supplied keyfiles is unknown. Only PEM, DER and plain hex formatted keys are supported.' );

						}

				} else {

					/*
					 * no keys or keyfiles specified. check for previously saved keyfile
					 */
					clearstatcache();

					if ( substr( $keyfiledir,-1 ) != '/' ) {
						$keyfiledir .= '/';
					}

					if ( !is_dir( $keyfiledir ) ) {
						die ( 'FATAL: No keyfiles, keyfile directory, or key values specified. Refer to the README.md file for usage instructions.' );
					}

					if ( !is_readable( $keyfiledir ) ) {
						die ( 'FATAL: Keyfile directory "' . $keyfiledir . '" specified but it is not readable. Check the directory permissions.' );

					}

					$savefiledata = $this->readKeypair( $keyfiledir );

					if ( $savefiledata == false ) {

						/*
						 * no keyfound from previous class instantiations. creating
						 * a new pair. but first check to see we have a valid keyfile
						 * directory specified so we can save these keys for future use.
						 */
						$this->addNotice( 'No EC keypair data or key files specified. Generating my own keypair and attempting to save to "' . 
										  $keyfiledir . $this->get( 'defaultSaveFile' ) . 
										  '".' );

						$keys = $this->generateKeypair();

						$this->set( 'keyfile_dir', $keyfiledir              );
						$this->set( 'privECKey'  , $keys['private_key_hex'] );
						$this->set( 'pubECKey'   , $keys['public_key']      );

						if ( $this->saveKeypair( $keys ) ) {

							$this->addNotice( 'Successfully saved newly generated keypair to "' .
											  $this->get( 'keyfile_dir' ) . $this->get( 'defaultSaveFile' ) .
											  '". For security reasons, ensure this file is NOT web accessible.' );

						} else {

							die ( 'FATAL: Could not save new keypair. Ensure the directory "' . $keyfiledir . '" is writable by the webserver.' );

						}

					} else {

						/*
						 * keyfile found. using those values.
						 */
						$this->set( 'keyfile_dir', $keyfiledir                      );
						$this->set( 'privECKey'  , $savefiledata['private_key_hex'] );
						$this->set( 'pubECKey'   , $savefiledata['public_key']      );

						$this->addNotice( 'No key data was provided but I found an existing keyfile in "' . $keyfiledir . '" and loaded the keypair data successfully.' );

					}

				}

		} else {

			/*
			 * keypair data provided directly. testing before assigning them
			 */
			if ( $this->parseKeyfile( $privECKey ) &&
				 $this->parseKeyfile( $pubECKey  )   ) {

					$this->set( 'privECKey', $privECKey );
					$this->set( 'pubECKey' , $pubECKey  );

					$this->addNotice( 'Key test passed. Using provided EC keypair.' );

			} else {

					die ( 'FATAL: The format of the supplied keyfiles is unknown. Only PEM, DER and plain hex formatted keys are supported.' );

			}

		}


		/*
		 * if you've generated a SIN somewhere else or want to reuse one
		 * previously generated by this class, pass it as the second
		 * parameter. otherwise we will generate a new one here.
		 *
		 * TODO: Fix to use saveSIN() & readSIN() functions.
		 */
		if ( isset( $prevSIN ) && trim( $prevSIN ) != '' ) {

			$this->set( 'SIN', $prevSIN );
			$this->addNotice( 'Loaded SIN successfully.' );

		} else {

			$this->set( 'SIN', $this->generateSIN( $pubECKey ) );
			$this->addNotice( 'No SIN specified. Generated my own.' );
			
			if ( !$this->saveSIN($this->SIN) ) {
				die( 'FATAL: Could not write the new SIN data to a file.' );
			}

		}


		/*
		 * by default, we want to sign all requests and self-initialize the client
		 * by retrieving access token from the server. these should be set to false
		 * in `options` passed if you don't have an account or don't have a sin
		 * associated with your account.
		 *
		 * TODO: This is done inside the sendRequest() function, I believe...
		 */
		$this->set( 'getTokens'   , true );
		$this->set( 'signRequests', true );


		/*
		 * override the default options if needed
		 */
		foreach ( $options as $k => $v ) {
			if ( isset( $this->$k ) ) {
				if ( !$this->set( $this->$k, $v ) ) {
					die ( 'FATAL: The option ' . $k .
						 ' is not an option or cannot be set.' );
				}
			}
		}


		/*
		 * initialize nonce
		 * TODO: This is also done inside the sendRequest() function,
		 * so I don't think I need this here...
		 */
		$this->set( 'nonce', $this->getNonce() );


		/*
		 * setup container for access tokens
		 */
		if ( $this->get( 'getTokens' ) )
			$this->set( 'tokens', $this->getAccessTokens() );


		/*
		 * determine if the openssl extension is installed
		 */
		if ( function_exists( 'openssl_error_string' ) ) {

			$this->set( 'opensslAvailable', true );

			/*
			 * populate the list of openssl digest methods
			 */
			$this->set( 'opensslDigestMethods', $this->getOpensslDigestMethods() );
			$this->addNotice( 'OpenSSL extension loaded and digest methods populated.' );

		} else {

			die ( 'FATAL: The PHP OpenSSL extension is not installed on this server. ' .
				 'Please contact your web hosting support.' );

		}


		/*
		 * determine if we can perform the required hash functions
		 * natively or have to use the fallback
		 */
		if ( function_exists( 'hash_algos' ) ) {

			$this->set( 'hashAlgos', hash_algos() );
			$this->addNotice( 'Using the native PHP hash functions.' );

		} else {

			$this->addNotice( 'The native PHP hash functions are not available. ' .
							 'Using slower fallback hash functions.' );

		}


		/*
		 * determine if the curl extension is installed
		 */
		if ( function_exists( 'curl_init' ) ) {

			$this->set( 'curlAvailable', true );

			/*
			 * check if we can talk to BitPay securely which is required per the official documentation
			 */
			$curl = curl_init();

			$header = array(
							'X-BitPay-Plugin-Info: newapiphplib' . $this->get( '_version' ),
							 );

			curl_setopt( $curl, CURLOPT_URL,            $this->get( 'bitpayURL' ) );
			curl_setopt( $curl, CURLOPT_PORT,           443                       );
			curl_setopt( $curl, CURLOPT_HTTPHEADER,     $header                   );
			curl_setopt( $curl, CURLOPT_TIMEOUT,        10                        );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 1                         );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 2                         );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true                      );
			curl_setopt( $curl, CURLOPT_FORBID_REUSE,   1                         );
			curl_setopt( $curl, CURLOPT_FRESH_CONNECT,  1                         );

			/*
			 * Returns TRUE on success or FALSE on failure. However, if the CURLOPT_RETURNTRANSFER
			 * option is set, it will return the result on success, FALSE on failure.
			 */
			if ( !curl_exec( $curl ) ) {

				$this->addError( 'Problem with the curl check: ' . curl_error( $curl ) );

			} else {

				$this->addNotice( 'Curl check passed successfully.' );
				$this->set( 'secureComms', true );

			}

			curl_close( $curl );

		} else {

			die ( 'FATAL: The PHP cURL extension is not installed on this server. ' .
				 'Please contact your web hosting support.' );

		}


		/*
		 * end of __construct() function
		 */
	}


	/**
	 * BitPay class property get magic method override
	 *
	 * @param  string         $name  Name of the variable to retrieve
	 * @return string|boolean $name  Returns variable, if exists, or false on failure
	 * @access public
	 * @since  Method available since Release 0.1
	 */
	public function __get( $name ) {

		if ( is_null( $name ) || trim( $name ) == '' )
			return false;

		if ( isset( $name ) ) {
			return $this->$name;
		} else {
			$this->addError( 'Error in get(): Property ' . $name .
							 ' does not exist. Cannot get.' );
			return false;
		}

	}


	/**
	 * BitPay class property set magic method override
	 *
	 * @param  string  $name  Name of the variable to retrieve
	 * @param  mixed   $val   Name of the variable to retrieve
	 * @return boolean $name  Returns true on success or false on failure
	 * @access public
	 * @since  Method available since Release 0.1
	 */
	public function __set( $name, $val ) {

		if ( is_null( $name ) || trim( $name ) == '' )
			return false;

		if ( isset( $name ) ) {
			$this->$name = $val;
			return true;
		} else {
			$this->addError( 'Error in set(): Property ' . $name .
							 ' does not exist. Cannot set.' );
			return false;
		}
	}


	/**
	 * BitPay class property setter
	 *
	 * @param  string  $name  Name of the variable to retrieve
	 * @param  mixed   $val   Name of the variable to retrieve
	 * @return boolean $name  Returns true on success or false on failure
	 * @access public
	 * @since  Method available since Release 0.1
	 */
	public function __unset( $name ) {

		/*
		 * we don't want anything important accidentally unset
		 */
		$this->addError( 'Error in unset(): Cannot unset properties of this class.' );

		return false;
	}


	/**
	 * Generates an elliptic curve public/private keypair
	 *
	 * @param  boolean       $verbose  Flag to show extra debugging information about the keys
	 * @return array|boolean $name     Returns array of keypair data on success or false on failure
	 * @access public
	 * @final
	 * @since  Method available since Release 0.1
	 */
	final public function generateKeypair( $verbose = false ) {

		/*
		 * generate a new EC keypair and return values. only set the
		 * verbose and testing variables to 'true' for development
		 * purposes ONLY otherwise your private key will be exposed!
		 */
		try {

			$time_start = microtime( true );
			$n_order    = $this->n;

			/*
			 * the ( x,y ) components from the uncompressed public key are the first
			 * 32-bytes and second 32-bytes after the leading 0x04 byte
			 */
			$Gx = '0x' . substr( $this->G,  2, 64 );
			$Gy = '0x' . substr( $this->G, 66, 64 );

			/*
			 * generate a crypto-strong 256-bit random number in the set [1, n-1]
			 * http://www.php.net/manual/en/function.openssl-random-pseudo-bytes.php
			 */
			do {

				$private_key = openssl_random_pseudo_bytes( 32, $cstrong );

				if ( !$cstrong ) {
					die ( 'FATAL: Could not generate cryptographically strong random number. ' .
						  'Your OpenSSL implementation may be broken or old.' );
				}

				/*
				 * convert the supplied binary data to hex for use in our crypto math functions
				 */
				$private_key_hex = self::add0x( strtoupper( bin2hex( $private_key ) ) );

			} while ( gmp_cmp( $private_key_hex, 1        ) <= 0 ||
					  gmp_cmp( $private_key_hex, $n_order ) >= 0   );

			/*
			 * initialize our secp256k1 curve point
			 */
			$P = array( 'x' => $Gx, 'y' => $Gy );

			/*
			 * R = private_key * P
			 */
			$R      = $this->doubleAndAdd( $private_key_hex, $P );
			$Rx_hex = self::encodeHex( $R['x'] );
			$Ry_hex = self::encodeHex( $R['y'] );

			/*
			 * ensure our new ( x,y ) point values are padded to 32-bytes
			 */
			while ( strlen( $Rx_hex ) < 64 )
				$Rx_hex = '0' . $Rx_hex;

			while ( strlen( $Ry_hex ) < 64 )
				$Ry_hex = '0' . $Ry_hex;

			$point_quality = $this->pointTest(
											  $R,
											  self::decodeHex( $this->a ),
											  self::decodeHex( $this->b ),
											  self::decodeHex( $this->p )
											 );

			/*
			 * capture our generation time for informational purposes only.
			 * it is safe to comment/remove the time generation code if needed.
			 */
			$time_end = microtime( true );
			$time     = $time_end - $time_start;


			/****************************************************************************
			 * You do not want to expose your private key to anyone so only uncomment
			 * this next code block if you need it for testing/debugging purposes.
			 * It is provided here as a convenience and is safe to delete entirely.
			 *
			 * **************************************************************************
			 * **************** DO NOT USE IN PRODUCTION ENVIRONMENTS!!! ****************
			 * ******************** ONLY FOR USE DURING DEVELOPMENT! ********************
			 *
			 * if ( $verbose && $testing ) {
			 * 	echo "<pre>";
			 * 	echo "Private key ( hex )  : " . substr( $private_key_hex,2 ) .        "\r\n";
			 * 	echo "Private key ( dec )  : " . self::decodeHex( $private_key_hex ) . "\r\n";
			 * 	echo "Public key  ( hex )  : 04" . $Rx_hex . "" . $Ry_hex .            "\r\n";
			 * 	echo "Public key  ( dec )  : 04" . $R['x'] . "" . $R['y'] .        "\r\n\r\n";
			 *
			 * 	if ( $point_quality )
			 * 		echo "Good point!";
			 * 	else
			 * 		echo "Bad point!";
			 *
			 * 	echo "</pre>";
			 * }
			 *
			 * **************************************************************************
			 * **************** DO NOT USE IN PRODUCTION ENVIRONMENTS!!! ****************
			 * ******************** ONLY FOR USE DURING DEVELOPMENT! ********************
			 ****************************************************************************/


			/*
			 * the pointTest() method returns true/false on success/failure
			 */
			if ( $point_quality ) {

				return array(
							 'private_key_hex'       => substr( $private_key_hex,2 ),
							 'private_key_dec'       => self::decodeHex( $private_key_hex ),
							 'public_key'            => '04' . $Rx_hex . $Ry_hex,
							 'public_key_compressed' => '02' . $Rx_hex,
							 'public_key_x'          => $Rx_hex,
							 'public_key_y'          => $Ry_hex,
							 'generation_time'       => $time
							 );

			} else {

				$this->addError( 'Error in generateKeypair(): The pointTest function failed - Do not use this EC point!' );
				return false;

			}

		} catch ( Exception $e ) {
			$this->addError( 'Error in generateKeypair(): ' . $e->getMessage() );
			return false;
		}


		/*
		 * end of generateKeypair() function
		 */
	}



	/**
	 * Public wrapper function for signature generation.
	 *
	 * @param  boolean $verbose  Name of the variable to retrieve
	 * @return boolean $name  Returns true on success or false on failure
	 * @access public
	 * @final
	 * @since  Method available since Release 0.1
	 */
	final public function generateSignature( $data, $private_key ) {


		$sig = $this->gmp_hash( $data, $private_key );

		if ( $sig != false )
			return $sig['sig_hex'];
		else
			return false;
	}


	final public function generateOpenSSLKeypair( $keybits = 512, $digest_alg = 'sha512' ) {
		/* function to generate a new RSA keypair. this is not used for point derivation
		 * or for generating signatures.  only used for assymetric data encryption, as needed
		 */

		try {

			/* see: http://www.php.net/manual/en/function.openssl-pkey-new.php */
			if ( function_exists( 'openssl_pkey_new' ) ) {
				$keypair = array();

				/* openssl keysize can't be smaller than 384 bits */
				if ( ( int )$keybits < 384 ) {
					$this->addNotice( 'generateOpenSSLKeypair: Keybits param of "' .
									 $keybits . '" is invalid. Setting to the minimum value of 384.' );

					$keybits = 384;
				}

				if ( !isset( $digest_alg ) || trim( $digest_alg ) == '' ) {
					$this->addNotice( 'generateOpenSSLKeypair: Digest algorithm missing. Using sha512.' );
					$digest_alg = 'sha512';
				}

				/* RSA is the only supported key time at this time
				 * http://www.php.net/manual/en/function.openssl-csr-new.php
				 */
				$config = array(
								'digest_alg'       => $digest_alg,
								'private_key_bits' => ( int )$keybits,
								'private_key_type' => OPENSSL_KEYTYPE_RSA
				 );

				$resource = openssl_pkey_new( $config );

				if ( !$resource ) {
					$this->addError( 'Error in generateOpenSSLKeypair: Could not create new OpenSSL resource.' );

					/* with the openssl extension, you also have it's own errors returned */
					while ( $msg = openssl_error_string() )
						$this->addError( 'Error in generateOpenSSLKeypair: OpenSSL reported error: ' . $msg );

					return false;
				}

				if ( openssl_pkey_export( $resource, $keypair['pri'] ) ) {
					$publickey      = openssl_pkey_get_details( $resource );
					$keypair['pub'] = $publickey['key'];
				} else {
					$this->addError( 'Error in generateOpenSSLKeypair: Private key could not be ' .
									'determined from OpenSSL key resource.' );

					while ( $msg = openssl_error_string() )
						$this->addError( 'Error in generateOpenSSLKeypair: OpenSSL reported error: ' . $msg );

					return false;
				}

				openssl_pkey_free( $resource );

				return $keypair;

			} else {
				$this->addError( 'Error in generateOpenSSLKeypair: OpenSSL PHP extension missing. Cannot continue.' );
				return false;
			}

		} catch ( Exception $e ) {
			while ( $msg = openssl_error_string() )
				$this->addError( 'Error in generateOpenSSLKeypair: OpenSSL reported error: ' . $msg );

			$this->addError( 'Error in generateOpenSSLKeypair(): ' . $e->getMessage() );
			return false;
		}

	}


	final public function generateBitcoinAddresspair( $testnet = false, $public_key = '', $private_key = '' ) {
		/* generated address keypairs will not be saved to disk, only returned.
		 * also, be careful with your private key. if it is compromised, any BTC
		 * associated with that address could be stolen. use at own risk!
		 */

		try {

			if ( $public_key == '' || $private_key == '' ) {
				$this->addError( 'Error in generateBitcoinAddresspair(): You must specify a EC keypair to ' .
								'generate a Bitcoin address pair.' );

				return false;
			}

			if ( $testnet )
				return self::BTCAddress_testnet( $public_key, $private_key );
			else
				return self::BTCAddress( $public_key, $private_key );

		} catch ( Exception $e ) {
			$this->addError( 'Error in generateBitcoinAddresspair(): ' . $e->getMessage() );
			return false;
		}

	}


	final public function asFacade( $newFacade ) {
		/* the facade determines the type of resource accessible
		 * either public, user, merchant or payroll. in the API
		 * document, this is referred to as 'scope'.
		 */

		try {

			/* check the newly requested facade against the list of
			 * available facades for this user. the public facade is
			 * usable by anyone and doesn't count.
			 */
			if ( isset( $newFacade )                     &&
				$newFacade != 'public'                                  &&
				in_array( $newFacade, $this->get( 'tokens[\'facades\']' ) ) &&
				trim( $newFacade )                                        != '' ) {

					$this->set( 'facade', strtolower( trim( $newFacade ) ) );

					return true;

			} else {

					if ( $newFacade == 'public' ) {
						/* every user can request the public facade */
						$this->resetFacade();
					} else {
						$this->addError( 'Error in asFacade(): You do not have access to the ' .
										$newFacade . ' facade.' );
						return false;
					}

			}

			/* failsafe if we get to this point. throw an error. */
			$this->addError( 'Error in asFacade(): Unknown facade:  "' . $newFacade .
							'". Cannot send transactions with this value.' );

			return false;

		} catch ( Exception $e ) {
			return 'Error in asFacade(): ' . $e->getMessage();
		}

	}


	final public function get( $name = false, $args = false ) {
		/* TODO: debatable getter method... */
		if ( !$args )
			return $this->__get( $name );
		else
			return $this->sendRequest( 'GET', $args );
	}


	final public function set( $name = false, $val = false, $args = false ) {
		/* TODO: debatable setter method */
		if ( !$args )
			return $this->__set( $name, $val );
		else
			return $this->sendRequest( 'PUT', $args );
	}


	final public function showMethods() {

		try {

			$class = new ReflectionClass( 'BitPay' );
			return $class->getMethods();

		} catch ( Exception $e ) {
			return 'Error in showMethods(): ' . $e->getMessage();
		}

	}


	final public function showProperties() {

		try {

			$class = new ReflectionClass( 'BitPay' );
			return $class->getProperties();

		} catch ( Exception $e ) {
			return 'Error in showProperties(): ' . $e->getMessage();
		}

	}




	final public static function encodeBase58( $hex ) {

		try {

			/* TODO: give reference for BASE-58 encoding here... */
			if ( strlen( $hex ) %2 != 0 ) {
				$return = 'Error in encodeBase58(): Uneven number of hex characters passed. ' .
						  'Cannot encode the string: ' . $hex;
			} else {
				$orighex = $hex;
				$chars   = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
				$hex     = self::decodeHex( $hex );
				$return  = '';

				/* TODO: why?? */
				$hex     = self::add0x( $hex );

				switch ( MATH_TYPE ) {
					case 'GMP':
						while ( gmp_cmp( $hex, 0 ) == 1 ) {
							$dv     = gmp_div_q( $hex, '58' );
							$rem    = gmp_strval( gmp_div_r( $hex, '58' ) );
							$hex    = $dv;
							$return = $return . $chars[$rem];
						}
						break;

					default:
						$this->addError( 'Error in encodeBase58(): Unknown MATH_TYPE' );
						return false;
						break;
				}

				$return = strrev( $return );

				for ( $i=0; $i < strlen( $orighex ) && substr( $orighex, $i, 2 ) == '00'; $i += 2 )
					$return = '1' . $return;
			}

			return $return;

		} catch ( Exception $e ) {
			return 'Error in encodeBase58(): ' . $e->getMessage();
		}

	}


	final public static function decodeBase58( $base58 ) {

		try {

			$origbase58 = $base58;
			$chars      = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
			$return     = '0';

			for ( $i = 0; $i < strlen( $base58 ); $i++ ) {
				$current = strpos( $chars, $base58[$i] );

				switch ( MATH_TYPE ) {
					case 'GMP':
						$return = gmp_mul( $return, '58' );
						$return = gmp_strval( gmp_add( $return, $current ) );
						break;

					default:
						$this->addError( 'Error in decodeBase58(): Unknown MATH_TYPE' );
						return false;
						break;
				}
			}

			$return = self::encodeHex( $return );

			for ( $i = 0; $i < strlen( $origbase58 ) && $origbase58[$i] == '1'; $i++ )
				$return = '00' . $return;

			if ( strlen( $return ) %2 != 0 )
				$return = '0' . $return;

			return $return;

		} catch ( Exception $e ) {
			return 'Error in decodeBase58(): ' . $e->getMessage();
		}

	}


	final public static function hash160ToAddress( $hash160, $addressversion = ADDRESSVERSION ) {

		try {

			$hash160 = $addressversion . $hash160;
			$check   = pack( 'H*', $hash160 );
			$check   = hash( 'sha256', hash( 'sha256', $check, true ) );
			$check   = substr( $check, 0, 8 );
			$hash160 = strtoupper( $hash160 . $check );

			return self::encodeBase58( $hash160 );

		} catch ( Exception $e ) {
			return 'Error in hash160ToAddress(): ' . $e->getMessage();
		}

	}


	final public static function addressToHash160( $addr ) {

		try {

			$addr = self::decodeBase58( $addr );
			$addr = substr( $addr, 2, strlen( $addr ) - 10 );

			return $addr;

		} catch ( Exception $e ) {
			return 'Error in addressToHash160(): ' . $e->getMessage();
		}

	}


	final public static function checkAddress( $addr, $addressversion = ADDRESSVERSION ) {

		try {

			$addr = self::decodeBase58( $addr );

			if ( strlen( $addr ) != 50 ) {
				$this->addError( 'Error in checkAddress(): Invalid address length.' );
				return false;
			}

			$version = substr( $addr, 0, 2 );

			if ( hexdec( $version ) > hexdec( $addressversion ) ) {
				$this->addError( 'Error in checkAddress(): Unknown address version byte.' );
				return false;
			}

			$check = substr( $addr, 0, strlen( $addr ) - 8 );
			$check = pack( 'H*', $check );
			$check = strtoupper( hash( 'sha256', hash( 'sha256', $check, true ) ) );
			$check = substr( $check, 0, 8 );

			return $check == substr( $addr, strlen( $addr ) - 8 );

		} catch ( Exception $e ) {
			return 'Error in checkAddress(): ' . $e->getMessage();
		}

	}


	final public static function hash160( $data ) {

		try {

			$data = pack( 'H*', $data );
			return strtoupper( hash( 'ripemd160', hash( 'sha256', $data, true ) ) );

		} catch ( Exception $e ) {
			return 'Error in hash160(): ' . $e->getMessage();
		}

	}


	final public static function pubKeyToAddress( $pubkey ) {

		try {

			return self::hash160ToAddress( self::hash160( $pubkey ) );

		} catch ( Exception $e ) {
			return 'Error in pubKeyToAddress(): ' . $e->getMessage();
		}

	}


	final public static function remove0x( $string ) {

		try {

			if ( trim( $string ) == '' || !is_string( $string ) ) {
				$this->addError( 'Error in remove0x(): Value passed was not a string.' );
				return false;
			}

			if ( strtolower( substr( $string, 0, 2 ) ) == '0x' )
				$string = substr( $string, 2 );

			return $string;

		} catch ( Exception $e ) {
			return 'Error in remove0x(): ' . $e->getMessage();
		}

	}


	final public static function add0x( $string ) {

		try {

			if ( !is_string( $string ) ) {
				$this->addError( 'Error in add0x(): Value passed was not a string.' );
				return false;
			}

			if ( strtolower( substr( $string, 0, 2 ) ) != '0x' )
				$string = '0x' . strtoupper( $string );

			return $string;

		} catch ( Exception $e ) {
			return 'Error in remove0x(): ' . $e->getMessage();
		}

	}


	final public static function decodeHex( $hex ) {

		try {

			if ( !is_string( $hex ) ) {
				$this->addError( 'Error in decodeHex(): Value passed was not a string.' );
				return false;
			}

			$hex    = self::add0x( $hex );
			$chars  = '0123456789ABCDEF';
			$return = '0';

			for ( $i=0;$i<strlen( $hex );$i++ ) {
				$current = strpos( $chars, $hex[$i] );

				switch ( MATH_TYPE ) {
					case 'GMP':
						$return = gmp_mul( $return, '16' );
						$return = gmp_strval( gmp_add( $return, $current ) );
						break;

					default:
						return 'Error in decodeHex(): Unknown MATH_TYPE';
						break;
				}
			}

			return $return;

		} catch ( Exception $e ) {
			return 'Error in decodeHex(): ' . $e->getMessage();
		}

	}


	final public static function encodeHex( $dec ) {

		try {

			if ( !is_string( $dec ) ) {
				$this->addError( 'Error in encodeHex(): Value passed was not a string.' );
				return false;
			}

			$chars  = '0123456789ABCDEF';
			$return = '';

			switch ( MATH_TYPE ) {
				case 'GMP':
					while ( gmp_cmp( $dec,0 ) == 1 ) {
						$dv     = gmp_div_q( $dec, '16' );
						$rem    = gmp_strval( gmp_div_r( $dec, '16' ) );
						$dec    = $dv;
						$return = $return . $chars[$rem];
					}
					break;

				default:
					$this->addError( 'Error in encodeHex(): ' . $e->getMessage() );
					return  'Error in encodeHex(): Unknown MATH_TYPE';
					break;
			}

			return strrev( $return );

		} catch ( Exception $e ) {
			$this->addError( 'Error in encodeHex(): ' . $e->getMessage() );
			return 'Error in encodeHex(): ' . $e->getMessage();
		}

	}


	final public function showErrors() {
		return $this->get( 'errors' );
	}


	final public function clearErrors() {
		return $this->set( 'errors', $blank = array() );
	}


	final public function showNotices() {
		return $this->get( 'notices' );
	}


	final public function clearNotices() {
		return $this->set( 'notices', $blank = array() );
	}


	final public function resetFacade() {
		$this->set( 'facade', 'public' );
	}


	final public function getResourceArgs() {
		// TODO: what was I doing here??
	}


	final public function autoloader( $f ) {

		// TODO: Not sure I need this...
		$interfaceFile = 'classes/interface/' . $f . 'Interface.php';

		if ( file_exists( $interfaceFile ) )
			require_once $interfaceFile;

		$classFile = 'classes/' . $f . '.php';

		if ( file_exists( $classFile ) )
			require_once $classFile;

		$utilFile = 'classes/util/' . $f . '.php';

		if ( file_exists( $utilFile ) )
			require_once $utilFile;

	}


	final public function REST( $verb = "GET", $args = array() ) {
		/*
		 * Public method to send REST request to the BitPay gateway.
		 * The default request will be GET.
		 */
		switch( strtoupper( trim( $verb ) ) ) {
			case 'GET':
				break;
			case 'PUT':
				break;
			case 'POST':
				break;
			default:
				/* Unknown verb. */
				break;
		}
	}


	final private function getOpensslDigestMethods() {

		try {

			/* http://www.php.net/manual/en/function.openssl-get-md-methods.php */
			if ( $this->get( 'opensslAvailable' ) ) {
				$digests = openssl_get_md_methods();
				$digests_and_aliases = openssl_get_md_methods( true );

				return array_diff( $digests_and_aliases, $digests );

			} else {
				return false;
			}

		} catch ( Exception $e ) {
			$this->addError( 'Error in getOpensslDigestMethods(): ' . $e->getMessage() );
			return false;
		}

	}


	final private function saveKeypair( $keypair = array() ) {
		/* write newly generated keypair to disk for re-use.
		 * you may change the name of the keyfile as needed
		 * but it is extremely important that this file is NOT
		 * in a web-accessible location!
		 */

		try {

			clearstatcache();

			$filename  = $this->get( 'defaultSaveFile' );
			$directory = trim( $this->get( 'keyfile_dir' ) );

			if ( trim( $filename )  == '' || is_null( $filename ) ||
				trim( $directory ) == '' || is_null( $directory ) ) {

					/* we have no directory or filename information */
					$this->addError( 'Error in saveKeypair(): No filename or directory name provided. ' .
									'Cannot save key data.' );

					return false;
				}

				if ( substr( $directory, -1 ) != '/' )
					$directory .= '/';

				if ( !is_writable( $directory ) ) {
					$this->addError( 'Error in saveKeypair(): Directory "' .
									$directory . '" is not writable. Cannot save key data.' );

					return false;
				}

				$savefile = $directory . $filename;

				$data     = '524d524d524d3002' .
							$keypair['private_key_hex'] . '02' .
							$keypair['public_key']	 . '02';

				if ( file_put_contents( $savefile,
												base64_encode(
																$this->gmp_binconv( $data . hash( 'sha256', $data ) )
															 ),
									  LOCK_EX ) === false ) {

					/* could not save our keypair data */
					$this->addError( 'Error in saveKeypair(): Error writing keyfile data to disk. Check file permissions and disk space.' );
					return false;

				} else {
					$this->addNotice( 'Successfully wrote keypair data to "' . $savefile . '".' );
					return true;
				}

		} catch ( Exception $e ) {
			$this->addError( 'Error in saveKeypair(): ' . $e->getMessage() );
			return false;
		}
	}


	final private function readKeypair( $location = '' ) {
		/* read previously saved keypair for re-use. */

		try {

			clearstatcache();

			if ( trim( $location )  == '' || is_null( $location ) ) {
				/* we have no directory information */
				$this->addError( 'Error in readKeypair(): No directory name provided. Cannot read key data.' );
				return false;
			}

			$filename        = $this->get( 'defaultSaveFile' );
			$directory       = $location;
			$private_key_hex = '';
			$public_key_hex  = '';
			$data            = '';

			if ( trim( $filename )  == '' || is_null( $filename ) ||
				trim( $directory ) == '' || is_null( $directory ) ) {

					/* we have no directory or filename information */
					$this->addError( 'Error in readKeypair(): No filename or directory name provided. Cannot read key data.' );
					return false;
				}

			if ( substr( $directory, -1 ) != '/' )
				$directory .= '/';

			$savefile = $directory . $filename;

			if ( !file_exists( $savefile ) && !is_readable( $savefile ) ) {
				$this->addError( 'Error in readKeypair(): Keypair data filename "' .
								$savefile . '" does not exist or is not readable. Cannot read key data.' );

				return false;
			}

			$data = file_get_contents( $savefile );

			if ( $data === false ) {
				$this->addError( 'Error in readKeypair(): Keypair data filename "' .
								$savefile . '" does not contain any data or is not readable.' );

				return false;
			} else {
				$this->addNotice( 'Keypair data successfully read from filename "' . $savefile . '".' );
				$data = bin2hex( base64_decode( $data ) );
			}

			if ( strtoupper( substr( $data, 0, 12 ) )      == '524d524d524d'	&&
				hash( 'sha256', substr( $data, 0, -64 ) ) == substr( $data, 148 ) &&
				strlen( $data )                         == 212 ) {

					/* check to ensure our data is good and hasn't been tampered with */
					$this->addNotice( 'Keypair data passed security checks. Appears to be valid.' );

					return array(
								'private_key_hex' => substr( $data, 15, 64 ),
								'public_key'      => substr( substr( $data, 81, 64 ) )
								 );

			} else {
					/* our keydata is corrupt/invalid */
					$this->addError( 'Error in readKeypair(): Keypair data is invalid. The file is corrupt and the data cannot be used.' );

					return false;
			}

		} catch ( Exception $e ) {
			$this->addError( 'Error in readKeypair(): ' . $e->getMessage() );
			return false;
		}

	}


	final private function gmpPointAdd( $P, $Q ) {
		/* elliptic curve point addition:
		 *   P + Q = R where
		 *   s = ( yP - yQ ) / ( xP - xQ ) mod p
		 *   xR = s2 - xP - xQ mod p
		 *   yR = -yP + s( xP - xR ) mod p
		 */

		try {
			if ( is_null( $P ) || is_null( $Q ) )
				return 'infinity';

			if ( $P == 'infinity' )
				return $Q;

			if ( $Q =='infinity' )
				return $P;

			if ( $P == $Q )
				return $this->gmpPointDouble( $P );

			$s      = 0;
			$R      = array( 'x' => 0, 'y' => 0, 's' => 0 );

			$m      = gmp_sub( $P['y'], $Q['y'] );
			$n      = gmp_sub( $P['x'], $Q['x'] );
			$o      = gmp_invert( $n, $this->p );
			$st     = gmp_mul( $m, $o );
			$s      = gmp_mod( $st, $this->p );

			$R['x'] = gmp_mod( gmp_sub( gmp_sub( gmp_mul( $s, $s ), $P['x'] ), $Q['x'] ), $this->p );
			$R['y'] = gmp_mod( gmp_add( gmp_sub( 0, $P['y'] ), gmp_mul( $s, gmp_sub( $P['x'], $R['x'] ) ) ), $this->p );
			$R['s'] = gmp_strval( $s );
			$R['x'] = gmp_strval( $R['x'] );
			$R['y'] = gmp_strval( $R['y'] );

			return $R;

		} catch ( Exception $e ) {
			$this->addError( 'Error in gmpPointAdd(): ' . $e->getMessage() );
			return false;
		}

	}


	final private function gmpPointDouble( $P ) {
		/* elliptic curve point doubling:
		 *   2P = R where
		 *   s = ( 3xP2 + a ) / ( 2yP ) mod p
		 *   xR = s2 - 2xP mod p
		 *   yR = -yP + s( xP - xR ) mod p
		 */

		try {
			if ( is_null( $P ) )
				return 'infinity';

			if ( $P == 'infinity' )
				return $P;

			$s      = 0;
			$R      = array( 'x' => 0, 'y' => 0, 's' => 0, 'p' => $this->p, 'a' => $this->a );

			$m      = gmp_add( gmp_mul( 3, gmp_mul( $P['x'], $P['x'] ) ), $this->a );
			$o      = gmp_mul( 2, $P['y'] );
			$n      = gmp_invert( $o, $this->p );
			$n2     = gmp_mod( $o, $this->p );
			$st     = gmp_mul( $m, $n );
			$st2    = gmp_mul( $m, $n2 );
			$s      = gmp_mod( $st, $this->p );
			$s2     = gmp_mod( $st2, $this->p );
			$xmul   = gmp_mul( 2, $P['x'] );
			$smul   = gmp_mul( $s, $s );
			$xsub   = gmp_sub( $smul, $xmul );
			$xmod   = gmp_mod( $xsub, $this->p );
			$R['x'] = $xmod;
			$ysub   = gmp_sub( $P['x'], $R['x'] );
			$ymul   = gmp_mul( $s, $ysub );
			$ysub2  = gmp_sub( 0, $P['y'] );
			$yadd   = gmp_add( $ysub2, $ymul );

			$R['x'] = gmp_strval( $R['x'] );
			$R['y'] = gmp_strval( gmp_mod( $yadd, $this->p ) );
			$R['s'] = gmp_strval( $s );

			return $R;

		} catch ( Exception $e ) {
			$this->addError( 'Error in gmpPointDouble(): ' . $e->getMessage() );
			return false;
		}

	}


	final private function gmpD2B( $num ) {
		/* convert a decimal number to its binary representation */

		try {

			if ( is_null( $num )    ||
				!is_string( $num ) ||
				!isset( $num )     ||
				trim( $num ) == '' ) {

					/* we have missing/invalid number data */
					$this->addError( 'Error in gmpD2B(): Number missing or NULL. Cannot continue.' );

					return false;
				}

			if ( substr( strtolower( $num ),0,2 ) == '0x' )
				$num = self::decodeHex( substr( $num, 2 ) );

			$tmp = $num;
			$bin = '';

			while ( gmp_cmp( $tmp,'0' ) > 0 ) {
				if ( gmp_mod( $tmp, 2 ) == '1' )
					$bin .= '1';
				else
					$bin .= '0';

				$tmp = gmp_div( $tmp, 2 );
			}

			return $bin;

		} catch ( Exception $e ) {
			$this->addError( 'Error in gmpD2B(): ' . $e->getMessage() );
			return false;
		}

	}


	final private function doubleAndAdd( $x, $P ) {
		/* one efficient method for handling scalar
		 * point multiplication. others exist that
		 * offer various benefits over this method.
		 */

		try {

			/* convert our number to binary */
			$tmp = $this->gmpD2B( $x );

			/* obtain the number of bits */
			$n   = strlen( $tmp ) - 1;

			/* our initial starting value */
			$S   = 'infinity';

			/* loop through and double the point each time
			 * but where the bits are set, perform an add
			*/
			while ( $n >= 0 ) {
				$S = $this->gmpPointDouble( $S );

				if ( $tmp[$n] == '1' )
					$S = $this->gmpPointAdd( $S, $P );

				$n--;
			}

			return $S;

		} catch ( Exception $e ) {
			$this->addError( 'Error in doubleAndAdd(): ' . $e->getMessage() );
			return false;
		}

	}


	final private function pointTest( $P, $a, $b, $p ) {
		/* general form of an elliptic curve:
		 * y^2 mod p = x^3 + ax + b mod p
		 *
		 * we will use that function to test if
		 * a point P is on the defined curve.
		 */

		try {

			/* calculate y^2 */
			$y2    = gmp_mul( $P['y'], $P['y'] );

			/* calculate x^3 */
			$x3    = gmp_mul( gmp_mul( $P['x'], $P['x'] ), $P['x'] );

			/* calculate x^3 + ax + b */
			$ax    = gmp_mul( $a, $P['x'] );
			$left  = gmp_strval( gmp_mod( $y2, $p ) );
			$right = gmp_strval( gmp_mod( gmp_add( gmp_add( $x3, $ax ), $b ), $p ) );

			if($this->_testing)
				$this->addNotice("DEBUG - In pointTest: left = $left and right = $right");

			/* if our left term is equal to our right term
			 * our point is good.
			 */
			if ( $left == $right )
				return true;
			else
				return false;

		} catch ( Exception $e ) {
			$this->addError( 'Error in pointTest(): ' . $e->getMessage() );
			return false;
		}

	}


	final private function gmp_hash( $message, $private_key ) {
		/* calculate the ECDSA signature for a given message.
		 * for more info, see the FIPS DSS standard
		 */

		try {

			/* convert the hex message to decimal */
			$e = self::decodeHex( $message );

			/* while r AND s are both not zero, do the following: */
			do {

				/* supplied private key, 'd' */
				$d = self::add0x( $private_key );

				/* get another random number 'k' */
				$k = openssl_random_pseudo_bytes( 32, $cstrong );

				if ( !$cstrong ) {
					die ( 'FATAL: Could not generate cryptographically strong random number. ' .
						 'Your OpenSSL implementation may be broken or old.' );
				}

				$k_hex  = self::add0x( strtoupper( bin2hex( $k ) ) );

				/* get the G point parameters ( x, y ) */
				$Gx     = self::add0x( substr( $this->G,  2, 64 ) );
				$Gy     = self::add0x( substr( $this->G, 66, 64 ) );

				/* calculate a new curve point from Q = k * G ( x1, y1 ) */
				$P      = array( 'x' => $Gx, 'y' => $Gy );
				$R      = $this->doubleAndAdd( $k_hex, $P );
				$Rx_hex = self::encodeHex( $R['x'] );
				$Ry_hex = self::encodeHex( $R['y'] );

				/* ensure our coordinates are padded to 32 bytes */
				while ( strlen( $Rx_hex ) < 64 )
					$Rx_hex = '0' . $Rx_hex;

				while ( strlen( $Ry_hex ) < 64 )
					$Ry_hex = '0' . $Ry_hex;

				/* ok, now that we have a new curve point we can
				 * calculate the actual signature values. first is 'r'
				 * where r = x1 mod n
				 */
				$r = gmp_strval( gmp_mod( self::add0x( $Rx_hex ), $this->n ) );

				/* now calculate 's' where s = k^-1 * ( e+d*r ) mod n */
				$edr  = gmp_add( $e, gmp_mul( $d, $r ) );
				$invk = gmp_invert( $k_hex, $this->n );
				$kedr = gmp_mul( $invk, $edr );
				$s    = gmp_strval( gmp_mod( $kedr, $this->n ) );

				/* the signature is the coordinate pair ( r,s ) */
				$signature = array(
									'r' => self::encodeHex( $r ),
									's' => self::encodeHex( $s )
								  );

				/* ensure our coordinates are padded to 32 bytes */
				while ( strlen( $signature['r'] ) < 64 )
					$signature['r'] = '0' . $signature['r'];

				while ( strlen( $signature['s'] ) < 64 )
					$signature['s'] = '0' . $signature['s'];

				/* according to the spec, if either 'r' or 's' is 0
				 * we have to re-calculate the entire signature
				 */
			} while ( gmp_cmp( $r,'0' ) <= 0 || gmp_cmp( $s, '0' ) <= 0 );

			$this->addNotice( 'Signature generation successful.' );

			/* the 'sig_hex' value is the actual value used in the x-signature header */
			return array(
							'sig_rs'  => $signature,
							'sig_hex' => $this->serializeSig( $signature['r'],
										 $signature['s'] )
						 );

		} catch ( Exception $e ) {
			$this->addError( 'Error in gmp_hash(): ' . $e->getMessage() );
			return false;
		}

	}


	final private function gmp_verifysig( $r, $s, $msg, $Q ) {
		/* calculate the ECDSA signature for a given message.
		 * for more info, see the FIPS DSS standard
		 */

		try {

			if ( !isset( $r )   || trim( $r ) == ''   || is_null( $r )   ||
				!isset( $s )   || trim( $s ) == ''   || is_null( $s )   ||
				!isset( $msg ) || trim( $msg ) == '' || is_null( $msg ) ||
				!isset( $Q )   || !is_array( $Q )    || is_null( $Q ) ) {

					/* we have invalid data to verify */
					$this->addError( 'Error in gmp_verifysig(): Invalid or missing parameters passed to function.' );
					return false;
				}

			/* get the G, P, Q point parameters ( x, y ) */
			$Gx = self::add0x( substr( $this->G, 2, 64 ) );
			$Gy = self::add0x( substr( $this->G, 66, 64 ) );

			$P  = array( 'x' => $Gx, 'y' => $Gy );

			$Qx = self::add0x( substr( $Q, 2, 64 ) );
			$Qy = self::add0x( substr( $Q, 66, 64 ) );

			$Q  = array( 'x' => $Qx, 'y' => $Qy );

			$r  = self::add0x( $r );
			$s  = self::add0x( $s );

			/* check to see if r,s are in [1, n-1] */
			if ( gmp_cmp( $r, 1 ) <= 0 && gmp_cmp( $r, $this->n ) > 0 ) {
				$this->addError( 'Error in gmp_verifysig(): r is out of range!' );
				return false;
			}

			if ( gmp_cmp( $s, 1 ) <= 0 && gmp_cmp( $s, $this->n ) > 0 ) {
				$this->addError( 'Error in gmp_verifysig(): s is out of range!' );
				return false;
			}

			/* calculate the hash 'e' of $msg
			 * first, convert 'e' to decimal
			 */
			$e = self::decodeHex( $msg );

			/* calculate 'w' where w = s^-1 mod n */
			$w = gmp_invert( $s, $this->n );

			/* calculate 'u1' where u1 = e*w mod n */
			$u1 = gmp_mod( gmp_mul( $e, $w ), $this->n );

			/* calculate 'u2' where u2 = r*w mod n */
			$u2 = gmp_mod( gmp_mul( $r, $w ), $this->n );

			/* Get new point 'Z' from two scalar multiplies and one add:
			 * Z( x1,y1 ) = ( u1 * G ) + ( u2 * Q )
			 */
			$Za = $this->doubleAndAdd( $u1, $P );
			$Zb = $this->doubleAndAdd( $u2, $Q );
			$Z  = $this->gmpPointAdd( $Za, $Zb );

			$Zx_hex = self::encodeHex( $Z['x'] );
			$Zy_hex = self::encodeHex( $Z['y'] );

			/* ensure our coordinates are padded to 32 bytes */
			while ( strlen( $Zx_hex ) < 64 )
				$Zx_hex = '0' . $Zx_hex;

			while ( strlen( $Zy_hex ) < 64 )
				$Zy_hex = '0' . $Zy_hex;

			/* the signature is valid if r is congruent to x1 ( mod n )
			 * or, in other words, if r - x1 is an integer multiple of n
			 */
			$rsubx     = gmp_sub( $r, self::add0x( $Zx_hex ) );
			$rsubx_rem = gmp_div_r( $rsubx, $this->n );

			if ( gmp_cmp( $rsubx_rem, '0' ) == 0 )
				return true;
			else
				return false;

		} catch ( Exception $e ) {
			$this->addError( 'Error in gmp_verifysig(): ' . $e->getMessage() );
			return false;
		}

	}


	final private function serializeSig( $r,$s ) {
		/* ASN.1 encodes the DER signature:
		 * 0x30 + size( all ) + 0x02 + size( r ) + r + 0x02 + size( s ) + s
		 */

		try {

			if ( !isset( $r )   || trim( $r ) == ''   || is_null( $r ) ||
				!isset( $s )   || trim( $s ) == ''   || is_null( $s ) ) {

					/* we have missing or null data passed */
					$this->addError( 'Error in serializeSig(): The r and s values cannot be missing or NULL.' );
					return false;
				}


			for ( $x=0;$x<256;$x++ )
				$digits[$x] = chr( $x );

			$dec    = self::decodeHex( $r );
			$byte   = '';
			$seq    = '';
			$retval = array();

			while ( gmp_cmp( $dec,'0' ) > 0 ) {
				$dv   = gmp_div( $dec, '256' );
				$rem  = gmp_strval( gmp_mod( $dec, '256' ) );
				$dec  = $dv;
				$byte = $byte . $digits[$rem];
			}

			$byte = strrev( $byte );

			/* if the msb is set add 0x00 */
			if ( gmp_cmp( self::add0x( bin2hex( $byte[0] ) ), '0x80' ) >= 0 )
				$byte = chr( 0x00 ) . $byte;

			$retval['bin_r'] = bin2hex( $byte );
			$seq             = chr( 0x02 ) . chr( strlen( $byte ) ) . $byte;
			$dec             = self::decodeHex( $s );
			$byte            = '';

			while ( gmp_cmp( $dec,'0' ) > 0 ) {
				$dv   = gmp_div( $dec, '256' );
				$rem  = gmp_strval( gmp_mod( $dec, '256' ) );
				$dec  = $dv;
				$byte = $byte . $digits[$rem];
			}

			$byte = strrev( $byte );

			/* if the msb is set add 0x00 */
			if ( gmp_cmp( self::add0x( bin2hex( $byte[0] ) ), '0x80' ) >= 0 )
				$byte = chr( 0x00 ) . $byte;

			$retval['bin_s'] = bin2hex( $byte );
			$seq             = $seq . chr( 0x02 ) . chr( strlen( $byte ) ) . $byte;
			$seq             = chr( 0x30 ) . chr( strlen( $seq ) ) . $seq;
			$retval['seq']   = bin2hex( $seq );

			return $retval;

		} catch ( Exception $e ) {
			$this->addError( 'Error in serializeSig(): ' . $e->getMessage() );
			return false;
		}

	}


	final private function generateSIN( $publicKey ) {
		/* the SIN is defined as the base58 representation of
		 * SINVERSION + SINTYPE + RIPEMD-160( SHA-256( public key ) )
		 * see: https://en.bitcoin.it/wiki/Identity_protocol_v1
		 */

		try {

			if ( is_null( $publicKey )        ||
				!isset( $publicKey )         ||
				trim( $publicKey ) == ''     ||
				strlen( $publicKey ) < 64    ||
				!$this->checkKeyfile( $publicKey ) ) {

					$this->addError( 'Error in generateSIN: Invalid public key passed to function.' );
					return false;
				}

			/* take the sha256 hash of the public key in binary form and returning binary */
			$step1   = hash( 'sha256', gmp_binconv( $pub2 ), true );

			/* take the ripemd160 hash of the sha256 hash in binary form returning hex */
			$step2   = hash( 'ripemd160', $step1 );

			/* prepend the hex version and hex SINtype to the hex form of the ripemd160 hash */
			$step3   = SINVERSION . SINTYPE . $step2;

			/* convert the appended hex string back to binary and double sha256 hash it leaving it in binary both times */
			$step4   = hash( 'sha256', hash( 'sha256', gmp_binconv( $step3 ), true ), true );

			/* convert it back to hex and take the first 4 hex bytes for a checksum */
			$step5   = substr( bin2hex( $step4 ), 0, 8 );

			/* append the first 4 bytes to the fully appended string in step 3 */
			$step6   = $step3 . $step5;

			/* finally base58 encode it and you're done! */
			$encoded = trim( encodeBase58( $step6 ) );

			if ( substr( $encoded, 0, 1 ) == 'T' ) {
				/* the SIN should start with a 'T' so if it doesn't something went wrong in the hashing */
				return $encoded;
			} else {
				$this->addError( 'Error in generateSIN(): Invalid SIN generated. It should start with the "T" character but I got "' .
								$encoded . '" instead. Cannot use this value.' );
				return false;
			}

		} catch ( Exception $e ) {
			$this->addError( 'Error in generateSIN(): ' . $e->getMessage() );
			return false;
		}

	}


	final private function saveSIN( $sin = '' ) {
		/* write newly generated SIN to disk for re-use.
		 * you may change the name of this file as needed
		 * but it is extremely important that this file is NOT
		 * in a web-accessible location!
		 */

		try {

			if ( !isset( $sin )	||
				trim( $sin ) == '' ||
				is_null( $sin ) ) {

					/* must have SIN data to store! */
					$this->addError( 'Error in saveSIN(): Missing or NULL SIN data passed to this function.' );
					return false;
				}

				$filename  = $this->get( 'defaultSINFile' );
				$directory = trim( $this->get( 'keyfile_dir' ) );

				if ( trim( $filename )    == ''   ||
					is_null( $filename )         ||
					$trim( $directory )  == ''   ||
					is_null( $directory ) ) {

						/* we have no filename or directory information to work with */
						$this->addError( 'Error in saveSIN(): Missing directory or filename information. Cannot save SIN data to disk.' );
						return false;
					}

				if ( substr( $directory,-1 ) != '/' )
					$directory .= '/';

				if ( !is_writable( $directory ) ) {
					$this->addError( 'Error in saveSIN(): The directory "' . $directory . '" is not writable. Cannot save SIN data to disk.' );
					return false;
				}

				$savefile = $directory . $filename;
				$data     = '524d524d524d3002' . $sin . '02';

				if ( file_put_contents( $savefile,
												base64_encode(
																$this->gmp_binconv( $data . hash( 'sha256', $data ) )
															 ),
									  LOCK_EX ) === false ) {

						$this->addError( 'Error in saveSIN(): Could not write SIN data to file.' );
						return false;

				} else {
						$this->addNotice( 'Successfully wrote SIN data to "' . $savefile . '".' );
						return true;
				}

		} catch ( Exception $e ) {
			$this->addError( 'Error in saveSIN(): ' . $e->getMessage() );
			return false;
		}

	}


	final private function readSIN( $location ) {
		/* read previously saved keypair for re-use. */

		try {

			clearstatcache();

			if ( !isset( $location )      ||
				trim( $location ) == ''  ||
				is_null( $location ) ) {

						/* must have file location to locate file */
						$this->addError( 'Error in readSIN(): Missing or NULL location data passed to this function.' );
						return false;
				}

				$filename        = $this->get( 'defaultSINFile' );
				$directory       = $location;
				$private_key_hex = '';
				$public_key_hex  = '';
				$data            = '';

				if ( trim( $filename )    == '' ||
					is_null( $filename )       ||
					$trim( $directory )  == '' ||
					is_null( $directory ) ) {

							/* we have no filename or directory information to work with */
							$this->addError( 'Error in readSIN(): Invalid fileame or directory data. Cannot read file from disk.' );
							return false;
				}

				if ( substr( $directory, -1 ) != '/' )
					$directory .= '/';

				$savefile = $directory . $filename;

				if ( !file_exists( $savefile ) && !is_readable( $savefile ) ) {
					$this->addError( 'Error in readSIN(): The filename "' .
									$savefile . '" does not exist or cannot be read. Check the file permissions.' );

					return false;
				}

				$data = file_get_contents( $savefile );

				if ( $data === false ) {
					$this->addError( 'Error in readSIN(): The filename "' .
									$savefile . '" is empty or could not be read. Check the file permissions.' );

					return false;
				} else {
					/* good, we could read the file and it contained data */
					$data = bin2hex( base64_decode( $data ) );
				}

				/* check to see if our data has been tampered with is has been corrupted */
				if ( strtoupper( substr( $data, 0, 12 ) )      == '524d524d524d' &&
					hash( 'sha256', substr( $data, 0, -64 ) ) == substr( $data, -64 ) ) {

						$this->addError( 'Error in readSIN(): The filename "' .
										$savefile . '" contained invalid or corrupted data. Cannot use this SIN file.' );

						return $SIN = substr( $data, 15, -66 );
				} else {
						$this->addNotice( 'Error in readSIN(): The filename "' .
										 $savefile . '" passed the security checks. Successfully loaded data.' );

						return false;
				}

		} catch ( Exception $e ) {
			$this->addError( 'Error in readKeypair(): ' . $e->getMessage() );
			return false;
		}

	}





	final private function getAccessTokens() {
		/* TODO: Flesh this function out more... */

		// Send a GET request to /tokens?nonce=( some higher nonce )

		// Example response:
		//  {"data":[{"merchant":"B7jGBcyMUc5GqGFvdMu4JytSbuU3Y2MnjYWoMeJtWf7p"},{"user/sin":"GXzw3Hyr8baajPfbkmJm7DsEtEDdjg3AKABVBz8hn5t2"}]}
		if( !isset( $this->tokens['merchant'] ) || !isset( $this->tokens['user/sin'] ) || !isset( $this->tokens['payroll'] ) ) {
			// We haven't retrieved our tokens/facades yet.
			$request = $this->sendRequest( 'GET', array( 'resource' => 'tokens', 'params' => '' ) );
		}

		return $this->get( 'tokens' );
	}


	final private function getNonce() {

		try {

			/* microtime() is preferred because it returns
			 * a higher resolution value
			 */
			if ( function_exists( 'microtime' ) )
				return str_replace( '.', '', microtime( true ) );
			else
				return time();

		} catch ( Exception $e ) {
			return 'Error in getNonce(): ' . $e->getMessage();
		}

	}


	final private function checkKeyfile( $data ) {
		/* TODO: attempt to determine if the format valid
		 * PEM or DER and return the result
		 */

	}


	final private function parseKeyfile( $data ) {
		/* TODO: convert the keyfile contents from either
		 * PEM or DER into hex format used here
		 */
	}


	final private function gmp_binconv( $hex ) {

		try {

			/* converts hex value into byte array */
			for ( $x=0; $x<256; $x++ )
				$digits[$x] = chr( $x );

			$dec  = self::add0x( $hex );

			$byte = '';
			$seq  = '';

			while ( gmp_cmp( $dec, '0' ) > 0 ) {
				$dv   = gmp_div( $dec, '256' );
				$rem  = gmp_strval( gmp_mod( $dec, '256' ) );
				$dec  = $dv;
				$byte = $byte . $digits[$rem];
			}

			$byte = strrev( $byte );

			return $byte;

		} catch ( Exception $e ) {
			return 'Error in gmp_binconv(): ' . $e->getMessage();
		}

	}


	final private function sendRequest( $verb = 'GET', $arguments ) {

		/* note: the api is currently only on the bitpay test server and
		 * not released for production. some of the methods may change.
		 * for more information on the new API requests, see:
		 * https://test.bitpay.com/api
		 *
		 * Two special headers must be present for all non-public facades:
		 * x-pubkey and x-signature
		 */
		try {

			if( $arguments == ''                 ||
			    is_null( $arguments )            ||
			    !isset( $arguments['resource'] ) ||
			    !isset( $arguments['params']   )   ) {

					$this->addError( 'Error in sendRequest(): Missing request arguments. Cannot process request.' );
					return false;
			}

			array( 'resource' => 'tokens', 'params' => '' );

			$userFacade = $this->get( 'facade' );

			switch ( $this->get( 'facade' ) ) {

				/*
				 * These are the three non-public facade types
				 * which requre the special x-headers.
				 */
				case 'user':
				case 'merchant':
				case 'payroll':
					if ( $this->get( 'pubECKey' ) != '' ) {
						$xpubkey = 'x-pubkey: ' . $this->get( 'pubECKey' );
					} else {
						$this->addError( 'Error in sendRequest(): Public key missing or invalid. Cannot process request.' );
						return false;
					}
					break;

				/*
				 * public specific headers
				 */
				case 'public':
					break;

				/*
				 * unknown facade
				 */
				default:
					$this->addError( 'Error in sendRequest(): Unknown facade. Cannot process request.' );
					return false;
			}

			/*
			 * The nonce is used to prevent transaction replay and therefore each
			 * nonce has to be higher than the one before. So briefly loop until
			 * getNonce() is higher than $currentNonce. Nonces are required for
			 * every API request regardless of verb.
			 */
			$currentNonce = $this->getNonce();

			/*
			 * Sanity check value to prevent infinite loops
			 */
			$sanity	  = 0;

			while ( ( $currentNonce == $this->get( 'nonce' ) ) && $sanity < 50 ) {
				$this->set( 'nonce', $this->getNonce() );
				$sanity++;
			}

			if ( $sanity >= 50 ) {
				$this->addError( 'Error in sendRequest: The plugin tried to generate a higher
								  nonce but could not. Was your webserver\'s time changed?' );
				return false;
			}

			$requestString = '';
			$signature     = '';

			/*
			 * This master switch/case block is based on the REST verb and
			 * contains the verb-specific resource logic inside each case.
			 * Supported verbs are GET, PUT and POST.
			 *
			 * For public resources, the $signature variable will never be
			 * set to any value since the x-signature HTTP header is not
			 * required for that facade.  Otherwise, the signature will
			 * be created after the request string is built.
			 *
			 * TODO: When building the signature, if it fails add a message
			 * to the error array.
			 */
			switch ( strtoupper( $verb ) ) {

				/*
				 * GET is the default verb when one is not specified
				 * and retrieves information about a particular resouce.
				 */
				case 'GET':

					/*
					 * >>> bills - merchant
					 *     Retrieves all of the caller's bills.
					 */
					if ( $userFacade            == 'merchant' &&
						 $arguments['resource'] == 'bills'    &&
						 $arguments['params']   == ''            ) {

							$requestString = $this->bitpayURL . '/bills?nonce=' .
											 $this->getNonce();

							$signature     = $this->generateSignature( $requestString, $this->privECKey );

							if ( $signature )
								'x-signature: ' . $signature;
							else
								return false;
					}

					/*
					 * >>> bills - merchant
					 *     Retrieves all of the caller's bills with a status of X
					 *
					 *     string params: status
					 */
					if ( $userFacade                                 == 'merchant' &&
						 $arguments['resource']                      == 'bills'    &&
						 (
						   $arguments['params']                      != ''         ||
						   stristr( $arguments['params'],'status=' ) !== false
						 )                                                            ) {

							$requestString = $this->bitpayURL . '/bills/{"status":"' .
											 substr( $arguments['params'], 6, strlen( $arguments['params'] ) ) .
											 '","nonce":"' . $this->getNonce().'"}';

							$signature     = $this->generateSignature( $requestString, $this->privECKey );

							if ( $signature )
								'x-signature: ' . $signature;
							else
								return false;
					}

					 /*
					  * >>> bills/:billId - merchant
					  *     Retrieves the specified bill.
					  */
					if ( $userFacade                                 == 'merchant' &&
						 $arguments['resource']                      == 'bills'    &&
						 (
						   $arguments['params']                      != ''         ||
						   stristr( $arguments['params'],'billId=' ) !== false
						 )
																					  ) {

							$requestString = $this->bitpayURL . '/bills/{"billId":"' .
											 substr( $arguments['params'], 6, strlen( $arguments['params'] ) ) .
											 '","nonce":"' . $this->getNonce().'"}';

							$signature     = $this->generateSignature( $requestString, $this->privECKey );

						if ( $signature )
							'x-signature: ' . $signature;
						else
							return false;
					}

					/*
					 * >>> clients - user
					 *     Retrieves the registered client access keys for the caller.
					 */
					if ( $userFacade            == 'user'    &&
						 $arguments['resource'] == 'clients' &&
						 $arguments['params']   == ''           ) {

							$requestString = $this->bitpayURL . '/clients?nonce=' . $this->getNonce();
							$signature     = $this->generateSignature( $requestString, $this->privECKey );

							if ( $signature )
								'x-signature: ' . $signature;
							else
								return false;
					}

					 /*
					  * >>> currencies - public
					  *     Retrieves the list of supported currencies.
					  */
					if ( $userFacade            == 'public'     &&
						 $arguments['resource'] == 'currencies' &&
						 $arguments['params']   == ''              ) {

							$requestString = $this->bitpayURL . '/currencies?nonce=' . $this->getNonce();
					}

					 /*
					  * >>> currencies/CODE - public
					  *     Retrieves the specified currency.
					  */
					if ( $userFacade                               == 'public'     &&
						 $arguments['resource']                    == 'currencies' &&
						 ( $arguments['params']                    != ''           ||
						   stristr( $arguments['params'],'code=' ) !== false )        ) {

							$requestString = $this->bitpayURL . '/currencies/' .
											 substr( $arguments['params'], 4, strlen( $arguments['params'] ) ) .
											 '?nonce=' . $this->getNonce();
					}

					 /*
					  * >>> invoices - merchant
					  *     Retrieves invoices for the calling merchant filtered by query.
					  *
					  *     string params: status, orderId, itemCode, dateStart (required), dateEnd
					  *     number params: limit, skip
					  */
					if( $userFacade            == 'merchant' &&
						$arguments['resource'] == 'invoices' &&
						$arguments['params']   == ''            ) {

							$this->addError( 'Error in sendRequest: No parameters specified for the invoices resource.' );
							return false;

					} else {

						$invoicesParams = explode( ',', $arguments['params'] );

						array_push();

						if( !isset( $invoicesParams['dateStart'] ) )
							return false;

						$invoicesParams = json_encode( $invoicesParams );


					}

					 /*
					  *     invoices/:invoiceId
					  */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$requestString = $this->bitpayURL . '/currencies?nonce=' . $this->getNonce();
					}

					 /*
					 *     invoices/:invoiceId/refunds
					 *
					 */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$requestString = $this->bitpayURL . '/currencies?nonce=' . $this->getNonce();
					}

					 /*     invoices/:invoiceId/refunds/:requestId
					 *
					 */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$requestString = $this->bitpayURL . '/currencies?nonce=' . $this->getNonce();
					}


					 /*
					 * >>> ledgers - merchant
					 *     Retrieves the caller's ledgers for each currency with summary.
					 */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$requestString = $this->bitpayURL . '/currencies?nonce=' . $this->getNonce();
					}

					 /*
					 *     ledgers/:currency
					 *
					 *     string params: startDate, endDate
					 */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$requestString = $this->bitpayURL . '/currencies?nonce=' . $this->getNonce();
					}

					 /*
					 * >>> orgs - user
					 *     Retrieves caller's organizations
					 */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$requestString = $this->bitpayURL . '/currencies?nonce=' . $this->getNonce();
					}

					 /*
					 * >>> payouts - payroll
					 *     Retrieves caller's payroll payouts
					 */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$requestString = $this->bitpayURL . '/currencies?nonce=' . $this->getNonce();
					}

					 /*
					  *     payouts/:payoutId
					  *     string params: status
					  */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$requestString = $this->bitpayURL . '/currencies?nonce=' . $this->getNonce();
					}

					 /*
					  * >>> rates - public
					  *     Retrieves the exchange rate for all currencies.
					  */
					if ( $userFacade            == 'merchant' &&
						 $arguments['resource'] == 'bills'    &&
						 $arguments['params']   == ''            ) {

							$requestString = $this->bitpayURL . '/rates?nonce=' . $this->getNonce();

					}

					 /*
					  * >>> rates/:currency - public
					  *     Retrieves the exchange rate for the given currency.
					  *
					  *     string params: format
					  */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$requestString = $this->bitpayURL . '/rates?nonce=' . $this->getNonce();
					}

					 /*
					  * >>> tokens - user
					  *     Retrieves the callers facade access tokens.
					  */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$requestString = $this->bitpayURL . '/rates?nonce=' . $this->getNonce();
					}

					 /*
					  * >>> user - user
					  *     Retrieves caller's user information.
					  */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$requestString = $this->bitpayURL . '/rates?nonce=' . $this->getNonce();
					}


					 /*
					  * End of GET verb logic.
					  */
					break;


				/*
				 * The POST verb creates a resource and takes a special GUID parameter
				 * to enforce idempotence (i.e. subsequent calls to the same POST payload
				 * should not create an additional resource of the same type.)
				 */
				case 'POST':

					/*
					 * >>> applications - public
					 *     Creates an application for a new merchant account.
					 *
					 *     array params: users => email ( string ), firstname ( string ), lastname ( string ), phone ( string ), agreedToTOSandPP ( boolean )
					 *                   orgs  => name ( string ), address1 ( string ), address2 ( string ), city ( string ), zip ( string ), country ( string ),
					 *	                          isNonProfit ( boolean ), usTaxId ( string ), industry ( string ), website ( string ), cartPos ( string ),
					 *	                          affiliate0id ( string )
					 */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$this->set( 'GUID', $this->generateGUID() );
						$requestString = $this->bitpayURL . '/rates?nonce=' . $this->getNonce();
					}

					 /*
					  * >>> bills - merchant
					  *     Creates a bill for the calling merchant.
					  *     [  array params: items => description ( string ), price ( number ), quantity ( number )                                              ]
					  *     [ string params: currency, showRate, archived, name, address1, address2, city, state, zip, country, email, phone               ]
					  */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$this->set( 'GUID', $this->generateGUID() );
						$requestString = $this->bitpayURL . '/rates?nonce=' . $this->getNonce();
					}

					 /*
					  * >>> invoices - merchant
					  *     Creates an invoice for the calling merchant.
					  *     [ number params: price                                                                                                         ]
					  *     [ string params: currency, orderID, itemDesc, itemCode, notificationEmail, notificationURL, redirectURL, posData               ]
					  */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$this->set( 'GUID', $this->generateGUID() );
						$requestString = $this->bitpayURL . '/rates?nonce=' . $this->getNonce();
					}

					 /*
					  * >>> invoices/:invoiceId/refunds - merchant
					  *     Creates a refund request for the given invoice.
					  *     [ number params: amount                                                                                                        ]
					  *     [ string params: bitcoinAddress, currency                                                                                      ]
					  */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$this->set( 'GUID', $this->generateGUID() );
						$requestString = $this->bitpayURL . '/rates?nonce=' . $this->getNonce();
					}

					 /*
					  * >>> invoices/:invoiceId/notifications - merchant
					  *     Resends the IPN for the specified invoice
					  */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$this->set( 'GUID', $this->generateGUID() );
						$requestString = $this->bitpayURL . '/rates?nonce=' . $this->getNonce();
					}

					 /*
					  * >>> keys - public
					  *     Adds the given key ( SIN ) to the given account.
					  *     [ string params: sin, email, label                                                                                             ]
					  */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$this->set( 'GUID', $this->generateGUID() );
						$requestString = $this->bitpayURL . '/rates?nonce=' . $this->getNonce();
					}

					 /*
					  * >>> payouts - payroll
					  *     Creates a payout batch request.
					  *     [  array params: instructions=> amount ( number ), address ( string ), label ( string )                                              ]
					  *     [ number params: amount, effectiveDate                                                                                         ]
					  *     [ string params: currency, reference, pricingMethod, notificationEmail, notificationURL                                        ]
					  */
					if ( $userFacade == 'merchant' && $arguments['resource'] == 'bills' && $arguments['params'] == '' ) {
						$this->set( 'GUID', $this->generateGUID() );
						$requestString = $this->bitpayURL . '/rates?nonce=' . $this->getNonce();
					}

					/*
					 * End of POST verb logic.
					 */
					break;


				/*
				 * The PUT verb is used for updating an existing resource.
				 */
				case 'PUT':

					/* >>> bills/:billId
					 *     Updates the specified bill.
					 *     [  array params: items => description ( string ), price ( number ), quantity ( number )                                              ]
					 *     [ string params: currency, showRate, archived, name, address1, address2, city, state, zip, country, email, phone               ]
					 *
					 *
					 * >>> keys/:keyId
					 *     Modifies the given key ( approving or disabled ).
					 *     [   bool params: disabled, approved                                                                                            ]
					 *     [ string params: verificationCode                                                                                              ]
					 *
					 *
					 * >>> orgs/:orgId
					 *     Updates organizations's merchant plan.
					 *     [   bool params: notifyOnPaid, notifyOnComplete                                                                                ]
					 *     [ string params: smsNumber, name, address1, address2, city, state, region, zip, country, industry, contactName, phone          ]
					 *     [                website, description, cartPOSsoftware, pricingCurrency, payoutCurrency, orderEmail, transactionSpeed          ]
					 *     [ number params: payoutPercentage                                                                                              ]
					 *
					 *
					 * >>> payouts/:payoutId
					 *     Updates the given payout request.
					 *     [ string params: status                                                                                                        ]
					 *
					 *
					 * >>> user
					 *     Updates caller's user information.
					 *     [ string params: phone, name                                                                                                   ]
					 *
					 *
					 */

					/*
					 * End of POST verb logic.
					 */
					break;

				default:
					$this->addError( 'The verb ' . var_export( $verb,true ) . ' is not supported.' );
					return false;
					break;
			}

			/*
			 * TODO: Include CURL code to make the call
			 */

			/*
			 * TODO: handle response here from API call.
			 */


		} catch ( Exception $e ) {
			$this->addError( 'Error in sendRequest(): ' . $e->getMessage() );
			return false;
		}

	}


	final private function addError( $msg ) {

		try {

			$errs = $this->get( 'errors' );

			if ( count( $errs ) < 64 ) {
				array_push( $errs, date( 'Y-m-d H:i:s' ) . ' :: ' . $msg );
				$this->set( 'errors', $errs );
			} else {
				$this->writeToLog( $errs );
				$this->clearErrors();
				array_push( $errs, date( 'Y-m-d H:i:s' ) . ' :: ' . $msg );
				$this->set( 'errors', $errs );
			}

			return true;

		} catch ( Exception $e ) {
			return false;
		}

	}


	final private function addNotice( $msg ) {

		try {

			$msgs = $this->get( 'notices' );

			if ( count( $msgs ) < 64 ) {
				array_push( $msgs, date( 'Y-m-d H:i:s' ) . ' :: ' . $msg );
				$this->set( 'notices', $msgs );
			} else {
				$this->clearNotices();
				array_push( $msgs, date( 'Y-m-d H:i:s' ) . ' :: ' . $msg );
				$this->set( 'notices', $msgs );
			}

			return true;

		} catch ( Exception $e ) {
			$this->addError( 'Error in sendRequest(): ' . $e->getMessage() );
			return false;
		}

	}


	final private function processResponse( $resp = array() ) {

		try {

			$processed_response = '';

			// TODO: handle response from sendRequest

			if ( isset( $resp ) ) {
				// TODO: do something

				return $processed_response;

			} else {
				$this->addError( 'Attempted to process a non-existent response.' );
				return false;
			}

		} catch ( Exception $e ) {
			$this->addError( $e->getMessage() );
			return false;
		}

	}


	final private function writeToLog( $filename = '', $data ) {

		try {

			$msg = '';

			foreach ( $data as $key => $value )
				$msg .= $value . "\r\n";

			//file_put_contents( $filename, $msg, FILE_APPEND );

			// TODO: flesh this out...
			//if()
				error_log($data);


		} catch ( Exception $e ) {
			$this->addError( $e->getMessage() );
			return false;
		}

	}


	final private function generateGUID() {

		try {

			$guid = '';

			if ( function_exists( 'com_create_guid' ) ) {
				$this->addNotice( 'Using Microsoft GUID creation method.' );
				return trim( com_create_guid(), '{}' );
			} elseif ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
				$this->addNotice( 'Using secure openssl random GUID creation method.' );
				$guid = bin2hex( openssl_random_pseudo_bytes( 4 ) ) . '-' .
						bin2hex( openssl_random_pseudo_bytes( 2 ) ) . '-' .
						bin2hex( openssl_random_pseudo_bytes( 2 ) ) . '-' .
						bin2hex( openssl_random_pseudo_bytes( 2 ) ) . '-' .
						bin2hex( openssl_random_pseudo_bytes( 6 ) );
			} else {
				$this->addNotice( 'Using fallback mt_rand GUID creation method.' );
				$guid = substr( md5( mt_rand() ),0,8 ) . '-' .
						substr( md5( mt_rand() ),0,4 ) . '-' .
						substr( md5( mt_rand() ),0,4 ) . '-' .
						substr( md5( mt_rand() ),0,4 ) . '-' .
						substr( md5( mt_rand() ),0,12 );
			}

			return strtoupper( $guid );

		} catch ( Exception $e ) {
			$this->addError( $e->getMessage() );
			return false;
		}

	}

	/* End BitPay Class */
}
