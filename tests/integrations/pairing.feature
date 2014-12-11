# pairing.feature
Feature: pairing with bitpay
  In order to access bitpay
  It is required that the library
  Is able to pair successfully

  Background:
    Given that there is no token saved locally

  Scenario: there is a local keyfile
    Given that there is a local keyfile
    When the user pairs with BitPay with a valid pairing code
    And tokens will be saved locally

  Scenario: there is no local keyfile
    Given that there is no local keyfile
    When the user pairs with BitPay with a valid pairing code
    Then the keyfile will be saved locally
    And tokens will be saved locally

  Scenario: the client has a bad pairing code
    Given the user fails to pair with a semantically valid code "a1b2c3d" 
    Then they will receive a BitPay::BitPayError matching "500: Unable to create token" 

  Scenario: the client has an incorrect pairing code
    Given the user fails to pair with a semantically invalid code "a2b2c3d4"
    Then they will receive a BitPay::ArgumentError matching "pairing code is not legal" 

  Scenario: the client has a bad port configuration to a closed port
    Given that there is a local keyfile
    When the fails to pair with BitPay because of an incorrect port
    Then they will receive a BitPay::ConnectionError matching "Connection refused" 