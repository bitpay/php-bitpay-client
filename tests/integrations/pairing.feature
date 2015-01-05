# pairing.feature
Feature: pairing with bitpay
  In order to access bitpay
  It is required that the library
  Is able to pair successfully

  @javascript
  Scenario: the client has a correct pairing code
    Given the user pairs with BitPay with a valid pairing code
    Then the user is paired with BitPay

  @javascript
  Scenario Outline: the client has a bad pairing code
    Given the user fails to pair with a semantically <valid> code <code>
    Then they will receive a <error> matching <message>
  Examples:
      | valid   | code       | error                               | message                       |
      | valid   | "a1b2c3d"  | "Bitpay\Client\BitpayException"     | '500: Unable to create token' |
      | invalid | "a1b2c3d4" | "Bitpay\Client\ArgumentException"   | 'pairing code is not legal'   |

  @javascript
  Scenario Outline: the client has a bad port configuration to an incorrect port
    When the client fails to pair with BitPay because <status> port <port> is an incorrect port
    Then they will receive a <error> matching <message>
  Examples:
      | status  | port | error                               | message              |
      | open    | 444  | "Bitpay\Client\ConnectionException" | 'timed out'          |
      | closed  | 8444 | "Bitpay\Client\ConnectionException" | 'Connection refused' |