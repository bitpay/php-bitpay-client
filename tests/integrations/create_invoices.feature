#create_invoices.feature
Feature: creating an invoice
  The user won't get any money 
  If they can't
  Create Invoices

  Background:
    Given the user is authenticated with BitPay

  Scenario Outline: The request is correct
    When the user creates an invoice for <price> <currency>
    Then they should recieve an invoice in response for <price> <currency>
  Examples:
    | price    | currency |
    | "500.23" | "USD"    |
    | "300.21" | "EUR"    |

  Scenario Outline: The invoice contains illegal characters
    When the user creates an invoice for <price> <currency>
    Then they will receive a BitPay::ArgumentError matching <message>
  Examples:
    | price    | currency  | message                              |
    | "50,023" | "USD"     | "Price must be formatted as a float" |
    | "300.21" | "EaUR"    | "Currency is invalid."               |
    | ""       | "USD"     | "Price must be formatted as a float" |
    | "Ten"    | "USD"     | "Price must be formatted as a float" |
    | "100"    | ""        | "Currency is invalid."               |