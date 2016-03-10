# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased][unreleased]
## [3.0.0] - 2016-03-10
###Fixed
- should respect that numbers coming from the api might not have decimal points at all, but they are still valid
- Bug fixes that include proper type checking for invoice object creation
- Fixed invoice, expiration, and current time error
- Repaired tests based on current changes to the code.

###Added
- Using this library with Symfony >=2.3 or Symfony 3.0.x should work. So if you are already using Symfony 3.0, then this library should work with it too.
- Leo Hochberg added PaymentUrl support to the library since the BitPay backend support it. Thanks Leo.

## [2.2.7] - 2015-10-28
###Fixed
- btcPaid on invoice responses now contains the amount paid instead of the price

## [2.2.6] - 2015-07-31
###Fixed
- The fixes several issues, including "Price only accepts .00 precision" and "Invoice exception state"

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
