<strong>(c)2011,2012,2013,2014 BITPAY, INC.</strong>

Permission is hereby granted to any person obtaining a copy of this software
and associated documentation for use and/or modification in association with
the bitpay.com service.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.


Bitcoin PHP payment library using the bitpay.com service.


Installation
------------
Copy these files into your custom shopping cart implementation directory.


Configuration
-------------
NOTE: PHP 5.x, curl, and SSL is required for use of this BitPay PHP code library.

1. Create an API key at bitpay.com by clicking My Account > API Access Keys > Add New API Key.
2. In the bp_options.php file, configure the options specific to your implementation.


Usage
-----
1. In your shopping cart code, call bpCreateInvoice() with the appropriate orderID, price,
   posData and options.
2. The library will attempt to POST the new invoice information via curl to the BitPay
   network.  If successful, you will receive an invoice in the return response.  Any errors
   in this process will return an array with a single element: 'error' and the exception msg.
3. You may use the bpLog function manually to log any information you would like to track or
   automatically by setting the useLogging option to true in the bp_options file.  The log file
   could potentially get very large, depending on usage, so monitor closely or only use
   during debugging.
4. Responses from the BitPay network are JSON. You can use the new decodeResponse() function to
   convert these to an associative array, if needed.
5. The bpCurrencyList() & bpGetRate() functions can be used to retrieve a list of all supported
   currencies and particular BTC/currency rates.


Troubleshooting
---------------
The official BitPay API documentation should always be your first reference for development:
https://bitpay.com/downloads/bitpayApi.pdf

1. Verify that your "notificationURL" for the invoice is "https://" (not "http://")
2. Ensure a valid SSL certificate is installed on your server. Also ensure your root CA cert is
   updated. If your CA cert is not current, you will see curl SSL verification errors.
3. Verify that your callback handler at the "notificationURL" is properly receiving POSTs. You
   can verify this by POSTing your own messages to the server from a tool like Chrome Postman.
4. Verify that the POST data received is properly parsed and that the logic that updates the
   order status on the merchants web server is correct.
5. Verify that the merchants web server is not blocking POSTs from servers it may not
   recognize. Double check this on the firewall as well, if one is being used.
6. Use the logging functionality to log errors during development. If you contact BitPay support,
   they will ask to see the server log file to help diagnose any problems.
7. Check the version of this PHP library agains the official repository to ensure you are using
   the latest version. Your issue might have been addressed in a newer version of the library.
8. If all else fails, send an email describing your issue *in detail* to support@bitpay.com


Change Log
----------
Version 1
  - Initial version

Version 1.1, rich@bitpay.com
  - Improved error handling, documentation
  - Added license information
  - Added automatic logging functionality (off by default)

Version 1.2, rich@bitpay.com
  - Added posData length checking (using hash can go over 100 char limit)

Version 1.3, rich@bitpay.com
  - Added version & usage statistics tracking stub
  - Added decodeResponse function
  - Fixed missing global options references

Version 1.4, rich@bitpay.com
  - Added bpCurrencyList() & bpGetRate() functions

Version 1.5, rich@bitpay.com
  - Added new HTTP header for version tracking & support purposes

Version 1.6, rich@bitpay.com
  - Fix to use server logging functionality
