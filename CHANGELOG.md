# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).


## [2.2.19] - 2018-03-08
### Added
- Added support for BCH fields in the invoice: transactionCurrency, amountPaid, exchangeRates, paymentSubtotals, paymentTotals

### Fixed
- Set timezone to UTC (=timezone that BitPay invoices use), to prevent PHP errors when no default timezone is set

### Deprecated
- Deprecated BTC specific fields, as documented at https://bitpay.com/api#resource-Invoices


## [2.2.18] - 2018-01-15
### Fixed
- Pushed actual code changes to GitHub from previous release


## [2.2.17] - 2018-01-15
### Fixed
- Fixed decimal check for currencies without decimals (e.g. HUF)

## [2.2.16] - 2017-12-12
### Fixed
- Fixed invoice time being set in milliseconds, whereas seconds are expected (Issue #256 and #257)
- Removed deprecated factory methods from services.xml


## [2.2.15] - 2017-11-28
### Fixed
- Fixed invoice time being set as numeric instead of datetime object

### Removed
- Removed support for mcrypt (#254)


## [2.2.14] - 2017-09-27
### Fixed
- Fixed token check in get invoices method for public facade calls (#243)
- Spell fixes (#233 and #233)
- Fix some of the tests (#228)

### Added
- Added encrypt & decrypt functions to OpenSSL (#247)


## [2.2.13] - 2017-05-12
### Fixed
- Updated VERSION and changelog
- Clarified tutorial text


## [2.2.12] - 2017-05-09
### Added
- Included IPN processor example in tutorials

### Fixed
- broken exception in Client.php


## [2.2.11] - 2017-05-09
### Added
- Added refund addresses to getInvoice
- Included extendedNotifications

Included buyer notify field (when creating an invoice)
### Fixed
- Improved tutorial (https://github.com/bitpay/php-bitpay-client/tree/master/examples/tutorial)
- Made fullNotifications=true default
- Symfony v3 compatibility fixes
- PHP 7 compatibility fixes

## [2.2.9] - 2017-02-21
### Fixed
- HTTP 100 messages not parsed correctly when doing payouts

## [Unreleased][unreleased]
### Changed
- Refactored function calls out of loops

## [2.2.5] - 2015-06-23
### Fixed
- Item::setPrice accepts en_US formatted numeric strings
- getInvoice now works with merchant and public facades

## [2.2.4] - 2015-06-04
### Added
- Token functionality for client-sided pairing
- Set payout bitcoin amount, as returned by BitPay
- Utility method for checking requirements
- Autoloader documentation

### Changed
- Documentation from RST to Markdown
- Autoloader now loads relative to library root directory
- Client now sets invoice tokens upon creation and retrieval

## [2.2.3] - 2015-05-29
### Fixed
- BitPay API errors are now passthrough

## [2.2.2] - 2015-01-13
### Added
- Mink/Behat testing

### Fixed
- Corrected behavior of Math::mod

## [2.2.1] - 2014-12-10
### Added
- Payroll feature
- Detailed tutorials

### Fixed
- Math Engine issues
- Stalling tests

## [2.2.0] - 2014-11-21
### Changed
- No longer solely depends on GMP for big integer math. Can now use BCMath as well

## [2.1.1] - 2014-11-19
### Changed
- Encrypted file storage is now default persistance for keys

### Fixed
- McryptExtensionTest no longer fails randomly

### Removed
- PHP 5.3 support.  Now requires PHP >= 5.4

## [2.1.0] - 2014-11-10
### Added
- Code Coverage tools
- Integration testing
- CA Bundle for Curl Adapter
- Additional invoice tests
- PEM Encoding and Decoding
- Storage Class tutorial
- `fullNotifications` for invoices

### Changed
- Point arrays to objects
- Better exception handling for Curl errors
- Refactored `isGenerated`

### Fixed
- MCrypt cipher type default is now a valid cipher type

## 2.0.0 - 2014-09-27
### Changed
- Client library now uses BitPay's new API

[unreleased]: https://github.com/bitpay/php-bitpay-client/compare/v2.2.5...HEAD
[2.2.5]: https://github.com/bitpay/php-bitpay-client/compare/v2.2.4...v2.2.5
[2.2.4]: https://github.com/bitpay/php-bitpay-client/compare/v2.2.3...v2.2.4
[2.2.3]: https://github.com/bitpay/php-bitpay-client/compare/v2.2.2...v2.2.3
[2.2.2]: https://github.com/bitpay/php-bitpay-client/compare/v2.2.1...v2.2.2
[2.2.1]: https://github.com/bitpay/php-bitpay-client/compare/v2.2.0...v2.2.1
[2.2.0]: https://github.com/bitpay/php-bitpay-client/compare/v2.1.1...v2.2.0
[2.1.1]: https://github.com/bitpay/php-bitpay-client/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/bitpay/php-bitpay-client/compare/v2.0.0...v2.1.0
