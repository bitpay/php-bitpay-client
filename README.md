©2011,2012,2013,2014 BITPAY, INC.

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


Change Log
----------
Version 1
  - Initial version

Version 1.1, rich@bitpay.com
  - Improved error handling, documentation
  - Added license information
  - Added automatic logging functionality (off by default)
