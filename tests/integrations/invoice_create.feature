#create_invoices.feature
Feature: creating an invoice
  The user won't get any money 
  If they can't
  Create Invoices

  Background:
    Given the user is authenticated with BitPay

  @javascript
  Scenario Outline: The request is correct
    When the user creates an invoice for <price> <currency>
    Then they should recieve an invoice in response for <price> <currency>
  Examples:
    | price    | currency |
    | "1.01"   | "USD"    |
    | "1.01"   | "EUR"    |

  @javascript
  Scenario Outline: The invoice contains illegal characters
    When the user creates an invoice for <price> <currency>
    Then they will receive a "Bitpay\Client\ArgumentException" matching <message>
  Examples:
    | price   | currency  | message                                      |
    | "1,023" | "USD"     | 'Price must be formatted as a float'         |
    | "1.21"  | "EaUR"    | 'The currency code "EaUR" is not supported.' |
    | ""      | "USD"     | 'Price must be formatted as a float'         |
    | "Ten"   | "USD"     | 'Price must be formatted as a float'         |
    | "1"     | ""        | 'The currency code "" is not supported.'     |
